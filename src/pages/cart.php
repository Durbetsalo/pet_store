<?php

$stmt = $connect->prepare("SELECT 
ci.id AS cart_item_id, 
p.id as product_id, 
p.img AS product_img, 
p.name AS product_name, 
p.price AS product_price, 
p.stock AS product_stock, 
p.is_new AS product_is_new, 
p.is_popular AS product_is_popular, 
p.is_featured AS product_is_featured, 
p.discount_percent AS product_discount_percent, 
ci.quantity 
FROM cart_items ci 
JOIN products p ON ci.product_id = p.id 
WHERE user_id = ?");
$stmt->bind_param("i", $_SESSION["user_id"]);
$stmt->execute();
$cart_items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$total_items = 0;
$total_amount = 0;

foreach ($cart_items as $cart_item) {
    $total_items += $cart_item["quantity"];
    $discount_price = $cart_item["product_price"] * (1 - $cart_item["product_discount_percent"] / 100);
    $total_amount += $discount_price * $cart_item["quantity"];
}

$delivery_cost = 500;
?>

<main class="cart-page">
    <div class="container">
        <div class="cart-page__header">
            <h1 class="cart-page__title">Корзина <span class="cart-page__count"><?= $total_items ?> товаров</span></h1>
            <?php if (!empty($cart_items)): ?>
                <label class="cart-page__select-all">
                    <input type="checkbox" id="selectAll" checked>
                    <span class="cart-page__checkbox"></span>
                    Все
                </label>
        </div>

        <div class="cart-page__layout">
            <div class="cart-page__items">
                <?php foreach ($cart_items as $cart_item):
                    $discount_price = $cart_item["product_price"] * (1 - $cart_item["product_discount_percent"] / 100);
                    $item_total = $discount_price * $cart_item["quantity"];
                    $old_total = $cart_item["product_price"] * $cart_item["quantity"];
                ?>
                    <div class="cart-item" id="cart_item_<?= $cart_item["product_id"] ?>" data-product-id="<?= $cart_item["product_id"] ?>" data-price="<?= $discount_price ?>" data-old-price="<?= $cart_item["product_price"] ?>" data-qty="<?= $cart_item["quantity"] ?>">
                        <div class="cart-item__image">
                            <label class="cart-item__checkbox-wrapper">
                                <input type="checkbox" class="cart-item__checkbox checkbox" data-product-id="<?= $cart_item["product_id"] ?>" checked>
                                <span class="cart-item__checkbox"></span>
                            </label>
                            <img src="<?= htmlspecialchars($cart_item["product_img"]) ?>" alt="<?= htmlspecialchars($cart_item["product_name"]) ?>">
                        </div>
                        <div class="cart-item__info">
                            <div class="cart-item__info-wrapper">
                                <h3 class="cart-item__name"><?= htmlspecialchars($cart_item["product_name"]) ?></h3>
                                <div class="cart-item__badges">
                                    <?php if ($cart_item["product_is_popular"]): ?>
                                        <span class="badge badge--tag">ХИТ</span>
                                    <?php endif; ?>
                                    <?php if ($cart_item["product_is_new"]): ?>
                                        <span class="badge badge--tag">НОВИНКА</span>
                                    <?php endif; ?>
                                    <?php if ($cart_item["product_discount_percent"] > 0): ?>
                                        <span class="badge badge--sale">СКИДКА <?= $cart_item["product_discount_percent"] ?>%</span>
                                    <?php endif; ?>
                                    <?php if ($cart_item["product_stock"] <= 5): ?>
                                        <span class="cart-item__stock"><img src="/public/assets/icons/thunder-icon.svg" alt=""> Осталось <?= $cart_item["product_stock"] ?> шт.</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="cart-item__price-row">
                                <span class="cart-item__price" data-product-id="<?= $cart_item["product_id"] ?>"><?= number_format($item_total, 0, '', ' ') ?> ₽</span>
                                <?php if ($cart_item["product_discount_percent"] > 0): ?>
                                    <span class="cart-item__price-old" data-product-id="<?= $cart_item["product_id"] ?>"><?= number_format($old_total, 0, '', ' ') ?> ₽</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="cart-item__actions">
                            <div class="cart-item__qty">
                                <button class="cart-item__qty-btn" data-product-id="<?= $cart_item["product_id"] ?>" onclick="decreaseCartItem(this)">−</button>
                                <span id="in_cart_<?= $cart_item["product_id"] ?>" class="cart-item__qty-value" id="in_cart_<?= $cart_item["product_id"] ?>"><?= $cart_item["quantity"] ?></span>
                                <button id="plus_button_<?= $cart_item["product_id"] ?>" class="cart-item__qty-btn" data-product-id="<?= $cart_item["product_id"] ?>" onclick="increaseCartItem(this)">+</button>
                            </div>
                            <button class="cart-item__delete" data-product-id="<?= $cart_item["product_id"] ?>" onclick="removeCartItem(this)">
                                <img src="/public/assets/icons/delete-icon.svg" alt="">
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="cart-page__summary" id="cartSummary">
                <h3 class="cart-page__summary-title">Общая сумма</h3>
                <div class="cart-page__summary-row">
                    <span>Товары, <span id="summaryItemCount"><?= $total_items ?></span> шт.</span>
                    <span id="summaryItemsTotal"><?= number_format($total_amount, 0, '', ' ') ?> ₽</span>
                </div>
                <div class="cart-page__summary-row">
                    <span>Доставка</span>
                    <span><?= number_format($delivery_cost, 0, '', ' ') ?> ₽</span>
                </div>
                <div class="cart-page__summary-total">
                    <span>Итого:</span>
                    <span id="summaryTotal"><?= number_format($total_amount + $delivery_cost, 0, '', ' ') ?> ₽</span>
                </div>
                <a class="btn btn--primary btn--full cart-page__checkout-btn" id="checkoutBtn" onclick="goToCheckout()">Перейти к оформлению</a>
            </div>
        </div>
    <?php else: ?>
        <div class="cart-page__empty">
            <p>Ваша корзина пуста</p>
            <a href="/catalog" class="btn btn--primary">Перейти в каталог</a>
        </div>
    <?php endif; ?>
    </div>
</main>

<script>
    const selectAll = document.getElementById('selectAll');
    const itemCheckboxes = Array.from(document.querySelectorAll('.cart-item__checkbox'));
    const stickyBar = document.getElementById('stickyBar');
    const cartSummary = document.getElementById('cartSummary');

    if (cartSummary && stickyBar) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                stickyBar.style.transform = entry.isIntersecting ? 'translateY(100%)' : 'translateY(0)';
            });
        }, {
            threshold: 0.3
        });
        observer.observe(cartSummary);
    }

    if (selectAll) {
        selectAll.addEventListener('change', function() {
            itemCheckboxes.forEach(cb => cb.checked = this.checked);
            updateCartTotals();
        });
    }

    itemCheckboxes.forEach(cb => cb.addEventListener('change', () => {
        updateCartTotals()

        selected_id = Array.from(document.querySelectorAll(".checkbox")).filter(elem => !elem.checked).map(elem => elem.getAttribute("data-product-id"))

        // console.log(selected_id);
        if (selected_id.length == 0) {
            selectAll.checked = true
        } else {
            selectAll.checked = false
        }
    }));

    function getSelectedItems() {
        return Array.from(document.querySelectorAll('.cart-item')).filter(item => {
            const cb = item.querySelector('.cart-item__checkbox');
            return cb && cb.checked;
        });
    }

    function formatPrice(num) {
        return Math.round(num).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ') + ' ₽';
    }

    function updateCartTotals() {
        // const allChecked = itemCheckboxes.every(cb => cb.checked);
        // if (selectAll) selectAll.checked = allChecked;

        const selected = getSelectedItems();
        let totalQty = 0;
        let totalAmount = 0;

        selected.forEach(item => {
            const qty = parseInt(item.dataset.qty) || 0;
            const price = parseFloat(item.dataset.price) || 0;
            totalQty += qty;
            totalAmount += price * qty;
        });

        const grandTotal = totalAmount + <?= $delivery_cost ?>;

        const summaryItemCount = document.getElementById('summaryItemCount');
        const summaryItemsTotal = document.getElementById('summaryItemsTotal');
        const summaryTotal = document.getElementById('summaryTotal');
        const stickyItemCount = document.getElementById('stickyItemCount');
        const stickyTotal = document.getElementById('stickyTotal');

        if (summaryItemCount) animateValue(summaryItemCount, summaryItemCount.textContent, totalQty, 400);
        if (summaryItemsTotal) animateValue(summaryItemsTotal, summaryItemsTotal.textContent, Math.round(totalAmount), 400, formatPrice);
        if (summaryTotal) animateValue(summaryTotal, summaryTotal.textContent, Math.round(grandTotal), 400, formatPrice);
        if (stickyItemCount) animateValue(stickyItemCount, stickyItemCount.textContent, totalQty, 400);
        if (stickyTotal) animateValue(stickyTotal, stickyTotal.textContent, Math.round(grandTotal), 400, formatPrice);
    }

    function numberFormat(num) {
        return Math.round(num).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
    }

    async function increaseCartItem(button) {
        let data = await addToCart(button, true);
        if (!data.success) return;

        const productId = button.getAttribute("data-product-id");
        const item = document.getElementById('cart_item_' + productId);
        const qtyEl = document.getElementById('in_cart_' + productId);
        console.log(qtyEl);
        const newQty = parseInt(qtyEl.textContent);
        animateValue(qtyEl, parseInt(qtyEl.textContent), newQty, 400);
        item.dataset.qty = newQty;


        const price = parseFloat(item.dataset.price);
        const oldPrice = parseFloat(item.dataset.oldPrice);
        const newTotal = price * newQty;
        const newOldTotal = oldPrice * newQty;

        const priceEl = item.querySelector('.cart-item__price');
        if (priceEl) animateValue(priceEl, priceEl.textContent, Math.round(newTotal), 400, formatPrice);

        const oldPriceEl = item.querySelector('.cart-item__price-old');
        if (oldPriceEl) animateValue(oldPriceEl, oldPriceEl.textContent, Math.round(newOldTotal), 400, formatPrice);

        updateCartTotals();
    }

    async function decreaseCartItem(button) {
        let data = await deleteFromCart(button, true);
        if (!data.success) return;

        const productId = button.getAttribute("data-product-id");

        if (data.status == "deleted") {
            const card = document.getElementById('cart_item_' + productId);
            card.remove();
            const remaining = document.querySelectorAll('.cart-item');
            if (remaining.length === 0) {
                location.reload();
            }
        } else {
            const item = document.getElementById('cart_item_' + productId);
            const qtyEl = document.getElementById('in_cart_' + productId);
            const newQty = parseInt(qtyEl.textContent);
            animateValue(qtyEl, parseInt(qtyEl.textContent), newQty, 400);
            item.dataset.qty = newQty;

            const price = parseFloat(item.dataset.price);
            const oldPrice = parseFloat(item.dataset.oldPrice);
            const newTotal = price * newQty;
            const newOldTotal = oldPrice * newQty;

            const priceEl = item.querySelector('.cart-item__price');
            if (priceEl) animateValue(priceEl, priceEl.textContent, Math.round(newTotal), 400, formatPrice);

            const oldPriceEl = item.querySelector('.cart-item__price-old');
            if (oldPriceEl) animateValue(oldPriceEl, oldPriceEl.textContent, Math.round(newOldTotal), 400, formatPrice);
        }
        updateCartTotals();
    }

    async function removeCartItem(button) {
        const productId = button.getAttribute("data-product-id");
        const card = document.getElementById('cart_item_' + productId);

        let formdata = new FormData();
        formdata.append("product_id", productId);
        formdata.append("delete_all", true);

        try {
            let result = await fetch("/src/actions/product/delete-from-cart.php", {
                method: "POST",
                body: formdata,
            });
            let data = await result.json();

            if (data.success) {
                card.remove();
                const remaining = document.querySelectorAll('.cart-item');
                if (remaining.length === 0) {
                    location.reload();
                }
                updateCartTotals();
            }
        } catch (error) {
            console.log(error);
        }
    }

    function goToCheckout() {
        selected_id = itemCheckboxes.filter(elem => elem.checked).map(elem => elem.getAttribute("data-product-id"));
        window.location.href = `/checkout?selected=${selected_id}`
    }
</script>