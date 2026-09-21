<?php
// Entry point — Festival Crato
use App\Helpers\Component;
use App\Services\LeBilletService;

// Autoloader
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/../app/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0)
        return;
    $file = $baseDir . str_replace('\\', '/', substr($class, $len)) . '.php';
    if (file_exists($file))
        require $file;
});

// ─────────────────────────────────────────────
//  Configuração
// ─────────────────────────────────────────────
$checkoutUrl = 'https://checkout.lebillet.eu/';
$siteName = 'Festival Crato';
$lebilletApiKey = '9f4c2a1b7e3d6a8cpewe8992801';
$festivalEventId = 1830;

// ─────────────────────────────────────────────
//  LeBillet Service — dados via API/checkout
// ─────────────────────────────────────────────
$lebillet = new LeBilletService();
$eventsLimit = $lebillet->getApiEvents($lebilletApiKey, 6);
$eventsAll = $lebillet->getApiEvents($lebilletApiKey);
$tickets = $lebillet->getCheckoutTickets($festivalEventId);

// ─────────────────────────────────────────────
//  Site data
// ─────────────────────────────────────────────
$festival = [
    'edition' => '41.ª',
    'date_start' => '2027-08-25',
    'date_end' => '2027-08-28',
    'date_festival_start' => '2027-08-25',
    'date_campista' => '2027-08-23',
    'location' => 'Crato, Alto Alentejo',
    'venue' => 'Vila do Crato',
    'organizer' => 'Festival do Crato',
    'description' => 'O Festival do Crato regressa de 25 a 28 de agosto de 2027.',
    'mission' => 'Promover e preservar o artesanato e a gastronomia enquanto valores culturais.',
    'contact' => [
        'email' => 'festivaldocrato@cm-crato.pt',
        'phone' => '245 990 110',
        'address' => 'Praça do Município',
        'zip' => '7430-999 Crato',
    ],
    'social' => [
        'instagram' => 'https://www.instagram.com/festivaldocrato',
        'facebook' => 'https://www.facebook.com/FestivaldoCrato',
        'youtube' => 'https://www.youtube.com/@CratoTV',
    ],
];

$artists = [
    ['name' => 'Slow J', 'day' => 1, 'stage' => 'Palco Festival', 'headliner' => true, 'genre' => 'Hip-Hop', 'image' => '/assets/img/WEBSITE_SLOWJ.png', 'confirmed' => true],
    ['name' => 'Dub Inc', 'day' => 1, 'stage' => 'Palco Festival', 'headliner' => true, 'genre' => 'Reggae', 'image' => '/assets/img/WEBSITE_DUBINC.png', 'confirmed' => true],
    ['name' => 'Bispo', 'day' => 1, 'stage' => 'Palco Festival', 'headliner' => true, 'genre' => 'Hip-Hop', 'image' => '/assets/img/artists/POST_bispo-_website.webp', 'confirmed' => true],
    ['name' => 'Zanova', 'day' => 1, 'stage' => 'Palco Festival', 'headliner' => true, 'genre' => 'DJ / Electronic', 'image' => '/assets/img/artists/Zanova_Website.webp', 'confirmed' => true],
    ['name' => 'Veigh', 'day' => 2, 'stage' => 'Palco Festival', 'headliner' => true, 'genre' => 'Trap', 'image' => '/assets/img/WEBSITE_VEIGH.png', 'confirmed' => true],
    ['name' => 'Papillon', 'day' => 2, 'stage' => 'Palco Festival', 'headliner' => true, 'genre' => 'Hip-Hop', 'image' => '/assets/img/WEBSITE_PAPILLON.png', 'confirmed' => true],
    ['name' => 'Soraia Ramos', 'day' => 2, 'stage' => 'Palco Festival', 'headliner' => true, 'genre' => 'Kizomba / Afropop', 'image' => '/assets/img/WEBSITE_SORAIA.png', 'confirmed' => true],
    ['name' => 'Calema', 'day' => 3, 'stage' => 'Palco Festival', 'headliner' => true, 'genre' => 'Afropop / Kizomba', 'image' => '/assets/img/artists/POST_calema_website.webp', 'confirmed' => true],
    ['name' => 'Sara Correia', 'day' => 3, 'stage' => 'Palco Festival', 'headliner' => true, 'genre' => 'Fado', 'image' => '/assets/img/WEBSITE_sara.png', 'confirmed' => true],
    ['name' => 'Delfins', 'day' => 3, 'stage' => 'Palco Festival', 'headliner' => true, 'genre' => 'Rock / Pop', 'image' => '/assets/img/artists/WEBSITE_Delfins.webp', 'confirmed' => true],
    ['name' => 'KURA', 'day' => 3, 'stage' => 'Palco Festival', 'headliner' => true, 'genre' => 'Electro House', 'image' => '/assets/img/artists/KURA_Website.webp', 'confirmed' => true],
    ['name' => 'Calum Scott', 'day' => 4, 'stage' => 'Palco Festival', 'headliner' => true, 'genre' => 'Pop / Soul', 'image' => '/assets/img/artists/WEBSITE_CalumScott.webp', 'confirmed' => true],
    ['name' => 'Karetus', 'day' => 4, 'stage' => 'Palco Festival', 'headliner' => true, 'genre' => 'Electronic', 'image' => '/assets/img/artists/WEBSITE_Karetus.webp', 'confirmed' => true],
    ['name' => 'Buba Espinho', 'day' => 4, 'stage' => 'Palco Festival', 'headliner' => true, 'genre' => 'Fado / Cante Alentejano', 'image' => '/assets/img/artists/POST_buba_website.webp', 'confirmed' => true],
];

$products = [
    [
        'id' => 'tshirt-crato-2026',
        'name' => 'T-Shirt Festival Crato 2026',
        'category' => 'Vestuário',
        'price' => null, // Preço vem do checkout LeBillet
        'description' => 'T-Shirt oficial do Festival do Crato 2026. 100% algodão orgânico.',
        'image' => '/assets/img/logo.png',
        'highlight' => true,
        'event_id' => 'crato-store-tshirt',
    ],
    [
        'id' => 'bone-crato-2026',
        'name' => 'Boné Festival Crato 2026',
        'category' => 'Acessórios',
        'price' => null, // Preço vem do checkout LeBillet
        'description' => 'Boné oficial do Festival do Crato 2026.',
        'image' => '/assets/img/logo.png',
        'highlight' => false,
        'event_id' => 'crato-store-bone',
    ],
    [
        'id' => 'eco-bag-crato-2026',
        'name' => 'Eco Bag Festival Crato 2026',
        'category' => 'Acessórios',
        'price' => null, // Preço vem do checkout LeBillet
        'description' => 'Saco reutilizável oficial do Festival do Crato 2026.',
        'image' => '/assets/img/logo.png',
        'highlight' => false,
        'event_id' => 'crato-store-ecobag',
    ],
    [
        'id' => 'hoodie-crato-2026',
        'name' => 'Hoodie Festival Crato 2026',
        'category' => 'Vestuário',
        'price' => null, // Preço vem do checkout LeBillet
        'description' => 'Hoodie oficial do Festival do Crato 2026. Edição limitada.',
        'image' => '/assets/img/logo.png',
        'highlight' => false,
        'event_id' => 'crato-store-hoodie',
    ],
];

$news = [
    [
        'date' => '2026-05-15',
        'tag' => 'Artistas',
        'title' => 'Veigh, Papillon e Soraia Ramos confirmados no Festival do Crato 2026',
        'excerpt' => 'Os artistas juntam-se aos já anunciados Buba Espinho & Convidados, Bispo, Calema, Slow J, Sara Correia e Dub Inc. O Festival está de regresso à vila alentejana de 26 a 29 de agosto!',
        'url' => '/noticias/veigh-papillon-soraia-ramos',
        'image' => '/assets/img/anuncio_veigh_papillon_soraia.png',
    ],
    [
        'date' => '2026-05-11',
        'tag' => 'Artistas',
        'title' => 'Slow J, Sara Correia e Dub Inc confirmados no Festival do Crato 2026',
        'excerpt' => 'Os novos artistas juntam-se a Buba Espinho & Convidados, Bispo e Calema. O Festival está de regresso à vila alentejana de 26 a 29 de agosto!',
        'url' => '/noticias/slow-j-sara-correia-dub-inc',
        'image' => '/assets/img/new artists.jpeg',
    ],
    [
        'date' => '2026-04-23',
        'tag' => 'Artistas',
        'title' => 'Bispo e Calema confirmados no Festival do Crato 2026',
        'excerpt' => 'Artistas juntam-se ao já anunciado Buba Espinho & Convidados. O Festival está de regresso à vila alentejana de 26 a 29 de agosto!',
        'url' => '/noticias/bispo-calema',
        'image' => '/assets/img/anuncio.jpeg',
    ],
];

$partnerGroups = [
    [
        'label' => 'Organização',
        'featured' => true,
        'partners' => [
            ['name' => 'Município do Crato', 'logo' => 'municipio-crato.webp', 'url' => 'https://cm-crato.pt'],
        ],
    ],
    [
        'label' => 'Patrocínios',
        'partners' => [
            ['name' => 'Super Bock', 'logo' => 'super-bock.webp', 'url' => 'https://www.superbock.pt'],
        ],
    ],
    [
        'label' => 'Televisão Oficial',
        'partners' => [
            ['name' => 'RTP', 'logo' => 'rtp.webp', 'url' => 'https://www.rtp.pt'],
        ],
    ],
    [
        'label' => 'Rádio Oficial',
        'partners' => [
            ['name' => 'Rádio Comercial', 'logo' => 'radio-comercial.webp', 'url' => 'https://radiocomercial.pt'],
        ],
    ],
    [
        'label' => 'Parceiros',
        'partners' => [
            ['name' => 'Delta Cafés', 'logo' => 'delta.webp', 'url' => 'https://deltacafes.com'],
            ['name' => 'Licor Beirão', 'logo' => 'licor-beirao.webp', 'url' => 'https://www.licorbeirao.com'],
            ['name' => 'Crédito Agrícola', 'logo' => 'credito-agricola.webp', 'url' => 'https://www.creditoagricola.pt'],
            ['name' => 'A Matos Car — Hyundai', 'logo' => 'a-matos-car.webp', 'url' => 'https://www.amatoscar.pt'],
            ['name' => 'CP — Comboios de Portugal', 'logo' => 'cp.webp', 'url' => 'https://www.cp.pt'],
            ['name' => 'Rede Expressos', 'logo' => 'rede-expressos.svg', 'url' => 'https://rede-expressos.pt'],
            ['name' => 'Rodoviária do Alentejo', 'logo' => 'rodoviaria-alentejo.webp', 'url' => 'https://www.rodalentejo.pt'],
            ['name' => 'BOL — Bilheteira Online', 'logo' => 'bol.webp', 'url' => 'https://bol.pt'],
            ['name' => 'LeBillet', 'logo' => 'lebillet.webp', 'url' => 'https://www.lebillet.eu'],
        ],
    ],
    [
        'label' => 'Produção',
        'partners' => [
            ['name' => 'Premium Stage', 'logo' => 'premium-stage.webp'],
        ],
    ],
    [
        'label' => 'Apoio Institucional',
        'partners' => [
            ['name' => 'IPDJ — Instituto Português do Desporto e Juventude', 'logo' => 'ipdj.webp', 'url' => 'https://ipdj.gov.pt'],
            ['name' => 'Turismo do Alentejo e Ribatejo', 'logo' => 'turismo-alentejo.webp', 'url' => 'https://www.visitalentejo.pt'],
            ['name' => 'Politécnico de Portalegre', 'logo' => 'politecnico-portalegre.svg', 'url' => 'https://www.ipportalegre.pt'],
        ],
    ],
];

// ─────────────────────────────────────────────
//  Guia do Festival — PDF descarregável
//  Basta colocar o ficheiro em public/assets/docs/ para a página passar a
//  mostrar o botão de download. O URL da página nunca muda (é a base do QR Code).
// ─────────────────────────────────────────────
$guidePdf = '/assets/docs/guia-festival-crato-2026.pdf';
$guideFile = __DIR__ . $guidePdf;
$guideReady = is_file($guideFile);
$guideSize = '';
if ($guideReady) {
    $bytes = filesize($guideFile);
    $guideSize = $bytes >= 1048576
        ? number_format($bytes / 1048576, 1, ',', ' ') . ' MB'
        : max(1, (int) round($bytes / 1024)) . ' KB';
}

// ─────────────────────────────────────────────
//  Routing
// ─────────────────────────────────────────────
$request = $_SERVER['REQUEST_URI'];
$path = rtrim(parse_url($request, PHP_URL_PATH), '/') ?: '/';

// Rotas da edição de 2026 que deixaram de fazer sentido depois do festival.
// 302 (temporário) e não 301: voltam a abrir na edição de 2027, e um 301 fica
// cacheado no browser de forma difícil de reverter.
// NOTA: o build estático para GitHub Pages não executa PHP — estas rotas estão
// também fora do $routes do build.php, por isso não geram página lá.
$redirects = ['/bilheteira', '/bilhetes', '/lineup', '/artistas'];
if (in_array($path, $redirects, true) && PHP_SAPI !== 'cli') {
    header('Location: /', true, 302);
    exit;
}

$routes = [
    '/' => 'home',
    '/bilheteira' => 'tickets',
    '/bilhetes' => 'tickets',
    '/lineup' => 'lineup',
    '/sobre' => 'about',
    '/eco-move-crato' => 'eco-move',
    '/parceiros' => 'partners',
    '/guia-do-festival' => 'guide',
    '/como-chegar' => 'directions',
    '/campismo' => 'camping',
    '/o-que-fazer' => 'todo',
    '/contactos' => 'contacts',
    '/noticias' => 'news',
    '/noticias/veigh-papillon-soraia-ramos' => 'news_veigh_papillon_soraia',
    '/noticias/slow-j-sara-correia-dub-inc' => 'news_slow_j',
    '/noticias/bispo-calema' => 'news_bispo_calema',
    '/artistas' => 'artists',
    '/info' => 'info',
    '/loja' => 'store',
    '/produto' => 'product',
    '/pesquisa' => 'search',
    '/politica-privacidade' => 'legal_privacy',
    '/cookies' => 'legal_cookies',
    '/termos' => 'legal_terms',
];

$activePage = $routes[$path] ?? '404';

$pageTitles = [
    'home' => 'Festival Crato 2027',
    'tickets' => 'Bilheteira',
    'lineup' => 'Programação',
    'about' => 'Sobre o Festival',
    'eco-move' => 'Eco Move Crato',
    'partners' => 'Parceiros',
    'guide' => 'Guia do Festival',
    'directions' => 'Como Chegar',
    'camping' => 'Campismo',
    'todo' => 'O que Fazer',
    'contacts' => 'Contactos',
    'news' => 'Notícias',
    'artists' => 'Artistas',
    'info' => 'Informações',
    'store' => 'Loja',
    'product' => 'Produto',
    'legal_privacy' => 'Política de Privacidade',
    'legal_cookies' => 'Política de Cookies',
    'legal_terms' => 'Termos e Condições',
];

$pageTitle = $pageTitles[$activePage] ?? 'Festival Crato';
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($siteName) ?> — <?= htmlspecialchars($pageTitle) ?></title>
    <meta name="description"
        content="Festival do Crato — 25 a 28 de Agosto de 2027, na Vila do Crato, Alto Alentejo">
    <meta property="og:title" content="Festival do Crato 2027">
    <meta property="og:description"
        content="Festival do Crato — 25 a 28 de Agosto de 2027">
    <meta property="og:type" content="website">
    <link rel="icon" href="/assets/img/favicon.ico" sizes="any">
    <link rel="icon" href="/assets/img/favicon-32x32.png" type="image/png" sizes="32x32">
    <link rel="apple-touch-icon" href="/assets/img/apple-touch-icon.png">

    <!-- Preconnect hints -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdn.jsdelivr.net">

    <!-- Google Fonts — single request -->
    <link
        href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@300;400;500;600;700;800&family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&display=swap"
        rel="stylesheet">

    <!-- CSS -->
    <link rel="stylesheet" href="/assets/css/base.css">
    <link rel="stylesheet" href="/assets/css/components.css">
    <link rel="stylesheet" href="/assets/css/theme.css">

    <!-- Toastify -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
</head>

<body data-page="<?= htmlspecialchars($activePage) ?>">
    <a href="#main-content" class="sr-only sr-only--focusable">Saltar para o conteúdo principal</a>

    <?= Component::render('Header', ['activePage' => $activePage, 'siteName' => $siteName]) ?>

    <main class="main-content" id="main-content">

        <?php if ($activePage === 'home'): ?>
            <?= Component::render('Hero', ['festival' => $festival]) ?>
            <?php // Secções ocultas após a edição de 2026 — repor com o cartaz e a venda de 2027:
                  // Component::render('Lineup', ['artists' => $artists]) ?>
            <?= Component::render('News', ['news' => $news]) ?>
            <?php // Component::render('Tickets', ['tickets' => $tickets, 'events' => $eventsLimit, 'checkoutUrl' => $checkoutUrl]) ?>
            <?php // Component::render('Store', ['products' => $products]) ?>
            <?= Component::render('About', ['festival' => $festival]) ?>

        <?php elseif ($activePage === 'store'): ?>
            <?= Component::render('Collections', ['products' => $products, 'checkoutUrl' => $checkoutUrl]) ?>

        <?php elseif ($activePage === 'product'): ?>
            <?= Component::render('ProductDetail', ['products' => $products, 'checkoutUrl' => $checkoutUrl]) ?>

        <?php elseif ($activePage === 'tickets'): ?>

            <?= Component::render('Tickets', ['tickets' => $tickets, 'events' => $eventsAll, 'checkoutUrl' => $checkoutUrl, 'showAll' => true]) ?>

        <?php elseif ($activePage === 'lineup'): ?>
            <?= Component::render('Lineup', ['artists' => $artists, 'showAll' => true]) ?>

        <?php elseif ($activePage === 'artists'): ?>

            <?= Component::render('Artists', ['artists' => $artists, 'showAll' => true]) ?>

        <?php elseif ($activePage === 'news'): ?>

            <?= Component::render('News', ['news' => $news]) ?>

        <?php elseif ($activePage === 'news_veigh_papillon_soraia'): ?>
            <article class="news-article">
                <div class="container">
                    <div class="news-article__hero">
                        <img src="/assets/img/anuncio_veigh_papillon_soraia.png" alt="Veigh, Papillon e Soraia Ramos confirmados no Festival do Crato 2026"
                            class="news-article__hero-img">
                    </div>
                    <div class="news-article__content">
                        <div class="news-article__meta">
                            <span class="news-card__tag">Artistas</span>
                            <time datetime="2026-05-15">15 Mai 2026</time>
                        </div>
                        <h1 class="news-article__title">Veigh, Papillon e Soraia Ramos confirmados no Festival do Crato 2026</h1>
                        <p class="news-article__lead">Artistas juntam-se aos já anunciados Buba Espinho &amp; Convidados, Bispo, Calema, Slow J, Sara Correia e Dub Inc</p>

                        <p>O <strong>Festival do Crato</strong> anuncia mais três nomes para a edição de 2026 com a confirmação de <strong>Veigh</strong>, <strong>Papillon</strong> e <strong>Soraia Ramos</strong>. Os artistas juntam-se a Buba Espinho &amp; Convidados, Bispo, Calema, Slow J, Sara Correia e Dub Inc. O Festival está de regresso à vila alentejana de <strong>26 a 29 de agosto</strong> de 2026.</p>

                        <p><strong>Veigh</strong> é um dos principais nomes do trap brasileiro, acumulando centenas de milhões de streams nas plataformas digitais. Temas como "Novo Balanço", "Vida Chique" ou "Movimento" contribuíram para a rápida afirmação do artista dentro e fora do Brasil. O álbum "Dos Prédios Deluxe" consolidou o seu crescimento internacional e presença nos tops digitais.</p>

                        <p><strong>Papillon</strong> tem vindo a afirmar-se como um dos nomes mais respeitados da música urbana portuguesa. Depois do percurso nos GROGNation, lançou-se a solo com projetos como "Deepak Looper" e temas como "Impec", "C'est La Vie" ou "00:00", mantendo uma presença consistente no circuito ao vivo e nos principais festivais nacionais.</p>

                        <p><strong>Soraia Ramos</strong> soma milhões de visualizações e ouvintes nas plataformas digitais, afirmando-se como uma das artistas lusófonas com maior crescimento nos últimos anos. Temas como "Bai", "BKBN" ou "O Nosso Amor" destacam-se no seu percurso, marcado por influências afro-pop, kizomba e R&amp;B. A artista tem atuado regularmente em vários países europeus e africanos, consolidando uma forte ligação junto do público lusófono.</p>

                        <h2>Sobre o Festival do Crato</h2>
                        <p>O Festival do Crato, situado no Alto Alentejo, é um dos festivais de verão mais relevantes em Portugal, combinando música, território e tradição. Para além do cartaz musical, o evento integra uma feira de artesanato e gastronomia que valoriza produtores e tradições locais, criando uma experiência que vai além dos concertos.</p>

                        <p>O recinto conta ainda com uma zona de campismo para portadores de passe geral com campismo, que permite prolongar a experiência ao longo de toda a programação.</p>

                        <p>Com uma média de cerca de 100 mil visitantes por edição, o <strong>Festival do Crato</strong> é hoje o principal festival de verão do Alentejo, reunindo diferentes gerações num ambiente marcado pela cultura local, música e gastronomia.</p>

                        <p>A edição de 2026 realiza-se de <strong>26 a 29 de agosto</strong>.</p>

                        <a href="/noticias" class="btn btn-ghost news-article__back">← Voltar às Notícias</a>
                    </div>
                </div>
            </article>

        <?php elseif ($activePage === 'news_slow_j'): ?>
            <article class="news-article">
                <div class="container">
                    <div class="news-article__hero">
                        <img src="/assets/img/new artists.jpeg" alt="Slow J, Sara Correia e Dub Inc confirmados no Festival do Crato 2026"
                            class="news-article__hero-img">
                    </div>
                    <div class="news-article__content">
                        <div class="news-article__meta">
                            <span class="news-card__tag">Artistas</span>
                            <time datetime="2026-05-11">11 Mai 2026</time>
                        </div>
                        <h1 class="news-article__title">Slow J, Sara Correia e Dub Inc confirmados no Festival do Crato 2026</h1>
                        <p class="news-article__lead">Os artistas juntam-se a Buba Espinho &amp; Convidados, Bispo e Calema</p>

                        <p>O <strong>Festival do Crato</strong> continua a revelar o cartaz da edição de 2026 com a confirmação de <strong>Slow J</strong>, <strong>Sara Correia</strong> e <strong>Dub Inc</strong>. Os artistas juntam-se a Buba Espinho &amp; Convidados, Bispo e Calema. O Festival está de regresso à vila alentejana de <strong>26 a 29 de agosto</strong> de 2026.</p>

                        <p><strong>Slow J</strong> é um dos principais nomes da música portuguesa atual, com presença consistente nos tops nacionais e centenas de milhões de streams acumulados. Temas como "Tata", "Teu Eternamente" e "Vida Boa" destacam-se no seu percurso, marcando diferentes fases da sua discografia. O seu álbum "Afro Fado" atingiu o primeiro lugar na tabela de vendas em Portugal.</p>

                        <p><strong>Sara Correia</strong> é uma das vozes mais reconhecidas do fado atual, distinguida com vários prémios (incluindo o de Melhor Artista Feminina nos Prémios Play 2026) e nomeações ao longo do seu percurso. No seu repertório destacam-se temas como "Chelas" ou "Quero é Viver", que têm contribuído para levar o fado a novos públicos. Tem atuado de forma regular em salas de referência e festivais.</p>

                        <p><strong>Dub Inc</strong> são uma das bandas europeias mais relevantes do reggae, com uma carreira consolidada ao longo de mais de duas décadas. Com vários álbuns editados e digressões internacionais, o grupo construiu uma forte base de seguidores, sendo presença habitual em grandes festivais na Europa e outros mercados.</p>

                        <h2>Sobre o Festival do Crato</h2>
                        <p>O Festival do Crato, situado no Alto Alentejo, é um dos festivais de verão mais relevantes em Portugal, combinando música, território e tradição. Para além do cartaz musical, o evento integra uma feira de artesanato e gastronomia que valoriza produtores e tradições locais, criando uma experiência que vai além dos concertos.</p>

                        <p>Com uma média de cerca de 100 mil visitantes por edição, o Festival do Crato é hoje o principal festival de verão do Alentejo, reunindo diferentes gerações num ambiente marcado pela cultura local, música e gastronomia.</p>

                        <p>A edição de 2026 realiza-se de <strong>26 a 29 de agosto</strong>.</p>

                        <a href="/noticias" class="btn btn-ghost news-article__back">← Voltar às Notícias</a>
                    </div>
                </div>
            </article>

        <?php elseif ($activePage === 'news_bispo_calema'): ?>
            <article class="news-article">
                <div class="container">
                    <div class="news-article__hero">
                        <img src="/assets/img/anuncio.jpeg" alt="Bispo e Calema confirmados no Festival do Crato 2026"
                            class="news-article__hero-img">
                    </div>
                    <div class="news-article__content">
                        <div class="news-article__meta">
                            <span class="news-card__tag">Artistas</span>
                            <time datetime="2026-04-23">23 Abr 2026</time>
                        </div>
                        <h1 class="news-article__title">Bispo e Calema confirmados no Festival do Crato 2026</h1>
                        <p class="news-article__lead">Artistas juntam-se ao já anunciado Buba Espinho &amp; Convidados</p>

                        <p>O <strong>Festival do Crato</strong> anuncia mais dois nomes para a próxima edição. Bispo e
                            Calema juntam-se ao já anunciado Buba Espinho &amp; Convidados. O Festival está de regresso à
                            vila alentejana de 26 a 29 de agosto!</p>

                        <p><strong>Bispo</strong> é um dos artistas mais ouvidos em Portugal, somando centenas de milhões de
                            streams nas plataformas digitais e presença recorrente nos tops nacionais. Tem vários temas
                            certificados com galardões de platina e ouro, incluindo "Nós2", "Pormenores" e "Essa Saia", que
                            marcaram diferentes fases do seu percurso.</p>

                        <p><strong>Calema</strong> são um dos projetos mais bem-sucedidos da música em português, acumulando
                            centenas de milhões de streams e visualizações nas plataformas digitais. Ao longo da sua
                            carreira, somam vários temas certificados com galardões de ouro e platina, incluindo "A Nossa
                            Vez" ou "Te Amo". Em 2024, tornaram-se os primeiros artistas portugueses a realizar um concerto
                            em nome próprio no Estádio da Luz.</p>

                        <h2>Sobre o Festival do Crato</h2>
                        <p>O Festival do Crato, situado no Alto Alentejo, é um dos festivais de verão mais relevantes em
                            Portugal, combinando música, território e tradição. Para além do cartaz musical, o evento
                            integra uma feira de artesanato e gastronomia que valoriza produtores e tradições locais,
                            criando uma experiência que vai além dos concertos.</p>

                        <p>O recinto conta ainda com uma zona de campismo para portadores de passe geral com campismo, que
                            permite prolongar a experiência ao longo de toda a programação.</p>

                        <p>Com uma média de cerca de 100 mil visitantes por edição, o Festival do Crato é hoje o principal
                            festival de verão do Alentejo, reunindo diferentes gerações num ambiente marcado pela cultura
                            local, música e gastronomia.</p>

                        <p>A edição de 2026 realiza-se de <strong>26 a 29 de agosto</strong>.</p>

                        <a href="/noticias" class="btn btn-ghost news-article__back">← Voltar às Notícias</a>
                    </div>
                </div>
            </article>

        <?php elseif ($activePage === 'about' || $activePage === 'info'): ?>

            <?= Component::render('About', ['festival' => $festival, 'showFull' => true]) ?>

        <?php elseif ($activePage === 'eco-move'): ?>
            <?= Component::render('PageHeader', [
                'label' => 'Festival Crato 2027',
                'title' => 'Eco Move',
                'accent' => 'Crato',
                'subtitle' => 'Recolha seletiva de proximidade — sê uma EcoTeam voluntária no Festival do Crato.',
            ]) ?>
            <?= Component::render('EcoMoveCrato', []) ?>

            <script>
                (function () {
                    const form = document.getElementById('eco-move-form');
                    if (!form) return;

                    // ── Seleção de turnos ───────────────────────────────────────
                    const shiftsInput = document.getElementById('eco-move-shifts');
                    const scheduleError = document.getElementById('eco-move-schedule-error');
                    const cells = form.querySelectorAll('.eco-move-cell');
                    const selected = new Set();

                    function syncShiftsInput() {
                        shiftsInput.value = Array.from(selected).join(',');
                    }

                    cells.forEach(cell => {
                        cell.addEventListener('click', function () {
                            const key = cell.dataset.date + '|' + cell.dataset.shift;
                            const isSelected = selected.has(key);
                            if (isSelected) {
                                selected.delete(key);
                            } else {
                                selected.add(key);
                            }
                            cell.classList.toggle('is-selected', !isSelected);
                            cell.setAttribute('aria-pressed', String(!isSelected));
                            syncShiftsInput();
                            if (selected.size > 0) {
                                scheduleError.classList.remove('is-visible');
                            }
                        });
                    });

                    // ── CTA Fixo Mobile (Scroll + Foco no primeiro campo) ───────
                    const floatingCta = document.getElementById('eco-move-floating-cta');
                    const floatingBar = document.getElementById('eco-move-floating-bar');
                    const nameInput = document.getElementById('eco-move-name');

                    if (floatingCta && nameInput) {
                        floatingCta.addEventListener('click', function () {
                            nameInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            setTimeout(() => {
                                nameInput.focus({ preventScroll: true });
                            }, 450);
                        });

                        // Ocultar suavemente o botão fixo quando o formulário estiver visível no ecrã
                        if ('IntersectionObserver' in window && floatingBar) {
                            const observer = new IntersectionObserver((entries) => {
                                entries.forEach(entry => {
                                    if (entry.isIntersecting) {
                                        floatingBar.classList.add('is-hidden');
                                    } else {
                                        floatingBar.classList.remove('is-hidden');
                                    }
                                });
                            }, { threshold: 0.1 });

                            observer.observe(form);
                        }
                    }

                    // ── Submissão do formulário ─────────────────────────────────
                    form.addEventListener('submit', async function (e) {
                        e.preventDefault();

                        if (!form.checkValidity()) {
                            form.reportValidity();
                            return;
                        }

                        if (selected.size === 0) {
                            scheduleError.textContent = 'Seleciona pelo menos um dia e turno.';
                            scheduleError.classList.add('is-visible');
                            scheduleError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            return;
                        }

                        const submit = form.querySelector('.contact-form__submit');
                        const feedback = form.querySelector('.contact-form__feedback');
                        const data = Object.fromEntries(new FormData(form).entries());

                        const fieldLabels = {
                            name: 'Nome', email: 'Email', phone: 'Telefone', nif: 'NIF',
                            age: 'Idade', address: 'Morada completa', rgpd: 'Autorização RGPD',
                            shifts: 'Dia(s) e turno(s)',
                        };
                        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

                        feedback.textContent = '';
                        feedback.className = 'contact-form__feedback';
                        submit.disabled = true;
                        submit.classList.add('is-loading');

                        try {
                            const res = await fetch('/eco-move-submit.php', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json' },
                                body: JSON.stringify(data),
                            });
                            const json = await res.json().catch(() => ({}));

                            if (res.ok && json.ok) {
                                feedback.textContent = 'Obrigado! A tua inscrição foi enviada com sucesso.';
                                feedback.classList.add('is-success');
                                form.reset();
                                selected.clear();
                                cells.forEach(c => { c.classList.remove('is-selected'); c.setAttribute('aria-pressed', 'false'); });
                                syncShiftsInput();
                            } else if (res.status === 429) {
                                feedback.textContent = 'Demasiadas submissões. Tenta novamente mais tarde.';
                                feedback.classList.add('is-error');
                            } else if (json.error === 'validation') {
                                const invalidFields = Array.isArray(json.fields) ? json.fields : [];
                                let firstInvalidEl = null;
                                invalidFields.forEach(fieldName => {
                                    const el = form.querySelector(`[name="${fieldName}"]`);
                                    if (el) {
                                        el.classList.add('is-invalid');
                                        firstInvalidEl = firstInvalidEl || el;
                                    }
                                });
                                const names = invalidFields.map(f => fieldLabels[f] || f).join(', ');
                                feedback.textContent = names
                                    ? `Verifica os campos: ${names}.`
                                    : 'Verifica os campos marcados e tenta novamente.';
                                feedback.classList.add('is-error');
                                if (firstInvalidEl) {
                                    firstInvalidEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                } else if (invalidFields.includes('shifts')) {
                                    scheduleError.textContent = 'Seleciona pelo menos um dia e turno.';
                                    scheduleError.classList.add('is-visible');
                                }
                            } else {
                                feedback.textContent = 'Não foi possível enviar. Tenta novamente em instantes.';
                                feedback.classList.add('is-error');
                            }
                        } catch (err) {
                            feedback.textContent = 'Erro de rede. Verifica a ligação e tenta novamente.';
                            feedback.classList.add('is-error');
                        } finally {
                            submit.disabled = false;
                            submit.classList.remove('is-loading');
                        }
                    });
                })();
            </script>

        <?php elseif ($activePage === 'partners'): ?>
            <?= Component::render('PageHeader', [
                'label' => 'Festival Crato 2027',
                'title' => 'Os nossos',
                'accent' => 'Parceiros',
                'subtitle' => 'As marcas e instituições que tornam o Festival do Crato possível.',
            ]) ?>
            <?= Component::render('Partners', ['partnerGroups' => $partnerGroups]) ?>

        <?php elseif ($activePage === 'guide'): ?>
            <?= Component::render('PageHeader', [
                'label' => 'Festival Crato 2027',
                'title' => 'Guia do',
                'accent' => 'Festival',
                'subtitle' => 'O guia oficial da 40.ª edição, para levares contigo.',
            ]) ?>
            <?= Component::render('FestivalGuide', [
                'guidePdf' => $guidePdf,
                'guideSize' => $guideSize,
                'guideReady' => $guideReady,
            ]) ?>

        <?php elseif ($activePage === 'directions'): ?>
            <?= Component::render('PageHeader', [
                'label' => 'Festival Crato 2027',
                'title' => 'Como',
                'accent' => 'Chegar',
                'subtitle' => 'Informações sobre acessos e transportes para o Crato.',
            ]) ?>
            <section class="generic-page">
                <div class="container">
                    <p>A Vila do Crato situa-se no Alto Alentejo, com fácil acesso pela A6 (saída para Elvas/Marvão).</p>
                    <h2>Transportes Públicos</h2>
                    <p>Comboio até Portalegre ou Elvas, seguido de autocarro ou táxi.</p>
                    <h2>Estacionamento</h2>
                    <p>Parques de estacionamento disponíveis junto ao recinto.</p>
                </div>
            </section>

        <?php elseif ($activePage === 'camping'): ?>
            <?= Component::render('PageHeader', [
                'label' => 'Festival Crato 2027',
                'title' => 'Parque de',
                'accent' => 'Campismo',
                'subtitle' => 'Vive o Festival do Crato até ao último momento.',
            ]) ?>
            <section class="camping-page">
                <div class="container">

                    <p class="camping-intro">O Parque de Campismo do Festival do Crato é a solução ideal para quem pretende
                        aproveitar ao máximo todos os dias do evento, com conforto, segurança e um ambiente de convívio
                        entre festivaleiros. A pensar na comodidade dos visitantes, a organização disponibiliza uma zona de
                        camping ocasional equipada com as condições essenciais para uma estadia tranquila, permitindo viver
                        o festival de forma prática e próxima de toda a animação.</p>

                    <div class="camping-hero-img">
                        <img src="/assets/img/campismo.jpeg" alt="Parque de Campismo do Festival do Crato — tendas e festivaleiros ao amanhecer" loading="lazy">
                        <div class="camping-hero-img__overlay">
                            <span class="camping-hero-img__badge">
                                <span class="material-symbols-outlined">outdoor_grill</span>
                                Parque de Campismo
                            </span>
                        </div>
                    </div>

                    <?php /* Datas de check-in/check-out retiradas — eram as de 2026.
                             Repor quando a logística da próxima edição estiver fechada. */ ?>

                    <div class="camping-accordion">
                        <details class="camping-details" name="camping-faq">
                            <summary class="camping-section-title">
                                Condições e Serviços Disponíveis
                                <span class="material-symbols-outlined accordion-icon" aria-hidden="true">expand_more</span>
                            </summary>
                            <div class="camping-details-content">
                                <p>A zona de campismo está equipada com um conjunto de infraestruturas e serviços que visam
                                    garantir
                                    conforto, segurança e bem-estar a todos os utilizadores:</p>
                                <ul class="camping-list">
                                    <li>Área destinada à utilização de fogareiros</li>
                                    <li>Lava-loiça</li>
                                    <li>Posto de carregamento de telemóveis</li>
                                    <li>Instalações sanitárias e duche para pessoas com mobilidade reduzida</li>
                                    <li>Chuveiros interiores e exteriores</li>
                                    <li>Área de refeitório ao ar livre</li>
                                    <li>Iluminação noturna</li>
                                    <li>Sistema de videovigilância</li>
                                    <li>Equipa de apoio e segurança no local</li>
                                </ul>
                            </div>
                        </details>

                        <details class="camping-details" name="camping-faq">
                            <summary class="camping-section-title">
                                Regras de Utilização
                                <span class="material-symbols-outlined accordion-icon" aria-hidden="true">expand_more</span>
                            </summary>
                            <div class="camping-details-content">
                                <p>Para garantir a segurança e o bom funcionamento da zona de campismo, devem ser
                                    respeitadas as
                                    seguintes normas.</p>
                                <p><strong>É proibido:</strong></p>
                                <ul class="camping-list camping-list--rules">
                                    <li>Fazer fogueiras</li>
                                    <li>Utilizar garrafas, vasilhame ou utensílios em vidro</li>
                                    <li>Deitar lixo para o chão</li>
                                    <li>Montar tendas em acessos reservados a viaturas de emergência</li>
                                    <li>Delimitar ou reservar espaço de forma abusiva</li>
                                    <li>A entrada de animais, exceto cães guia</li>
                                </ul>
                                <p class="camping-warning">O incumprimento das normas poderá implicar a perda do direito de
                                    acesso ao parque de campismo e ao Festival.</p>
                            </div>
                        </details>

                        <details class="camping-details" name="camping-faq">
                            <summary class="camping-section-title">
                                O Que Levar
                                <span class="material-symbols-outlined accordion-icon" aria-hidden="true">expand_more</span>
                            </summary>
                            <div class="camping-details-content">
                                <ul class="camping-list camping-list--pack">
                                    <li>Tenda e material de campismo</li>
                                    <li>Saco-cama ou colchão insuflável</li>
                                    <li>Roupa adequada às condições climatéricas</li>
                                    <li>Produtos de higiene pessoal</li>
                                    <li>Lanterna ou iluminação portátil</li>
                                    <li>Protetor solar</li>
                                </ul>
                            </div>
                        </details>

                        <details class="camping-details" name="camping-faq">
                            <summary class="camping-section-title">
                                Informações Importantes
                                <span class="material-symbols-outlined accordion-icon" aria-hidden="true">expand_more</span>
                            </summary>
                            <div class="camping-details-content">
                                <ul class="camping-list">
                                    <li>A zona de campismo dispõe de sistema de videovigilância</li>
                                    <li>Recomenda-se a vigilância permanente de crianças</li>
                                    <li>Cada utilizador é responsável pelos seus bens pessoais</li>
                                    <li>A organização reserva-se o direito de aplicar medidas necessárias para garantir a
                                        segurança de
                                        todos</li>
                                </ul>
                            </div>
                        </details>

                        <details class="camping-details" name="camping-faq">
                            <summary class="camping-section-title">
                                Localização do Campismo
                                <span class="material-symbols-outlined accordion-icon" aria-hidden="true">expand_more</span>
                            </summary>
                            <div class="camping-details-content camping-location-layout">
                                <div class="camping-map-wrapper">
                                    <iframe
                                        src="https://maps.google.com/maps?q=Crato,+Portugal&t=&z=14&ie=UTF8&iwloc=&output=embed"
                                        frameborder="0" allowfullscreen="" loading="lazy"
                                        referrerpolicy="no-referrer-when-downgrade"></iframe>
                                </div>
                                <div class="camping-location-info">
                                    <h3>Parque de Campismo</h3>
                                    <p>Vila do Crato<br>7430-999 Crato, Alto Alentejo</p>
                                    <a href="https://maps.app.goo.gl/zKYtMe21nYnLhKzE9" target="_blank" rel="noopener"
                                        class="btn btn-primary camping-map-btn">
                                        <span class="material-symbols-outlined">open_in_new</span>
                                        Abrir no Google Maps
                                    </a>
                                </div>
                            </div>
                        </details>
                    </div>

                </div>
            </section>

        <?php elseif ($activePage === 'todo'): ?>
            <?= Component::render('PageHeader', [
                'label' => 'Festival Crato 2027',
                'title' => 'O que',
                'accent' => 'Fazer',
                'subtitle' => 'Artesanato, gastronomia, música e muito mais.',
            ]) ?>
            <section class="generic-page">
                <div class="container">
                    <p>Além dos espetáculos musicais, o Festival oferece exposição de artesanato, degustação de
                        produtos gastronómicos regionais e atividades culturais.</p>
                </div>
            </section>

        <?php elseif ($activePage === 'contacts'): ?>
            <?= Component::render('PageHeader', [
                'label' => 'Fala Connosco',
                'title' => 'Entra em',
                'accent' => 'Contacto',
                'subtitle' => 'Bilhetes, parcerias ou candidaturas — estamos aqui para te ajudar.',
            ]) ?>

            <section class="contact-cta-section">
                <div class="container">
                    <div class="contact-cta-grid contact-cta-grid--3">
                        <div class="contact-cta-card">
                            <div class="contact-cta-card__icon">
                                <span class="material-symbols-outlined">mail</span>
                            </div>
                            <h2>Entra em Contacto</h2>
                            <p>Tens dúvidas sobre bilhetes ou o festival? Envia-nos uma mensagem.</p>
                            <button type="button" class="btn btn-primary" id="btn-scroll-to-form">
                                Escreve-nos
                            </button>
                        </div>
                        <div class="contact-cta-card">
                            <div class="contact-cta-card__icon">
                                <span class="material-symbols-outlined">work</span>
                            </div>
                            <h2>Trabalhar Connosco</h2>
                            <p>Procuramos pessoas apaixonadas pela cultura e pelo festival.</p>
                            <button type="button" class="btn btn-primary" data-open-modal="contact-modal-work">
                                Vem trabalhar connosco
                            </button>
                        </div>
                        <div class="contact-cta-card">
                            <div class="contact-cta-card__icon">
                                <span class="material-symbols-outlined">handshake</span>
                            </div>
                            <h2>Sê Nosso Parceiro</h2>
                            <p>Junta a tua marca ao maior festival do Alto Alentejo.</p>
                            <button type="button" class="btn btn-primary" data-open-modal="contact-modal-partner">
                                Sê nosso parceiro
                            </button>
                        </div>
                    </div>
                </div>
            </section>

            <section class="contact-section" id="contact-form-section">
                <div class="container contact-section__container">
                    <?= Component::render('ContactForm', ['formId' => 'contact-form-main', 'subject' => 'Dúvidas sobre bilhetes']) ?>
                </div>
            </section>

            <!-- Secção de histórias — oculta temporariamente, aguarda página própria -->
            <section class="contact-stories-section" style="display:none;" aria-hidden="true">
                <div class="container contact-section__container">
                    <div class="contact-stories-intro">
                        <h2>O Festival do Crato faz parte da tua vida e tens uma história para contar?</h2>
                        <p>Preenche o seguinte formulário e partilha connosco a tua memória.</p>
                    </div>
                    <?= Component::render('ContactForm', ['formId' => 'contact-form-stories', 'subject' => 'História do festival', 'submitLabel' => 'Partilhar história']) ?>
                </div>
            </section>

            <section class="contact-info-section">
                <div class="container">
                    <div class="contact-info-card">
                        <h3>Festival do Crato</h3>
                        <p>
                            <a href="mailto:festivaldocrato@cm-crato.pt">festivaldocrato@cm-crato.pt</a>
                        </p>
                    </div>
                </div>
            </section>

            <?= Component::render('ContactModal', [
                'modalId' => 'contact-modal-work',
                'title' => 'Vem trabalhar connosco',
                'intro' => 'Conta-nos sobre ti e deixa o teu contacto — entraremos em contacto em breve.',
                'formId' => 'contact-form-work',
                'subject' => 'Candidatura — trabalhar connosco',
            ]) ?>

            <?= Component::render('ContactModal', [
                'modalId' => 'contact-modal-partner',
                'title' => 'Sê nosso parceiro',
                'intro' => 'Fala-nos da tua marca e da oportunidade de parceria que tens em mente.',
                'formId' => 'contact-form-partner',
                'subject' => 'Proposta de parceria',
            ]) ?>

            <script>
                (function () {
                    // ── Contact form submission ─────────────────────────────────
                    function attachForm(form) {
                        form.addEventListener('submit', async function (e) {
                            e.preventDefault();

                            const submit = form.querySelector('.contact-form__submit');
                            const feedback = form.querySelector('.contact-form__feedback');
                            const data = Object.fromEntries(new FormData(form).entries());

                            feedback.textContent = '';
                            feedback.className = 'contact-form__feedback';
                            submit.disabled = true;
                            submit.classList.add('is-loading');

                            try {
                                const res = await fetch('/contact-submit.php', {
                                    method: 'POST',
                                    headers: { 'Content-Type': 'application/json' },
                                    body: JSON.stringify(data),
                                });
                                const json = await res.json().catch(() => ({}));

                                if (res.ok && json.ok) {
                                    feedback.textContent = 'Obrigado! A tua mensagem foi enviada.';
                                    feedback.classList.add('is-success');
                                    form.reset();
                                } else if (res.status === 429) {
                                    feedback.textContent = 'Demasiadas submissões. Tenta novamente mais tarde.';
                                    feedback.classList.add('is-error');
                                } else if (json.error === 'validation') {
                                    feedback.textContent = 'Verifica os campos marcados e tenta novamente.';
                                    feedback.classList.add('is-error');
                                } else {
                                    feedback.textContent = 'Não foi possível enviar. Tenta novamente em instantes.';
                                    feedback.classList.add('is-error');
                                }
                            } catch (err) {
                                feedback.textContent = 'Erro de rede. Verifica a ligação e tenta novamente.';
                                feedback.classList.add('is-error');
                            } finally {
                                submit.disabled = false;
                                submit.classList.remove('is-loading');
                            }
                        });
                    }
                    document.querySelectorAll('[data-contact-form]').forEach(attachForm);

                    // ── Scroll to form ──────────────────────────────────────────
                    const scrollBtn = document.getElementById('btn-scroll-to-form');
                    if (scrollBtn) {
                        scrollBtn.addEventListener('click', function () {
                            const section = document.getElementById('contact-form-section');
                            if (!section) return;
                            section.scrollIntoView({ behavior: 'smooth', block: 'start' });
                            const firstInput = section.querySelector('input:not([type="hidden"]):not(.contact-form__hp)');
                            if (firstInput) setTimeout(() => firstInput.focus(), 400);
                        });
                    }

                    // ── Contact modals ──────────────────────────────────────────
                    let _lastTrigger = null;

                    function openModal(id) {
                        const modal = document.getElementById(id);
                        if (!modal) return;
                        _lastTrigger = document.activeElement;
                        modal.classList.add('active');
                        document.body.style.overflow = 'hidden';
                        const firstInput = modal.querySelector('input:not([type="hidden"]):not(.contact-form__hp), textarea');
                        if (firstInput) setTimeout(() => firstInput.focus(), 60);
                    }

                    function closeModal(modal) {
                        if (!modal) return;
                        modal.classList.remove('active');
                        document.body.style.overflow = '';
                        if (_lastTrigger && typeof _lastTrigger.focus === 'function') {
                            _lastTrigger.focus();
                        }
                    }

                    document.querySelectorAll('[data-open-modal]').forEach(btn => {
                        btn.addEventListener('click', () => openModal(btn.dataset.openModal));
                    });
                    document.querySelectorAll('[data-close-modal]').forEach(btn => {
                        btn.addEventListener('click', () => closeModal(document.getElementById(btn.dataset.closeModal)));
                    });
                    document.querySelectorAll('[data-contact-modal]').forEach(modal => {
                        modal.addEventListener('click', e => {
                            if (e.target === modal) closeModal(modal);
                        });
                    });
                    document.addEventListener('keydown', e => {
                        if (e.key !== 'Escape') return;
                        const open = document.querySelector('[data-contact-modal].active');
                        if (open) closeModal(open);
                    });
                })();
            </script>

        <?php elseif (str_starts_with($activePage, 'legal_')): ?>
            <?= Component::render('PageHeader', [
                'label' => 'Legal',
                'title' => explode(' ', $pageTitle)[0],
                'accent' => implode(' ', array_slice(explode(' ', $pageTitle), 1)),
                'subtitle' => 'Informações legais e transparência.',
            ]) ?>
            <section class="legal-page">
                <div class="container">
                    <div class="legal-content">
                        <?php if ($activePage === 'legal_privacy'): ?>
                            <h2>1. Recolha de Dados</h2>
                            <p>O Festival do Crato recolhe dados pessoais através do formulário de contacto e no processo de compra de bilhetes (gerido pela plataforma externa LeBillet). Os dados recolhidos limitam-se ao estritamente necessário para a prestação do serviço.</p>
                            <h2>2. Finalidade</h2>
                            <p>Os seus dados são utilizados para responder a pedidos de informação, processar candidaturas e garantir o acesso ao recinto do festival. Não partilhamos dados com terceiros para fins comerciais.</p>
                            <h2>3. Direitos do Utilizador</h2>
                            <p>Ao abrigo do RGPD, tem o direito de aceder, retificar ou solicitar a eliminação dos seus dados. Para tal, contacte-nos através de festivaldocrato@cm-crato.pt.</p>

                        <?php elseif ($activePage === 'legal_cookies'): ?>
                            <h2>O que são Cookies?</h2>
                            <p>Cookies são pequenos ficheiros de texto armazenados no seu dispositivo para melhorar a experiência de navegação.</p>
                            <h2>Cookies Utilizados</h2>
                            <p>Utilizamos apenas cookies essenciais para o funcionamento do site (como a sessão de checkout) e cookies de análise anónima para entender como os visitantes interagem com o site.</p>
                            <h2>Gestão de Cookies</h2>
                            <p>Pode alterar as suas preferências de cookies nas definições do seu navegador a qualquer momento.</p>

                        <?php elseif ($activePage === 'legal_terms'): ?>
                            <h2>1. Bilheteira</h2>
                            <p>A compra de bilhetes é final. Não se efetuam trocas ou devoluções, exceto em caso de cancelamento do evento nos termos previstos na lei.</p>
                            <h2>2. Acesso ao Recinto</h2>
                            <p>A organização reserva-se o direito de admissão. É proibida a entrada de objetos perigosos, vidro e substâncias ilícitas. Os portadores de bilhete podem ser sujeitos a revistas de segurança.</p>
                            <h2>3. Direitos de Imagem</h2>
                            <p>Ao entrar no recinto, o portador do bilhete consente na eventual captação e utilização da sua imagem para fins de divulgação e arquivo do evento.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </section>

        <?php elseif ($activePage === '404'): ?>
            <section class="page-404">
                <div class="container">
                    <h1>Página não encontrada</h1>
                    <p>Erro 404 — A página que procura não existe.</p>
                    <a href="/" class="btn btn-primary">Voltar ao início</a>
                </div>
            </section>

        <?php else: ?>
            <section class="generic-page">
                <div class="container">
                    <h1><?= htmlspecialchars($pageTitle) ?></h1>
                </div>
            </section>
        <?php endif; ?>

    </main>

    <?= Component::render('Footer', ['siteName' => $siteName, 'festival' => $festival]) ?>

    <!-- Modais -->
    <?= Component::render('CheckoutModal', ['checkoutUrl' => $checkoutUrl]) ?>

    <!-- Cookie Banner -->
    <div id="cookie-banner" class="cookie-banner">
        <div class="container">
            <div class="cookie-banner__content">
                <p>Utilizamos cookies para melhorar a sua experiência no nosso site. Ao continuar a navegar, está a aceitar a nossa <a href="/politica-privacidade">Política de Privacidade</a>.</p>
                <div class="cookie-banner__actions">
                    <button id="cookie-accept" class="btn btn-primary btn-sm">Aceitar</button>
                    <button id="cookie-reject" class="btn btn-outline btn-sm">Rejeitar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script src="/assets/js/theme.js"></script>
</body>

</html>