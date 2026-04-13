<?php
use App\Config\Config;

$products    = Config::get('products', []);
$checkoutUrl = Config::get('api.checkout_url', 'https://checkout.lebillet.eu/');

// Busca produto pelo ID via query string
$productId = $_GET['id'] ?? '';
$product   = null;

foreach ($products as $p) {
    if ($p['id'] === $productId) {
        $product = $p;
        break;
    }
}

// Fallback se não encontrado
if (!$product) {
    $product = [
        'id'          => '',
        'name'        => 'Produto não encontrado',
        'category'    => '',
        'price'       => 0,
        'description' => '',
        'image'       => '/themes/crato/img/logo.png',
        'event_id'    => '',
    ];
}

$priceFmt = number_format($product['price'], 2, ',', '.');
$eventId  = $product['event_id'] ?? '';
?>

<section class="product-detail section">
    <div class="container">

        <a href="/loja" class="product-detail__back">
            ← Voltar à loja
        </a>

        <div class="product-detail__grid">

            <!-- Imagem -->
            <div class="product-detail__gallery">
                <div class="product-detail__main-image">
                    <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                </div>
            </div>

            <!-- Informações -->
            <div class="product-detail__info">
                <?php if (!empty($product['category'])): ?>
                    <p class="product-detail__category"><?= htmlspecialchars($product['category']) ?></p>
                <?php endif; ?>

                <h1 class="product-detail__name"><?= htmlspecialchars($product['name']) ?></h1>

                <p class="product-detail__price"><?= $priceFmt ?> €</p>

                <?php if (!empty($product['description'])): ?>
                    <p class="product-detail__desc"><?= htmlspecialchars($product['description']) ?></p>
                <?php endif; ?>

                <?php if (!empty($eventId)): ?>
                    <button
                        class="btn btn-primary product-detail__buy"
                        onclick="CheckoutModal.open('<?= htmlspecialchars(addslashes($product['name'])) ?>', '<?= htmlspecialchars($eventId) ?>', <?= (float)$product['price'] ?>)"
                    >
                        Comprar Agora
                    </button>
                <?php else: ?>
                    <p class="product-detail__unavailable">Brevemente disponível</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
