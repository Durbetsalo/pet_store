<?php

session_start();


include_once __DIR__ . "/../src/config/config.php";
include_once __DIR__ . "/../src/config/database.php";


//получение url ()
$url = trim(parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH), "/");

$file = null;
$title = "Bloop";
$show_footer = false;
$show_header = true;

//динамический пути
$entity_map = [
    'product' => ['pattern' => '/^product\/(\d+)$/', 'file' => 'src/pages/product.php', 'show_footer' => true],
];

//статические пути
$routes = [
    ''       => ['file' => 'src/pages/main.php', 'title' => 'Главная', 'show_footer' => true, 'roles' => []],
    'catalog'       => ['file' => 'src/pages/catalog.php', 'title' => 'Главная', 'show_footer' => true, 'roles' => []],
    'checkout'       => ['file' => 'src/pages/checkout.php', 'title' => 'Оформление заказа', 'roles' => []],
    'cart'   => ['file' => 'src/pages/cart.php', 'title' => 'Корзина', 'show_footer' => true, 'roles' => ['user', 'admin', 'manager']],
    'profile'   => ['file' => 'src/pages/profile.php', 'title' => 'Профиль', 'show_footer' => true, 'roles' => ['user', 'admin', 'manager']],
    'admin'  => ['file' => 'src/pages/admin/index.php', 'title' => 'Админка', 'roles' => ['admin']],
    'orders' => ['file' => 'src/pages/orders.php', 'title' => 'Заказы', 'roles' => ['admin', 'manager']],
    'my-orders' => ['file' => 'src/pages/orders.php', 'title' => 'Мои заказы', 'show_footer' => true, 'roles' => ['user']],
    'logout'  => ['file' => 'src/pages/auth/logout.php', 'title' => 'Выход', 'roles' => []],
    'login'  => ['file' => 'src/pages/auth/login.php', 'title' => 'Вход', 'show_header' => false, 'roles' => []],
    'register'  => ['file' => 'src/pages/auth/register.php', 'title' => 'Регистрация', 'show_header' => false, 'roles' => []],
    'verify-code'  => ['file' => 'src/pages/auth/verify-code.php', 'title' => '', 'show_header' => false, 'roles' => []],
    'not-found'  => ['file' => 'src/pages/not-found.php', 'title' => '', 'show_header' => false, 'roles' => []],
    'thank-you'  => ['file' => 'src/pages/thank-you.php', 'title' => 'Спасибо за заказ', 'show_header' => false, 'roles' => []],
    'contacts'   => ['file' => 'src/pages/contacts.php', 'title' => 'Контакты', 'show_footer' => true, 'roles' => []],
    'access-denied'  => ['file' => 'src/pages/access-denied.php', 'title' => '', 'show_header' => false, 'roles' => []],
];

//проверяем url на динамический маршрут через регулярное выражение
foreach ($entity_map as $route) {
    if (preg_match($route['pattern'], $url, $matches)) {
        $id = $matches[1];
        $_GET['id'] = $id;
        $file = $route['file'];
        $title = $route["title"] ?? "Bloop";
        $show_footer = $route["show_footer"] ?? false;
        $show_header = $route["show_header"] ?? true;
        break;
    }
}

//если не динамический маршрут, то проверяем на статический
if (!$file && array_key_exists($url, $routes)) {
    $file = $routes[$url]['file'];
    $title = $routes[$url]["title"] ?? "Bloop";
    $show_footer = $routes[$url]["show_footer"] ?? false;
    $show_header = $routes[$url]["show_header"] ?? true;
}

//если url начинается с admin то загружаем файл с роутингом админ панели   
if ($url == "admin" || str_starts_with($url, "admin/")) {
    $file = "src/pages/admin/index.php";
    $title = "Admin";
}


//если не статический маршрут, то перекидываем на страницу not-found
if (!$file) {
    header("Location: /not-found");
    exit;
}

if ($url == "not-found") {
    http_response_code(404);
}

ob_start();

require_once __DIR__ . "/../" . $file;
$page_content = ob_get_clean();

?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link rel="stylesheet" href="/public/assets/css/main.css">
    <script src="/public/assets/js/add-to-cart.js" defer></script>
    <script src="/public/assets/js/delete-from-cart.js" defer></script>
    <script src="/public/assets/js/animate-value.js" defer></script>
    <script src="/public/assets/js/burger-menu.js" defer></script>
</head>

<body>
    <?php if ($show_header == true): ?>
        <?php require_once __DIR__ . "/../" . "src/components/header.php" ?>
    <?php endif ?>
    <div id="root">
        <?= $page_content ?>
    </div>
    <?php if ($show_footer == true): ?>
        <?php require_once __DIR__ . "/../" . "src/components/footer.php" ?>
    <?php endif ?>

</body>

</html>