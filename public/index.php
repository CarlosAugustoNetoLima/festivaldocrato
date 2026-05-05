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
    'date_start' => '2026-08-26',
    'date_end' => '2026-08-29',
    'date_festival_start' => '2026-08-26',
    'date_campista' => '2026-08-24',
    'location' => 'Crato, Alto Alentejo',
    'venue' => 'Vila do Crato',
    'organizer' => 'Festival do Crato',
    'description' => 'A 40.ª Feira de Artesanato e Gastronomia e o Festival do Crato regressam de 26 a 29 de agosto de 2026.',
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
    ['name' => 'Bispo', 'day' => 1, 'stage' => 'Palco Festival', 'headliner' => true, 'genre' => 'Rap / Hip-Hop', 'image' => '/assets/img/artists/POST_bispo-_website.webp', 'confirmed' => true],
    ['name' => 'Calema', 'day' => 3, 'stage' => 'Palco Festival', 'headliner' => true, 'genre' => 'R&B / Pop', 'image' => '/assets/img/artists/POST_calema_website.webp', 'confirmed' => true],
    ['name' => 'Buba Espinho', 'day' => 4, 'stage' => 'Palco Festival', 'headliner' => true, 'genre' => 'Música Portuguesa', 'image' => '/assets/img/artists/POST_buba_website.webp', 'confirmed' => true],
    ['name' => 'A Anunciar', 'day' => 5, 'stage' => 'Palco Festival', 'headliner' => true, 'genre' => '', 'image' => '/assets/img/artists/WEBSITE_anunciar.webp', 'confirmed' => false, 'announced' => false],
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
    '/politica-privacidade' => 'legal_privacy',
    '/cookies' => 'legal_cookies',
    '/termos' => 'legal_terms',
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
        content="40.ª Feira de Artesanato e Gastronomia e Festival do Crato — 26 a 29 de Agosto de 2026">
    <meta property="og:title" content="40.ª FAG & Festival do Crato 2026">
    <meta property="og:description"
        content="Feira de Artesanato e Gastronomia e Festival do Crato — 26 a 29 de Agosto de 2026">
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
            <?= Component::render('Lineup', ['artists' => $artists]) ?>
            <?= Component::render('News', ['news' => $news]) ?>
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

            <?= Component::render('About', ['festival' => $festival, 'showFull' => true]) ?>

        <?php elseif ($activePage === 'directions'): ?>
            <?= Component::render('PageHeader', [
                'label' => 'Festival Crato 2026',
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
                'label' => 'Festival Crato 2026',
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
                'label' => 'Festival Crato 2026',
                'title' => 'O que',
                'accent' => 'Fazer',
                'subtitle' => 'Artesanato, gastronomia, música e muito mais.',
            ]) ?>
            <section class="generic-page">
                <div class="container">
                    <p>Além dos espetáculos musicais, a FAG oferece exposição de artesanato, degustação de produtos
                        gastronómicos regionais e atividades culturais.</p>
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