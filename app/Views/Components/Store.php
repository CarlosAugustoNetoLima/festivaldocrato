<?php
$products = $products ?? [];
$featured = array_slice($products, 0, 4);
?>

<section class="store section" id="loja">
    <div class="container">
        <div class="store__header reveal">
            <p class="section-label">Loja Oficial</p>
            <h2 class="section-title">Merch <span>Festival do Crato</span></h2>
            <p class="store__desc">Leva um bocado do festival contigo. Merchandising oficial do Festival do Crato 2026.</p>
        </div>

        <div class="store__grid">
            <?php foreach ($featured as $i => $product): ?>
                <a
                    href="/produto?id=<?= urlencode($product['id']) ?>"
                    class="store-card reveal"
                    style="transition-delay:<?= $i * 0.08 ?>s;"
                >
                    <div class="store-card__image">
                        <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                    </div>
                    <div class="store-card__content">
                        <p class="store-card__name"><?= htmlspecialchars($product['name']) ?></p>
                        <p class="store-card__price"><?= number_format($product['price'], 2, ',', '.') ?> €</p>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>

        <div class="store__cta reveal">
            <a href="/loja" class="btn btn-ghost">Ver toda a loja →</a>
        </div>
    </div>
</section>
