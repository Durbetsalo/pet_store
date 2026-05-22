<?php

$table = "categories";
$tableSQL = str_replace("-", "_", $table);

$stmt = $connect->prepare("SELECT * FROM $tableSQL WHERE id = ?");
$stmt->bind_param("i", $_GET["id"]);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);


    $error = [];

    if (empty($name)) {
        $error["name"] = "Заполните поле";
    }
    if (!empty($error)) {
        $_SESSION["error"] = $error;
        $_SESSION["old_input"] = [
            "name" => $name
        ];
        header("Location: /admin/$table/update/{$_GET["id"]}");
        exit;
    }

    $stmt = $connect->prepare("UPDATE $tableSQL SET name=? WHERE id = ?");
    $stmt->bind_param("si", $name, $_GET["id"]);
    $stmt->execute();
    header("Location: /admin/$table");
}

$input_error = $_SESSION["error"] ?? [];
$old_input = $_SESSION["old_input"] ?? $result;

unset($_SESSION["error"]);
unset($_SESSION["old_input"]);
?>

<div class="admin-action">
    <div class="admin-action__header">
        <a href="/admin/<?= $table ?>" class="admin-action__back">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M10 12L6 8L10 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Назад
        </a>
        <h1 class="admin-action__title">Обновить категорию</h1>
    </div>

    <form class="admin-form" action="" method="POST">
        <input type="text" name="name" class="admin-form__input <?= isset($input_error['name']) ? 'admin-form__input--error' : '' ?>" value="<?= htmlspecialchars($old_input["name"] ?? "") ?>" placeholder="Название">
        <?php if (isset($input_error["name"])): ?>
            <div class="admin-form__error"><?= $input_error["name"] ?></div>
        <?php endif ?>

        <button type="submit" class="admin-form__submit">ОБНОВИТЬ</button>
    </form>
</div>
