<?php
/**
 * Configuração do Site — Festival Crato
 */

return [
    'site_id'   => 'festival-crato',
    'site_name' => 'Festival Crato',
    'site_url'  => 'http://localhost:8000',
    'lang'      => 'pt',

    // Configurações da API Lebillet
    'api' => [
        'base_url'         => 'https://lebillet.eu',
        'events_endpoint'  => '/api_events/events',
        'checkout_url'     => 'https://checkout.lebillet.eu/',
        'auth_token'       => 'Basic 9f4c2a1b7e3d6a8cpewe8992801',
        'default_limit'    => 6,
    ],

    // Tema
    'theme' => [
        'name'            => 'crato',
        'primary_color'   => '#C8111F',
        'secondary_color' => '#E01828',
        'font_family'     => 'Inter, sans-serif',
    ],

    // Carrinho
    'cart' => [
        'storage_key'      => 'crato_cart',
        'operation_fee'    => 1.50,
        'currency'         => '€',
        'currency_position'=> 'after',
    ],

    // Rotas — estrutura baseada no site oficial
    'routes' => [
        '/'              => 'home',
        '/bilheteira'    => 'tickets',
        '/sobre'         => 'about',
        '/como-chegar'   => 'directions',
        '/campismo'      => 'camping',
        '/o-que-fazer'   => 'todo',
        '/contactos'     => 'contacts',
        '/noticias'      => 'news',
        '/pesquisa'      => 'search',
        // Rotas antigas (redirect/legacy)
        '/artistas'      => 'artists',
        '/bilhetes'      => 'tickets',
        '/info'          => 'info',
        '/lineup'        => 'lineup',
        // Style Guide (acesso direto, sem menu)
        '/style-guide'   => 'style-guide',
    ],

    // Páginas
    'pages' => [
        'home' => [
            'title'        => 'Festival Crato 2026',
            'show_events'  => false,
            'events_limit' => 6,
        ],
        'tickets' => [
            'title'       => 'Bilheteira',
            'show_events' => false,
        ],
        'about' => [
            'title'       => 'Sobre o Festival',
            'show_events' => false,
        ],
        'directions' => [
            'title'       => 'Como Chegar',
            'show_events' => false,
        ],
        'camping' => [
            'title'       => 'Campismo',
            'show_events' => false,
        ],
        'todo' => [
            'title'       => 'O que Fazer',
            'show_events' => false,
        ],
        'contacts' => [
            'title'       => 'Contactos Úteis',
            'show_events' => false,
        ],
        'news' => [
            'title'       => 'Notícias',
            'show_events' => false,
        ],
        'search' => [
            'title'       => 'Pesquisa',
            'show_events' => false,
        ],
        // Legacy pages
        'artists' => [
            'title'       => 'Artistas',
            'show_events' => false,
        ],
        'info' => [
            'title'       => 'Informações',
            'show_events' => false,
        ],
        'lineup' => [
            'title'       => 'Programação',
            'show_events' => false,
        ],
        'style-guide' => [
            'title'       => 'Style Guide',
            'show_events' => false,
        ],
    ],

    // Componentes habilitados
    'components' => [
        'header'         => true,
        'footer'         => true,
        'cart'           => true,
        'checkout'       => true,
        'events_section' => false,
    ],

    // Dados do festival
    'festival' => [
        'edition'                => '40.ª',
        'date_start'             => '2026-08-25',
        'date_end'               => '2026-08-29',
        'date_fag_start'         => '2026-08-25',
        'date_festival_start'    => '2026-08-26',
        'date_campista'          => '2026-08-24',
        'location'               => 'Crato, Alto Alentejo',
        'venue'                  => 'Vila do Crato',
        'organizer'              => 'Município do Crato',
        'description'            => 'A 40.ª Feira de Artesanato e Gastronomia e o Festival do Crato regressam de 25 a 29 de agosto de 2026 para celebrar o artesanato, a gastronomia e a identidade cultural do Alto Alentejo.',
        'mission'                => 'Promover e preservar o artesanato e a gastronomia enquanto valores culturais e fatores de dinamização da atividade económica, visando a promoção do turismo e da identidade cultural local.',
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
    ],

    // Notícias / Anúncios
    'news' => [
        [
            'date'     => '2026-04-01',
            'label'    => '40.ª FAG 2026',
            'title'    => 'Inscrições para a Feira de Artesanato e Gastronomia 2026 já disponíveis!',
            'excerpt'  => 'As candidaturas para participar na 40.ª FAG estão abertas. Prazo de inscrição: 19 de junho de 2026. Podem candidatar-se expositores individuais ou coletivos, do setor público ou privado.',
            'url'      => 'https://festivaldocrato.cm-crato.pt/',
            'tag'      => 'FAG',
            'image'    => '/themes/crato/img/1200X630_FAG_INS-copiar.jpg',
        ],
        [
            'date'     => '2026-03-02',
            'label'    => 'Primeiro Artista Confirmado',
            'title'    => 'Buba Espinho é o primeiro artista confirmado para o Festival do Crato 2026!',
            'excerpt'  => 'Uma das vozes mais distintivas da nova geração da música portuguesa, Buba Espinho sobe ao palco do Festival do Crato 2026 no dia 29 de agosto.',
            'url'      => 'https://festivaldocrato.cm-crato.pt/',
            'tag'      => 'Artistas',
            'image'    => '/themes/crato/img/1200X630_buba.png',
        ],
        [
            'date'     => '2026-01-21',
            'label'    => 'Datas Confirmadas',
            'title'    => 'Festival do Crato celebra mais uma edição no último fim de semana de agosto',
            'excerpt'  => 'O Festival do Crato 2026 está confirmado para 26, 27, 28 e 29 de agosto. A 40.ª Feira de Artesanato e Gastronomia decorre de 25 a 29 de agosto, com acesso gratuito ao público.',
            'url'      => 'https://festivaldocrato.cm-crato.pt/',
            'tag'      => 'Festival',
            'image'    => '/themes/crato/img/01.jpg',
        ],
    ],

    // Artistas / Programação
    'artists' => [
        [
            'name'     => 'A Anunciar',
            'day'      => 1,
            'stage'    => 'Palco FAG',
            'headliner'=> true,
            'genre'    => 'Música Portuguesa',
            'image'    => '/themes/crato/img/artist-1.jpg',
        ],
        [
            'name'     => 'A Anunciar',
            'day'      => 1,
            'stage'    => 'Palco FAG',
            'headliner'=> false,
            'genre'    => 'Folk / Tradicional',
            'image'    => '/themes/crato/img/artist-2.jpg',
        ],
        [
            'name'     => 'A Anunciar',
            'day'      => 2,
            'stage'    => 'Palco Festival',
            'headliner'=> true,
            'genre'    => 'Música Portuguesa',
            'image'    => '/themes/crato/img/artist-3.jpg',
        ],
        [
            'name'     => 'A Anunciar',
            'day'      => 2,
            'stage'    => 'Palco Festival',
            'headliner'=> false,
            'genre'    => 'Pop / Rock',
            'image'    => '/themes/crato/img/artist-4.jpg',
        ],
        [
            'name'     => 'A Anunciar',
            'day'      => 3,
            'stage'    => 'Palco Festival',
            'headliner'=> true,
            'genre'    => 'Música Portuguesa',
            'image'    => '/themes/crato/img/artist-5.jpg',
        ],
        [
            'name'     => 'A Anunciar',
            'day'      => 3,
            'stage'    => 'Palco FAG',
            'headliner'=> false,
            'genre'    => 'Fado / Tradicional',
            'image'    => '/themes/crato/img/artist-6.jpg',
        ],
        [
            'name'     => 'Buba Espinho',
            'day'      => 4,
            'stage'    => 'Palco Festival',
            'headliner'=> true,
            'genre'    => 'Música Portuguesa',
            'image'    => '/themes/crato/img/artist-7.jpg',
            'confirmed'=> true,
        ],
        [
            'name'     => 'A Anunciar',
            'day'      => 4,
            'stage'    => 'Palco FAG',
            'headliner'=> false,
            'genre'    => 'Folk / World Music',
            'image'    => '/themes/crato/img/artist-8.jpg',
        ],
    ],

    // Bilhetes
    // Preços com duas fases: até 31 julho (price) e a partir de 1 agosto (price_late)
    // Acesso à FAG é GRATUITO (ponto 10.1 do normativo)
    // Crianças até 11 anos: gratuitas acompanhadas de adulto (ponto 10.3)
    'tickets' => [
        [
            'id'          => 'passe-4dias',
            'name'        => 'Passe 4 Dias',
            'subtitle'    => '26–29 Agosto · Sem Campismo',
            'price'       => 45.00,
            'price_late'  => 50.00,
            'description' => 'Acesso completo aos 4 dias do Festival do Crato 2026. A partir de 1 de agosto: 50€. Não inclui acesso ao Concerto Solidário nem ao campismo.',
            'highlight'   => true,
            'event_id'    => 'crato-2026-passe',
        ],
        [
            'id'          => 'passe-4dias-campismo',
            'name'        => 'Passe 4 Dias + Campismo',
            'subtitle'    => '26–29 Agosto · Com Campismo',
            'price'       => 60.00,
            'price_late'  => 70.00,
            'description' => 'Acesso completo aos 4 dias do Festival do Crato 2026 com direito a campismo. A partir de 1 de agosto: 70€. Não inclui acesso ao Concerto Solidário.',
            'highlight'   => false,
            'event_id'    => 'crato-2026-passe-campismo',
        ],
        [
            'id'          => 'dia-26',
            'name'        => 'Bilhete Dia 26',
            'subtitle'    => '26 Agosto · Quarta-feira',
            'price'       => 15.00,
            'price_late'  => 20.00,
            'description' => 'Acesso ao Festival do Crato — 1.º dia. A partir de 1 de agosto: 20€.',
            'highlight'   => false,
            'event_id'    => 'crato-2026-dia1',
        ],
        [
            'id'          => 'dia-27',
            'name'        => 'Bilhete Dia 27',
            'subtitle'    => '27 Agosto · Quinta-feira',
            'price'       => 15.00,
            'price_late'  => 20.00,
            'description' => 'Acesso ao Festival do Crato — 2.º dia. A partir de 1 de agosto: 20€.',
            'highlight'   => false,
            'event_id'    => 'crato-2026-dia2',
        ],
        [
            'id'          => 'dia-28',
            'name'        => 'Bilhete Dia 28',
            'subtitle'    => '28 Agosto · Sexta-feira',
            'price'       => 20.00,
            'price_late'  => 25.00,
            'description' => 'Acesso ao Festival do Crato — 3.º dia. A partir de 1 de agosto: 25€.',
            'highlight'   => false,
            'event_id'    => 'crato-2026-dia3',
        ],
        [
            'id'          => 'dia-29',
            'name'        => 'Bilhete Dia 29',
            'subtitle'    => '29 Agosto · Sábado',
            'price'       => 20.00,
            'price_late'  => 25.00,
            'description' => 'Acesso ao Festival do Crato — dia final. A partir de 1 de agosto: 25€.',
            'highlight'   => false,
            'event_id'    => 'crato-2026-dia4',
        ],
        [
            'id'          => 'concerto-solidario',
            'name'        => 'Concerto Solidário',
            'subtitle'    => '25 Agosto · Terça-feira',
            'price'       => 10.00,
            'price_late'  => 10.00,
            'description' => 'Concerto Solidário no Palco FAG. Sem descontos aplicáveis. O passe 4 dias não dá acesso a este concerto.',
            'highlight'   => false,
            'event_id'    => 'crato-2026-solidario',
        ],
    ],
];

