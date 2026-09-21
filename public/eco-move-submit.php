<?php
/**
 * Endpoint de inscrição de voluntários — EcoMove Crato 2026.
 * Recebe POST em JSON ou form-data, valida, e envia email.
 */

header('Content-Type: application/json; charset=utf-8');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'method_not_allowed']);
    exit;
}

// Origem do request — apenas o próprio site
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
$host = $_SERVER['HTTP_HOST'] ?? '';
$selfHost = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $host;
if ($origin && $origin !== $selfHost) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'error' => 'forbidden']);
    exit;
}

// Rate limit básico por IP (3 submissões por 10 min)
session_start();
$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$now = time();
$key = 'eco_move_rate_' . md5($ip);
$hits = $_SESSION[$key] ?? [];
$hits = array_filter($hits, fn($t) => $now - $t < 600);
if (count($hits) >= 3) {
    http_response_code(429);
    echo json_encode(['ok' => false, 'error' => 'rate_limited']);
    exit;
}

// Parsing: suporta JSON e form-encoded
$raw = file_get_contents('php://input');
$data = [];
if ($raw && stripos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false) {
    $data = json_decode($raw, true) ?: [];
} else {
    $data = $_POST;
}

// Honeypot
if (!empty($data['website'])) {
    echo json_encode(['ok' => true]);
    exit;
}

$name = trim((string) ($data['name'] ?? ''));
$email = trim((string) ($data['email'] ?? ''));
$phone = trim((string) ($data['phone'] ?? ''));
$nif = trim((string) ($data['nif'] ?? ''));
$age = trim((string) ($data['age'] ?? ''));
$address = trim((string) ($data['address'] ?? ''));
$rgpd = !empty($data['rgpd']);
$shiftsRaw = trim((string) ($data['shifts'] ?? ''));

// Datas/turnos permitidos
$allowedDates = ['2026-08-25', '2026-08-26', '2026-08-27', '2026-08-28', '2026-08-29'];
$allowedShifts = ['turno1' => 'Turno 1 (20h00–23h00)', 'turno2' => 'Turno 2 (23h00–02h00)'];

$selections = [];
if ($shiftsRaw !== '') {
    foreach (explode(',', $shiftsRaw) as $pair) {
        $parts = explode('|', trim($pair));
        if (count($parts) !== 2)
            continue;
        [$d, $s] = $parts;
        if (in_array($d, $allowedDates, true) && isset($allowedShifts[$s])) {
            $selections[] = $d . '|' . $s;
        }
    }
    $selections = array_values(array_unique($selections));
}

// Validação
$errors = [];
if ($name === '' || mb_strlen($name) > 120)
    $errors[] = 'name';
if (!filter_var($email, FILTER_VALIDATE_EMAIL))
    $errors[] = 'email';
if ($phone === '' || !preg_match('/^[\d\s\+\-\(\)]{5,30}$/', $phone))
    $errors[] = 'phone';
if (!preg_match('/^\d{9}$/', $nif))
    $errors[] = 'nif';
if (!ctype_digit($age) || (int) $age < 16 || (int) $age > 99)
    $errors[] = 'age';
if ($address === '' || mb_strlen($address) > 200)
    $errors[] = 'address';
if (!$rgpd)
    $errors[] = 'rgpd';
if (empty($selections))
    $errors[] = 'shifts';

if ($errors) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'error' => 'validation', 'fields' => $errors]);
    exit;
}

// Sanitização para cabeçalhos (evita header injection)
$cleanName = preg_replace("/[\r\n]+/", ' ', $name);

$weekdays = [
    '2026-08-25' => 'terça-feira',
    '2026-08-26' => 'quarta-feira',
    '2026-08-27' => 'quinta-feira',
    '2026-08-28' => 'sexta-feira',
    '2026-08-29' => 'sábado',
];

$selectionsText = '';
foreach ($selections as $sel) {
    [$d, $s] = explode('|', $sel);
    $dateFmt = substr($d, 8, 2) . '/' . substr($d, 5, 2) . '/' . substr($d, 0, 4);
    $selectionsText .= "  - Dia {$dateFmt} ({$weekdays[$d]}) — {$allowedShifts[$s]}\n";
}

$to = 'carlosnetolima@gmail.com';

$mailSubject = '[EcoMove Crato 2026] Nova inscrição de voluntário';
$mailBody = "Nova inscrição — EcoMove Crato 2026\n\n"
    . "Nome: {$cleanName}\n"
    . "Email: {$email}\n"
    . "Telefone: {$phone}\n"
    . "NIF: {$nif}\n"
    . "Idade: {$age}\n"
    . "Morada: {$address}\n\n"
    . "Turnos selecionados:\n{$selectionsText}\n"
    . "---\n"
    . "Enviado em: " . date('Y-m-d H:i:s') . "\n"
    . "IP: {$ip}\n";

$headers = "From: Festival Crato <no-reply@" . preg_replace('/[^a-z0-9\.\-]/i', '', $host) . ">\r\n";
$headers .= "Reply-To: {$cleanName} <{$email}>\r\n";
$headers .= "Cc: junior@idera.com.br\r\n";
$headers .= "Content-Type: text/plain; charset=utf-8\r\n";
$headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";

$sent = @mail($to, $mailSubject, $mailBody, $headers);

// Regista o hit do rate limit
$hits[] = $now;
$_SESSION[$key] = $hits;

if (!$sent) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'send_failed']);
    exit;
}

echo json_encode(['ok' => true]);
