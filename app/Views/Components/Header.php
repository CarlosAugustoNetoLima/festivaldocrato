<?php
$activePage = $activePage ?? 'home';
$siteName   = $siteName ?? 'Festival Crato';

$navItems = [
    ['id' => 'tickets',    'label' => 'BILHETEIRA',  'url' => '/bilheteira'],
    ['id' => 'lineup',     'label' => 'LINE UP',     'url' => '/lineup'],
    ['id' => 'about',      'label' => 'O FESTIVAL',  'url' => '#', 'submenu' => [
        ['id' => 'news',          'label' => 'Novidades',              'url' => '/noticias'],
        ['id' => 'directions',    'label' => 'Como Chegar',            'url' => '/como-chegar'],
        ['id' => 'accommodation', 'label' => 'Onde Ficar',             'url' => '/onde-ficar'],
        ['id' => 'map',           'label' => 'Ver Mapa do Festival',   'url' => '/mapa'],
        ['id' => 'transport',     'label' => 'Transportes',            'url' => '/transportes'],
        ['id' => 'sponsors',      'label' => 'Patrocinadores',         'url' => '/patrocinadores'],
        ['id' => 'accessibility', 'label' => 'Acessibilidade',         'url' => '/acessibilidade'],
    ]],
    ['id' => 'camping',    'label' => 'CAMPISMO',    'url' => '/campismo'],
    ['id' => 'info',       'label' => 'INFO',        'url' => '#', 'submenu' => [
        ['id' => 'press',    'label' => 'Imprensa',          'url' => '/imprensa'],
        ['id' => 'faq',      'label' => 'FAQs',              'url' => '/faqs'],
        ['id' => 'about',    'label' => 'Sobre o Festival',  'url' => '/sobre'],
        ['id' => 'contacts', 'label' => 'Contactos',         'url' => '/contactos'],
    ]],
    ['id' => 'store',      'label' => 'LOJA',        'url' => '/loja'],
];
?>

<header class="site-header" id="site-header">
    <div class="header-container">
        <!-- Logo -->
        <a href="/" class="header-logo" aria-label="Festival do Crato 2026">
            <img src="/assets/img/logo.png" alt="Festival do Crato 2026" class="header-logo-img">
        </a>

        <!-- Nav Desktop -->
        <nav class="header-nav" aria-label="Navegação principal">
            <?php foreach ($navItems as $item): ?>
                <?php if (isset($item['submenu'])): ?>
                    <div class="nav-dropdown">
                        <a href="<?= htmlspecialchars($item['url']) ?>"
                            class="nav-link dropdown-toggle <?= $activePage === $item['id'] ? 'active' : '' ?>">
                            <?= htmlspecialchars($item['label']) ?>
                            <span class="material-symbols-outlined chevron">expand_more</span>
                        </a>
                        <div class="dropdown-menu">
                            <?php foreach ($item['submenu'] as $sub): ?>
                                <a href="<?= htmlspecialchars($sub['url']) ?>"
                                    class="dropdown-item <?= $activePage === $sub['id'] ? 'active' : '' ?>">
                                    <?= htmlspecialchars($sub['label']) ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="<?= htmlspecialchars($item['url']) ?>"
                        class="nav-link <?= $activePage === $item['id'] ? 'active' : '' ?>">
                        <?= htmlspecialchars($item['label']) ?>
                    </a>
                <?php endif; ?>
            <?php endforeach; ?>
        </nav>

        <!-- Actions -->
        <div class="header-actions">
            <button class="header-search-btn" id="search-toggle" aria-label="Pesquisar">
                <span class="material-symbols-outlined">search</span>
            </button>

            <button
                class="cart-toggle"
                aria-label="Abrir carrinho"
                onclick="CheckoutModal.open()"
            >
                <span class="material-symbols-outlined">shopping_bag</span>
            </button>

            <button class="menu-toggle" id="menu-toggle" data-mobile-menu-toggle aria-label="Menu" aria-expanded="false">
                <span></span><span></span><span></span>
            </button>
        </div>
    </div>

    <!-- Search Overlay -->
    <div class="header-search-overlay" id="search-overlay" role="dialog" aria-label="Pesquisar">
        <div class="header-search-container">
            <button class="header-search-close" id="search-close" aria-label="Fechar pesquisa">
                <span class="material-symbols-outlined">close</span>
            </button>
            <form action="/pesquisa" method="GET" class="header-search-form">
                <input type="search" name="q" placeholder="Pesquisar..." class="header-search-input" autocomplete="off">
                <button type="submit" class="header-search-submit">
                    <span class="material-symbols-outlined">search</span>
                </button>
            </form>
        </div>
    </div>
</header>

<!-- Mobile Menu -->
<div class="mobile-menu" id="mobile-menu" role="dialog" aria-label="Menu de navegação">
    <nav class="mobile-nav">
        <?php foreach ($navItems as $item): ?>
            <?php if (isset($item['submenu'])): ?>
                <div class="mobile-nav-group">
                    <button class="mobile-nav-toggle" data-mobile-dropdown aria-expanded="false">
                        <?= htmlspecialchars($item['label']) ?>
                        <span class="material-symbols-outlined toggle-icon">expand_more</span>
                    </button>
                    <div class="mobile-nav-sub-wrapper">
                        <div class="mobile-nav-sub">
                            <?php foreach ($item['submenu'] as $sub): ?>
                                <a href="<?= htmlspecialchars($sub['url']) ?>"
                                    class="mobile-nav-link sub <?= $activePage === $sub['id'] ? 'active' : '' ?>">
                                    <?= htmlspecialchars($sub['label']) ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <a href="<?= htmlspecialchars($item['url']) ?>"
                    class="mobile-nav-link <?= $activePage === $item['id'] ? 'active' : '' ?>">
                    <?= htmlspecialchars($item['label']) ?>
                </a>
            <?php endif; ?>
        <?php endforeach; ?>
    </nav>
</div>
