<?php
$stmt = $connect->prepare("SELECT r.comment, r.assessment, u.name AS user_name, u.img AS user_img, p.name AS product_name, p.id AS product_id FROM reviews r JOIN users u ON r.user_id = u.id JOIN products p ON r.product_id = p.id WHERE r.assessment = 5 ORDER BY r.created_at DESC");
$stmt->execute();
$reviews = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<section class="reviews-page">
    <div class="container">
        <h1 class="reviews-page__title">Отзывы</h1>

        <?php if (empty($reviews)): ?>
            <p class="reviews-page__empty">Пока нет отзывов с оценкой 5.</p>
        <?php else: ?>
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
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>