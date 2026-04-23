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
    'edition' => '40.ª',
    'date_start' => '2026-08-25',
    'date_end' => '2026-08-29',
    'date_festival_start' => '2026-08-26',
    'date_campista' => '2026-08-24',
    'location' => 'Crato, Alto Alentejo',
    'venue' => 'Vila do Crato',
    'organizer' => 'Município do Crato',
    'description' => 'A 40.ª Feira de Artesanato e Gastronomia e o Festival do Crato regressam de 25 a 29 de agosto de 2026.',
    'mission' => 'Promover e preservar o artesanato e a gastronomia enquanto valores culturais.',
    'contact' => [
        'email' => 'fag@cm-crato.pt',
        'phone' => '245 990 110',
        'address' => 'Praça do Município',
        'zip' => '7430-999 Crato',
    ],
    'social' => [
        'instagram' => 'https://instagram.com/',
        'facebook' => 'https://facebook.com/',
        'twitter' => 'https://twitter.com/',
        'youtube' => 'https://youtube.com/',
    ],
];

$artists = [
    ['name' => 'Bispo', 'day' => 1, 'stage' => 'Palco Festival', 'headliner' => true, 'genre' => 'Rap / Hip-Hop', 'image' => '/assets/img/26_agosto.jpeg', 'confirmed' => true],
    ['name' => 'Calema', 'day' => 3, 'stage' => 'Palco Festival', 'headliner' => true, 'genre' => 'R&B / Pop', 'image' => '/assets/img/28_agosto.jpeg', 'confirmed' => true],
    ['name' => 'Buba Espinho', 'day' => 4, 'stage' => 'Palco Festival', 'headliner' => true, 'genre' => 'Música Portuguesa', 'image' => '/assets/img/29_agosto.jpeg', 'confirmed' => true],
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
        'date' => '2026-04-23',
        'tag' => 'Artistas',
        'title' => 'Bispo e Calema confirmados no Festival do Crato 2026',
        'excerpt' => 'Artistas juntam-se ao já anunciado Buba Espinho & Convidados. O Festival está de regresso à vila alentejana de 26 a 29 de agosto!',
        'url' => '/noticias/bispo-calema',
        'image' => '/assets/img/anuncio.jpeg',
    ],
];

// ─────────────────────────────────────────────
//  Routing
// ─────────────────────────────────────────────
$request = $_SERVER['REQUEST_URI'];
$path = rtrim(parse_url($request, PHP_URL_PATH), '/') ?: '/';

$routes = [
    '/' => 'home',
    '/bilheteira' => 'tickets',
    '/bilhetes' => 'tickets',
    '/lineup' => 'lineup',
    '/sobre' => 'about',
    '/como-chegar' => 'directions',
    '/campismo' => 'camping',
    '/o-que-fazer' => 'todo',
    '/contactos' => 'contacts',
    '/noticias' => 'news',
    '/noticias/bispo-calema' => 'news_bispo_calema',
    '/artistas' => 'artists',
    '/info' => 'info',
    '/loja' => 'store',
    '/produto' => 'product',
    '/pesquisa' => 'search',
];

$activePage = $routes[$path] ?? '404';

$pageTitles = [
    'home' => 'Festival Crato 2026',
    'tickets' => 'Bilheteira',
    'lineup' => 'Programação',
    'about' => 'Sobre o Festival',
    'directions' => 'Como Chegar',
    'camping' => 'Campismo',
    'todo' => 'O que Fazer',
    'contacts' => 'Contactos',
    'news' => 'Notícias',
    'artists' => 'Artistas',
    'info' => 'Informações',
    'store' => 'Loja',
    'product' => 'Produto',
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
        content="40.ª Feira de Artesanato e Gastronomia e Festival do Crato — 25 a 29 de Agosto de 2026">
    <meta property="og:title" content="40.ª FAG & Festival do Crato 2026">
    <meta property="og:description"
        content="Feira de Artesanato e Gastronomia e Festival do Crato — 25 a 29 de Agosto de 2026">
    <meta property="og:type" content="website">
    <link rel="icon" href="/assets/img/favicon.ico" sizes="any">
    <link rel="icon" href="/assets/img/favicon-32x32.png" type="image/png" sizes="32x32">
    <link rel="apple-touch-icon" href="/assets/img/apple-touch-icon.png">

    <!-- Google Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0"
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
            <?= Component::render('Lineup', ['artists' => $artists]) ?>
            <?= Component::render('News', ['news' => $news]) ?>
            <?= Component::render('Artists', ['artists' => $artists]) ?>
            <?= Component::render('Tickets', ['tickets' => $tickets, 'events' => $eventsLimit, 'checkoutUrl' => $checkoutUrl]) ?>
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
                    <h2>Transportes Públicos</h2>
                    <p>Comboio até Portalegre ou Elvas, seguido de autocarro ou táxi.</p>
                    <h2>Estacionamento</h2>
                    <p>Parques de estacionamento disponíveis junto ao recinto.</p>
                </div>
            </section>

        <?php elseif ($activePage === 'camping'): ?>
            <section class="page-hero page-hero--inner">
                <div class="container">
                    <h1 class="page-hero__title">Campismo</h1>
                    <p class="page-hero__sub">Vive o Festival do Crato até ao último momento</p>
                </div>
            </section>
            <section class="camping-page">
                <div class="container">

                    <p class="camping-intro">O Parque de Campismo do Festival do Crato é a solução ideal para quem pretende aproveitar ao máximo todos os dias do evento, com conforto, segurança e um ambiente de convívio entre festivaleiros. A pensar na comodidade dos visitantes, a organização disponibiliza uma zona de camping ocasional equipada com as condições essenciais para uma estadia tranquila, permitindo viver o festival de forma prática e próxima de toda a animação.</p>

                    <div class="camping-checkin">
                        <div class="camping-checkin__item">
                            <span class="camping-checkin__label">Check-in</span>
                            <span class="camping-checkin__value">23 de agosto, a partir das 10h00</span>
                            <small>Acesso reservado a portadores do Passe 4 Dias com Camping Ocasional</small>
                        </div>
                        <div class="camping-checkin__item">
                            <span class="camping-checkin__label">Check-out</span>
                            <span class="camping-checkin__value">30 de agosto, até às 18h00</span>
                        </div>
                    </div>

                    <h2 class="camping-section-title">Condições e Serviços Disponíveis</h2>
                    <p>A zona de campismo está equipada com um conjunto de infraestruturas e serviços que visam garantir conforto, segurança e bem-estar a todos os utilizadores:</p>
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

                    <div class="camping-two-cols">
                        <div>
                            <h2 class="camping-section-title">Regras de Utilização</h2>
                            <p>Para garantir a segurança e o bom funcionamento da zona de campismo, devem ser respeitadas as seguintes normas.</p>
                            <p><strong>É proibido:</strong></p>
                            <ul class="camping-list camping-list--rules">
                                <li>Fazer fogueiras</li>
                                <li>Utilizar garrafas, vasilhame ou utensílios em vidro</li>
                                <li>Deitar lixo para o chão</li>
                                <li>Montar tendas em acessos reservados a viaturas de emergência</li>
                                <li>Delimitar ou reservar espaço de forma abusiva</li>
                                <li>A entrada de animais, exceto cães guia</li>
                            </ul>
                            <p class="camping-warning">O incumprimento das normas poderá implicar a perda do direito de acesso ao parque de campismo e ao Festival.</p>
                        </div>
                        <div>
                            <h2 class="camping-section-title">O Que Levar</h2>
                            <ul class="camping-list camping-list--pack">
                                <li>Tenda e material de campismo</li>
                                <li>Saco-cama ou colchão insuflável</li>
                                <li>Roupa adequada às condições climatéricas</li>
                                <li>Produtos de higiene pessoal</li>
                                <li>Lanterna ou iluminação portátil</li>
                                <li>Protetor solar</li>
                            </ul>
                        </div>
                    </div>

                    <h2 class="camping-section-title">Informações Importantes</h2>
                    <ul class="camping-list">
                        <li>A zona de campismo dispõe de sistema de videovigilância</li>
                        <li>Recomenda-se a vigilância permanente de crianças</li>
                        <li>Cada utilizador é responsável pelos seus bens pessoais</li>
                        <li>A organização reserva-se o direito de aplicar medidas necessárias para garantir a segurança de todos</li>
                    </ul>

                    <h2 class="camping-section-title">Localização do Campismo</h2>
                    <a href="https://maps.app.goo.gl/zKYtMe21nYnLhKzE9" target="_blank" rel="noopener" class="btn btn-primary camping-map-btn">
                        <span class="material-symbols-outlined">location_on</span>
                        Ver localização no mapa
                    </a>

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
                    <p>Além dos espetáculos musicais, a FAG oferece exposição de artesanato, degustação de produtos
                        gastronómicos regionais e atividades culturais.</p>
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
                    <p>Email: <a href="mailto:fag@cm-crato.pt">fag@cm-crato.pt</a> | Telf: <a href="tel:+351245990110">245 990 110</a></p>
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

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script src="/assets/js/theme.js"></script>
</body>

</html>