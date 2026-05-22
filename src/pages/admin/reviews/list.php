<?php
$table = "reviews";
$tableSQL = str_replace("-", "_", $table);

$stmt = $connect->prepare("SELECT r.id, u.name AS user_name, p.name AS product_name, r.comment, r.created_at, r.assessment FROM $tableSQL r JOIN users u ON r.user_id = u.id JOIN products p ON r.product_id = p.id");
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
                    <th>Товар</th>
                    <th>Комментарий</th>
                    <th>Дата создания</th>
                    <th>Рейтинг</th>
                    <th class="admin-table__actions-header">Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row["id"]) ?></td>
                        <td><?= htmlspecialchars($row["user_name"]) ?></td>
                        <td><?= htmlspecialchars($row["product_name"]) ?></td>
                        <td><?= htmlspecialchars($row["comment"]) ?></td>
                        <td><?= htmlspecialchars($row["created_at"]) ?></td>
                        <td><?= htmlspecialchars($row["assessment"]) ?></td>
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
