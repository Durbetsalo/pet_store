<?php

$table = "promotions";
$tableSQL = str_replace("-", "_", $table);


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST["title"]);
    $description = trim($_POST["description"]);


    $error = [];

    if (empty($title)) {
        $error["title"] = "Заполните поле";
    }
    if (empty($description)) {
        $error["description"] = "Заполните поле";
    }
    

    if (!empty($error)) {
        $_SESSION["error"] = $error;
        $_SESSION["old_input"] = [
            "title" => $title,
            "description" => $description
        ];
        header("Location: /admin/$table/create");
        exit;
    }

    $stmt = $connect->prepare("INSERT INTO $tableSQL (title, description) VALUES (?, ?)");
    $stmt->bind_param("ss", $title, $description);
    $stmt->execute();
    header("Location: /admin/$table");
}

$input_error = $_SESSION["error"] ?? [];
$old_input = $_SESSION["old_input"] ?? [];

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
        <h1 class="admin-action__title">Добавить акцию</h1>
    </div>

    <form class="admin-form" action="" method="POST">
        <input type="text" name="title" class="admin-form__input <?= isset($input_error['title']) ? 'admin-form__input--error' : '' ?>" value="<?= htmlspecialchars($old_input["title"] ?? "") ?>" placeholder="Название">
        <?php if (isset($input_error["title"])): ?>
            <div class="admin-form__error"><?= $input_error["title"] ?></div>
        <?php endif ?>

        <textarea name="description" class="admin-form__textarea <?= isset($input_error['description']) ? 'admin-form__textarea--error' : '' ?>" placeholder="Описание"><?= htmlspecialchars($old_input["description"] ?? "") ?></textarea>
        <?php if (isset($input_error["description"])): ?>
            <div class="admin-form__error"><?= $input_error["description"] ?></div>
        <?php endif ?>

        <button type="submit" class="admin-form__submit">СОЗДАТЬ</button>
    </form>
</div>
