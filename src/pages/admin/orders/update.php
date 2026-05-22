<?php

$table = "orders";
$tableSQL = str_replace("-", "_", $table);

$stmt = $connect->prepare("SELECT * FROM $tableSQL WHERE id = ?");
$stmt->bind_param("i", $_GET["id"]);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();

$stmt = $connect->prepare("SELECT * FROM order_statuses");
$stmt->execute();
$order_statuses = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $status_id = trim($_POST["status_id"]);

    $error = [];


    if (!empty($error)) {
        $_SESSION["error"] = $error;
        $_SESSION["old_input"] = [
            "status_id" => $status_id
        ];
        header("Location: /admin/$table/update/{$_GET["id"]}");
        exit;
    }

    $stmt = $connect->prepare("UPDATE $tableSQL SET status_id=? WHERE id = ?");
    $stmt->bind_param("ii", $status_id,$_GET["id"]);
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
        <h1 class="admin-action__title">Обновить заказ</h1>
    </div>

    <form class="admin-form" action="" method="POST">
        <select name="status_id" class="admin-form__select">
            <?php foreach ($order_statuses as $order_status): ?>
                <option value="<?= htmlspecialchars($order_status["id"]) ?>" <?= $result["status_id"] == $order_status["id"] ? "selected" : "" ?>><?= htmlspecialchars($order_status["name"]) ?></option>
            <?php endforeach ?>
        </select>

        <button type="submit" class="admin-form__submit">ОБНОВИТЬ</button>
    </form>
</div>
