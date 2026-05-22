<?php
$table = "products";
$tableSQL = str_replace("-", "_", $table);

$stmt = $connect->prepare("SELECT p.id, p.img, p.name, p.price, p.description, p.stock, p.is_new, p.is_popular, p.is_featured, p.discount_percent, p.discount_end_date, c.name AS category_name, b.name AS brand_name FROM $tableSQL p JOIN categories c ON p.category_id = c.id JOIN brands b ON p.brand_id = b.id");
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
                    <th>Картинка</th>
                    <th>Название</th>
                    <th>Цена</th>
                    <th>Описание</th>
                    <th>На складе</th>
                    <th>Новинка</th>
                    <th>Популярное</th>
                    <th>Рекомендуемое</th>
                    <th>Процент скидки</th>
                    <th>Конец скидки</th>
                    <th>Категория</th>
                    <th>Бренд</th>
                    <th class="admin-table__actions-header">Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row["id"]) ?></td>
                        <td><img style="height: 50px;" src="<?= htmlspecialchars($row["img"]) ?>" alt="img"></td>
                        <td><?= htmlspecialchars($row["name"]) ?></td>
                        <td><?= htmlspecialchars($row["price"]) ?></td>
                        <td><?= htmlspecialchars($row["description"]) ?></td>
                        <td><?= htmlspecialchars($row["stock"]) ?></td>
                        <td><?= htmlspecialchars($row["is_new"]) ?></td>
                        <td><?= htmlspecialchars($row["is_popular"]) ?></td>
                        <td><?= htmlspecialchars($row["is_featured"]) ?></td>
                        <td><?= htmlspecialchars($row["discount_percent"]) ?></td>
                        <td><?= htmlspecialchars($row["discount_end_date"] ?? "null") ?></td>
                        <td><?= htmlspecialchars($row["category_name"]) ?></td>
                        <td><?= htmlspecialchars($row["brand_name"]) ?></td>
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
