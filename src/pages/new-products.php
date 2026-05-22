<?php
$stmt = $connect->prepare("SELECT p.*, c.name AS category_name, b.name AS brand_name FROM products p LEFT JOIN categories c ON p.category_id = c.id LEFT JOIN brands b ON p.brand_id = b.id WHERE p.is_new = 1");
$stmt->execute();
$products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

if (isset($_SESSION['user_id'])) {
    $stmt = $connect->prepare("SELECT * FROM cart_items WHERE user_id = ?");
    $stmt->bind_param('i', $_SESSION['user_id']);
    $stmt->execute();
    $cart_items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
?>

<main class="new-products-page">
    <div class="container">
        <h1 class="new-products-page__title">Новинки</h1>

        <?php if (empty($products)): ?>
            <p class="new-products-page__empty">Сейчас нет новых товаров.</p>
        <?php else: ?>
            <div class="new-products-page__grid">
                <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <div class="product-card__image">
                            <div class="product-card__badges">
                                <?php if ($product['discount_percent']): ?>
                                    <span class="badge badge--sale">СКИДКА <?= $product['discount_percent'] ?>%</span>
                                <?php endif ?>
                                <?php if ($product['is_new']): ?>
                                    <span class="badge badge--tag">НОВИНКА</span>
                                <?php endif ?>
                                <?php if ($product['is_popular']): ?>
                                    <span class="badge badge--tag">ПОПУЛЯРНО</span>
                                <?php endif ?>
                            </div>
                            <a href="/product/<?= $product['id'] ?>">
                                <img src="<?= htmlspecialchars($product['img']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                            </a>
                        </div>
                        <div class="product-card__content">
                            <h3 class="product-card__title"><a href="/product/<?= $product['id'] ?>"><?= htmlspecialchars($product['name']) ?></a></h3>
                            <div class="product-card__bottom">
                                <div class="">
                                    <span class="product-card__price"><?= floor(round($product['price'] * (1 - $product['discount_percent'] / 100))) ?> ₽</span>
                                    <?php if ($product['discount_percent']): ?>
                                        <span class="product-card__price-old"><?= $product['price'] ?> ₽</span>
                                    <?php endif ?>
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
                                    <?php else: ?>
                                        <button data-product-id="<?= $product["id"] ?>" onclick="addToCart(this)" id="cart_button_<?= $product["id"] ?>" class="btn btn--primary btn--sm">В корзину</button>
                                        <div id="group_<?= $product["id"] ?>" style="display: none; gap: 1rem;" class="btn btn--primary btn--sm">
                                            <button data-product-id=<?= $product["id"] ?> onclick="deleteFromCart(this)"><img src="/public/assets/icons/minus.svg" alt=""></button>
                                            <span style="font-weight: 600; font-size: 1rem;" id="in_cart_<?= $product["id"] ?>">0</span>
                                            <button id="plus_button_<?= $product["id"] ?>" <?= $product["stock"] == 0 ? "disabled" : "" ?> data-product-id="<?= $product["id"] ?>" onclick="addToCart(this)"><img src="/public/assets/icons/plus.svg" alt=""></button>
                                        </div>
                                    <?php endif ?>
                                <?php else: ?>
                                    <button data-product-id="<?= $product["id"] ?>" onclick="addToCart(this)" class="btn btn--primary btn--sm">В корзину</button>
                                <?php endif ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>
        <?php endif; ?>
    </div>
</main>
