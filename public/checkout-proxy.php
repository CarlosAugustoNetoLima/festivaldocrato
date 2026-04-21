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

    // Content-Type da requisição original
    $ct = $_SERVER['CONTENT_TYPE'] ?? 'application/x-www-form-urlencoded';

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
    // Também reencaminhar quaisquer cookies do browser que possam existir
    foreach ($_COOKIE as $k => $v) {
        if ($k !== 'PHPSESSID') { // Não sobrescrever com o cookie local errado
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

    // Restringir CORS apenas ao dominio do site
    $allowedOrigin = $_SERVER['HTTP_ORIGIN'] ?? '';
    $siteDomain = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost');
    if ($allowedOrigin === $siteDomain || empty($allowedOrigin)) {
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

// ─── Injetar CSS de layout fix e THEME CRATO ──────────────────────────────────
// Injetar no <head> para garantir alta prioridade
$layoutFix = '
<style id="proxy-layout-fix">
@import url("https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@400;500;700&display=swap");

/* Reset visual Base -> Forçar Cores Crato */
html, body, .main-panel, .wrapper {
    background-color: #0C0306 !important;
    background-image: none !important;
    color: #FFFFFF !important;
    font-family: "Inter", sans-serif !important;
}

h1, h2, h3, h4, h5, h6, .subtitle, .title {
    font-family: "Bebas Neue", sans-serif !important;
    color: #FFFFFF !important;
    letter-spacing: 0.05em !important;
}

/* Caixas e Paineis (Esquerda e Direita) */
/* Caixas e Paineis (Esquerda e Direita) */
/* Maior especificidade para anular estilos embutidos do LeBillet */
html body #tickets-container, 
html body .calema-section, 
html body .contain\ter-fluid, 
html body .container-fluid, 
html body .row.cart-content, 
html body div.bg-blue, 
html body div.bg-event, 
html body div.cart-sidebar, 
html body div.map-card, 
html body .ticket-box, 
html body .modal-content, 
html body .card,
html body .card-body,
html body .content,
html body .main-panel {
    background: #0C0306 !important;
    background-color: #0C0306 !important;
    background-image: none !important;
    border-color: rgba(255, 255, 255, 0.08) !important;
    color: #FFFFFF !important;
    box-shadow: none !important;
    border-radius: 0 !important;
}

/* Remover Blobs coloridos invasivos do LeBillet que causam os gradientes! */
html body .blob, html body .blob1, html body .blob2 {
    display: none !important;
    opacity: 0 !important;
    visibility: hidden !important;
}

/* Tabelas reset para não herdarem azuis interiores */
table.ticket-box, table.ticket-box tbody, table.ticket-box tr, table.ticket-box td {
    background: transparent !important;
}

/* ───────────────────────────────────────────────────────── */
/* ESTILIZAÇÃO DO CUPOM PROMOCIONAL E SEUS BOTÕES */
/* ───────────────────────────────────────────────────────── */

/* O Botão Delineado Inicial "Código Promocional" */
button#promo-btn {
    background: transparent !important;
    border: 1px dashed rgba(255, 255, 255, 0.4) !important;
    color: #FFFFFF !important;
    font-family: inherit !important;
    font-size: 13px !important;
    padding: 6px 12px !important;
    border-radius: 4px !important;
    transition: all 0.3s ease !important;
    display: inline-flex !important;
    align-items: center !important;
    box-shadow: none !important;
    width: 100% !important;
    justify-content: center !important;
}
button#promo-btn:hover {
    border-color: #C8111F !important;
    color: #C8111F !important;
    background: rgba(200, 17, 31, 0.1) !important;
}

/* Container do Input de Cupom (escondendo contornos indesejados) */
.coupon-section {
    display: flex !important;
    flex-wrap: nowrap !important;
    align-items: center !important;
    gap: 10px !important;
    margin-top: 10px !important;
    width: 100% !important;
    border: none !important;
}

/* Input de Cupom */
input#coupon-code {
    background-color: #180508 !important;
    color: #FFFFFF !important;
    border: 1px solid rgba(255, 255, 255, 0.2) !important;
    border-radius: 4px !important;
    padding: 8px 12px !important;
    font-size: 14px !important;
    height: 38px !important;
    box-shadow: none !important;
    outline: none !important;
    flex: 1 1 auto !important;
    width: 100% !important;
}
input#coupon-code:focus {
    border-color: #C8111F !important;
    box-shadow: none !important;
    outline: none !important;
}
input#coupon-code::placeholder {
    color: #666666 !important;
}

/* Botão "Aplicar" do Cupom */
button#button-coupon, .btn-outline-success {
    background: #180508 !important;
    border: 1px solid #C8111F !important;
    color: #C8111F !important;
    padding: 8px 16px !important;
    border-radius: 4px !important;
    font-weight: bold !important;
    font-size: 13px !important;
    height: 38px !important;
    text-transform: uppercase !important;
    transition: all 0.2s ease !important;
    white-space: nowrap !important;
    box-shadow: none !important;
    outline: none !important;
}
button#button-coupon:hover, .btn-outline-success:hover {
    background: #C8111F !important;
    color: #FFFFFF !important;
    box-shadow: none !important;
}

/* ───────────────────────────────────────────────────────── */

/* Botões de Controlo (+ e -) */
.button-select-product, .quantity-select {
    display: inline-flex !important;
    align-items: center;
    justify-content: center;
    background-color: #180508 !important;
    border: 1px solid rgba(255, 255, 255, 0.2) !important;
    color: #FFFFFF !important;
    margin: 0 4px !important;
    border-radius: 4px !important;
    font-weight: bold !important;
    min-width: 28px !important;
    height: 28px !important;
    text-align: center;
    transition: all 0.2s ease !important;
    box-shadow: none !important;
    text-decoration: none !important;
}

.button-select-product:hover {
    background-color: #C8111F !important;
    border-color: #C8111F !important;
    color: #fff !important;
    cursor: pointer !important;
}

select.quantity-select, input.quantity-select, input.quantity {
    background-color: transparent !important;
    color: #FFFFFF !important;
    border: none !important;
    box-shadow: none !important;
    font-family: "Inter", sans-serif !important;
    padding: 0 !important;
    -webkit-appearance: none !important;
    appearance: none !important;
    text-align: center !important;
    text-align-last: center !important;
}

input.quantity-select::-webkit-inner-spin-button, 
input.quantity-select::-webkit-outer-spin-button,
input.quantity::-webkit-inner-spin-button, 
input.quantity::-webkit-outer-spin-button { 
    -webkit-appearance: none !important; 
    margin: 0 !important; 
}

/* Ocultar outlines horriveis em focus */
select.quantity-select:focus, .button-select-product:focus {
    outline: none !important;
}

/* Linhas dos produtos */
.ticket-row, .row {
    border-color: rgba(255, 255, 255, 0.08) !important;
    color: #FFFFFF !important;
}

/* Textos descritivos */
.text-muted, small, .price, p {
    color: #999999 !important;
}

/* Inputs e Selects */
input, select, textarea, .form-control {
    background-color: #180508 !important;
    color: #FFFFFF !important;
    border: 1px solid rgba(255, 255, 255, 0.2) !important;
    border-radius: 6px !important;
}

.form-control:focus {
    background-color: #200608 !important;
    color: #FFFFFF !important;
    border-color: #C8111F !important;
    box-shadow: 0 0 0 0.2rem rgba(200, 17, 31, 0.25) !important;
}

/* Botões com destaque vermelho */
.btn-primary, .btn-success, .btn-info, button.btn {
    background: linear-gradient(135deg, #C8111F 0%, #E01828 100%) !important;
    border: none !important;
    color: #FFFFFF !important;
    font-family: "Inter", sans-serif !important;
    font-weight: 700 !important;
    text-transform: uppercase !important;
    border-radius: 6px !important;
    box-shadow: 0 4px 15px rgba(232, 49, 26, 0.3) !important;
    transition: all 0.3s ease !important;
}

/* Fix específico SVG de ícones para não ficarem escondidos */
svg#icon-cart, svg {
    stroke: #FFFFFF !important;
}

/* Para botões outline ou secundários que fiquem muito feios se corrompidos */
.btn-outline-success {
    background: transparent !important;
    border: 1.5px solid rgba(255, 255, 255, 0.3) !important;
    color: #FFFFFF !important;
    box-shadow: none !important;
}

/* Remover botões flutuantes originais da Lebillet que não combinam */
.btn-float { display: none !important; }

/* Forçar layout de 2 colunas sempre, independente de breakpoint */
.row > .col-12.col-md-7.map-card {
    -webkit-box-flex: 0 !important;
    -ms-flex: 0 0 58.333333% !important;
    flex: 0 0 58.333333% !important;
    max-width: 58.333333% !important;
    width: 58.333333% !important;
    float: left !important;
    padding-right: 15px !important;
}
.row > .col-12.col-md-5.cart-sidebar {
    -webkit-box-flex: 0 !important;
    -ms-flex: 0 0 41.666667% !important;
    flex: 0 0 41.666667% !important;
    max-width: 41.666667% !important;
    width: 41.666667% !important;
    float: left !important;
    display: block !important;
    padding-left: 15px !important;
    background: #0C0306 !important;
    border-left: 1px solid rgba(255, 255, 255, 0.08) !important;
}
</style>';

if ($productId !== '') {
    $layoutFix = str_replace('</style>', '
/* Esconder todos os produtos inicialmente para evitar FOUC */
table.ticket-box, .ticket-box { display: none !important; }
</style>', $layoutFix);
}

$html = str_replace('</head>', $layoutFix . '</head>', $html);

// ─── Injetar filtro de produto ────────────────────────────────────────────────
$filterScript = '';
if ($productId !== '') {
    $filterScript = '
<script id="proxy-filter-js">
(function(){
    var pid = "' . $productId . '";

    function applyFilter() {
        var tables = document.querySelectorAll(".div-table-product table.ticket-box");
        if (!tables.length) return false;
        tables.forEach(function(t) {
            var match = t.querySelector(
                "#product_" + pid + "_1, [id^=\"product_" + pid + "_\"]"
            );
            t.style.setProperty("display", match ? "table" : "none", "important");
        });
        return true;
    }

    var _originalAddProducts;
    function hookAddProducts() {
        if (typeof window.addProducts === "function" && !window._proxyFilterHooked) {
            _originalAddProducts = window.addProducts;
            window.addProducts = function() {
                _originalAddProducts.apply(this, arguments);
                setTimeout(applyFilter, 60);
            };
            window._proxyFilterHooked = true;
            applyFilter();
        }
    }

    var filterTimeout;
    new MutationObserver(function() {
        if(filterTimeout) clearTimeout(filterTimeout);
        filterTimeout = setTimeout(function() {
            hookAddProducts();
            applyFilter();
        }, 50);
    }).observe(document.documentElement, { childList: true, subtree: true });

    [0, 200, 600, 1500, 3000].forEach(function(ms) {
        setTimeout(function() { hookAddProducts(); applyFilter(); }, ms);
    });
})();
</script>';
}

$moveMessageScript = '
<script>
document.addEventListener("DOMContentLoaded", function() {
    function moveCouponMessage() {
        var alerts = document.querySelectorAll(".alert, .text-danger, .coupon-msg, #msg-coupon, .invalid-feedback, div[style*=\"color: red\"], p, span");
        var couponCodeInput = document.querySelector("#coupon-code");
        var couponSection = couponCodeInput ? couponCodeInput.closest(".coupon-section") : null;
        if (!couponSection && couponCodeInput) couponSection = couponCodeInput.parentElement;
        
        var cartBox = document.querySelector(".cart-sidebar .cart-box") || document.querySelector(".cart-sidebar");
        
        if (!couponSection && !cartBox) return;

        alerts.forEach(function(alert) {
            var text = alert.innerText ? alert.innerText.toLowerCase() : "";
            if (text.includes("cupom não localizado") || ((alert.classList.contains("alert") || alert.classList.contains("text-danger")) && text.includes("cupom"))) {
                if (couponSection && alert.previousElementSibling !== couponSection) {
                    couponSection.insertAdjacentElement("afterend", alert);
                    alert.style.color = "#C8111F";
                    alert.style.marginTop = "5px";
                    alert.style.marginBottom = "10px";
                    alert.style.padding = "0";
                    alert.style.background = "transparent";
                    alert.style.border = "none";
                    alert.style.fontWeight = "bold";
                    alert.style.fontSize = "13px";
                    alert.style.width = "100%";
                    alert.style.textAlign = "left";
                } else if (cartBox && !couponSection && alert.parentElement !== cartBox && !cartBox.contains(alert)) {
                    cartBox.insertBefore(alert, cartBox.firstChild);
                    alert.style.color = "#C8111F";
                }
            }
        });
    }

    var moveTimeout;
    var observer = new MutationObserver(function() {
        if(moveTimeout) clearTimeout(moveTimeout);
        moveTimeout = setTimeout(moveCouponMessage, 100);
    });
    observer.observe(document.documentElement, { childList: true, subtree: true });
    
    setTimeout(moveCouponMessage, 500);
});
</script>';

$html = str_replace('</body>', $filterScript . $moveMessageScript . '</body>', $html);

// ─── Resposta ─────────────────────────────────────────────────────────────────
header('Content-Type: text/html; charset=utf-8');
header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: payment=(self)');
echo $html;
