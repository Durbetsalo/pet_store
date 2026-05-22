<?php
$stmt = $connect->prepare("SELECT * FROM products WHERE is_popular = 1");
$stmt->execute();
$products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$stmt = $connect->prepare("SELECT r.comment, r.assessment, u.name AS user_name, u.img AS user_img, p.name AS product_name, p.id AS product_id FROM reviews r JOIN users u ON r.user_id = u.id JOIN products p ON r.product_id = p.id WHERE r.assessment = 5 ORDER BY r.created_at DESC");
$stmt->execute();
$reviews = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<main class="main">
    <section class="hero">
        <div class="container">
            <div class="hero__grid">
                <div class="hero__banner hero__banner--big">
                    <div class="hero__banner-overlay">
                        <img src="/public/assets/images/pink-dog-banner.png" alt="">
                    </div>
                    <div class="hero__banner-content">
                        <span class="hero__badge">АКЦИЯ</span>
                        <h2 class="hero__title">Скидка 20% на весь премиум уход</h2>
                        <p class="hero__subtitle">Обновите арсенал для вашего питомца лучшими средствами от ведущих брендов. Предложение ограничено.</p>
                        <a href="/promotions" class="btn btn--primary hero__btn">
                            Смотреть все
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M5 12h14M12 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>

                <div class="hero__side">
                    <div class="hero__banner hero__banner--small hero__banner--green">
                        <div class="hero__banner-content">
                            <span class="hero__badge hero__badge--dark">НОВИНКИ</span>
                            <h3 class="hero__banner-title">Весенняя коллекция</h3>
                            <p class="hero__banner-subtitle">Стильные аксессуары</p>
                            <a href="/new-products" class="hero__banner-arrow">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M5 12h14M12 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                    </div>

                    <div class="hero__banner hero__banner--small hero__banner--white">
                        <div class="hero__banner-content">
                            <span class="hero__badge hero__badge--red">ХИТЫ ПРОДАЖ</span>
                            <h3 class="hero__banner-title hero__banner-title--dark">Выбор покупателей</h3>
                            <p class="hero__banner-subtitle hero__banner-subtitle--dark">Бестселлеры месяца</p>
                            <a href="/catalog" class="hero__banner-arrow hero__banner-arrow--dark">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M5 12h14M12 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="about-section">
        <div class="container">
            <div class="about-section__grid">
                <div class="about-section__content">
                    <span class="section-label">
                        <img src="/public/assets/icons/premium-stars.svg" alt="">
                        ПРЕМИАЛЬНЫЙ УХОД
                    </span>
                    <h2 class="section-title about-section__title">Естественная забота для хороших мальчиков и девочек</h2>
                    <p class="about-section__text">Bloop — это курируемый выбор премиальных кормов, аксессуаров и косметики для вашего питомца. Только проверенные бренды, только честные составы.</p>
                    <div class="about-section__actions">
                        <a href="/catalog" class="btn btn--primary">
                            Перейти в каталог
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M5 12h14M12 5l7 7-7 7" />
                            </svg>
                        </a>
                        <a href="/about" class="btn btn--outline">О магазине</a>
                    </div>
                    <div class="about-section__stats">
                        <div class="about-section__stat">
                            <span class="about-section__stat-value">4.9</span>
                            <span class="about-section__stat-label">Средняя оценка</span>
                        </div>
                        <div class="about-section__stat">
                            <span class="about-section__stat-value">12 000+</span>
                            <span class="about-section__stat-label">Счастливых питомцев</span>
                        </div>
                        <div class="about-section__stat">
                            <span class="about-section__stat-value">320+</span>
                            <span class="about-section__stat-label">Товаров в каталоге</span>
                        </div>
                    </div>
                </div>
                <div class="about-section__image">
                    <div class="about-section__image-wrapper">
                        <img src="/public/assets/images/golden-retriever.png" alt="Золотистый ретривер" class="about-section__img">
                        <div class="about-section__image-badge">
                            <div class="about-section__image-badge__icon">
                                <img src="/public/assets/icons/shield.svg" alt="">
                            </div>
                            <div class="about-section__image-badge-wrapper">
                                <span>Одобрено ветеринарами</span>
                                <small>Все товары протестированы</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="about-store">
        <div class="container">
            <div class="about-store__header-wrapper">
                <div class="about-store__header">
                    <span class="section-label section-label--red">О МАГАЗИНЕ</span>
                    <h3 class="section-title section-title--about-shop">Мы верим, что питомцы заслуживают лучшего</h3>
                </div>
                <p class="about-store__text">Bloop — это не просто магазин. Это пространство, где каждый товар проходит тщательный отбор. Мы работаем только с теми производителями, которые разделяют нашу философию: натуральные ингредиенты, честные составы и забота о природе.</p>
            </div>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-card__icon">
                        <img src="/public/assets/icons/premium-stars.svg" alt="">
                    </div>
                    <h3 class="feature-card__title">Премиум качество</h3>
                    <p class="feature-card__text">Каждый товар проходит многоступенчатый контроль качества. Никаких компромиссов.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-card__icon">
                        <img src="/public/assets/icons/truck-icon.svg" alt="">
                    </div>
                    <h3 class="feature-card__title">Быстрая доставка</h3>
                    <p class="feature-card__text">Доставим по Москве за 2-4 часа, по России — за 1-3 рабочих дня.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-card__icon">
                        <img src="/public/assets/icons/shield.svg" alt="">
                    </div>
                    <h3 class="feature-card__title">Одобрено ветеринарами</h3>
                    <p class="feature-card__text">Весь ассортимент согласован с практикующими ветеринарами-диетологами.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="popular-products">
        <div class="container">
            <div class="section-header">
                <span class="section-label section-label--red">КАТАЛОГ</span>
                <div class="section-header__row">
                    <h2 class="section-title section-title--popular-products">Популярные товары</h2>
                    <a href="/catalog" class="section-header__link">
                        Весь каталог
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M5 12h14M12 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            </div>
            <div class="products-grid">
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
    </section>

    <section class="promotions-section">
        <div class="container">
            <div class="section-header">
                <span class="section-label section-label--red">АКЦИИ</span>
                <h2 class="section-title section-title--popular-promotions">Актуальные предложения</h2>
            </div>
            <div class="promotions-grid">
                <div class="promo-card promo-card--light">
                    <div class="promo-card__content">
                        <span class="promo-card__label">КОРМА</span>
                        <h3 class="promo-card__title">Скидка 20% на премиум корма</h3>
                        <p class="promo-card__text">Отборные рационы от ведущих европейских производителей. Успейте до конца месяца.</p>
                        <a href="/promotions" class="btn btn--outline btn--sm">
                            Смотреть
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M5 12h14M12 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                    <div class="promo-card__image">
                        <img src="/public/assets/images/boxes.png" alt="Скидка на корма">
                    </div>
                </div>

                <div class="promo-card promo-card--dark">
                    <div class="promo-card__content">
                        <span class="promo-card__label">ДОСТАВКА</span>
                        <h3 class="promo-card__title">Бесплатная доставка от 3 000 ₽</h3>
                        <p class="promo-card__text">Курьером по Москве и Санкт-Петербургу, а также транспортными компаниями по всей России.</p>
                        <a href="/promotions" class="btn btn--outline btn--sm">
                            Подробнее
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M5 12h14M12 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                    <div class="promo-card__icon">
                        <img src="/public/assets/icons/truck-icon.svg" alt="">
                    </div>
                </div>
            </div>
        </div>
        </div>
    </section>

    <section class="reviews-section">
        <div class="container">
            <div class="section-header">
                <span class="section-label section-label--red">ОТЗЫВЫ</span>
                <h2 class="section-title section-title--reviews">Что говорят владельцы</h2>
            </div>
            <div class="reviews-grid">
                <?php foreach ($reviews as $review): ?>
                    <div class="review-card">
                        <div class="review-card__stars">
                            <?php for ($i = 0; $i < 5; $i++): ?>
                                <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 26 26" fill="none">
                                    <path d="M12.482 2.48638C12.5295 2.39048 12.6028 2.30977 12.6937 2.25333C12.7846 2.1969 12.8895 2.16699 12.9965 2.16699C13.1035 2.16699 13.2084 2.1969 13.2993 2.25333C13.3902 2.30977 13.4635 2.39048 13.5109 2.48638L16.0128 7.55396C16.1776 7.8875 16.4209 8.17607 16.7218 8.3949C17.0227 8.61372 17.3721 8.75627 17.7402 8.8103L23.3353 9.62908C23.4413 9.64444 23.5409 9.68916 23.6228 9.75818C23.7047 9.8272 23.7657 9.91777 23.7988 10.0196C23.832 10.1215 23.8359 10.2306 23.8103 10.3346C23.7846 10.4386 23.7304 10.5334 23.6537 10.6082L19.6074 14.5483C19.3406 14.8083 19.1409 15.1293 19.0256 15.4836C18.9104 15.838 18.8829 16.215 18.9457 16.5823L19.9009 22.1491C19.9196 22.2551 19.9082 22.3642 19.8679 22.4639C19.8276 22.5637 19.7601 22.6502 19.673 22.7134C19.5859 22.7766 19.4828 22.8141 19.3755 22.8216C19.2682 22.8291 19.1609 22.8062 19.0659 22.7556L14.0644 20.126C13.7348 19.9529 13.3682 19.8625 12.996 19.8625C12.6237 19.8625 12.2571 19.9529 11.9275 20.126L6.92709 22.7556C6.83214 22.8059 6.72499 22.8285 6.61783 22.8209C6.51067 22.8133 6.40779 22.7757 6.3209 22.7125C6.23402 22.6494 6.16661 22.563 6.12635 22.4634C6.08608 22.3638 6.07458 22.2549 6.09314 22.1491L7.04731 16.5833C7.11034 16.2159 7.08303 15.8386 6.96775 15.4841C6.85246 15.1296 6.65266 14.8084 6.38557 14.5483L2.3393 10.6092C2.26196 10.5345 2.20716 10.4396 2.18113 10.3353C2.15511 10.231 2.1589 10.1215 2.19209 10.0192C2.22528 9.91692 2.28652 9.82602 2.36884 9.75686C2.45116 9.6877 2.55126 9.64305 2.65772 9.628L8.25166 8.8103C8.62017 8.75668 8.97014 8.61433 9.27144 8.39547C9.57274 8.17662 9.81634 7.88783 9.98129 7.55396L12.482 2.48638Z" />
                                </svg>
                            <?php endfor; ?>
                        </div>
                        <p class="review-card__text">"<?= htmlspecialchars($review['comment']) ?>"</p>
                        <div class="review-card__author">
                            <div class="review-card__avatar">
                                <?php if (!empty($review['user_img'])): ?>
                                    <img src="<?= htmlspecialchars($review['user_img']) ?>" alt="<?= htmlspecialchars($review['user_name']) ?>">
                                <?php else: ?>
                                    <span><?= mb_strtoupper(mb_substr($review['user_name'], 0, 1)) ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="review-card__name-wrapper">
                                <span class="review-card__name"><?= htmlspecialchars($review['user_name']) ?></span>
                                <a href="/product/<?= $review['product_id'] ?>" class="review-card__product"><?= htmlspecialchars($review['product_name']) ?></a>
                            </div>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>
        </div>
    </section>

    <section class="contact-section">
        <div class="container">
            <div class="contact-section__grid">
                <div class="contact-section__info">
                    <span class="section-label section-label--red">КОНТАКТЫ</span>
                    <h2 class="section-title section-title--contacts">Остались вопросы?</h2>
                    <p class="contact-section__text">Напишите нам — мы отвечаем в течение нескольких часов. Наши специалисты помогут подобрать идеальный рацион для вашего питомца.</p>
                    <div class="contact-section__details">
                        <a href="tel:+74950000000" class="contact-section__detail">
                            <div class="contact-section__detail-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z" />
                                </svg>
                            </div>
                            <div>
                                <span class="contact-section__detail-label">Телефон</span>
                                <span class="contact-section__detail-value">+7 (495) 000-00-00</span>
                            </div>
                        </a>
                        <a href="mailto:hello@bloop.ru" class="contact-section__detail">
                            <div class="contact-section__detail-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                                    <polyline points="22,6 12,13 2,6" />
                                </svg>
                            </div>
                            <div>
                                <span class="contact-section__detail-label">Email</span>
                                <span class="contact-section__detail-value">hello@bloop.ru</span>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="contact-section__form-wrapper">
                    <form class="contact-form">
                        <div class="form-group">
                            <label for="contact-name" class="form-label">Ваше имя</label>
                            <input type="text" id="contact-name" class="form-input" placeholder="Иван Иванов">
                        </div>
                        <div class="form-group">
                            <label for="contact-email" class="form-label">Email</label>
                            <input type="email" id="contact-email" class="form-input" placeholder="ivan@example.com">
                        </div>
                        <div class="form-group">
                            <label for="contact-message" class="form-label">Сообщение</label>
                            <textarea id="contact-message" class="form-input form-textarea" placeholder="Расскажите о вашем питомце и задайте вопрос..."></textarea>
                        </div>
                        <button type="submit" class="btn btn--primary btn--full">
                            Отправить сообщение
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="22" y1="2" x2="11" y2="13" />
                                <polygon points="22 2 15 22 11 13 2 9 22 2" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>
</main>