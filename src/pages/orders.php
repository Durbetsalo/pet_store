<?php

$stmt = $connect->prepare("SELECT 
o.id, 
u.name AS user_name, 
os.name AS status,
o.address, o.total_amount, 
o.delivery_date, 
o.created_at 
FROM orders o 
JOIN users u ON o.user_id = u.id 
JOIN order_statuses os ON o.status_id = os.id
WHERE o.user_id = ?
ORDER BY o.created_at DESC");
$stmt->bind_param('i', $_SESSION["user_id"]);
$stmt->execute();
$orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$title = "Мои заказы";
?>

<main class="orders-page">
    <div class="container">
        <h1 class="orders-page__title">Мои заказы</h1>

        <?php if (empty($orders)): ?>
            <div class="orders-page__empty">
                <p>Пока ничего нет.</p>
                <a href="/catalog" class="btn btn--primary">ПЕРЕЙТИ В КАТАЛОГ</a>
            </div>
        <?php else: ?>
            <div class="orders-page__list">
                <?php foreach ($orders as $order):
                    $stmt = $connect->prepare("
                        SELECT oi.*, p.price, p.name, p.img, p.discount_percent
                        FROM order_items oi
                        JOIN products p ON oi.product_id = p.id
                        WHERE oi.order_id = ?
                    ");
                    $stmt->bind_param('i', $order['id']);
                    $stmt->execute();
                    $products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
                ?>
                    <div class="order-card">
                        <div class="order-card__header">
                            <div class="order-card__meta">
                                <span class="order-card__number">Заказ №<?= $order['id'] ?></span>
                                <span class="order-card__date"><?= date('d.m.Y', strtotime($order['created_at'])) ?></span>
                            </div>
                            <span class="badge badge--tag" style="color: var(--color-primary);"><?= $order['status'] ?></span>
                        </div>

                        <a href="/product/<?= $product['id'] ?>" class="order-card__items">
                            <?php foreach ($products as $product): ?>
                                <div class="order-card__item">
                                    <div class="order-card__item-image">
                                        <img src="<?= htmlspecialchars($product['img']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                                    </div>
                                    <div class="order-card__item-info">
                                        <span class="order-card__item-name"><?= htmlspecialchars($product['name']) ?></span>
                                        <span class="order-card__item-qty"><?= $product['quantity'] ?> шт.</span>
                                    </div>
                                    <div class="order-card__item-price">
                                        <?php if (!empty($product['discount_percent'])): ?>
                                            <span class="order-card__item-price-current"><?= number_format($product['discount_price_at_order'] * $product['quantity'], 0, '', ' ') ?> ₽</span>
                                            <span class="order-card__item-price-old"><?= number_format($product['price_at_order'] * $product['quantity'], 0, '', ' ') ?> ₽</span>
                                        <?php else: ?>
                                            <span class="order-card__item-price-current"><?= number_format($product['price_at_order'] * $product['quantity'], 0, '', ' ') ?> ₽</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </a>

                        <div class="order-card__footer">
                            <span class="order-card__total-label">Итого</span>
                            <span class="order-card__total-value"><?= number_format($order['total_amount'], 0, '', ' ') ?> ₽</span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</main>
