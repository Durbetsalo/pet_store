<?php

$table = "reviews";
$tableSQL = str_replace("-", "_", $table);

$stmt = $connect->prepare("SELECT * FROM $tableSQL WHERE id = ?");
$stmt->bind_param("i", $_GET["id"]);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $comment = trim($_POST["comment"]);
    $assessment = $_POST["assessment"];


    $error = [];

    if (empty($comment)) {
        $error["comment"] = "Заполните поле";
    }
    if (!empty($error)) {
        $_SESSION["error"] = $error;
        $_SESSION["old_input"] = [
            "comment" => $comment,
            "assessment" => $assessment
        ];
        header("Location: /admin/$table/update/{$_GET["id"]}");
        exit;
    }

    $stmt = $connect->prepare("UPDATE $tableSQL SET comment=?, assessment=? WHERE id = ?");
    $stmt->bind_param("sii", $comment, $assessment, $_GET["id"]);
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
        <h1 class="admin-action__title">Обновить отзыв</h1>
    </div>

    <form class="admin-form" action="" method="POST">
        <textarea name="comment" class="admin-form__textarea <?= isset($input_error['comment']) ? 'admin-form__textarea--error' : '' ?>" placeholder="Комментарий"><?= htmlspecialchars($old_input["comment"] ?? "") ?></textarea>
        <?php if (isset($input_error["comment"])): ?>
            <div class="admin-form__error"><?= $input_error["comment"] ?></div>
        <?php endif ?>

        <select name="assessment" class="admin-form__select">
            <option value="1" <?= $old_input["assessment"] == 1 ? "selected" : '' ?>>1</option>
            <option value="2" <?= $old_input["assessment"] == 2 ? "selected" : '' ?>>2</option>
            <option value="3" <?= $old_input["assessment"] == 3 ? "selected" : '' ?>>3</option>
            <option value="4" <?= $old_input["assessment"] == 4 ? "selected" : '' ?>>4</option>
            <option value="5" <?= $old_input["assessment"] == 5 ? "selected" : '' ?>>5</option>
        </select>
        <?php if (isset($input_error["assessment"])): ?>
            <div class="admin-form__error"><?= $input_error["assessment"] ?></div>
        <?php endif ?>

        <button type="submit" class="admin-form__submit">ОБНОВИТЬ</button>
    </form>
</div>
