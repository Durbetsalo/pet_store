<?php
$stmt = $connect->prepare("SELECT p.*, ci.quantity, b.name AS brand_name FROM products p LEFT JOIN cart_items ci ON ci.product_id = p.id AND ci.user_id = ? JOIN brands b ON p.brand_id = b.id WHERE p.id = ?");
$stmt->bind_param("ii", $_SESSION["user_id"], $_GET["id"]);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

$title = $product["name"];
if (!$product) {
    header("Location: /not-found");
    exit;
}

$stmt = $connect->prepare("SELECT oi.id FROM order_items oi JOIN orders o ON oi.order_id = o.id WHERE o.user_id = ? AND oi.product_id = ?");
$stmt->bind_param("ii", $_SESSION["user_id"], $_GET["id"]);
$stmt->execute();
$order_items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$stmt = $connect->prepare("SELECT r.id, u.img, u.name AS user_name, r.comment, r.created_at, r.assessment FROM reviews r JOIN users u ON r.user_id = u.id WHERE product_id = ?");
$stmt->bind_param("i", $product["id"]);
$stmt->execute();
$reviews = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

if (isset($_SESSION['user_id'])) {
    $stmt = $connect->prepare("SELECT * FROM cart_items WHERE user_id = ? AND product_id = ?");
    $stmt->bind_param('ii', $_SESSION['user_id'], $_GET["id"]);
    $stmt->execute();
    $cart_item = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $comment = trim($_POST["comment"]);
    $assessment = $_POST["assessment"];
    $error = [];

    if (empty($comment)) {
        $error["comment"] = "Заполните поле";
    }
    if (!empty($error)) {
        $_SESSION["error"] = $error;
        $_SESSION["old_input"] = [
            "comment" => $comment,
            "assessment" => $assessment
        ];
        header("Location: /product/{$_GET["id"]}");
        exit;
    }

    $stmt = $connect->prepare("INSERT INTO reviews (user_id, product_id, comment, assessment) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iisi", $_SESSION["user_id"], $_GET["id"], $comment, $assessment);
    $stmt->execute();
    header("Location: /product/{$_GET["id"]}");
}


$input_error = $_SESSION["error"] ?? [];
$old_input = $_SESSION["old_input"] ?? [];
unset($_SESSION["error"]);
unset($_SESSION["old_input"]);

$discount_price = $product["discount_percent"] ? $product["price"] * (1 - $product["discount_percent"] / 100) : null;
$has_discount = $product["discount_percent"] && $product["discount_percent"] > 0;
?>

<main class="product-page">
    <div class="container">
        <nav class="breadcrumbs">
            <a href="/" class="breadcrumbs__link">Главная</a>
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M9 18l6-6-6-6" />
            </svg>
            <a href="/catalog" class="breadcrumbs__link">Каталог</a>
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M9 18l6-6-6-6" />
            </svg>
            <span class="breadcrumbs__current"><?= htmlspecialchars($product["name"]) ?></span>
        </nav>

        <div class="product-page__layout">
            <div class="product-page__image">
                <img src="<?= htmlspecialchars($product["img"]) ?>" alt="<?= htmlspecialchars($product["name"]) ?>">
                <div class="product-card__badges product-card__badges--product-page">
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
            </div>

            <div class="product-page__info">
                <span class="product-page__brand"><?= $product['brand_name'] ?></span>
                <h1 class="product-page__title"><?= htmlspecialchars($product["name"]) ?></h1>
                <p class="product-page__stock">В наличии: <strong><?= $product["stock"] ?> шт.</strong></p>

                <div class="product-page__price-row">
                    <?php if ($has_discount): ?>
                        <span class="product-page__price"><?= number_format($discount_price, 0, '', ' ') ?> ₽</span>
                        <span class="product-page__price-old"><?= number_format($product["price"], 0, '', ' ') ?> ₽</span>
                    <?php else: ?>
                        <span class="product-page__price"><?= number_format($product["price"], 0, '', ' ') ?> ₽</span>
                    <?php endif; ?>
                </div>

                <div class="product-page__actions">
                    <?php if (isset($cart_item)):
                        $in_cart = false;
                        $quantity = 0;
                        $in_cart = true;
                        $quantity = $product["quantity"];
                    ?>
                    <div class="product-page__qty <?= $product["quantity"] == 0 ? 'product-page__qty--hidden' : '' ?>" id="group_<?= $product["id"] ?>">
                        <button class="product-page__qty-btn" data-product-id="<?= $product["id"] ?>" onclick="handleDeleteFromProduct(this)">−</button>
                        <span class="product-page__qty-value" id="in_cart_<?= $product["id"] ?>"><?= $product["quantity"] ?></span>
                        <button class="product-page__qty-btn" id="plus_button_<?= $product["id"] ?>" <?= $product["stock"] == 0 ? "disabled" : "" ?> data-product-id="<?= $product["id"] ?>" onclick="addToCart(this)">+</button>
                    </div>
                    <?php endif ?>
                    <?php if ($product["quantity"] != 0): ?>
                    <a href="/cart" class="btn btn--primary product-page__cart-btn product-page__cart-btn--in-cart">
                        В корзине
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 18l6-6-6-6" />
                        </svg>
                    </a>
                    <?php else: ?>
                    <button class="btn btn--primary product-page__cart-btn" data-product-id="<?= $product["id"] ?>" onclick="addToCartFromProduct(this)">
                        В корзину
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 18l6-6-6-6" />
                        </svg>
                    </button>
                    <?php endif; ?>
                </div>

                <div class="product-page__description">
                    <h2 class="product-page__desc-title">Описание</h2>
                    <p class="product-page__desc-text"><?= htmlspecialchars($product["description"]) ?></p>
                </div>
            </div>
        </div>

        <section class="product-reviews">
            <h2 class="product-reviews__title">Отзывы</h2>

            <?php if (!empty($order_items)): ?>
                <div class="product-reviews__form-card">
                    <h3 class="product-reviews__form-title">Оставьте отзыв</h3>
                    <form action="" method="POST" class="product-reviews__form">
                        <div class="product-reviews__rating">
                            <label class="product-reviews__rating-label">Оценка</label>
                            <div class="product-reviews__stars" id="ratingStars">
                                <input type="radio" id="star-5" name="assessment" value="5" required>
                                <label for="star-5" class="product-reviews__star">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 26 26" fill="none">
                                        <path d="M12.482 2.48638C12.5295 2.39048 12.6028 2.30977 12.6937 2.25333C12.7846 2.1969 12.8895 2.16699 12.9965 2.16699C13.1035 2.16699 13.2084 2.1969 13.2993 2.25333C13.3902 2.30977 13.4635 2.39048 13.5109 2.48638L16.0128 7.55396C16.1776 7.8875 16.4209 8.17607 16.7218 8.3949C17.0227 8.61372 17.3721 8.75627 17.7402 8.8103L23.3353 9.62908C23.4413 9.64444 23.5409 9.68916 23.6228 9.75818C23.7047 9.8272 23.7657 9.91777 23.7988 10.0196C23.832 10.1215 23.8359 10.2306 23.8103 10.3346C23.7846 10.4386 23.7304 10.5334 23.6537 10.6082L19.6074 14.5483C19.3406 14.8083 19.1409 15.1293 19.0256 15.4836C18.9104 15.838 18.8829 16.215 18.9457 16.5823L19.9009 22.1491C19.9196 22.2551 19.9082 22.3642 19.8679 22.4639C19.8276 22.5637 19.7601 22.6502 19.673 22.7134C19.5859 22.7766 19.4828 22.8141 19.3755 22.8216C19.2682 22.8291 19.1609 22.8062 19.0659 22.7556L14.0644 20.126C13.7348 19.9529 13.3682 19.8625 12.996 19.8625C12.6237 19.8625 12.2571 19.9529 11.9275 20.126L6.92709 22.7556C6.83214 22.8059 6.72499 22.8285 6.61783 22.8209C6.51067 22.8133 6.40779 22.7757 6.3209 22.7125C6.23402 22.6494 6.16661 22.563 6.12635 22.4634C6.08608 22.3638 6.07458 22.2549 6.09314 22.1491L7.04731 16.5833C7.11034 16.2159 7.08303 15.8386 6.96775 15.4841C6.85246 15.1296 6.65266 14.8084 6.38557 14.5483L2.3393 10.6092C2.26196 10.5345 2.20716 10.4396 2.18113 10.3353C2.15511 10.231 2.1589 10.1215 2.19209 10.0192C2.22528 9.91692 2.28652 9.82602 2.36884 9.75686C2.45116 9.6877 2.55126 9.64305 2.65772 9.628L8.25166 8.8103C8.62017 8.75668 8.97014 8.61433 9.27144 8.39547C9.57274 8.17662 9.81634 7.88783 9.98129 7.55396L12.482 2.48638Z" />
                                    </svg>
                                </label>

                                <input type="radio" id="star-4" name="assessment" value="4">
                                <label for="star-4" class="product-reviews__star">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 26 26" fill="none">
                                        <path d="M12.482 2.48638C12.5295 2.39048 12.6028 2.30977 12.6937 2.25333C12.7846 2.1969 12.8895 2.16699 12.9965 2.16699C13.1035 2.16699 13.2084 2.1969 13.2993 2.25333C13.3902 2.30977 13.4635 2.39048 13.5109 2.48638L16.0128 7.55396C16.1776 7.8875 16.4209 8.17607 16.7218 8.3949C17.0227 8.61372 17.3721 8.75627 17.7402 8.8103L23.3353 9.62908C23.4413 9.64444 23.5409 9.68916 23.6228 9.75818C23.7047 9.8272 23.7657 9.91777 23.7988 10.0196C23.832 10.1215 23.8359 10.2306 23.8103 10.3346C23.7846 10.4386 23.7304 10.5334 23.6537 10.6082L19.6074 14.5483C19.3406 14.8083 19.1409 15.1293 19.0256 15.4836C18.9104 15.838 18.8829 16.215 18.9457 16.5823L19.9009 22.1491C19.9196 22.2551 19.9082 22.3642 19.8679 22.4639C19.8276 22.5637 19.7601 22.6502 19.673 22.7134C19.5859 22.7766 19.4828 22.8141 19.3755 22.8216C19.2682 22.8291 19.1609 22.8062 19.0659 22.7556L14.0644 20.126C13.7348 19.9529 13.3682 19.8625 12.996 19.8625C12.6237 19.8625 12.2571 19.9529 11.9275 20.126L6.92709 22.7556C6.83214 22.8059 6.72499 22.8285 6.61783 22.8209C6.51067 22.8133 6.40779 22.7757 6.3209 22.7125C6.23402 22.6494 6.16661 22.563 6.12635 22.4634C6.08608 22.3638 6.07458 22.2549 6.09314 22.1491L7.04731 16.5833C7.11034 16.2159 7.08303 15.8386 6.96775 15.4841C6.85246 15.1296 6.65266 14.8084 6.38557 14.5483L2.3393 10.6092C2.26196 10.5345 2.20716 10.4396 2.18113 10.3353C2.15511 10.231 2.1589 10.1215 2.19209 10.0192C2.22528 9.91692 2.28652 9.82602 2.36884 9.75686C2.45116 9.6877 2.55126 9.64305 2.65772 9.628L8.25166 8.8103C8.62017 8.75668 8.97014 8.61433 9.27144 8.39547C9.57274 8.17662 9.81634 7.88783 9.98129 7.55396L12.482 2.48638Z" />
                                    </svg>
                                </label>

                                <input type="radio" id="star-3" name="assessment" value="3">
                                <label for="star-3" class="product-reviews__star">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 26 26" fill="none">
                                        <path d="M12.482 2.48638C12.5295 2.39048 12.6028 2.30977 12.6937 2.25333C12.7846 2.1969 12.8895 2.16699 12.9965 2.16699C13.1035 2.16699 13.2084 2.1969 13.2993 2.25333C13.3902 2.30977 13.4635 2.39048 13.5109 2.48638L16.0128 7.55396C16.1776 7.8875 16.4209 8.17607 16.7218 8.3949C17.0227 8.61372 17.3721 8.75627 17.7402 8.8103L23.3353 9.62908C23.4413 9.64444 23.5409 9.68916 23.6228 9.75818C23.7047 9.8272 23.7657 9.91777 23.7988 10.0196C23.832 10.1215 23.8359 10.2306 23.8103 10.3346C23.7846 10.4386 23.7304 10.5334 23.6537 10.6082L19.6074 14.5483C19.3406 14.8083 19.1409 15.1293 19.0256 15.4836C18.9104 15.838 18.8829 16.215 18.9457 16.5823L19.9009 22.1491C19.9196 22.2551 19.9082 22.3642 19.8679 22.4639C19.8276 22.5637 19.7601 22.6502 19.673 22.7134C19.5859 22.7766 19.4828 22.8141 19.3755 22.8216C19.2682 22.8291 19.1609 22.8062 19.0659 22.7556L14.0644 20.126C13.7348 19.9529 13.3682 19.8625 12.996 19.8625C12.6237 19.8625 12.2571 19.9529 11.9275 20.126L6.92709 22.7556C6.83214 22.8059 6.72499 22.8285 6.61783 22.8209C6.51067 22.8133 6.40779 22.7757 6.3209 22.7125C6.23402 22.6494 6.16661 22.563 6.12635 22.4634C6.08608 22.3638 6.07458 22.2549 6.09314 22.1491L7.04731 16.5833C7.11034 16.2159 7.08303 15.8386 6.96775 15.4841C6.85246 15.1296 6.65266 14.8084 6.38557 14.5483L2.3393 10.6092C2.26196 10.5345 2.20716 10.4396 2.18113 10.3353C2.15511 10.231 2.1589 10.1215 2.19209 10.0192C2.22528 9.91692 2.28652 9.82602 2.36884 9.75686C2.45116 9.6877 2.55126 9.64305 2.65772 9.628L8.25166 8.8103C8.62017 8.75668 8.97014 8.61433 9.27144 8.39547C9.57274 8.17662 9.81634 7.88783 9.98129 7.55396L12.482 2.48638Z" />
                                    </svg>
                                </label>

                                <input type="radio" id="star-2" name="assessment" value="2">
                                <label for="star-2" class="product-reviews__star">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 26 26" fill="none">
                                        <path d="M12.482 2.48638C12.5295 2.39048 12.6028 2.30977 12.6937 2.25333C12.7846 2.1969 12.8895 2.16699 12.9965 2.16699C13.1035 2.16699 13.2084 2.1969 13.2993 2.25333C13.3902 2.30977 13.4635 2.39048 13.5109 2.48638L16.0128 7.55396C16.1776 7.8875 16.4209 8.17607 16.7218 8.3949C17.0227 8.61372 17.3721 8.75627 17.7402 8.8103L23.3353 9.62908C23.4413 9.64444 23.5409 9.68916 23.6228 9.75818C23.7047 9.8272 23.7657 9.91777 23.7988 10.0196C23.832 10.1215 23.8359 10.2306 23.8103 10.3346C23.7846 10.4386 23.7304 10.5334 23.6537 10.6082L19.6074 14.5483C19.3406 14.8083 19.1409 15.1293 19.0256 15.4836C18.9104 15.838 18.8829 16.215 18.9457 16.5823L19.9009 22.1491C19.9196 22.2551 19.9082 22.3642 19.8679 22.4639C19.8276 22.5637 19.7601 22.6502 19.673 22.7134C19.5859 22.7766 19.4828 22.8141 19.3755 22.8216C19.2682 22.8291 19.1609 22.8062 19.0659 22.7556L14.0644 20.126C13.7348 19.9529 13.3682 19.8625 12.996 19.8625C12.6237 19.8625 12.2571 19.9529 11.9275 20.126L6.92709 22.7556C6.83214 22.8059 6.72499 22.8285 6.61783 22.8209C6.51067 22.8133 6.40779 22.7757 6.3209 22.7125C6.23402 22.6494 6.16661 22.563 6.12635 22.4634C6.08608 22.3638 6.07458 22.2549 6.09314 22.1491L7.04731 16.5833C7.11034 16.2159 7.08303 15.8386 6.96775 15.4841C6.85246 15.1296 6.65266 14.8084 6.38557 14.5483L2.3393 10.6092C2.26196 10.5345 2.20716 10.4396 2.18113 10.3353C2.15511 10.231 2.1589 10.1215 2.19209 10.0192C2.22528 9.91692 2.28652 9.82602 2.36884 9.75686C2.45116 9.6877 2.55126 9.64305 2.65772 9.628L8.25166 8.8103C8.62017 8.75668 8.97014 8.61433 9.27144 8.39547C9.57274 8.17662 9.81634 7.88783 9.98129 7.55396L12.482 2.48638Z" />
                                    </svg>
                                </label>

                                <input type="radio" id="star-1" name="assessment" value="1">
                                <label for="star-1" class="product-reviews__star">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 26 26" fill="none">
                                        <path d="M12.482 2.48638C12.5295 2.39048 12.6028 2.30977 12.6937 2.25333C12.7846 2.1969 12.8895 2.16699 12.9965 2.16699C13.1035 2.16699 13.2084 2.1969 13.2993 2.25333C13.3902 2.30977 13.4635 2.39048 13.5109 2.48638L16.0128 7.55396C16.1776 7.8875 16.4209 8.17607 16.7218 8.3949C17.0227 8.61372 17.3721 8.75627 17.7402 8.8103L23.3353 9.62908C23.4413 9.64444 23.5409 9.68916 23.6228 9.75818C23.7047 9.8272 23.7657 9.91777 23.7988 10.0196C23.832 10.1215 23.8359 10.2306 23.8103 10.3346C23.7846 10.4386 23.7304 10.5334 23.6537 10.6082L19.6074 14.5483C19.3406 14.8083 19.1409 15.1293 19.0256 15.4836C18.9104 15.838 18.8829 16.215 18.9457 16.5823L19.9009 22.1491C19.9196 22.2551 19.9082 22.3642 19.8679 22.4639C19.8276 22.5637 19.7601 22.6502 19.673 22.7134C19.5859 22.7766 19.4828 22.8141 19.3755 22.8216C19.2682 22.8291 19.1609 22.8062 19.0659 22.7556L14.0644 20.126C13.7348 19.9529 13.3682 19.8625 12.996 19.8625C12.6237 19.8625 12.2571 19.9529 11.9275 20.126L6.92709 22.7556C6.83214 22.8059 6.72499 22.8285 6.61783 22.8209C6.51067 22.8133 6.40779 22.7757 6.3209 22.7125C6.23402 22.6494 6.16661 22.563 6.12635 22.4634C6.08608 22.3638 6.07458 22.2549 6.09314 22.1491L7.04731 16.5833C7.11034 16.2159 7.08303 15.8386 6.96775 15.4841C6.85246 15.1296 6.65266 14.8084 6.38557 14.5483L2.3393 10.6092C2.26196 10.5345 2.20716 10.4396 2.18113 10.3353C2.15511 10.231 2.1589 10.1215 2.19209 10.0192C2.22528 9.91692 2.28652 9.82602 2.36884 9.75686C2.45116 9.6877 2.55126 9.64305 2.65772 9.628L8.25166 8.8103C8.62017 8.75668 8.97014 8.61433 9.27144 8.39547C9.57274 8.17662 9.81634 7.88783 9.98129 7.55396L12.482 2.48638Z" />
                                    </svg>
                                </label>
                            </div>
                        </div>
                        <textarea name="comment" class="product-reviews__textarea" placeholder="Комментарий..."><?= htmlspecialchars($old_input["comment"] ?? "") ?></textarea>
                        <?php if (isset($input_error["comment"])): ?>
                            <span class="product-reviews__error"><?= $input_error["comment"] ?></span>
                        <?php endif; ?>
                        <button type="submit" class="btn btn--primary btn--full">Отправить</button>
                    </form>
                </div>
            <?php endif; ?>

            <?php if (empty($reviews)): ?>
                <p class="product-reviews__empty">Отзывов пока нет. Будьте первыми!</p>
            <?php else: ?>
                <?php foreach ($reviews as $review): ?>
                    <div class="product-reviews__card">
                        <div class="product-reviews__card-stars">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <?php if ($review["assessment"] >= $i): ?>
                                    <img src="/public/assets/icons/star.svg" alt="">
                                <?php else: ?>
                                    <img src="/public/assets/icons/non-filled-star.svg" alt="">
                                <?php endif; ?>
                            <?php endfor; ?>
                        </div>
                        <p class="product-reviews__card-text">"<?= htmlspecialchars($review["comment"]) ?>"</p>
                        <div class="product-reviews__card-author">
                            <div class="product-reviews__card-avatar">
                                <?php if ($review["img"]): ?>
                                    <img src="<?= htmlspecialchars($review["img"]) ?>" alt="">
                                <?php else: ?>
                                    <span><?= mb_strtoupper(mb_substr($review["user_name"], 0, 1)) ?></span>
                                <?php endif; ?>
                            </div>
                            <span class="product-reviews__card-name"><?= htmlspecialchars($review["user_name"]) ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>
    </div>
</main>

<script>
    async function handleDeleteFromProduct(button) {
        const productId = button.dataset.productId;
        let res = await deleteFromCart(button);
        if (res) {
            const qtyEl = document.getElementById("in_cart_" + productId);
            let currentQty = parseInt(qtyEl.textContent) || 0;

            if (currentQty === 0) {
                const actionsDiv = document.querySelector(".product-page__actions");
                const cartBtn = actionsDiv.querySelector(".product-page__cart-btn");
                cartBtn.outerHTML = `<button class="btn btn--primary product-page__cart-btn" data-product-id="${productId}" onclick="addToCartFromProduct(this)">В корзину <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg></button>`;
            }
        }
    }

    async function addToCartFromProduct(button) {
        const productId = button.dataset.productId;
        const qty = document.querySelector(".product-page__qty");
        let res = await addToCart(button);
        if (res.success) {
            qty.classList.remove("product-page__qty--hidden");
            document.getElementById("in_cart_" + productId).textContent = "1";
            button.outerHTML = `<a href="/cart" class="btn btn--primary product-page__cart-btn product-page__cart-btn--in-cart">В корзине <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg></a>`;
        }
    }

    (function() {
        var stars = document.querySelectorAll('.product-reviews__star');
        var ratingValue = document.getElementById('ratingValue');
        var currentValue = parseInt(ratingValue.value) || 0;

        function updateStars(value) {
            stars.forEach(function(star, index) {
                var starValue = parseInt(star.getAttribute('data-value'));
                var path = star.querySelector('svg path');
                if (starValue <= value) {
                    path.setAttribute('fill', '#D96A4A');
                } else {
                    path.setAttribute('fill', 'none');
                }
            });
        }

        updateStars(currentValue);

        stars.forEach(function(star) {
            star.addEventListener('click', function() {
                var value = parseInt(this.getAttribute('data-value'));
                ratingValue.value = value;
                updateStars(value);
            });
        });
    })();
</script>