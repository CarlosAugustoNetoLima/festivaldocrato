<?php
use App\Config\Config;

$products    = $products ?? Config::get('products', []);
$checkoutUrl = $checkoutUrl ?? Config::get('api.checkout_url', 'https://checkout.lebillet.eu/');

// Build category list
$categories = ['Todos'];
foreach ($products as $p) {
    if (!empty($p['category']) && !in_array($p['category'], $categories)) {
        $categories[] = $p['category'];
    }
}
?>

<section class="store-collections section">
    <div class="container">
        <div class="store-collections__header reveal">
            <p class="section-label">Loja Oficial</p>
            <h1 class="section-title">Merch <span>Festival do Crato</span></h1>
        </div>

        <!-- Tabs de categoria -->
        <div class="store-collections__tabs" id="store-tabs">
            <?php foreach ($categories as $cat): ?>
                <button
                    class="store-tab <?= $cat === 'Todos' ? 'active' : '' ?>"
                    data-category="<?= htmlspecialchars($cat) ?>"
                >
                    <?= htmlspecialchars($cat) ?>
                </button>
            <?php endforeach; ?>
        </div>

        <!-- Grid de produtos -->
        <div class="store-collections__grid" id="store-grid">
            <?php foreach ($products as $i => $product): ?>
                <a
                    href="/produto?id=<?= urlencode($product['id']) ?>"
                    class="store-card reveal"
                    data-category="<?= htmlspecialchars($product['category'] ?? '') ?>"
                    style="transition-delay:<?= $i * 0.06 ?>s;"
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
    </div>
</section>
