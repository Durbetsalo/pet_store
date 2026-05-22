<?php
if (!isset($_SESSION["user_id"])) {
    header("Location: /login");
    exit;
}

$stmt = $connect->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION["user_id"]);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    header("Location: /login");
    exit;
}

$active_tab = $_GET["tab"] ?? "info";

if ($_SERVER["REQUEST_METHOD"] == "POST" && $active_tab == "info") {
    $avatar = $user["img"] ?? "";
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $error = [];
    $remove_img_input = $_POST["removeImg"];

    if (empty($name)) {
        $error["name"] = "Заполните поле";
    }
    if (empty($email)) {
        $error["email"] = "Заполните поле";
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error["email"] = "Неверный формат email";
    }

    // echo($remove_img_input == 1);
    // exit;

    if (empty($_FILES["avatar"]["name"]) && $remove_img_input == 1) {
        $avatar = null;
        unlink(__DIR__ . "/../../" . $user["img"]);
    }
    if (isset($_FILES["avatar"]) && !empty($_FILES["avatar"]["name"])) {
        if (!empty($avatar)) {
            unlink(__DIR__ . "/../../" . $user["img"]);
        }
        $file = $_FILES['avatar'];

        $allowed = ["jpg", "jpeg", "png", "webp", "gif"];
        $ext = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            $error["avatar"] = "Неподдерживаемый формат файла";
        }

        if ($file["size"] > 1024 * 1024 * 5) {
            $error["avatar"] = "Файл больше 5МБ";
        }

        if (!isset($error["avatar"])) {
            $avatar_name = uniqid("avatar_") . "." . $ext;

            $upload_dir = __DIR__ . "/../../public/uploads/avatar/";

            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            if (move_uploaded_file($file["tmp_name"], $upload_dir . $avatar_name)) {
                $avatar = "/public/uploads/avatar/" . $avatar_name;
            } else {
                $error["avatar"] = "Ошибка загрузки файла";
            }
        }
    }

    if (!empty($error)) {
        $_SESSION["profile_error"] = $error;
        $_SESSION["profile_old"] = [
            "name" => $name,
            "email" => $email,
        ];
        header("Location: /profile?tab=info");
        exit;
    }

    $stmt = $connect->prepare("UPDATE users SET name = ?, email = ?, img = ? WHERE id = ?");
    $stmt->bind_param("sssi", $name, $email, $avatar, $_SESSION["user_id"]);
    $stmt->execute();

    $_SESSION["user_avatar"] = $user["img"];
    header("Location: /profile?tab=info");
    exit;
}

$profile_error = $_SESSION["profile_error"] ?? [];
$profile_old = $_SESSION["profile_old"] ?? [];
unset($_SESSION["profile_error"]);
unset($_SESSION["profile_old"]);

$display_name = $profile_old["name"] ?? $user["name"];
$display_email = $profile_old["email"] ?? $user["email"];
$display_phone = $profile_old["phone"] ?? ($user["phone"] ?? "");
$initial = mb_strtoupper(mb_substr($display_name, 0, 1));

?>

<main class="profile-page">
    <div class="container">
        <div class="profile-page__layout">
            <aside class="profile-page__sidebar">
                <nav class="profile-page__nav">
                    <a href="/profile?tab=info" class="profile-page__nav-item <?= $active_tab == 'info' ? 'profile-page__nav-item--active' : '' ?>">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                            <circle cx="12" cy="7" r="4" />
                        </svg>
                        Личная информация
                    </a>
                    <a href="/orders" class="profile-page__nav-item <?= $active_tab == 'orders' ? 'profile-page__nav-item--active' : '' ?>">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z" />
                            <polyline points="3.27 6.96 12 12.01 20.73 6.96" />
                            <line x1="12" y1="22.08" x2="12" y2="12" />
                        </svg>
                        История заказов
                    </a>
                    <?php if (in_array($user["role"], ["admin", "manager"])): ?>
                        <a href="/admin" class="profile-page__nav-item">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="3" />
                                <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z" />
                            </svg>
                            Админ-панель
                        </a>
                    <?php endif; ?>
                    <a href="/logout" class="profile-page__nav-item profile-page__nav-item--logout">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                            <polyline points="16 17 21 12 16 7" />
                            <line x1="21" y1="12" x2="9" y2="12" />
                        </svg>
                        Выйти
                    </a>
                </nav>
            </aside>

            <div class="profile-page__content">
                <?php if ($active_tab == "info"): ?>
                    <h1 class="profile-page__title">Личная информация</h1>
                    <form action="/profile?tab=info" method="POST" enctype="multipart/form-data" class="profile-page__form">
                        <div class="profile-page__avatar-wrapper">
                            <div class="profile-page__avatar">
                                <input type="file" name="avatar" id="avatarInput" class="profile-page__avatar-input" accept="image/*">
                                <input type="hidden" id="removeImg" name="removeImg" value="0">
                                <span id="initial" style="display: <?= isset($user['img']) ? "none" : "block" ?>"><?= $initial ?></span>
                                <img src="<?= htmlspecialchars($user["img"]) ?>" id="avatarPreview" class="profile-page__avatar-preview <?= isset($user["img"]) ? "is-visible" : "" ?>" alt="">
                            </div>
                            <button class="profile-page__avatar-remove <?= $user["img"] ? "is-visible" : "" ?>" id="avatarRemove" type="button">
                                <svg viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M1 1L13 13M1 13L13 1" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path>
                                </svg>
                            </button>
                        </div>
                        <?php if (isset($profile_error["avatar"])): ?>
                            <span class="profile-page__error"><?= $profile_error["avatar"] ?></span>
                        <?php endif; ?>

                        <div class="profile-page__field <?= isset($profile_error["name"]) ? 'profile-page__field--error' : '' ?>">
                            <label class="profile-page__label">Имя</label>
                            <input type="text" name="name" class="profile-page__input" value="<?= htmlspecialchars($display_name) ?>">
                            <?php if (isset($profile_error["name"])): ?>
                                <span class="profile-page__error"><?= $profile_error["name"] ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="profile-page__field <?= isset($profile_error["email"]) ? 'profile-page__field--error' : '' ?>">
                            <label class="profile-page__label">Почта</label>
                            <input type="text" name="email" class="profile-page__input" value="<?= htmlspecialchars($display_email) ?>">
                            <?php if (isset($profile_error["email"])): ?>
                                <span class="profile-page__error"><?= $profile_error["email"] ?></span>
                            <?php endif; ?>
                        </div>
                        <button type="submit" class="btn btn--primary btn--full profile-page__submit">СОХРАНИТЬ ИЗМЕНЕНИЯ</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<script>
    const initial = document.getElementById('initial');
    const avatarInput = document.getElementById('avatarInput');
    const avatarRemove = document.getElementById('avatarRemove');
    const avatarPreview = document.getElementById('avatarPreview');
    const removeImgInput = document.getElementById('removeImg');

    avatarInput.addEventListener('change', function(e) {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                avatarPreview.src = e.target.result;
                initial.style.display = "none"
                avatarPreview.classList.add('is-visible');
                avatarRemove.classList.add('is-visible');
            }
            reader.readAsDataURL(this.files[0]);
        }
    });

    avatarRemove.addEventListener('click', function(e) {
        e.stopPropagation();
        avatarInput.value = '';
        avatarPreview.src = '';
        removeImgInput.value = 1
        initial.style.display = "block"
        avatarPreview.classList.remove('is-visible');
        avatarRemove.classList.remove('is-visible');
    });
</script>