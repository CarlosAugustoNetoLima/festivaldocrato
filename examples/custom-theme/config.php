<?php
/**
 * Exemplo de configuração para um tema customizado
 *
 * Coloque isso em config/site.php
 */

return [
    'site_id' => 'artista-musical',
    'site_name' => 'Artista Musical',
    'site_url' => 'https://artistamusical.com',

    'api' => [
        'base_url' => 'https://lebillet.eu',
        'events_endpoint' => '/api_events/events',
        'checkout_url' => 'https://checkout.lebillet.eu/',
        'auth_token' => 'Basic SEU_TOKEN_AQUI',
        'default_limit' => 6,
    ],

    'theme' => [
        'name' => 'artista-theme',
        'primary_color' => '#1a1a1a',
        'secondary_color' => '#f5f5f5',
        'accent_color' => '#e91e63',
        'font_family' => 'Inter, sans-serif',
    ],

    'cart' => [
        'storage_key' => 'artista_cart_items',
        'operation_fee' => 2.05,
        'currency' => '€',
        'currency_position' => 'before',
    ],

    'routes' => [
        '/' => 'home',
        '/dates' => 'dates',
        '/biographie' => 'biography',
        '/musique' => 'music',
        '/contact' => 'contact',
    ],

    'pages' => [
        'home' => [
            'title' => 'Accueil',
            'show_events' => true,
            'events_limit' => 6,
            'hero_component' => 'Hero',
            'hero_props' => [
                'title' => 'NOUVELLE TOURNÉE',
                'subtitle' => '2024 - 2025',
                'background' => '/themes/artista-theme/img/hero-bg.jpg',
            ],
        ],
        'dates' => [
            'title' => 'Toutes les Dates',
            'show_events' => true,
            'events_limit' => null,
        ],
    ],

    'components' => [
        'header' => true,
        'footer' => true,
        'cart' => true,
        'checkout' => true,
        'events_section' => true,
    ],
];
