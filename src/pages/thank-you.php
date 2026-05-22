<?php
if (!isset($_SESSION["user_id"])) {
    header("Location: /login");
    exit;
}

$stmt = $connect->prepare("SELECT id FROM orders WHERE user_id = ? ORDER BY id DESC LIMIT 1");
$stmt->bind_param("i", $_SESSION["user_id"]);
$stmt->execute();
$last_order = $stmt->get_result()->fetch_assoc();
$order_number = $last_order["id"] ?? "123456";
?>

<main class="thank-you-page">
    <div class="thank-you-page__content">
        <a href="/" class="thank-you-page__logo">Bloop</a>
        <div class="thank-you-page__inner">
            <h1 class="thank-you-page__title">Спасибо за заказ!</h1>
            <p class="thank-you-page__text">Кто-то скоро будет очень доволен. Мы уже начали бережно собирать посылку для вашего питомца.</p>
            <p class="thank-you-page__order">Номер заказа: <strong>#<?= $order_number ?></strong></p>
            <a href="/catalog" class="btn btn--primary btn--full thank-you-page__btn">ПРОДОЛЖИТЬ ПОКУПКИ</a>
        </div>
    </div>
    <div class="thank-you-page__image">
        <img src="/public/assets/images/thank-you-panda.png" alt="">
    </div>
</main>
