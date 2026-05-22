<?php

if (!isset($_SESSION["user_id"])) {
    header('Location: /login');
    exit;
}
$stmt = $connect->prepare("SELECT role FROM users WHERE id=?");
$stmt->bind_param("i", $_SESSION["user_id"]);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    session_destroy();
    header('Location: /index.php');
    exit;
}
if (!in_array($user["role"], ['admin', 'manager'])) {
    $_SESSION["user_role"] = "user";
    header('Location: /index.php');
    exit;
}
$_SESSION["user_role"] = $user["role"];


$user_role = $_SESSION["user_role"];

$permissions = [
    "products" => ["admin"],
    "categories" => ["admin"],
    "brands" => ["admin"],
    "order-statuses" => ["admin"],
    "orders" => ["admin", "manager"],
    "promotions" => ["admin"],
    "reviews" => ["admin", "manager"],
    "users" => ["admin"],
];

$managerActions = [
    "orders" => ["list", "update"],
    "reviews" => ["list", "update"],
];

$admin_url = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
$file = null;
$section = trim(str_replace("admin", "", $admin_url), "/");

$parts = explode("/", $section, 4);
$page   = $parts[0] ?? '';
$action = $parts[1] ?? 'list';
$id = $parts[2] ?? null;
$trash = $parts[3] ?? null;

if ($page && isset($permissions[$page]) && !in_array($user_role, $permissions[$page])) {
    header('Location: /admin');
    exit;
}
if ($user_role == "manager" && isset($managerActions[$page])) {
    if (!in_array($action, $managerActions[$page])) {
        header('Location: /admin');
        exit;
    }
}


$routes = [
    "" => ["list" => ''],
    "products" => [
        "list" => "/products/list.php",
        "create" => "/products/create.php",
        "update" => "/products/update.php",
        "delete" => "/delete.php"
    ],
    "categories" => [
        "list" => "/categories/list.php",
        "create" => "/categories/create.php",
        "update" => "/categories/update.php",
        "delete" => "/delete.php"
    ],
    "brands" => [
        "list" => "/brands/list.php",
        "create" => "/brands/create.php",
        "update" => "/brands/update.php",
        "delete" => "/delete.php"
    ],
    "order-statuses" => [
        "list" => "/order-statuses/list.php",
        "create" => "/order-statuses/create.php",
        "update" => "/order-statuses/update.php",
        "delete" => "/delete.php"
    ],
    "orders" => [
        "list" => "/orders/list.php",
        "update" => "/orders/update.php",
        "delete" => "/delete.php"
    ],
    "promotions" => [
        "list" => "/promotions/list.php",
        "create" => "/promotions/create.php",
        "update" => "/promotions/update.php",
        "delete" => "/delete.php"
    ],
    "reviews" => [
        "list" => "/reviews/list.php",
        "update" => "/reviews/update.php",
        "delete" => "/delete.php"
    ],
    "users" => [
        "list" => "/users/list.php",
        "update" => "/users/update.php",
        "delete" => "/delete.php"
    ],
];

if ($page && array_key_exists($page, $routes)) {
    $file = $routes[$page][$action];
}
if ($action == "update" || $action == "delete") {
    $_GET["id"] = $id;
    $_GET["table"] = $page;
}

$proverka1 = $id != null && !ctype_digit($id);
$proverka2 = !$file && $section !== "";
$proverka3 = $id == null && in_array($action, ["update", 'delete']);
if ($proverka1 || $proverka2 || $proverka3 || $trash) {
    header('Location: /not-found');
    exit;
}


?>
<?php if (!in_array($action, ["update", 'create'])): ?>
<main class="admin-page">
    <div class="container">
        <h1 class="admin-page__title">Админ-панель</h1>

        <nav class="admin-tabs">
            <?php if ($user_role == "admin"): ?>
                <a href="/admin/products" class="admin-tab <?= $page === 'products' ? 'active' : '' ?>">ТОВАРЫ</a>
                <a href="/admin/categories" class="admin-tab <?= $page === 'categories' ? 'active' : '' ?>">КАТЕГОРИИ</a>
                <a href="/admin/brands" class="admin-tab <?= $page === 'brands' ? 'active' : '' ?>">БРЕНДЫ</a>
                <a href="/admin/order-statuses" class="admin-tab <?= $page === 'order-statuses' ? 'active' : '' ?>">СТАТУСЫ ЗАКАЗОВ</a>
                <a href="/admin/promotions" class="admin-tab <?= $page === 'promotions' ? 'active' : '' ?>">АКЦИИ</a>
                <a href="/admin/users" class="admin-tab <?= $page === 'users' ? 'active' : '' ?>">ПОЛЬЗОВАТЕЛИ</a>
            <?php endif; ?>
            <a href="/admin/orders" class="admin-tab <?= $page === 'orders' ? 'active' : '' ?>">ЗАКАЗЫ</a>
            <a href="/admin/reviews" class="admin-tab <?= $page === 'reviews' ? 'active' : '' ?>">ОТЗЫВЫ</a>
        </nav>
<?php endif ?>

<?php
if ($file):
    require_once __DIR__ . $file;
elseif (!in_array($action, ["update", 'create'])):
?>
        <p class="admin-empty">Добро пожаловать! Выберите раздел.</p>
    </div>
</main>
<?php endif ?>