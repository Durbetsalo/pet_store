<?php
$table = "brands";
$tableSQL = str_replace("-", "_", $table);

$stmt = $connect->prepare("SELECT id, name, img FROM $tableSQL");
$stmt->execute();
$rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<a href="<?= $table ?>/create" class="btn-admin-add">ДОБАВИТЬ</a>

<?php if (empty($rows)): ?>
    <p class="admin-empty">Пока ничего нет.</p>
<?php else: ?>
    <div class="admin-table-wrapper">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Название</th>
                    <th>Лого</th>
                    <th class="admin-table__actions-header">Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row["id"]) ?></td>
                        <td><?= htmlspecialchars($row["name"]) ?></td>
                        <td><img style="height: 50px" src="<?= htmlspecialchars($row["img"]) ?>" alt="img"></td>
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
