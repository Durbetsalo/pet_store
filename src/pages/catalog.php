<?php


$stmt = $connect->prepare("SELECT * FROM categories");
$stmt->execute();
$categories = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$stmt = $connect->prepare("SELECT * FROM brands");
$stmt->execute();
$brands = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$filter_category_id = $_GET["category"] ?? "";
$filter_brand_id = $_GET["brand"] ?? "";
$filter_price_from = $_GET["price_from"] ?? "";
$filter_price_to = $_GET["price_to"] ?? "";
$filter_sort = $_GET["sort"] ?? "";
$filter_search = $_GET["search"] ?? "";

$sql = "SELECT * FROM products WHERE 1=1";
$params = [];
$types = "";
$order_by = " ORDER BY is_popular DESC";

if (!empty($filter_category_id)) {
    $sql .= " AND category_id = ?";
    $params[] = (int)$filter_category_id;
    $types .= "i";
}

if (!empty($filter_brand_id)) {
    $sql .= " AND brand_id = ?";
    $params[] = (int)$filter_brand_id;
    $types .= "i";
}

if (!empty($filter_search)) {
    $sql .= " AND (name LIKE ? OR description LIKE ?)";
    $params[] = "%$filter_search%";
    $params[] = "%$filter_search%";
    $types .= "ss";
}

if (in_array($filter_sort, ["is_popular", "is_new"])) {
    $order_by = " ORDER BY $filter_sort DESC";
}
$sql .= $order_by;

if (!empty($params)) {
    $stmt = $connect->prepare($sql);
    $stmt->bind_param($types, ...$params);
} else {
    $stmt = $connect->prepare($sql);
}
$stmt->execute();
$products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

if (!empty($filter_price_from)) {
    $products = array_filter($products, function ($product) use ($filter_price_from) {
        return floor(round($product['price'] * (1 - $product["discount_percent"] / 100))) >= $filter_price_from;
    });
}

if (!empty($filter_price_to)) {
    $products = array_filter($products, function ($product) use ($filter_price_to) {
        return floor(round($product['price'] * (1 - $product["discount_percent"] / 100))) <= $filter_price_to;
    });
}

if ($filter_sort == "price-asc") {
    usort($products, function ($a, $b) {
        return floor(round($a['price'] * (1 - $a["discount_percent"] / 100))) <=> floor(round($b['price'] * (1 - $b["discount_percent"] / 100)));
    });
}

if ($filter_sort == "price-desc") {
    usort($products, function ($a, $b) {
        return floor(round($b['price'] * (1 - $b["discount_percent"] / 100))) <=> floor(round($a['price'] * (1 - $a["discount_percent"] / 100)));
    });
}

if (isset($_SESSION['user_id'])) {
    $stmt = $connect->prepare("SELECT * FROM cart_items WHERE user_id = ?");
    $stmt->bind_param('i', $_SESSION['user_id']);
    $stmt->execute();
    $cart_items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
?>

<main class="catalog">
    <div class="container">
        <form action="/catalog" method="GET" id="filter-form">
            <div class="catalog__header">
                <h1 class="catalog__title">Каталог товаров</h1>
                <div class="catalog__search">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8" />
                        <path d="m21 21-4.35-4.35" />
                    </svg>
                    <input type="text" class="catalog__search-input" placeholder="Поиск по названию или бренду..." name="search" onchange="this.form.submit()" value="<?= htmlspecialchars($filter_search) ?>">
                </div>
            </div>

            <div class="catalog__layout">
                <aside class="catalog__sidebar">
                    <div class="filters-card">
                        <div class="filters-card__header">
                            <img src="/public/assets/icons/filter-icon.svg" alt="">
                            <span>Фильтры</span>
                        </div>


                        <div class="filters-card__group">
                            <label class="filters-card__label">Категория</label>
                            <select class="filters-card__select" id="category-select" name="category" onchange="submitForm()">
                                <option value="">Все категории</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category["id"] ?>" <?= $filter_category_id == $category["id"] ? "selected" : "" ?>><?= $category["name"] ?></option>
                                <?php endforeach ?>
                                <!-- <option value="care">Уход</option>
                                <option value="accessories">Аксессуары</option>
                                <option value="toys">Игрушки</option> -->
                            </select>
                        </div>

                        <div class="filters-card__group" name="brand">
                            <label class="filters-card__label">Бренд</label>
                            <select class="filters-card__select" name="brand" onchange="submitForm()">
                                <option value="">Все бренды</option>
                                <?php foreach ($brands as $brand): ?>
                                    <option value="<?= $brand["id"] ?>" <?= $filter_brand_id == $brand["id"] ? "selected" : "" ?>><?= $brand["name"] ?></option>
                                <?php endforeach ?>
                                <!-- <option value="holistic">Holistic</option>
                                <option value="green">Green Forest</option>
                                <option value="pure">Pure Care</option> -->
                            </select>
                        </div>

                        <div class="filters-card__group">
                            <label class="filters-card__label">Цена, ₽</label>
                            <div class="filters-card__price">
                                <input type="number" class="filters-card__price-input" value="<?= htmlspecialchars($filter_price_from) ?>" placeholder="От" name="price_from" onchange="submitForm()">
                                <span class="filters-card__price-sep">—</span>
                                <input type="number" class="filters-card__price-input" value="<?= htmlspecialchars($filter_price_to) ?>" placeholder="До" name="price_to" onchange="submitForm()">
                            </div>
                        </div>

                        <div class="filters-card__group">
                            <label class="filters-card__label">Сортировка</label>
                            <select class="filters-card__select" name="sort" onchange="submitForm()">
                                <option value="is_popular" <?= $filter_sort == "is_popular" ? "selected" : "" ?>>По популярности</option>
                                <option value="price-asc" <?= $filter_sort == "price-asc" ? "selected" : "" ?>>Сначала дешёвые</option>
                                <option value="price-desc" <?= $filter_sort == "price-desc" ? "selected" : "" ?>>Сначала дорогие</option>
                                <option value="is_new" <?= $filter_sort == "is_new" ? "selected" : "" ?>>Новинки</option>
                            </select>
                        </div>


                        <a href="/catalog" class="filters-card__reset">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="18" y1="6" x2="6" y2="18" />
                                <line x1="6" y1="6" x2="18" y2="18" />
                            </svg>
                            Сбросить фильтры
                        </a>
                    </div>
                </aside>

                <div class="catalog__content">
                    <p class="catalog__count">Найдено товаров: <strong><?= count($products) ?></strong></p>

                    <div class="catalog__grid">
                        <?php foreach ($products as $product): ?>
                            <div class="product-card">
                                <div class="product-card__image">
                                    <div class="product-card__badges">
                                        <?php if ($product['discount_percent']): ?>
                                            <span class="badge badge--sale">СКИДКА <?= $product['discount_percent'] ?>%</span>
                                        <?php endif ?>
                                        <?php if ($product['is_new']): ?>
                                            <span class="badge badge--tag">НОВОЕ</span>
                                        <?php endif ?>
                                        <?php if ($product['is_popular']): ?>
                                            <span class="badge badge--tag">ПОПУЛЯРНО</span>
                                        <?php endif ?>
                                    </div>
                                    <a href="/product/<?= $product['id'] ?>">
                                        <img src=<?= htmlspecialchars($product['img']) ?> alt=<?= htmlspecialchars($product['name']) ?>>
                                    </a>
                                </div>
                                <div class="product-card__content">
                                    <h3 class="product-card__title"><a href="/product/<?= $product['id'] ?>"><?= htmlspecialchars($product['name']) ?></a></h3>
                                    <div class="product-card__bottom">
                                        <div class="">
                                            <span class="product-card__price"><?= floor(round($product['price'] * (1 - $product['discount_percent'] / 100))) ?> ₽</span>
                                            <?php if ($product['discount_percent']):  ?>
                                                <span class="product-card__price-old"><?= $product['price'] ?> ₽</span>
                                            <?php endif  ?>
                                        </div>
                                        <?php if (isset($cart_items)):
                                            $in_cart = false;
                                            $quantity = 0;
                                            foreach ($cart_items as $cart_item) {
                                                if ($cart_item["product_id"] == $product["id"]) {
                                                    $in_cart = true;
                                                    $quantity = $cart_item["quantity"];
                                                    break;
                                                }
                                            }
                                        ?>
                                            <?php if ($in_cart): ?>
                                                <button data-product-id="<?= $product["id"] ?>" id="cart_button_<?= $product["id"] ?>" style="display: <?= $cart_item["quantity"] ?? 0 >= 1 ? "none" : "block" ?>;" onclick="addToCart(this)" class="btn btn--primary btn--sm">В корзину</button>
                                                <div id="group_<?= $product["id"] ?>" style="display: <?= $cart_item["quantity"] ?? 0 >= 1 ? "flex" : "none" ?>; gap: 1rem;" class="btn btn--primary btn--sm">
                                                    <button data-product-id=<?= $product["id"] ?> onclick="deleteFromCart(this)"><img src="/public/assets/icons/minus.svg" alt=""></button>
                                                    <span style="font-weight: 600; font-size: 1rem;" id="in_cart_<?= $product["id"] ?>"><?= $cart_item["quantity"] ?></span>
                                                    <button id="plus_button_<?= $product["id"] ?>" <?= $product["stock"] == $cart_item["quantity"] ? "disabled" : "" ?> data-product-id="<?= $product["id"] ?>" onclick="addToCart(this)"><img src="/public/assets/icons/plus.svg" alt=""></button>
                                                </div>
                                            <?php else:  ?>
                                                <button data-product-id="<?= $product["id"] ?>" onclick="addToCart(this)" id="cart_button_<?= $product["id"] ?>" class="btn btn--primary btn--sm">В корзину</button>
                                                <div id="group_<?= $product["id"] ?>" style="display: none; gap: 1rem;" class="btn btn--primary btn--sm">
                                                    <button data-product-id=<?= $product["id"] ?> onclick="deleteFromCart(this)"><img src="/public/assets/icons/minus.svg" alt=""></button>
                                                    <span style="font-weight: 600; font-size: 1rem;" id="in_cart_<?= $product["id"] ?>">0</span>
                                                    <button id="plus_button_<?= $product["id"] ?>" <?= $product["stock"] == 0 ? "disabled" : "" ?> data-product-id="<?= $product["id"] ?>" onclick="addToCart(this)"><img src="/public/assets/icons/plus.svg" alt=""></button>
                                                </div>
                                            <?php endif  ?>
                                        <?php else:  ?>
                                            <button data-product-id="<?= $product["id"] ?>" onclick="addToCart(this)" class="btn btn--primary btn--sm">В корзину</button>
                                        <?php endif  ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach ?>
                    </div>
                </div>
            </div>
        </form>
    </div>
</main>
<script>
    function submitForm() {
        document.getElementById("filter-form").submit();
    }
</script>