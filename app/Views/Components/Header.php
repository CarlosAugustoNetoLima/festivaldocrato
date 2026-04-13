<?php
use App\Config\Config;

$activePage = $activePage ?? 'home';
$siteName = Config::get('site_name', 'Site');
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


})();
</script>
