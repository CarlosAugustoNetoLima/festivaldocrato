<?php
/**
 * Proxy do checkout LeBillet — versão completa com relay de API
 *
 * GET /checkout-proxy.php?event_id=1830&product_id=11022  → carrega checkout filtrado
 * POST /checkout-proxy.php?relay=1&path=1830/cart/resume  → relay de chamadas AJAX
 */

$eventId = preg_replace('/\D/', '', $_GET['event_id'] ?? '1830');
$productId = preg_replace('/\D/', '', $_GET['product_id'] ?? '');

// ─── MODO RELAY: reencaminhar chamadas AJAX do checkout.js ────────────────────
if (isset($_GET['relay'])) {
    $path = trim($_GET['path'] ?? '', '/');

    // Validar que o path só contém caracteres seguros e aponta para a LeBillet
    if (!preg_match('/^[\w\-\/\.]+$/', $path)) {
        http_response_code(400);
        exit('{"error":"invalid path"}');
    }

    $apiUrl = 'https://checkout.lebillet.eu/' . $path;
    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    // Apenas GET e POST são permitidos no relay
    if (!in_array($method, ['GET', 'POST'], true)) {
        http_response_code(405);
        exit('{"error":"method not allowed"}');
    }
    $rawBody = file_get_contents('php://input');
    if (empty($rawBody) && $method === 'POST') {
        $rawBody = http_build_query($_POST);
    }

    // Content-Type da requisição original — whitelist para evitar injeção de headers
    $rawCt = $_SERVER['CONTENT_TYPE'] ?? '';
    $allowedCts = ['application/json', 'application/x-www-form-urlencoded', 'multipart/form-data', 'text/plain'];
    $ct = 'application/x-www-form-urlencoded';
    foreach ($allowedCts as $allowed) {
        if (stripos($rawCt, $allowed) === 0) { $ct = $allowed; break; }
    }

    // Extrair o SID do POST body (o checkout.js envia sid=XXXX no body)
    // e usá-lo como PHPSESSID no cookie para o LeBillet
    $sidFromBody = '';
    if ($rawBody) {
        parse_str($rawBody, $parsedBody);
        // Sanitizar SID: apenas alfanuméricos
        $sidFromBody = preg_replace('/[^a-zA-Z0-9]/', '', $parsedBody['sid'] ?? '');
    }

    $reqHeaders = [
        'Content-Type: ' . $ct,
        'User-Agent: Mozilla/5.0',
        'Accept: */*',
        'X-Requested-With: XMLHttpRequest',
        'Referer: https://checkout.lebillet.eu/' . $eventId,
        'Origin: https://checkout.lebillet.eu',
    ];

    // Construir cookie a enviar ao LeBillet
    $cookieParts = [];
    if ($sidFromBody) {
        // O SID no body É o PHPSESSID do LeBillet
        $cookieParts[] = 'PHPSESSID=' . rawurlencode($sidFromBody);
    }
    // Reencaminhar cookies da LeBillet (definidos pelo proxy via Set-Cookie),
    // excluindo cookies da aplicação própria para não os expor a terceiros
    $ownCookies = ['PHPSESSID', 'crato_cart_count', 'crato_session'];
    foreach ($_COOKIE as $k => $v) {
        if (!in_array($k, $ownCookies, true)) {
            $cookieParts[] = rawurlencode($k) . '=' . rawurlencode($v);
        }
    }
    if (!empty($cookieParts)) {
        $reqHeaders[] = 'Cookie: ' . implode('; ', $cookieParts);
    }

    $opts = [
        'http' => [
            'method' => $method,
            'header' => implode("\r\n", $reqHeaders),
            'content' => $rawBody,
            'timeout' => 10,
            'follow_location' => 1,
            'max_redirects' => 3,
        ],
        // NOTA: Manter verify_peer false apenas para ambiente de dev/proxy local
        // Em produção com certificado válido no servidor, ativar para true
        'ssl' => ['verify_peer' => false, 'verify_peer_name' => false],
    ];

    $result = @file_get_contents($apiUrl, false, stream_context_create($opts));

    // Reencaminhar Set-Cookie da resposta
    foreach ($http_response_header ?? [] as $h) {
        if (stripos($h, 'Set-Cookie:') === 0) {
            // Remover flags de domínio/secure para funcionar no localhost
            $h = preg_replace('/;\s*domain=[^;]+/i', '', $h);
            $h = preg_replace('/;\s*secure\b/i', '', $h);
            $h = preg_replace('/;\s*samesite=[^;]+/i', '; SameSite=Lax', $h);
            header($h, false);
        }
        if (stripos($h, 'Content-Type:') === 0) {
            header($h);
        }
    }

    // CORS: só permite origem do próprio domínio do site
    $requestOrigin = $_SERVER['HTTP_ORIGIN'] ?? '';
    $siteDomain    = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost');
    if (empty($requestOrigin) || $requestOrigin === $siteDomain) {
        header('Access-Control-Allow-Origin: ' . $siteDomain);
    }
    header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');
    header('X-Content-Type-Options: nosniff');

    echo $result !== false ? $result : '{"error":"relay failed"}';
    exit;
}

// ─── MODO NORMAL: carregar HTML do checkout ──────────────────────────────────
if (empty($eventId)) {
    http_response_code(400);
    exit('Parametro event_id em falta.');
}

$checkoutUrl = 'https://checkout.lebillet.eu/' . $eventId;

$opts = [
    'http' => [
        'method' => 'GET',
        'timeout' => 15,
        'follow_location' => 1,
        'header' =>
            "User-Agent: Mozilla/5.0\r\n" .
            "Accept: text/html\r\n" .
            "Accept-Language: pt-PT,pt;q=0.9\r\n",
    ],
    'ssl' => ['verify_peer' => false, 'verify_peer_name' => false],
];

$html = @file_get_contents($checkoutUrl, false, stream_context_create($opts));

if ($html === false) {
    http_response_code(502);
    exit('Nao foi possivel carregar o checkout.');
}

// Repassar cookies de sessão do LeBillet para o browser
foreach ($http_response_header ?? [] as $h) {
    if (stripos($h, 'Set-Cookie:') === 0) {
        $h = preg_replace('/;\s*domain=[^;]+/i', '', $h);
        $h = preg_replace('/;\s*secure\b/i', '', $h);
        $h = preg_replace('/;\s*samesite=[^;]+/i', '; SameSite=Lax', $h);
        header($h, false);
    }
}

// ─── Remover atributos crossorigin e integrity ────────────────────────────────
// Isto evita que o browser bloqueie os ficheiros CSS nativos da LeBillet devido a CORS
$html = preg_replace('/\s+crossorigin=(["\'])[^"\']*\1/i', '', $html);
$html = preg_replace('/\s+integrity=(["\'])[^"\']*\1/i', '', $html);

// ─── Reescrever URLs relativas → absolutas ───────────────────────────────────
$base = 'https://checkout.lebillet.eu';

$html = preg_replace_callback(
    '/\b(src|href)\s*=\s*(["\'])(?!https?:\/\/|\/\/|#|javascript:|data:|mailto:)([^"\']+)\2/i',
    function ($m) use ($base) {
        return $m[1] . '=' . $m[2] . $base . '/' . ltrim($m[3], '/') . $m[2];
    },
    $html
);

$html = preg_replace_callback(
    '/\baction\s*=\s*(["\'])(?!https?:\/\/|\/\/)([^"\']+)\1/i',
    function ($m) use ($base) {
        return 'action=' . $m[1] . $base . '/' . ltrim($m[2], '/') . $m[1];
    },
    $html
);

// ─── Redirecionar BASE_URL para o nosso proxy relay ──────────────────────────
// O checkout.js usa: $.post(BASE_URL + ID_EVENT + "/cart/resume", ...)
// Vamos interceptar BASE_URL para apontar para /checkout-proxy.php?relay=1&path=
$relayBase = '/checkout-proxy.php?event_id=' . $eventId . '&relay=1&path=';

$html = str_replace(
    'var BASE_URL = "https://checkout.lebillet.eu/";',
    'var BASE_URL = "' . $relayBase . '";',
    $html
);

// ─── Injetar Cores Dark Crato ───────────────────────────────────────────────
$colorFix = '
<style id="proxy-color-fix">
/* Fundos Globais -> Dark Crato (#0C0306 ou #180508) */
html, body, .main-panel, .wrapper, 
#tickets-container, .calema-section, div.bg-blue, div.bg-event, 
div.cart-sidebar, div.map-card, .ticket-box, .modal-content, 
.card, .card-body, .content{
    background-color: #0C0306 !important;
    background-image: none !important;
    color: #FFFFFF !important;
    border-color: rgba(255, 255, 255, 0.08) !important;
    box-shadow: none !important;
}

/* Tabelas */
table, .table, table th, table td, .table-borderless th, .table-borderless td {
    background: transparent !important;
    color: #FFFFFF !important;
    border-color: rgba(255, 255, 255, 0.1) !important;
}

/* Campos de Input -> Fundo Escuro (#180508) */
input, select, textarea, .form-control {
    background-color: #180508 !important;
    color: #FFFFFF !important;
    border: 1px solid rgba(255, 255, 255, 0.2) !important;
}
.form-control:focus {
    background-color: #200608 !important;
    color: #FFFFFF !important;
    border-color: #C8111F !important;
    box-shadow: 0 0 0 0.2rem rgba(200, 17, 31, 0.25) !important;
}

/* Textos descritivos e links suaves */
.text-muted, small, .price, p, .subtitle {
    color: rgba(255, 255, 255, 0.6) !important;
}

/* Títulos */
h1, h2, h3, h4, h5, h6 {
    color: #FFFFFF !important;
}

/* Botões Azuis -> Vermelho Crato (#C8111F) */
.btn-primary, .btn-info, button#button-delivery, button#button-products {
    background: linear-gradient(135deg, #C8111F 0%, #E01828 100%) !important;
    border: none !important;
    color: #FFFFFF !important;
}
.btn-outline-success {
    background: transparent !important;
    border: 1px solid #C8111F !important;
    color: #C8111F !important;
}
.btn-outline-success:hover {
    background: #C8111F !important;
    color: #FFFFFF !important;
}

/* Campo de quantidade */
.quantity-select {
    border: none !important;
    background-color: transparent !important;
    outline: none !important;
}

/* Botões + e - (Quantidade) */
.button-select-product {
    background-color: #180508 !important;
    border: 1px solid rgba(255, 255, 255, 0.2) !important;
    color: #FFFFFF !important;
    text-decoration: none !important;
}
.button-select-product:hover {
    background-color: #C8111F !important;
    border-color: #C8111F !important;
    color: #FFFFFF !important;
}

/* Botão Código Promocional */
button#promo-btn {
    border: 1px dashed rgba(255, 255, 255, 0.4) !important;
    color: #FFFFFF !important;
    background-color: transparent !important;
}
button#promo-btn:hover {
    border-color: #C8111F !important;
    color: #C8111F !important;
    background-color: rgba(200, 17, 31, 0.1) !important;
}


/* Ocultar as bolhas azuis que ficam flutuando no background da LeBillet */
.blob, .blob1, .blob2 {
    display: none !important;
}

/* Ícone de SVG de Carrinho ou afins */
svg {
    stroke: #FFFFFF !important;
}
</style>
';

// Ocultar produtos iniciais se houver product_id (para evitar FOUC)
if ($productId !== '') {
    $colorFix .= '
<style id="proxy-filter-fix">
table.ticket-box, .ticket-box { display: none !important; }
</style>';
}

$filterScript = '';
if ($productId !== '') {
    $filterScript = '
<script id="proxy-filter-js">
(function(){
    var pid = "' . $productId . '";
    window._cratoFilterPid = pid;

    var _originalAddProducts;
    function hookAddProducts() {
        if (typeof window.addProducts === "function" && !window._proxyFilterHooked) {
            _originalAddProducts = window.addProducts;
            window.addProducts = function() {
                _originalAddProducts.apply(this, arguments);
                setTimeout(function(){ applyProductFilter(window._cratoFilterPid); }, 60);
            };
            window._proxyFilterHooked = true;
            applyProductFilter(pid);
        }
    }

    var filterTimeout;
    new MutationObserver(function() {
        if(filterTimeout) clearTimeout(filterTimeout);
        filterTimeout = setTimeout(function() {
            hookAddProducts();
            applyProductFilter(window._cratoFilterPid);
        }, 50);
    }).observe(document.documentElement, { childList: true, subtree: true });

    [0, 200, 600, 1500, 3000].forEach(function(ms) {
        setTimeout(function() { hookAddProducts(); applyProductFilter(window._cratoFilterPid); }, ms);
    });
})();
</script>';
}

$updateFilterScript = '
<script id="proxy-postmessage-filter">
// Escuta mensagens do parent para actualizar o filtro de produto sem reload
window.addEventListener("message", function(e) {
    if (!e.data || e.data.type !== "cratoUpdateFilter") return;
    var pid = String(e.data.productId || "");
    window._cratoFilterPid = pid;
    applyProductFilter(pid);
});

function applyProductFilter(pid) {
    var tables = document.querySelectorAll(".div-table-product table.ticket-box");
    if (!tables.length) return;
    tables.forEach(function(t) {
        if (!pid) {
            t.style.setProperty("display", "table", "important");
            return;
        }
        var match = t.querySelector(
            "#product_" + pid + "_1, [id^=\"product_" + pid + "_\"]"
        );
        t.style.setProperty("display", match ? "table" : "none", "important");
    });
}
</script>';

$cartSyncScript = '
<script id="proxy-cart-sync">
(function(){
    function totalQty() {
        var total = 0;
        document.querySelectorAll("select.quantity-select").forEach(function(s) {
            total += parseInt(s.value || "0", 10);
        });
        return total;
    }

    function notify() {
        window.parent.postMessage({ type: "cratoCartCount", count: totalQty() }, "*");
    }

    // Cliques nos botões + e -
    document.addEventListener("click", function(e) {
        if (e.target && e.target.classList.contains("button-select-product")) {
            setTimeout(notify, 150);
        }
    });
    // Mudança direta no select de quantidade
    document.addEventListener("change", function(e) {
        if (e.target && e.target.classList.contains("quantity-select")) {
            notify();
        }
    });
})();
</script>';

$html = str_replace('</body>', $colorFix . $updateFilterScript . $filterScript . $cartSyncScript . '</body>', $html);


// ─── Resposta ─────────────────────────────────────────────────────────────────
header('Content-Type: text/html; charset=utf-8');
header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: payment=(self)');
echo $html;
