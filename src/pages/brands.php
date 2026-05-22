<?php
$stmt = $connect->prepare("SELECT id, name, img FROM brands");
$stmt->execute();
$brands = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<section class="brands-page">
    <div class="container">
        <h1 class="brands-page__title">Бренды</h1>

        <?php if (empty($brands)): ?>
            <p class="brands-page__empty">Пока ничего нет.</p>
        <?php else: ?>
            <div class="brands-grid">
                <?php foreach ($brands as $brand): ?>
                    <a href="/catalog?brand=<?= $brand['id'] ?>" class="brand-card">
                        <div class="brand-card__image">
                            <img src="<?= htmlspecialchars($brand['img']) ?>" alt="<?= htmlspecialchars($brand['name']) ?>">
                        </div>
                        <span class="brand-card__name"><?= htmlspecialchars($brand['name']) ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
