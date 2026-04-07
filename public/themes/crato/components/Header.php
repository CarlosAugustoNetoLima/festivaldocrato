<?php
use App\Config\Config;

$activePage = $activePage ?? 'home';
$siteName   = Config::get('site_name', 'Festival Crato');
$cartEnabled = Config::get('components.cart', true);

$navItems = [
    ['id' => 'tickets',    'label' => 'Bilheteira',      'url' => '/bilheteira'],
    ['id' => 'about',      'label' => 'Sobre o Festival', 'url' => '/sobre'],
    ['id' => 'directions', 'label' => 'Como chegar',     'url' => '/como-chegar'],
    ['id' => 'camping',    'label' => 'Campismo',        'url' => '/campismo'],
    ['id' => 'todo',       'label' => 'O que fazer',     'url' => '/o-que-fazer'],
    ['id' => 'contacts',   'label' => 'Contactos úteis', 'url' => '/contactos'],
    ['id' => 'news',       'label' => 'Notícias',        'url' => '/noticias'],
];
?>

<header class="site-header" id="site-header">
    <div class="header-container">
        <!-- Logo -->
        <a href="/" class="header-logo" aria-label="Festival do Crato 2026">
            <img src="/themes/crato/img/logo.png" alt="Festival do Crato 2026" class="header-logo-img">
        </a>

        <!-- Nav Desktop -->
        <nav class="header-nav" aria-label="Navegação principal">
            <?php foreach ($navItems as $item): ?>
                <a
                    href="<?= htmlspecialchars($item['url']) ?>"
                    class="nav-link <?= $activePage === $item['id'] ? 'active' : '' ?>"
                    id="nav-<?= htmlspecialchars($item['id']) ?>"
                >
                    <?= htmlspecialchars($item['label']) ?>
                </a>
            <?php endforeach; ?>
        </nav>

        <!-- Actions -->
        <div class="header-actions">
            <!-- Search -->
            <button class="header-search-btn" id="search-toggle" aria-label="Pesquisar">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/>
                    <path d="m21 21-4.35-4.35"/>
                </svg>
            </button>

            <?php if ($cartEnabled): ?>
                <button class="cart-toggle" data-cart-open aria-label="Abrir carrinho" id="cart-open-btn">
                    <svg class="cart-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/>
                        <line x1="3" y1="6" x2="21" y2="6"/>
                        <path d="M16 10a4 4 0 01-8 0"/>
                    </svg>
                    <span class="cart-badge hidden" id="cart-badge">0</span>
                </button>
            <?php endif; ?>

            <button class="menu-toggle" id="menu-toggle" data-mobile-menu-toggle aria-label="Menu" aria-expanded="false">
                <span></span><span></span><span></span>
            </button>
        </div>
    </div>

    <!-- Search Overlay -->
    <div class="header-search-overlay" id="search-overlay" role="dialog" aria-label="Pesquisar">
        <div class="header-search-container">
            <button class="header-search-close" id="search-close" aria-label="Fechar pesquisa">&times;</button>
            <form action="/pesquisa" method="GET" class="header-search-form">
                <input type="search" name="q" placeholder="Pesquisar..." class="header-search-input" autocomplete="off">
                <button type="submit" class="header-search-submit">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"/>
                        <path d="m21 21-4.35-4.35"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>
</header>

<!-- Mobile Menu -->
<div class="mobile-menu" id="mobile-menu" role="dialog" aria-label="Menu de navegação">
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
