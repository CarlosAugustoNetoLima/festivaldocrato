<?php
use App\Config\Config;

$activePage = $activePage ?? 'home';
$siteName = Config::get('site_name', 'Site');
$cartEnabled = Config::get('components.cart', true);

$navItems = $navItems ?? [
    ['id' => 'home', 'label' => 'Home', 'url' => '/'],
    ['id' => 'dates', 'label' => 'Dates', 'url' => '/dates'],
];
?>

<header class="site-header">
    <div class="header-container">
        <!-- Logo -->
        <a href="/" class="header-logo">
            <?= htmlspecialchars($siteName) ?>
        </a>

        <!-- Navigation Desktop -->
        <nav class="header-nav">
            <?php foreach ($navItems as $item): ?>
                <a
                    href="<?= htmlspecialchars($item['url']) ?>"
                    class="nav-link <?= $activePage === $item['id'] ? 'active' : '' ?>"
                >
                    <?= htmlspecialchars($item['label']) ?>
                </a>
            <?php endforeach; ?>
        </nav>

        <!-- Actions -->
        <div class="header-actions">
            <?php if ($cartEnabled): ?>
                <button class="cart-toggle" data-cart-open aria-label="Open Cart">
                    <span class="cart-icon">🛒</span>
                    <span class="cart-badge hidden" id="cart-badge">0</span>
                </button>
            <?php endif; ?>

            <button class="menu-toggle" data-mobile-menu-toggle aria-label="Toggle Menu">
                <span>☰</span>
            </button>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div class="mobile-menu" id="mobile-menu">
        <nav class="mobile-nav">
            <?php foreach ($navItems as $item): ?>
                <a
                    href="<?= htmlspecialchars($item['url']) ?>"
                    class="mobile-nav-link <?= $activePage === $item['id'] ? 'active' : '' ?>"
                >
                    <?= htmlspecialchars($item['label']) ?>
                </a>
            <?php endforeach; ?>
        </nav>
    </div>
</header>

<script>
(function() {
    // Mobile menu toggle
    const menuToggle = document.querySelector('[data-mobile-menu-toggle]');
    const mobileMenu = document.getElementById('mobile-menu');

    if (menuToggle && mobileMenu) {
        menuToggle.addEventListener('click', () => {
            mobileMenu.classList.toggle('active');
            menuToggle.classList.toggle('active');
        });
    }

    // Update cart badge on load
    const storageKey = document.querySelector('[data-storage-key]')?.dataset.storageKey || 'cart_items';
    const cartBadge = document.getElementById('cart-badge');

    if (cartBadge) {
        try {
            const cart = JSON.parse(localStorage.getItem(storageKey) || '[]');
            const qty = cart.reduce((sum, item) => sum + (item.qty || 0), 0);
            cartBadge.textContent = qty;
            cartBadge.classList.toggle('hidden', qty === 0);
        } catch (e) {
            cartBadge.classList.add('hidden');
        }
    }
})();
</script>
