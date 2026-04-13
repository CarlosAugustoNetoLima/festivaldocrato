<?php
/**
 * Entry Point — Festival Crato
 */

use App\Config\Config;
use App\Helpers\Component;

// Autoloader
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/../app/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

// Carrega configurações
Config::load();

// Roteamento
$request = $_SERVER['REQUEST_URI'];
$path = parse_url($request, PHP_URL_PATH);
$path = rtrim($path, '/') ?: '/';

$routes = Config::get('routes', []);
$activePage = $routes[$path] ?? '404';

// Dados
$events     = [];
$pageConfig = Config::get("pages.$activePage", []);
$artists    = Config::get('artists', []);
$tickets    = Config::get('tickets', []);
$news       = Config::get('news', []);
$products   = Config::get('products', []);

// API desativada por enquanto — usando dados mockados do config
// Para ativar a API, descomente o bloco abaixo:
/*
$apiEvents = $apiService->getEvents(3);
if (!empty($apiEvents)) {
    $news = [];
    foreach ($apiEvents as $event) {
        $eventDate = $event->date_start ?? '';
        $eventName = $event->name ?? 'Evento';
        $eventLocation = ($event->city->name ?? '') . (($event->country->name ?? '') ? ', ' . $event->country->name : '');
        $eventImage = !empty($event->image->name) ? '/themes/crato/img/1200X630_FAG_INS-copiar.jpg' : '';

        $excerpt = "Evento em {$eventLocation}.";
        if (!empty($eventDate)) {
            $dateObj = new DateTime($eventDate);
            $excerpt = "Dia " . $dateObj->format('d') . " de " . $dateObj->format('F') . " de " . $dateObj->format('Y') . " em {$eventLocation}.";
        }

        $news[] = [
            'date'      => substr($eventDate, 0, 10) ?: date('Y-m-d'),
            'label'     => 'Evento',
            'title'     => $eventName,
            'excerpt'   => $excerpt,
            'url'       => $checkoutUrl . ($event->id ?? ''),
            'tag'       => 'Lebillet',
            'image'     => $eventImage,
        ];
    }
}
*/

// API desativada — eventos mockados no config
// Para ativar, implementar chamada cURL aqui

$checkoutUrl = Config::get('api.checkout_url', 'https://checkout.lebillet.eu/');
$pageTitle   = $pageConfig['title'] ?? ucfirst($activePage);
$themeName   = Config::get('theme.name', 'default');
?>
<!DOCTYPE html>
<html lang="<?= Config::get('lang', 'pt') ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars(Config::get('site_name', 'Festival Crato')) ?> — <?= htmlspecialchars($pageTitle) ?></title>
    <meta name="description" content="<?= htmlspecialchars(Config::get('festival.description', 'Festival Crato 2026')) ?>">
    <meta property="og:title" content="40.ª FAG & Festival do Crato 2026">
    <meta property="og:description" content="Feira de Artesanato e Gastronomia e Festival do Crato — 25 a 29 de Agosto de 2026">
    <meta property="og:type" content="website">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" rel="stylesheet">
    <!-- CSS Base -->
    <link rel="stylesheet" href="/assets/css/base.css">
    <link rel="stylesheet" href="/assets/css/components.css">

    <!-- CSS do Tema -->
    <link rel="stylesheet" href="/themes/<?= htmlspecialchars($themeName) ?>/theme.css">

    <!-- Toastify -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
</head>

<body data-page="<?= htmlspecialchars($activePage) ?>">

    <?= Component::renderIfEnabled('Header', 'header', ['activePage' => $activePage]) ?>

    <main class="main-content">

        <?php if ($activePage === 'home'): ?>
            <?= Component::render('Hero') ?>
            <?= Component::render('Lineup', ['artists' => $artists]) ?>
            <?= Component::render('News', ['news' => $news]) ?>
            <?= Component::render('Artists', ['artists' => $artists]) ?>
            <?= Component::render('Tickets', ['tickets' => $tickets, 'checkoutUrl' => $checkoutUrl]) ?>
            <?= Component::render('Store', ['products' => $products, 'checkoutUrl' => $checkoutUrl]) ?>
            <?= Component::render('About') ?>

        <?php elseif ($activePage === 'store'): ?>
            <?= Component::render('Collections', ['products' => $products, 'checkoutUrl' => $checkoutUrl]) ?>

        <?php elseif ($activePage === 'product'): ?>
            <?= Component::render('ProductDetail', ['checkoutUrl' => $checkoutUrl]) ?>

        <?php elseif ($activePage === 'artists'): ?>
            <section class="page-hero page-hero--inner">
                <div class="container">
                    <h1 class="page-hero__title">Artistas</h1>
                </div>
            </section>
            <?= Component::render('Artists', ['artists' => $artists, 'showAll' => true]) ?>

        <?php elseif ($activePage === 'tickets'): ?>
            <section class="page-hero page-hero--inner">
                <div class="container">
                    <h1 class="page-hero__title">Bilhetes</h1>
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

        <?php elseif ($activePage === 'info'): ?>
            <section class="page-hero page-hero--inner">
                <div class="container">
                    <h1 class="page-hero__title">Informações</h1>
                </div>
            </section>
            <?= Component::render('About', ['showFull' => true]) ?>

        <?php elseif ($activePage === 'about'): ?>
            <section class="page-hero page-hero--inner">
                <div class="container">
                    <h1 class="page-hero__title">Sobre o Festival</h1>
                    <p class="page-hero__sub">40.ª Feira de Artesanato e Gastronomia e Festival do Crato</p>
                </div>
            </section>
            <?= Component::render('About', ['showFull' => true]) ?>

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

        <?php elseif ($activePage === 'news'): ?>
            <section class="page-hero page-hero--inner">
                <div class="container">
                    <h1 class="page-hero__title">Notícias</h1>
                </div>
            </section>
            <?= Component::render('News', ['news' => $news]) ?>

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

    <?= Component::renderIfEnabled('Footer', 'footer') ?>

    <!-- Modais -->
    <?php if (Config::get('components.checkout', true)): ?>
        <?= Component::render('CheckoutModal', ['checkoutUrl' => $checkoutUrl]) ?>
    <?php endif; ?>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script src="/themes/<?= htmlspecialchars($themeName) ?>/theme.js"></script>
</body>
</html>
