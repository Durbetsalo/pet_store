<?php

if (!$connect->ping()) {
    require_once __DIR__ . "/../src/config/database.php";
}


if (isset($_SESSION["user_id"])) {
    $stmt = $connect->prepare("SELECT quantity FROM cart_items WHERE user_id=?");
    $stmt->bind_param("i", $_SESSION["user_id"]);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $total_count = 0;
    foreach ($result as $value) {
        $total_count += $value['quantity'];
    }
}
?>

<header class="header">
    <div class="container header__container">
        <a href="/" class="header__logo">Bloop</a>

        <nav class="header__nav">
            <a href="/about" class="header__nav-link">О магазине</a>
            <a href="/catalog" class="header__nav-link">Каталог</a>
            <a href="/promotions" class="header__nav-link">Акции</a>
            <a href="/new-products" class="header__nav-link">Новинки</a>
            <a href="/brands" class="header__nav-link">Бренды</a>
            <a href="/reviews" class="header__nav-link">Отзывы</a>
            <a href="/contacts" class="header__nav-link">Контакты</a>
        </nav>

        <div class="header__actions">
            <?php if (isset($_SESSION["user_id"])): ?>
                <div class="header__desktop-icons">
                    <a href="/profile" aria-label="Профиль">
                        <img src="/public/assets/icons/profile-icon.svg" alt="">
                    </a>
                    <a style="position:relative;" href="/cart" aria-label="Корзина">
                        <img src="/public/assets/icons/cart-icon.svg" alt="">
                        <span class="cart-count"><?= $total_count ?? 0 ?></span>
                    </a>
                </div>
            <?php else: ?>
                <a href="/login" class="header__login-btn">ВОЙТИ</a>
            <?php endif; ?>
            <button class="header__burger" id="burgerBtn" aria-label="Меню">
                <span class="header__burger-line header__burger-line--top"></span>
                <span class="header__burger-line header__burger-line--middle"></span>
                <span class="header__burger-line header__burger-line--bottom"></span>
            </button>
        </div>
    </div>

    <div class="header__mobile-overlay" id="mobileOverlay"></div>

    <div class="header__mobile-nav" id="mobileNav">
        <div class="header__mobile-nav-header">
            <span class="header__mobile-nav-logo">Bloop</span>
            <button class="header__mobile-close" id="mobileClose" aria-label="Закрыть">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18" />
                    <line x1="6" y1="6" x2="18" y2="18" />
                </svg>
            </button>
        </div>
        <div class="header__mobile-links">
            <a href="/about" class="header__mobile-link">О магазине</a>
            <a href="/brands" class="header__mobile-link">Бренды</a>
            <a href="/catalog" class="header__mobile-link">Каталог</a>
            <a href="/reviews" class="header__mobile-link">Отзывы</a>
            <a href="/promotions" class="header__mobile-link">Акции</a>
            <a href="/contacts" class="header__mobile-link">Контакты</a>
            <a href="/new-products" class="header__mobile-link">Новинки</a>
            <a href="/cart" class="header__mobile-link">Корзина</a>
        </div>
        <?php if (isset($_SESSION["user_id"])): ?>
            <a href="/profile" class="header__mobile-login">ПРОФИЛЬ</a>
        <?php else: ?>
            <a href="/login" class="header__mobile-login">ВОЙТИ</a>
        <?php endif; ?>
    </div>
</header>