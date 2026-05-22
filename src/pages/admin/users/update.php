<?php

$table = "users";
$tableSQL = str_replace("-", "_", $table);

$stmt = $connect->prepare("SELECT * FROM $tableSQL WHERE id = ?");
$stmt->bind_param("i", $_GET["id"]);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $role = trim($_POST["role"]);
    $img = $result["img"];
    $password = trim($_POST["password"]);
    $remove_img = $_POST["remove_img"];


    $error = [];
    if (empty($name)) {
        $error["name"] = "Заполните поле";
    }

    if (empty($email)) {
        $error["email"] = "Заполните поле";
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error["email"] = "Неверный формат email";
    } else if ($email != $result["email"]) {
        $stmt = $connect->prepare("SELECT * FROM users WHERE email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $error["email"] = "Этот email уже зарегистрирован";
        }
    }

    if ($_FILES["img"]["error"] == UPLOAD_ERR_NO_FILE && $remove_img == 1) {
        if (!empty($result["img"]) && empty($error)) {
            $old_file = __DIR__ . "/../../../.." . $result['img'];
            if (file_exists($old_file)) {
                unlink($old_file);
            }
        }
        $img = null;
    }


    if (isset($_FILES["img"]) && $_FILES["img"]["error"] == UPLOAD_ERR_OK) {

        if (!empty($result["img"]) && empty($error)) {
            $old_file = __DIR__ . "/../../../.." . $result['img'];
            if (file_exists($old_file)) {
                unlink($old_file);
            }
        }

        $file = $_FILES["img"];

        $allowed = ["jpg", "jpeg", "png", "webp", "gif"];
        $ext = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            $error["img"] = "Неподдерживаемый формат файла";
        }

        if ($file["size"] > 1024 * 1024 * 5) {
            $error["img"] = "Файл больше 5МБ";
        }

        if (!empty($error)) {
            $_SESSION["error"] = $error;
            $_SESSION["old_input"] = [
                "name" => $name,
                "email" => $email,
                "role" => $role,
                "img" => '',
                "password" => $password,
            ];
            header("Location: /admin/$table/update/{$_GET["id"]}");
            exit;
        }

        if (!isset($error["img"])) {
            $img_name = uniqid("img_") . "." . $ext;

            $upload_dir = __DIR__ . "/../../../../public/uploads/avatar/";

            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            if (move_uploaded_file($file["tmp_name"], $upload_dir . $img_name)) {
                $img = "/public/uploads/avatar/" . $img_name;
            } else {
                $error["img"] = "Ошибка загрузки файла";
            }
        }
    }

    if (!empty($error)) {
        $_SESSION["error"] = $error;
        $_SESSION["old_input"] = [
            "name" => $name,
            "email" => $email,
            "role" => $role,
            "img" => $img,
            "password" => $password,
        ];
        header("Location: /admin/$table/update/{$_GET["id"]}");
        exit;
    }
    if (empty($password)) {
        $stmt = $connect->prepare("UPDATE $tableSQL SET name=?, email=?, role=?, img=? WHERE id = ?");
        $stmt->bind_param("ssssi", $name, $email, $role, $img,  $_GET["id"]);
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $connect->prepare("UPDATE $tableSQL SET name=?, email=?, role=?, password=?, img=? WHERE id = ?");
        $stmt->bind_param("sssssi", $name, $email, $role, $hashed_password, $img,  $_GET["id"]);
    }

    $stmt->execute();
    header("Location: /admin/$table");
}

$input_error = $_SESSION["error"] ?? [];
$old_input = $_SESSION["old_input"] ?? $result;

unset($_SESSION["error"]);
unset($_SESSION["old_input"]);

$hasImage = !empty($old_input["img"]);
?>

<div class="admin-action">
    <div class="admin-action__header">
        <a href="/admin/<?= $table ?>" class="admin-action__back">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M10 12L6 8L10 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Назад
        </a>
        <h1 class="admin-action__title">Обновить пользователя</h1>
    </div>

    <form class="admin-form" action="" method="POST" enctype="multipart/form-data" id="userUpdateForm">
        <div class="admin-form__upload" id="uploadArea">
            <input type="file" name="img" id="imgInput" class="admin-form__upload-input" accept="image/*">
            <div class="admin-form__upload-placeholder" id="uploadPlaceholder" style="<?= $hasImage ? 'display:none;' : '' ?>">
                <svg class="admin-form__upload-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M23 19a2 2 0 01-2 2H3a2 2 0 01-2-2V8a2 2 0 012-2h4l2-3h6l2 3h4a2 2 0 012 2z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <circle cx="12" cy="13" r="4" stroke="currentColor" stroke-width="2"/>
                </svg>
                <span class="admin-form__upload-text">Нажмите, чтобы выбрать фото</span>
            </div>
            <img class="admin-form__upload-preview <?= $hasImage ? 'is-visible' : '' ?>" id="uploadPreview" src="<?= htmlspecialchars($old_input["img"] ?? "") ?>" alt="Preview">
            <button type="button" class="admin-form__upload-remove <?= $hasImage ? 'is-visible' : '' ?>" id="uploadRemove">
                <svg viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M1 1L13 13M1 13L13 1" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </button>
        </div>
        <input type="hidden" id="remove_img" name="remove_img" value="<?= $hasImage ? '0' : '1' ?>">
        <?php if (isset($input_error["img"])): ?>
            <div class="admin-form__error"><?= $input_error["img"] ?></div>
        <?php endif ?>

        <input type="text" name="name" class="admin-form__input <?= isset($input_error['name']) ? 'admin-form__input--error' : '' ?>" value="<?= htmlspecialchars($old_input["name"] ?? "") ?>" placeholder="Имя">
        <?php if (isset($input_error["name"])): ?>
            <div class="admin-form__error"><?= $input_error["name"] ?></div>
        <?php endif ?>

        <input type="text" name="email" class="admin-form__input <?= isset($input_error['email']) ? 'admin-form__input--error' : '' ?>" value="<?= htmlspecialchars($old_input["email"] ?? "") ?>" placeholder="Email">
        <?php if (isset($input_error["email"])): ?>
            <div class="admin-form__error"><?= $input_error["email"] ?></div>
        <?php endif ?>

        <select name="role" class="admin-form__select">
            <option value="user" <?= $old_input["role"] == "user" ? "selected" : '' ?>>Пользователь</option>
            <option value="manager" <?= $old_input["role"] == "manager" ? "selected" : '' ?>>Менеджер</option>
            <option value="admin" <?= $old_input["role"] == "admin" ? "selected" : '' ?>>Админ</option>
        </select>

        <input type="password" name="password" class="admin-form__input" placeholder="Новый пароль" autocomplete="new-password">

        <button type="submit" class="admin-form__submit">ОБНОВИТЬ</button>
    </form>
</div>

<script>
    const imgInput = document.getElementById('imgInput');
    const uploadPreview = document.getElementById('uploadPreview');
    const uploadPlaceholder = document.getElementById('uploadPlaceholder');
    const uploadRemove = document.getElementById('uploadRemove');
    const removeImgInput = document.getElementById('remove_img');

    imgInput.addEventListener('change', function(e) {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                uploadPreview.src = e.target.result;
                uploadPreview.classList.add('is-visible');
                uploadPlaceholder.style.display = 'none';
                uploadRemove.classList.add('is-visible');
                removeImgInput.value = 0;
            }
            reader.readAsDataURL(this.files[0]);
        }
    });

    uploadRemove.addEventListener('click', function(e) {
        e.stopPropagation();
        imgInput.value = '';
        uploadPreview.src = '';
        uploadPreview.classList.remove('is-visible');
        uploadPlaceholder.style.display = 'flex';
        uploadRemove.classList.remove('is-visible');
        removeImgInput.value = 1;
    });
</script>
