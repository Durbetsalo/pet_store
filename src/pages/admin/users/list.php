<?php
$table = "users";
$tableSQL = str_replace("-", "_", $table);

$stmt = $connect->prepare("SELECT id, name, email, phone, role, img, created_at FROM $tableSQL");
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
                    <th>Имя</th>
                    <th>Почта</th>
                    <th>Телефон</th>
                    <th>Роль</th>
                    <th>Аватар</th>
                    <th>Дата создания</th>
                    <th class="admin-table__actions-header">Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row["id"]) ?></td>
                        <td><?= htmlspecialchars($row["name"]) ?></td>
                        <td><?= htmlspecialchars($row["email"]) ?></td>
                        <td><?= htmlspecialchars($row["phone"]) ?></td>
                        <td><?= htmlspecialchars($row["role"]) ?></td>
                        <?php if (!empty($row["img"])): ?>
                            <td><img style="height: 50px" src="<?= $row["img"] ?>" alt=""></td>
                        <?php else: ?>
                            <td><?= htmlspecialchars(mb_substr($row["name"], 0, 1)) ?></td>
                        <?php endif; ?>
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
