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
    
    // START DEBUG LOG
    if (strpos($path, 'cart/resume') !== false || strpos($path, 'cart') !== false) {
        file_put_contents(__DIR__ . '/../cart_debug.json', $result);
    }
    // END DEBUG LOG
    
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

/* ═══════════════════════════════════════════════════════════════
   FORMULÁRIO DE REGISTO / COMPRADOR  — Layout Crato
   ═══════════════════════════════════════════════════════════════ */

/* Container geral do formulário */
#div-fields-register,
#div-fields-login,
.payment-form,
.cart.map-card > div {
    padding: 24px 28px !important;
    color: #FFFFFF !important;
}

/* ── Linha de campos → 2 colunas em flex wrap ── */
.payment-form .form-row,
#div-fields-register .row,
#div-fields-register .form-row {
    display: flex !important;
    flex-direction: row !important;
    flex-wrap: wrap !important;
    gap: 0 16px !important;
    margin-left: 0 !important;
    margin-right: 0 !important;
    margin-bottom: 0 !important;
}

/* Forçar labels acima dos inputs */
.payment-form label,
#div-fields-register label,
.cart label {
    display: block !important;
    font-family: "Inter", sans-serif !important;
    font-size: 11px !important;
    font-weight: 600 !important;
    letter-spacing: 0.08em !important;
    text-transform: uppercase !important;
    color: rgba(255, 255, 255, 0.5) !important;
    margin-bottom: 6px !important;
    margin-top: 16px !important;
}

/* Inputs do formulário de comprador */
.payment-form input:not([type="hidden"]):not([type="checkbox"]):not([type="radio"]),
.payment-form select,
#div-fields-register input:not([type="hidden"]):not([type="checkbox"]):not([type="radio"]),
#div-fields-register select,
#tickets-form-register input:not([type="hidden"]):not([type="checkbox"]):not([type="radio"]),
#tickets-form-register select {
    display: block !important;
    width: 100% !important;
    background-color: #180508 !important;
    color: #FFFFFF !important;
    border: 1px solid rgba(255, 255, 255, 0.15) !important;
    border-radius: 6px !important;
    padding: 10px 14px !important;
    font-family: "Inter", sans-serif !important;
    font-size: 14px !important;
    line-height: 1.5 !important;
    margin-bottom: 0 !important;
    box-shadow: none !important;
    outline: none !important;
    transition: border-color 0.2s ease !important;
    -webkit-appearance: none !important;
    appearance: none !important;
    height: auto !important;
}

.payment-form input:focus,
#div-fields-register input:focus,
#tickets-form-register input:focus,
.payment-form select:focus,
#div-fields-register select:focus {
    border-color: #C8111F !important;
    background-color: #200608 !important;
    box-shadow: 0 0 0 3px rgba(200, 17, 31, 0.15) !important;
}

.payment-form input::placeholder,
#div-fields-register input::placeholder {
    color: rgba(255, 255, 255, 0.25) !important;
}

/* Select — seta customizada */
.payment-form select,
#div-fields-register select,
#tickets-form-register select {
    background-image: url("data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%2212%22 height=%228%22 viewBox=%220 0 12 8%22%3E%3Cpath d=%22M1 1l5 5 5-5%22 stroke=%22rgba(255,255,255,0.4)%22 stroke-width=%221.5%22 fill=%22none%22 stroke-linecap=%22round%22/%3E%3C/svg%3E") !important;
    background-repeat: no-repeat !important;
    background-position: right 12px center !important;
    padding-right: 36px !important;
    cursor: pointer !important;
}

/* Colunas col-md-6 → 50% lado a lado (2 campos por linha) */
#div-fields-register .col-md-6,
.payment-form .col-md-6 {
    flex: 0 0 calc(50% - 8px) !important;
    max-width: calc(50% - 8px) !important;
    padding-left: 0 !important;
    padding-right: 0 !important;
    min-width: 0 !important;
}

/* Coluna col-md-12 → largura total */
#div-fields-register .col-md-12,
.payment-form .col-md-12 {
    flex: 0 0 100% !important;
    max-width: 100% !important;
    padding-left: 0 !important;
    padding-right: 0 !important;
}

/* form-group espaçamento */
.payment-form .form-group,
#div-fields-register .form-group {
    margin-bottom: 4px !important;
}

/* ── Telefone (intl-tel-input) ── */
.iti {
    width: 100% !important;
    display: block !important;
}

.iti__flag-container,
.iti .selected-flag {
    background-color: #200608 !important;
    border-right: 1px solid rgba(255, 255, 255, 0.15) !important;
    border-radius: 6px 0 0 6px !important;
}

.iti input,
.iti input[type="tel"],
.iti input[type="text"] {
    padding-left: 80px !important;
}

.iti__selected-flag,
.iti__flag-container .selected-flag {
    background: transparent !important;
}

.iti__country-list,
.iti--allow-dropdown .iti__flag-container:hover .iti__selected-flag {
    background-color: #180508 !important;
    color: #FFFFFF !important;
    border-color: rgba(255, 255, 255, 0.15) !important;
}

.iti__country-list {
    max-height: 200px !important;
    overflow-y: auto !important;
    z-index: 10000 !important;
}

.iti__dial-code {
    color: rgba(255, 255, 255, 0.6) !important;
}

/* ── Botão VOLTAR ── */
a[onclick*="goProducts"],
button[onclick*="goProducts"],
.btn-back,
a.back-link {
    display: inline-flex !important;
    align-items: center !important;
    gap: 6px !important;
    color: rgba(255, 255, 255, 0.5) !important;
    font-size: 13px !important;
    font-family: "Inter", sans-serif !important;
    text-decoration: none !important;
    background: none !important;
    border: none !important;
    padding: 0 !important;
    margin-bottom: 20px !important;
    cursor: pointer !important;
    transition: color 0.2s ease !important;
}

a[onclick*="goProducts"]:hover,
button[onclick*="goProducts"]:hover {
    color: #FFFFFF !important;
}

/* ── Botão Continuar dentro do formulário ── */
.continue-btn,
#button-products,
button[onclick*="setRegister"],
button[onclick*="goNewFunction"] {
    width: 100% !important;
    padding: 12px 24px !important;
    background: linear-gradient(135deg, #C8111F 0%, #E01828 100%) !important;
    color: #FFFFFF !important;
    border: none !important;
    border-radius: 6px !important;
    font-family: "Inter", sans-serif !important;
    font-size: 0.875rem !important;
    font-weight: 700 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.08em !important;
    box-shadow: 0 4px 20px rgba(232, 49, 26, 0.35) !important;
    transition: transform 0.2s ease, box-shadow 0.2s ease !important;
    cursor: pointer !important;
    margin-top: 24px !important;
}

.continue-btn:hover,
#button-products:hover {
    transform: translateY(-2px) !important;
    box-shadow: 0 8px 30px rgba(232, 49, 26, 0.5) !important;
}

/* ── Aviso "bilhetes enviados por e-mail e whatsapp" ── */
#div-fields-register p,
.payment-form p.info-msg,
.payment-form p small {
    font-size: 12px !important;
    color: rgba(255, 255, 255, 0.4) !important;
    text-align: left !important;
    margin-top: 12px !important;
    margin-bottom: 16px !important;
}

/* ── Mensagens de erro ── */
#div-msg-register .alert-danger,
#div-msg-login .alert-danger {
    background: rgba(200, 17, 31, 0.1) !important;
    border: 1px solid rgba(200, 17, 31, 0.3) !important;
    color: #FF6B7A !important;
    border-radius: 6px !important;
    padding: 10px 14px !important;
    font-size: 13px !important;
}

/* ═══════════════════════════════════════════════════════════════
   ETAPA DE PAGAMENTO — Layout Crato
   ═══════════════════════════════════════════════════════════════ */

/* Container da etapa */
#tickets-form-delivery-payment {
    padding: 24px 28px !important;
    color: #FFFFFF !important;
}

#tickets-form-delivery-payment h2.subtitle,
#tickets-form-delivery-payment .subtitle {
    display: none !important;
}

/* ── Títulos de secção ── */
#tickets-form-delivery-payment label {
    display: block !important;
    font-family: "Inter", sans-serif !important;
    font-size: 11px !important;
    font-weight: 600 !important;
    letter-spacing: 0.08em !important;
    text-transform: uppercase !important;
    color: rgba(255, 255, 255, 0.5) !important;
    margin-bottom: 12px !important;
    margin-top: 0 !important;
}

/* ── Cartões de pagamento (table → grid 3 colunas) ── */
table.table-tipo {
    width: 100% !important;
    display: block !important;
    border: none !important;
    background: transparent !important;
}

table.table-tipo tbody {
    display: flex !important;
    flex-direction: row !important;
    flex-wrap: wrap !important;
    gap: 10px !important;
    width: 100% !important;
}

table.table-tipo td {
    flex: 1 1 calc(25% - 10px) !important;
    min-width: 120px !important;
}

table.table-tipo tr {
    display: contents !important;
}

table.table-tipo td {
    display: block !important;
    padding: 0 !important;
    border: none !important;
    background: transparent !important;
}

/* Cada cartão */
.div-tipos.imagem-new {
    display: flex !important;
    align-items: center !important;
    gap: 10px !important;
    padding: 12px 14px !important;
    background: #180508 !important;
    border: 1.5px solid rgba(255, 255, 255, 0.1) !important;
    border-radius: 8px !important;
    cursor: pointer !important;
    transition: border-color 0.2s ease, background 0.2s ease !important;
    min-height: 56px !important;
    height: 100% !important;
}

.div-tipos.imagem-new:hover {
    border-color: rgba(255, 255, 255, 0.25) !important;
    background: #200608 !important;
}

/* Cartão seleccionado */
.div-tipos.imagem-new:has(input:checked),
.div-tipos.imagem-new.active {
    border-color: #C8111F !important;
    background: rgba(200, 17, 31, 0.08) !important;
}

/* Radio dentro do cartão */
.div-tipos.imagem-new .form-check-input,
.div-tipos.imagem-new input[type="radio"] {
    position: relative !important;
    margin: 0 !important;
    flex-shrink: 0 !important;
    width: 16px !important;
    height: 16px !important;
    appearance: auto !important;
    -webkit-appearance: auto !important;
    accent-color: #C8111F !important;
    cursor: pointer !important;
}

/* Label do cartão (contém a imagem dos logos) */
.div-tipos.imagem-new .form-check-label {
    display: flex !important;
    align-items: center !important;
    flex: 1 !important;
    cursor: pointer !important;
    margin: 0 !important;
    font-size: 0 !important;
    text-transform: none !important;
    letter-spacing: 0 !important;
    color: transparent !important;
}

.div-tipos.imagem-new .form-check-label img {
    max-height: 28px !important;
    max-width: 100% !important;
    object-fit: contain !important;
    filter: brightness(1) !important;
}

/* Esconder o select redundante de pagamento (a table de cards já substitui) */
select#payment-method {
    display: none !important;
}

/* Alerta de cartão desabilitado */
#alert_card_credit {
    font-size: 12px !important;
    color: rgba(255, 255, 255, 0.4) !important;
    margin-bottom: 8px !important;
}

/* ── ENTREGA dropdown ── */
#delivery-method {
    background-color: #180508 !important;
    color: #FFFFFF !important;
    border: 1px solid rgba(255, 255, 255, 0.15) !important;
    border-radius: 6px !important;
    padding: 10px 14px !important;
    font-size: 14px !important;
    width: 100% !important;
    margin-top: 4px !important;
}

/* ── EMITIR FATURA — opções NÃO/SIM lado a lado ── */
.col-sm-12.mt-4 {
    margin-top: 20px !important;
}

/* Remover o <br> visual que separa o título dos radios */
.col-sm-12.mt-4 br {
    display: none !important;
}

/* Container dos dois form-check-inline → linha horizontal */
.col-sm-12.mt-4 {
    display: flex !important;
    flex-wrap: wrap !important;
    align-items: center !important;
    gap: 0 !important;
}

.col-sm-12.mt-4 > label:first-child {
    width: 100% !important;
    margin-bottom: 10px !important;
}

.col-sm-12.mt-4 .form-check.form-check-inline {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    padding: 10px 18px !important;
    background: #180508 !important;
    border: 1.5px solid rgba(255, 255, 255, 0.1) !important;
    border-radius: 6px !important;
    margin-right: 10px !important;
    margin-bottom: 0 !important;
    cursor: pointer !important;
    transition: border-color 0.2s ease !important;
    min-width: 80px !important;
}

.col-sm-12.mt-4 .form-check.form-check-inline:has(input:checked) {
    border-color: #C8111F !important;
    background: rgba(200, 17, 31, 0.08) !important;
}

.col-sm-12.mt-4 .form-check-input {
    position: relative !important;
    margin: 0 !important;
    accent-color: #C8111F !important;
    width: 15px !important;
    height: 15px !important;
    flex-shrink: 0 !important;
    appearance: auto !important;
    -webkit-appearance: auto !important;
}

.col-sm-12.mt-4 .form-check-label {
    font-size: 13px !important;
    font-weight: 600 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.06em !important;
    color: #FFFFFF !important;
    margin: 0 !important;
    cursor: pointer !important;
}

/* ── Checkboxes de Termos ── */
#tickets-form-delivery-payment .form-check:not(.form-check-inline) {
    display: flex !important;
    align-items: flex-start !important;
    gap: 10px !important;
    margin-bottom: 14px !important;
    padding-left: 0 !important;
    background: none !important;
    border: none !important;
}

#tickets-form-delivery-payment .form-check:not(.form-check-inline) .form-check-input,
#tickets-form-delivery-payment .form-check:not(.form-check-inline) input[type="checkbox"] {
    position: relative !important;
    margin: 3px 0 0 0 !important;
    flex-shrink: 0 !important;
    width: 15px !important;
    height: 15px !important;
    min-width: 15px !important;
    accent-color: #C8111F !important;
    appearance: auto !important;
    -webkit-appearance: auto !important;
    cursor: pointer !important;
    border-radius: 3px !important;
    background: #180508 !important;
    border: 1px solid rgba(255,255,255,0.2) !important;
}

#tickets-form-delivery-payment .form-check:not(.form-check-inline) .form-check-label {
    font-size: 12px !important;
    font-weight: 400 !important;
    text-transform: none !important;
    letter-spacing: 0 !important;
    color: rgba(255, 255, 255, 0.55) !important;
    line-height: 1.5 !important;
    cursor: pointer !important;
}

/* ── Secção de e-mail informativo ── */
#tickets-form-delivery-payment .form-group.row:last-of-type label,
#tickets-form-delivery-payment small {
    font-size: 12px !important;
    color: rgba(255, 255, 255, 0.4) !important;
    text-transform: none !important;
    font-weight: 400 !important;
    letter-spacing: 0 !important;
}

/* ── Botão Continuar (esta etapa usa #button-delivery) ── */
#button-delivery {
    width: 100% !important;
    padding: 12px 24px !important;
    background: linear-gradient(135deg, #C8111F 0%, #E01828 100%) !important;
    color: #FFFFFF !important;
    border: none !important;
    border-radius: 6px !important;
    font-family: "Inter", sans-serif !important;
    font-size: 0.875rem !important;
    font-weight: 700 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.08em !important;
    box-shadow: 0 4px 20px rgba(232, 49, 26, 0.35) !important;
    transition: transform 0.2s ease, box-shadow 0.2s ease !important;
    cursor: pointer !important;
    margin-top: 20px !important;
}

#button-delivery:hover {
    transform: translateY(-2px) !important;
    box-shadow: 0 8px 30px rgba(232, 49, 26, 0.5) !important;
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
    float: none !important;
    display: flex !important;
    flex-direction: column !important;
    padding: 30px 24px !important;
    background: #0C0306 !important;
    border-left: 1px solid rgba(255, 255, 255, 0.08) !important;
    min-height: 100% !important;
    height: auto !important;
}

/* Fazer com que a linha ocupe o espaço e os paineis estiquem */
.row:has(.cart-sidebar) {
    display: flex !important;
    align-items: stretch !important;
    min-height: 100% !important;
}
html, body, .wrapper, .main-panel, .content, .container-fluid {
    min-height: 100% !important;
}

/* Fazer com que o carrinho ocupe o espaço inteiro e jogue o botão para baixo */
.cart-sidebar > div, .cart-sidebar .cart-box {
    display: flex !important;
    flex-direction: column !important;
    height: 100% !important;
    flex-grow: 1 !important;
}

/* O form do cupom e do carrinho */
#tickets-form-cart {
    display: flex !important;
    flex-direction: column !important;
    flex-grow: 1 !important;
    height: 100% !important;
}

/* Tabelas e itens do carrinho com largura 100% */
.cart-sidebar table,
.cart-sidebar .ticket-box,
.cart-sidebar .cart-item {
    width: 100% !important;
    max-width: 100% !important;
}

/* Esconder inputs estranhos que aparecem debaixo do Ola (ex: readonly name/email) */
.cart-sidebar input[readonly],
.cart-sidebar input[disabled],
.cart-sidebar .user-logged-in input,
.cart-sidebar input[type="text"]:not(#coupon-code) {
    display: none !important;
}

/* Empurrar totais para o fundo */
.cart-box hr:last-of-type,
.cart-box .row:has(.price-total),
.cart-box button[onclick*="goDelivery"] {
    margin-top: auto !important;
}

/* Remove Button Clean Style */
button.close,
.remove-btn,
button.btn-danger, 
a.btn-danger,
button[onclick*="removeItem"],
a[onclick*="removeItem"] {
    background: transparent !important;
    background-color: transparent !important;
    border: none !important;
    box-shadow: none !important;
    color: rgba(255,255,255,0.5) !important;
    padding: 0 !important;
    margin: 0 !important;
    min-width: 0 !important;
    width: auto !important;
    height: auto !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    opacity: 0.7 !important;
}
button.close:hover,
.remove-btn:hover,
button.btn-danger:hover, 
a.btn-danger:hover,
button[onclick*="removeItem"]:hover,
a[onclick*="removeItem"]:hover {
    background: transparent !important;
    background-color: transparent !important;
    color: #C8111F !important;
    opacity: 1 !important;
}

</style>';

if ($productId !== '') {
    $layoutFix = str_replace('</style>', '
/* Esconder todos os produtos inicialmente para evitar FOUC */
table.ticket-box, .ticket-box { display: none !important; }
</style>', $layoutFix);
}

$html = str_replace('</body>', $layoutFix . '</body>', $html);

// ─── Injetar filtro de produto ────────────────────────────────────────────────
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

// JS Adicional para formatar "Olá Nome" e "Sair" e ícones de remover
document.addEventListener("DOMContentLoaded", function() {
    function fixUserLogout() {
        // Encontra o texto "Olá" e o botão "Sair" e os coloca inline
        var allSpans = document.querySelectorAll("span, p, div, b, strong");
        allSpans.forEach(function(el) {
            if (el.childNodes.length > 0 && el.textContent.trim().toUpperCase().startsWith("OLÁ")) {
                var parent = el.parentElement;
                if (!parent || parent.classList.contains("crato-user-fixed")) return;
                
                var logoutBtn = parent.querySelector("button, a");
                if (logoutBtn && logoutBtn.textContent.trim().toUpperCase() === "SAIR") {
                    parent.classList.add("crato-user-fixed");
                    parent.style.display = "flex";
                    parent.style.justifyContent = "space-between";
                    parent.style.alignItems = "center";
                    parent.style.width = "100%";
                    parent.style.borderBottom = "1px solid rgba(255,255,255,0.1)";
                    parent.style.paddingBottom = "12px";
                    parent.style.marginBottom = "15px";
                    
                    el.style.margin = "0";
                    el.style.fontSize = "14px";
                    el.style.textTransform = "uppercase";
                    el.style.color = "rgba(255,255,255,0.7)";
                    
                    logoutBtn.style.background = "transparent";
                    logoutBtn.style.color = "#FFFFFF";
                    logoutBtn.style.border = "none";
                    logoutBtn.style.textDecoration = "underline";
                    logoutBtn.style.padding = "0";
                    logoutBtn.style.margin = "0";
                    logoutBtn.style.boxShadow = "none";
                    logoutBtn.style.fontSize = "13px";
                    logoutBtn.style.fontWeight = "normal";
                }
            }
        });
    }

    function fixRemoveIcons() {
        var removeBtns = document.querySelectorAll("button.close, button.btn-danger, button[onclick*=\'removeItem\'], a[onclick*=\'removeItem\'], .remove-btn, .button-select-product");
        removeBtns.forEach(function(btn) {
            var text = btn.textContent.trim().toUpperCase();
            if (text === "X" || text === "×" || btn.classList.contains("close") || btn.classList.contains("remove-btn") || btn.classList.contains("btn-danger")) {
                if (!btn.classList.contains("crato-icon-fixed")) {
                    btn.classList.add("crato-icon-fixed");
                    btn.innerHTML = \'<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>\';
                    
                    // Force overrides just in case
                    btn.style.setProperty("background", "transparent", "important");
                    btn.style.setProperty("background-color", "transparent", "important");
                    btn.style.setProperty("border", "none", "important");
                    btn.style.setProperty("box-shadow", "none", "important");
                    btn.style.setProperty("color", "rgba(255,255,255,0.5)", "important");
                    btn.style.setProperty("padding", "0", "important");
                    btn.style.setProperty("min-width", "0", "important");
                }
            }
        });
    }

    var fixTimeout;
    var observer = new MutationObserver(function() {
        if(fixTimeout) clearTimeout(fixTimeout);
        fixTimeout = setTimeout(function() {
            fixUserLogout();
            fixRemoveIcons();
        }, 100);
    });
    observer.observe(document.body, { childList: true, subtree: true });
    
    setTimeout(function() {
        fixUserLogout();
        fixRemoveIcons();
    }, 300);
});
</script>';

$html = str_replace('</body>', $updateFilterScript . $filterScript . $moveMessageScript . '</body>', $html);

// ─── Resposta ─────────────────────────────────────────────────────────────────
header('Content-Type: text/html; charset=utf-8');
header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: payment=(self)');
echo $html;
