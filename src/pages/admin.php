<?php
$table = $_GET["table"] ?? "products";

$result = $connect->query("SELECT * FROM $table");
$rows = $result->fetch_all(MYSQLI_ASSOC);

$tabs = [
    'products' => 'ТОВАРЫ',
    'categories' => 'КАТЕГОРИИ',
    'orders' => 'ЗАКАЗЫ',
    'brands' => 'БРЕНДЫ',
    'order_statuses' => 'СТАТУСЫ ЗАКАЗОВ',
    'reviews' => 'ОТЗЫВЫ',
    'promotions' => 'АКЦИИ',
    'users' => 'ПОЛЬЗОВАТЕЛИ'
];
?>

<main class="admin-page">
    <div class="container">
        <h1 class="admin-page__title">Админ-панель</h1>

        <nav class="admin-tabs">
            <?php foreach ($tabs as $tab_key => $tab_name): ?>
                <a href="?table=<?= $tab_key ?>" class="admin-tab <?= $table === $tab_key ? 'active' : '' ?>"><?= $tab_name ?></a>
            <?php endforeach; ?>
        </nav>

        <a href="/admin-add?table=<?= $table ?>" class="btn-admin-add">ДОБАВИТЬ</a>

        <?php if (!empty($rows)): ?>
            <div class="admin-table-wrapper">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <?php foreach (array_keys($rows[0]) as $col): ?>
                                <th><?= $col ?></th>
                            <?php endforeach; ?>
                            <th class="admin-table__actions-header">Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rows as $row): ?>
                            <tr>
                                <?php foreach ($row as $cell): ?>
                                    <td><?= htmlspecialchars($cell) ?></td>
                                <?php endforeach; ?>
                                <td class="admin-table__actions">
                                    <a href="/admin-edit?table=<?= $table ?>&id=<?= $row['id'] ?>" class="btn-admin btn-admin--update">ОБНОВИТЬ</a>
                                    <a href="/admin-delete?table=<?= $table ?>&id=<?= $row['id'] ?>" class="btn-admin btn-admin--delete">УДАЛИТЬ</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="admin-empty">Нет данных</p>
        <?php endif; ?>
    </div>
</main>
