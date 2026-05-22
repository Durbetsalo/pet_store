<?php
$table = "orders";
$tableSQL = str_replace("-", "_", $table);

$stmt = $connect->prepare("SELECT o.id, u.name AS user_name, os.name AS status, o.address, o.total_amount, o.delivery_date, o.created_at FROM $tableSQL o JOIN users u ON o.user_id = u.id JOIN order_statuses os ON o.status_id = os.id");
$stmt->execute();
$rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<?php if (empty($rows)): ?>
    <p class="admin-empty">Пока ничего нет.</p>
<?php else: ?>
    <div class="admin-table-wrapper">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Пользователь</th>
                    <th>Статус</th>
                    <th>Адрес</th>
                    <th>Общая сумма</th>
                    <th>Дата доставки</th>
                    <th>Дата создания</th>
                    <th class="admin-table__actions-header">Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row["id"]) ?></td>
                        <td><?= htmlspecialchars($row["user_name"]) ?></td>
                        <td><?= htmlspecialchars($row["status"]) ?></td>
                        <td><?= htmlspecialchars($row["address"]) ?></td>
                        <td><?= htmlspecialchars($row["total_amount"]) ?></td>
                        <td><?= htmlspecialchars($row["delivery_date"]) ?></td>
                        <td><?= htmlspecialchars($row["created_at"]) ?></td>
                        <td class="admin-table__actions">
                            <a href="<?= $table ?>/update/<?= $row['id'] ?>" class="btn-admin btn-admin--update">ОБНОВИТЬ</a>
                            <a href="<?= $table ?>/delete/<?= $row['id'] ?>" class="btn-admin btn-admin--delete">УДАЛИТЬ</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
