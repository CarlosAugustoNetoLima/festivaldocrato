<?php
$activePage = $activePage ?? 'home';
$siteName   = $siteName ?? 'Festival Crato';

$navItems = [
    // Ocultos após a edição de 2026 — repor quando abrir a venda para 2027:
    // ['id' => 'tickets',  'label' => 'BILHETEIRA',  'url' => '/bilheteira'],
    // ['id' => 'lineup',   'label' => 'LINE UP',     'url' => '/lineup'],
    ['id' => 'festival', 'label' => 'O FESTIVAL',  'url' => '#', 'submenu' => [
        ['id' => 'about',    'label' => 'Sobre o Festival', 'url' => '/sobre'],
        ['id' => 'news',     'label' => 'Novidades',        'url' => '/noticias'],
        // Oculto: os turnos e a validação ainda são de 2026. Repor com a
        // logística da próxima edição.
        // ['id' => 'eco-move', 'label' => 'Eco Move Crato',   'url' => '/eco-move-crato'],
    ]],
    ['id' => 'camping',  'label' => 'CAMPISMO',    'url' => '/campismo'],
    ['id' => 'partners', 'label' => 'PARCEIROS',   'url' => '/parceiros'],
    ['id' => 'info',     'label' => 'INFO',        'url' => '#', 'submenu' => [
        // Oculto: o guia de 2026 saiu e ainda não há o da próxima edição.
        // ['id' => 'guide',    'label' => 'Guia do Festival', 'url' => '/guia-do-festival'],
        ['id' => 'stay',     'label' => 'Onde Ficar',    'url' => 'https://cm-crato.pt/visitar/onde-ficar/', 'external' => true],
        ['id' => 'eat',      'label' => 'Onde Comer',    'url' => 'https://cm-crato.pt/visitar/onde-comer/', 'external' => true],
        ['id' => 'contacts', 'label' => 'Contactos',     'url' => '/contactos'],
    ]],
    // ['id' => 'store',    'label' => 'LOJA',       'url' => '/loja'],
];
?>

<header class="site-header" id="site-header">
    <div class="header-container">
        <!-- Logo -->
        <a href="/" class="header-logo" aria-label="Festival do Crato">
            <img src="/assets/img/logo-sem-ano.png" alt="" class="header-logo-img">
        </a>

        <!-- Nav Desktop -->
        <nav class="header-nav" aria-label="Navegação principal">
            <?php foreach ($navItems as $item): ?>
                <?php if (isset($item['submenu'])): ?>
                    <?php $submenuActive = $activePage === $item['id'] || in_array($activePage, array_column($item['submenu'], 'id'), true); ?>
                    <div class="nav-dropdown">
                        <button type="button"
                            class="nav-link dropdown-toggle <?= $submenuActive ? 'active' : '' ?>"
                            aria-haspopup="true" aria-expanded="false">
                            <?= htmlspecialchars($item['label']) ?>
                            <span class="material-symbols-outlined chevron" aria-hidden="true">expand_more</span>
                        </button>
                        <div class="dropdown-menu">
                            <?php foreach ($item['submenu'] as $sub): ?>
                                <a href="<?= htmlspecialchars($sub['url']) ?>"
                                    class="dropdown-item <?= $activePage === $sub['id'] ? 'active' : '' ?>"
                                    <?php if (!empty($sub['external'])): ?>target="_blank" rel="noopener noreferrer"<?php endif; ?>>
                                    <?= htmlspecialchars($sub['label']) ?>
                                    <?php if (!empty($sub['external'])): ?><span class="material-symbols-outlined external-icon" aria-hidden="true" style="font-size:14px;vertical-align:middle;margin-left:4px;opacity:.6;">open_in_new</span><span class="sr-only"> (abre em nova janela)</span><?php endif; ?>
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
            <?php /* Carrinho oculto — bilheteira fechada após a edição de 2026.
                     Repor quando abrir a venda para 2027.
            <button
                class="cart-toggle"
                id="cart-toggle-btn"
                aria-label="Abrir carrinho"
                onclick="CheckoutModal.open()"
            >
                <span class="material-symbols-outlined">shopping_bag</span>
                <span class="cart-badge" id="cart-badge" aria-hidden="true" style="display:none"></span>
            </button>
            */ ?>

            <button class="menu-toggle" id="menu-toggle" data-mobile-menu-toggle aria-label="Menu" aria-expanded="false" aria-controls="mobile-menu">
                <span aria-hidden="true"></span><span aria-hidden="true"></span><span aria-hidden="true"></span>
            </button>
        </div>
    </div>

    <!-- Search Overlay -->
    <div class="header-search-overlay" id="search-overlay" role="dialog" aria-modal="true" aria-label="Pesquisar">
        <div class="header-search-container">
            <button class="header-search-close" id="search-close" aria-label="Fechar pesquisa">
                <span class="material-symbols-outlined" aria-hidden="true">close</span>
            </button>
            <form action="/pesquisa" method="GET" class="header-search-form">
                <label for="search-input" class="sr-only">Pesquisar no site</label>
                <input type="search" id="search-input" name="q" placeholder="Pesquisar..." class="header-search-input" autocomplete="off">
                <button type="submit" class="header-search-submit" aria-label="Submeter pesquisa">
                    <span class="material-symbols-outlined" aria-hidden="true">search</span>
                </button>
            </form>
        </div>
    </div>
</header>

<!-- Mobile Menu -->
<div class="mobile-menu" id="mobile-menu" role="dialog" aria-modal="true" aria-label="Menu de navegação">
    <nav class="mobile-nav">
        <?php foreach ($navItems as $item): ?>
            <?php if (isset($item['submenu'])): ?>
                <?php $submenuActive = $activePage === $item['id'] || in_array($activePage, array_column($item['submenu'], 'id'), true); ?>
                <div class="mobile-nav-group<?= $submenuActive ? ' active' : '' ?>">
                    <button class="mobile-nav-toggle<?= $submenuActive ? ' active' : '' ?>" data-mobile-dropdown aria-expanded="false" aria-haspopup="true">
                        <?= htmlspecialchars($item['label']) ?>
                        <span class="material-symbols-outlined toggle-icon" aria-hidden="true">expand_more</span>
                    </button>
                    <div class="mobile-nav-sub-wrapper">
                        <div class="mobile-nav-sub">
                            <?php foreach ($item['submenu'] as $sub): ?>
                                <a href="<?= htmlspecialchars($sub['url']) ?>"
                                    class="mobile-nav-link sub <?= $activePage === $sub['id'] ? 'active' : '' ?>"
                                    <?php if (!empty($sub['external'])): ?>target="_blank" rel="noopener noreferrer"<?php endif; ?>>
                                    <?= htmlspecialchars($sub['label']) ?>
                                    <?php if (!empty($sub['external'])): ?><span class="material-symbols-outlined" aria-hidden="true" style="font-size:14px;vertical-align:middle;margin-left:4px;opacity:.6;">open_in_new</span><span class="sr-only"> (abre em nova janela)</span><?php endif; ?>
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
