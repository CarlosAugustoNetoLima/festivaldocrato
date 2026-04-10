<?php
use App\Config\Config;

$activePage = $activePage ?? 'home';
$siteName   = Config::get('site_name', 'Festival Crato');
$cartEnabled = Config::get('components.cart', true);

$navItems = [
    ['id' => 'tickets',    'label' => 'BILHETEIRA',  'url' => '/bilheteira'],
    ['id' => 'lineup',     'label' => 'LINE UP',     'url' => '/lineup'],
    ['id' => 'about',      'label' => 'O FESTIVAL',  'url' => '#', 'submenu' => [
        ['id' => 'news',       'label' => 'NOVIDADES',            'url' => '/noticias'],
        ['id' => 'directions', 'label' => 'COMO CHEGAR',          'url' => '/como-chegar'],
        ['id' => 'todo',       'label' => 'O QUE FAZER',          'url' => '/o-que-fazer'],
        ['id' => 'history',    'label' => 'HISTÓRIA',             'url' => '/sobre'],
        ['id' => 'sustainability','label'=> 'SUSTENTABILIDADE',   'url' => '/sobre#sustentabilidade'],
        ['id' => 'accessibility','label' => 'ACESSIBILIDADE',     'url' => '/sobre#acessibilidade'],
        ['id' => 'municipality','label' => 'O MUNICÍPIO DA VILA', 'url' => 'https://cm-crato.pt']
    ]],
    ['id' => 'camping',    'label' => 'CAMPISMO',    'url' => '/campismo'],
    ['id' => 'info',       'label' => 'INFO',        'url' => '#', 'submenu' => [
        ['id' => 'contacts',   'label' => 'Contactos O Festival', 'url' => '/contactos'],
        ['id' => 'regulations','label' => 'Regulamento e Restrições', 'url' => '/contactos#regulamentos'],
        ['id' => 'press',      'label' => 'Espaço Press',         'url' => '/contactos#press'],
        ['id' => 'faq',        'label' => 'Perguntas Frequentes', 'url' => '/contactos#faq']
    ]],
    ['id' => 'store',      'label' => 'LOJA',        'url' => '/loja'],
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
            <!-- Search -->
            <button class="header-search-btn" id="search-toggle" aria-label="Pesquisar">
                <span class="material-symbols-outlined">search</span>
            </button>

            <?php if ($cartEnabled): ?>
                <button class="cart-toggle" data-cart-open aria-label="Abrir carrinho" id="cart-open-btn">
                    <span class="material-symbols-outlined">shopping_bag</span>
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
