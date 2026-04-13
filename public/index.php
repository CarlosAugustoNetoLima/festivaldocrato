<?php
// Entry point — Festival Crato
use App\Helpers\Component;

// Autoloader
spl_autoload_register(function ($class) {
    $prefix  = 'App\\';
    $baseDir = __DIR__ . '/../app/';
    $len     = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    $file = $baseDir . str_replace('\\', '/', substr($class, $len)) . '.php';
    if (file_exists($file)) require $file;
});

// ─────────────────────────────────────────────
//  Site data (hardcoded — padrão yanns)
// ─────────────────────────────────────────────
$checkoutUrl = 'https://checkout.lebillet.eu/';
$siteName    = 'Festival Crato';
$themeName   = 'crato';

$festival = [
    'edition'             => '40.ª',
    'date_start'          => '2026-08-25',
    'date_end'            => '2026-08-29',
    'date_festival_start' => '2026-08-26',
    'date_campista'       => '2026-08-24',
    'location'            => 'Crato, Alto Alentejo',
    'venue'               => 'Vila do Crato',
    'organizer'           => 'Município do Crato',
    'description'         => 'A 40.ª Feira de Artesanato e Gastronomia e o Festival do Crato regressam de 25 a 29 de agosto de 2026.',
    'mission'             => 'Promover e preservar o artesanato e a gastronomia enquanto valores culturais.',
    'contact' => [
        'email'   => 'fag@cm-crato.pt',
        'phone'   => '245 990 110',
        'address' => 'Praça do Município',
        'zip'     => '7430-999 Crato',
    ],
    'social' => [
        'instagram' => 'https://instagram.com/',
        'facebook'  => 'https://facebook.com/',
        'twitter'   => 'https://twitter.com/',
        'youtube'   => 'https://youtube.com/',
    ],
];

$tickets = [
    [
        'id'          => 'passe-4dias',
        'name'        => 'Passe 4 Dias',
        'subtitle'    => '26–29 Agosto · Sem Campismo',
        'price'       => 45.00,
        'description' => 'Acesso completo aos 4 dias do Festival do Crato 2026. A partir de 1 de agosto: 50€.',
        'highlight'   => true,
        'event_id'    => 'crato-2026-passe',
    ],
    [
        'id'          => 'passe-4dias-campismo',
        'name'        => 'Passe 4 Dias + Campismo',
        'subtitle'    => '26–29 Agosto · Com Campismo',
        'price'       => 60.00,
        'description' => 'Acesso completo aos 4 dias com campismo. A partir de 1 de agosto: 70€.',
        'highlight'   => false,
        'event_id'    => 'crato-2026-passe-campismo',
    ],
    [
        'id'          => 'dia-26',
        'name'        => 'Bilhete Dia 26',
        'subtitle'    => '26 Agosto · Quarta-feira',
        'price'       => 15.00,
        'description' => 'Acesso ao Festival do Crato — 1.º dia. A partir de 1 de agosto: 20€.',
        'highlight'   => false,
        'event_id'    => 'crato-2026-dia1',
    ],
    [
        'id'          => 'dia-27',
        'name'        => 'Bilhete Dia 27',
        'subtitle'    => '27 Agosto · Quinta-feira',
        'price'       => 15.00,
        'description' => 'Acesso ao Festival do Crato — 2.º dia. A partir de 1 de agosto: 20€.',
        'highlight'   => false,
        'event_id'    => 'crato-2026-dia2',
    ],
    [
        'id'          => 'dia-28',
        'name'        => 'Bilhete Dia 28',
        'subtitle'    => '28 Agosto · Sexta-feira',
        'price'       => 20.00,
        'description' => 'Acesso ao Festival do Crato — 3.º dia. A partir de 1 de agosto: 25€.',
        'highlight'   => false,
        'event_id'    => 'crato-2026-dia3',
    ],
    [
        'id'          => 'dia-29',
        'name'        => 'Bilhete Dia 29',
        'subtitle'    => '29 Agosto · Sábado',
        'price'       => 20.00,
        'description' => 'Acesso ao Festival do Crato — dia final. A partir de 1 de agosto: 25€.',
        'highlight'   => false,
        'event_id'    => 'crato-2026-dia4',
    ],
    [
        'id'          => 'concerto-solidario',
        'name'        => 'Concerto Solidário',
        'subtitle'    => '25 Agosto · Terça-feira',
        'price'       => 10.00,
        'description' => 'Concerto Solidário no Palco FAG. O passe 4 dias não dá acesso a este concerto.',
        'highlight'   => false,
        'event_id'    => 'crato-2026-solidario',
    ],
];

$artists = [
    ['name' => 'A Anunciar', 'day' => 1, 'stage' => 'Palco FAG',      'headliner' => true,  'genre' => 'Música Portuguesa', 'image' => '/themes/crato/img/artist-1.jpg'],
    ['name' => 'A Anunciar', 'day' => 1, 'stage' => 'Palco FAG',      'headliner' => false, 'genre' => 'Folk / Tradicional', 'image' => '/themes/crato/img/artist-2.jpg'],
    ['name' => 'A Anunciar', 'day' => 2, 'stage' => 'Palco Festival', 'headliner' => true,  'genre' => 'Música Portuguesa', 'image' => '/themes/crato/img/artist-3.jpg'],
    ['name' => 'A Anunciar', 'day' => 2, 'stage' => 'Palco Festival', 'headliner' => false, 'genre' => 'Pop / Rock',        'image' => '/themes/crato/img/artist-4.jpg'],
    ['name' => 'A Anunciar', 'day' => 3, 'stage' => 'Palco Festival', 'headliner' => true,  'genre' => 'Música Portuguesa', 'image' => '/themes/crato/img/artist-5.jpg'],
    ['name' => 'A Anunciar', 'day' => 3, 'stage' => 'Palco FAG',      'headliner' => false, 'genre' => 'Fado / Tradicional','image' => '/themes/crato/img/artist-6.jpg'],
    ['name' => 'Buba Espinho','day'=> 4, 'stage' => 'Palco Festival', 'headliner' => true,  'genre' => 'Música Portuguesa', 'image' => '/themes/crato/img/artist-7.jpg', 'confirmed' => true],
    ['name' => 'A Anunciar', 'day' => 4, 'stage' => 'Palco FAG',      'headliner' => false, 'genre' => 'Folk / World Music','image' => '/themes/crato/img/artist-8.jpg'],
];

$products = [
    [
        'id'          => 'tshirt-crato-2026',
        'name'        => 'T-Shirt Festival Crato 2026',
        'category'    => 'Vestuário',
        'price'       => 20.00,
        'description' => 'T-Shirt oficial do Festival do Crato 2026. 100% algodão orgânico.',
        'image'       => '/themes/crato/img/logo.png',
        'highlight'   => true,
        'event_id'    => 'crato-store-tshirt',
    ],
    [
        'id'          => 'bone-crato-2026',
        'name'        => 'Boné Festival Crato 2026',
        'category'    => 'Acessórios',
        'price'       => 15.00,
        'description' => 'Boné oficial do Festival do Crato 2026.',
        'image'       => '/themes/crato/img/logo.png',
        'highlight'   => false,
        'event_id'    => 'crato-store-bone',
    ],
    [
        'id'          => 'eco-bag-crato-2026',
        'name'        => 'Eco Bag Festival Crato 2026',
        'category'    => 'Acessórios',
        'price'       => 10.00,
        'description' => 'Saco reutilizável oficial do Festival do Crato 2026.',
        'image'       => '/themes/crato/img/logo.png',
        'highlight'   => false,
        'event_id'    => 'crato-store-ecobag',
    ],
    [
        'id'          => 'hoodie-crato-2026',
        'name'        => 'Hoodie Festival Crato 2026',
        'category'    => 'Vestuário',
        'price'       => 40.00,
        'description' => 'Hoodie oficial do Festival do Crato 2026. Edição limitada.',
        'image'       => '/themes/crato/img/logo.png',
        'highlight'   => false,
        'event_id'    => 'crato-store-hoodie',
    ],
];

$news = [
    [
        'date'    => '2026-04-01',
        'label'   => '40.ª FAG 2026',
        'title'   => 'Inscrições para a Feira de Artesanato e Gastronomia 2026 já disponíveis!',
        'excerpt' => 'As candidaturas para participar na 40.ª FAG estão abertas. Prazo de inscrição: 19 de junho de 2026.',
        'url'     => 'https://festivaldocrato.cm-crato.pt/',
        'tag'     => 'FAG',
        'image'   => '/themes/crato/img/1200X630_FAG_INS-copiar.jpg',
    ],
    [
        'date'    => '2026-03-02',
        'label'   => 'Primeiro Artista Confirmado',
        'title'   => 'Buba Espinho é o primeiro artista confirmado para o Festival do Crato 2026!',
        'excerpt' => 'Uma das vozes mais distintivas da nova geração da música portuguesa, Buba Espinho sobe ao palco no dia 29 de agosto.',
        'url'     => 'https://festivaldocrato.cm-crato.pt/',
        'tag'     => 'Artistas',
        'image'   => '/themes/crato/img/1200X630_buba.png',
    ],
    [
        'date'    => '2026-01-21',
        'label'   => 'Datas Confirmadas',
        'title'   => 'Festival do Crato celebra mais uma edição no último fim de semana de agosto',
        'excerpt' => 'O Festival do Crato 2026 está confirmado para 26, 27, 28 e 29 de agosto.',
        'url'     => 'https://festivaldocrato.cm-crato.pt/',
        'tag'     => 'Festival',
        'image'   => '/themes/crato/img/01.jpg',
    ],
];

// ─────────────────────────────────────────────
//  Routing
// ─────────────────────────────────────────────
$request = $_SERVER['REQUEST_URI'];
$path    = rtrim(parse_url($request, PHP_URL_PATH), '/') ?: '/';

$routes = [
    '/'            => 'home',
    '/bilheteira'  => 'tickets',
    '/bilhetes'    => 'tickets',
    '/lineup'      => 'lineup',
    '/sobre'       => 'about',
    '/como-chegar' => 'directions',
    '/campismo'    => 'camping',
    '/o-que-fazer' => 'todo',
    '/contactos'   => 'contacts',
    '/noticias'    => 'news',
    '/artistas'    => 'artists',
    '/info'        => 'info',
    '/loja'        => 'store',
    '/produto'     => 'product',
    '/pesquisa'    => 'search',
];

$activePage = $routes[$path] ?? '404';

$pageTitles = [
    'home'       => 'Festival Crato 2026',
    'tickets'    => 'Bilheteira',
    'lineup'     => 'Programação',
    'about'      => 'Sobre o Festival',
    'directions' => 'Como Chegar',
    'camping'    => 'Campismo',
    'todo'       => 'O que Fazer',
    'contacts'   => 'Contactos',
    'news'       => 'Notícias',
    'artists'    => 'Artistas',
    'info'       => 'Informações',
    'store'      => 'Loja',
    'product'    => 'Produto',
];

$pageTitle = $pageTitles[$activePage] ?? 'Festival Crato';
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($siteName) ?> — <?= htmlspecialchars($pageTitle) ?></title>
    <meta name="description" content="40.ª Feira de Artesanato e Gastronomia e Festival do Crato — 25 a 29 de Agosto de 2026">
    <meta property="og:title" content="40.ª FAG & Festival do Crato 2026">
    <meta property="og:description" content="Feira de Artesanato e Gastronomia e Festival do Crato — 25 a 29 de Agosto de 2026">
    <meta property="og:type" content="website">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" rel="stylesheet">

    <!-- CSS -->
    <link rel="stylesheet" href="/assets/css/base.css">
    <link rel="stylesheet" href="/assets/css/components.css">
    <link rel="stylesheet" href="/themes/<?= htmlspecialchars($themeName) ?>/theme.css">

    <!-- Toastify -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
</head>

<body data-page="<?= htmlspecialchars($activePage) ?>">

    <?= Component::render('Header', ['activePage' => $activePage, 'siteName' => $siteName]) ?>

    <main class="main-content">

        <?php if ($activePage === 'home'): ?>
            <?= Component::render('Hero',    ['festival' => $festival]) ?>
            <?= Component::render('Lineup',  ['artists' => $artists]) ?>
            <?= Component::render('News',    ['news' => $news]) ?>
            <?= Component::render('Artists', ['artists' => $artists]) ?>
            <?= Component::render('Tickets', ['tickets' => $tickets, 'checkoutUrl' => $checkoutUrl]) ?>
            <?= Component::render('Store',   ['products' => $products]) ?>
            <?= Component::render('About',   ['festival' => $festival]) ?>

        <?php elseif ($activePage === 'store'): ?>
            <?= Component::render('Collections', ['products' => $products, 'checkoutUrl' => $checkoutUrl]) ?>

        <?php elseif ($activePage === 'product'): ?>
            <?= Component::render('ProductDetail', ['products' => $products, 'checkoutUrl' => $checkoutUrl]) ?>

        <?php elseif ($activePage === 'tickets'): ?>
            <section class="page-hero page-hero--inner">
                <div class="container">
                    <h1 class="page-hero__title">Bilheteira</h1>
                    <p class="page-hero__sub">Garante o teu lugar no Festival Crato 2026</p>
                </div>
            </section>
            <?= Component::render('Tickets', ['tickets' => $tickets, 'checkoutUrl' => $checkoutUrl, 'showAll' => true]) ?>

        <?php elseif ($activePage === 'lineup'): ?>
            <section class="page-hero page-hero--inner">
                <div class="container">
                    <h1 class="page-hero__title">Programação</h1>
                </div>
            </section>
            <?= Component::render('Lineup', ['artists' => $artists, 'showAll' => true]) ?>

        <?php elseif ($activePage === 'artists'): ?>
            <section class="page-hero page-hero--inner">
                <div class="container">
                    <h1 class="page-hero__title">Artistas</h1>
                </div>
            </section>
            <?= Component::render('Artists', ['artists' => $artists, 'showAll' => true]) ?>

        <?php elseif ($activePage === 'news'): ?>
            <section class="page-hero page-hero--inner">
                <div class="container">
                    <h1 class="page-hero__title">Notícias</h1>
                </div>
            </section>
            <?= Component::render('News', ['news' => $news]) ?>

        <?php elseif ($activePage === 'about' || $activePage === 'info'): ?>
            <section class="page-hero page-hero--inner">
                <div class="container">
                    <h1 class="page-hero__title">Sobre o Festival</h1>
                    <p class="page-hero__sub">40.ª Feira de Artesanato e Gastronomia e Festival do Crato</p>
                </div>
            </section>
            <?= Component::render('About', ['festival' => $festival, 'showFull' => true]) ?>

        <?php elseif ($activePage === 'directions'): ?>
            <section class="page-hero page-hero--inner">
                <div class="container">
                    <h1 class="page-hero__title">Como Chegar</h1>
                    <p class="page-hero__sub">Informações sobre acessos e transportes para o Crato</p>
                </div>
            </section>
            <section class="generic-page">
                <div class="container">
                    <p>A Vila do Crato situa-se no Alto Alentejo, com fácil acesso pela A6 (saída para Elvas/Marvão).</p>
                    <h3>Transportes Públicos</h3>
                    <p>Comboio até Portalegre ou Elvas, seguido de autocarro ou táxi.</p>
                    <h3>Estacionamento</h3>
                    <p>Parques de estacionamento disponíveis junto ao recinto.</p>
                </div>
            </section>

        <?php elseif ($activePage === 'camping'): ?>
            <section class="page-hero page-hero--inner">
                <div class="container">
                    <h1 class="page-hero__title">Campismo</h1>
                    <p class="page-hero__sub">Receção ao Campista e informações sobre alojamento</p>
                </div>
            </section>
            <section class="generic-page">
                <div class="container">
                    <p>O Parque de Campismo do Festival Crato abre no dia 24 de agosto de 2026.</p>
                    <p>Bilhetes com campismo disponíveis em combinação com o passe de 4 dias.</p>
                </div>
            </section>

        <?php elseif ($activePage === 'todo'): ?>
            <section class="page-hero page-hero--inner">
                <div class="container">
                    <h1 class="page-hero__title">O que Fazer</h1>
                    <p class="page-hero__sub">Artesanato, gastronomia, música e muito mais</p>
                </div>
            </section>
            <section class="generic-page">
                <div class="container">
                    <p>Além dos espetáculos musicais, a FAG oferece exposição de artesanato, degustação de produtos gastronómicos regionais e atividades culturais.</p>
                </div>
            </section>

        <?php elseif ($activePage === 'contacts'): ?>
            <section class="page-hero page-hero--inner">
                <div class="container">
                    <h1 class="page-hero__title">Contactos Úteis</h1>
                </div>
            </section>
            <section class="generic-page">
                <div class="container">
                    <p>Município do Crato</p>
                    <p>Praça do Município, 7430-999 Crato</p>
                    <p>Email: fag@cm-crato.pt | Telf: 245 990 110</p>
                </div>
            </section>

        <?php elseif ($activePage === '404'): ?>
            <section class="page-404">
                <div class="container">
                    <h1>404</h1>
                    <p>Página não encontrada.</p>
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
    <?= Component::render('CheckoutModal') ?>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script src="/themes/<?= htmlspecialchars($themeName) ?>/theme.js"></script>
</body>
</html>
