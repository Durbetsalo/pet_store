<?php
$stmt = $connect->prepare("SELECT * FROM products");
$stmt->execute();
$products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

if (isset($_SESSION['user_id'])) {
    $stmt = $connect->prepare("SELECT * FROM cart_items WHERE user_id = ?");
    $stmt->bind_param('i', $_SESSION['user_id']);
    $stmt->execute();
    $cart_items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
?>

<main class="catalog">
    <div class="container">
        <div class="catalog__header">
            <h1 class="catalog__title">Каталог товаров</h1>
            <div class="catalog__search">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8" />
                    <path d="m21 21-4.35-4.35" />
                </svg>
                <input type="text" class="catalog__search-input" placeholder="Поиск по названию или бренду...">
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
                        <select class="filters-card__select">
                            <option value="">Все категории</option>
                            <option value="food">Корма</option>
                            <option value="care">Уход</option>
                            <option value="accessories">Аксессуары</option>
                            <option value="toys">Игрушки</option>
                        </select>
                    </div>

                    <div class="filters-card__group">
                        <label class="filters-card__label">Бренд</label>
                        <select class="filters-card__select">
                            <option value="">Все бренды</option>
                            <option value="holistic">Holistic</option>
                            <option value="green">Green Forest</option>
                            <option value="pure">Pure Care</option>
                        </select>
                    </div>

                    <div class="filters-card__group">
                        <label class="filters-card__label">Цена, ₽</label>
                        <div class="filters-card__price">
                            <input type="number" class="filters-card__price-input" placeholder="От">
                            <span class="filters-card__price-sep">—</span>
                            <input type="number" class="filters-card__price-input" placeholder="До">
                        </div>
                    </div>

                    <div class="filters-card__group">
                        <label class="filters-card__label">Сортировка</label>
                        <select class="filters-card__select">
                            <option value="popular">По популярности</option>
                            <option value="price-asc">Сначала дешёвые</option>
                            <option value="price-desc">Сначала дорогие</option>
                            <option value="new">Новинки</option>
                        </select>
                    </div>

                    <button class="filters-card__reset">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="18" y1="6" x2="6" y2="18" />
                            <line x1="6" y1="6" x2="18" y2="18" />
                        </svg>
                        Сбросить фильтры
                    </button>
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
                                        <span class="product-card__price"><?= floor($product['price'] * (1 - $product['discount_percent'] / 100)) ?> ₽</span>
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
                                            <div id="group_<?= $product["id"] ?>" style="display: none;" class="btn btn--primary btn--sm">
                                                <button data-product-id=<?= $product["id"] ?> onclick="deleteFromCart(this)">-</button>
                                                <span id="in_cart_<?= $product["id"] ?>">0</span>
                                                <button id="plus_button_<?= $product["id"] ?>" <?= $product["stock"] == 0 ? "disabled" : "" ?> data-product-id="<?= $product["id"] ?>" onclick="addToCart(this)">+</button>
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
    </div>
</main>


<script>
    // async function decreaseCartItem(button) {
    //     let res = await deleteFromCart(button)
    //     console.log(document.getElementById("plus_button_" + button.getAttribute("data-product-id")).disabled = false);
    //     document.getElementById("plus_button_" + button.getAttribute("data-product-id")).disabled = false;

    //     if (res.status === "deleted") {
    //         document.getElementById("cart_button_" + button.getAttribute("data-product-id")).style.display = "block";
    //         document.getElementById("group_" + button.getAttribute("data-product-id")).style.display = "none";

    //     }
    // }
</script>