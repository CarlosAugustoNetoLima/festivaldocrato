<?php
/**
 * Endpoint de envio de formulários de contacto.
 * Recebe POST em JSON ou form-data, valida, e envia email.
 */

header('Content-Type: application/json; charset=utf-8');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'method_not_allowed']);
    exit;
}

// Origem do request — apenas o próprio site
$origin   = $_SERVER['HTTP_ORIGIN'] ?? '';
$host     = $_SERVER['HTTP_HOST']   ?? '';
$selfHost = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $host;
if ($origin && $origin !== $selfHost) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'error' => 'forbidden']);
    exit;
}

// Rate limit básico por IP (3 submissões por 10 min)
session_start();
$ip   = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$now  = time();
$key  = 'contact_rate_' . md5($ip);
$hits = $_SESSION[$key] ?? [];
$hits = array_filter($hits, fn($t) => $now - $t < 600);
if (count($hits) >= 3) {
    http_response_code(429);
    echo json_encode(['ok' => false, 'error' => 'rate_limited']);
    exit;
}

// Parsing: suporta JSON e form-encoded
$raw  = file_get_contents('php://input');
$data = [];
if ($raw && stripos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false) {
    $data = json_decode($raw, true) ?: [];
} else {
    $data = $_POST;
}

// Honeypot
if (!empty($data['website'])) {
    // Finge sucesso para não dar pista ao bot
    echo json_encode(['ok' => true]);
    exit;
}

$name    = trim((string)($data['name']    ?? ''));
$email   = trim((string)($data['email']   ?? ''));
$phone   = trim((string)($data['phone']   ?? ''));
$message = trim((string)($data['message'] ?? ''));
$subject = trim((string)($data['subject'] ?? 'Contacto Festival do Crato'));
$privacy = !empty($data['privacy']);
$consent = !empty($data['consent']);

// Validação
$errors = [];
if ($name === '' || mb_strlen($name) > 120)                             $errors[] = 'name';
if (!filter_var($email, FILTER_VALIDATE_EMAIL))                         $errors[] = 'email';
if ($phone !== '' && !preg_match('/^[\d\s\+\-\(\)]{5,30}$/', $phone))   $errors[] = 'phone';
if ($message === '' || mb_strlen($message) > 4000)                      $errors[] = 'message';
if (!$privacy || !$consent)                                             $errors[] = 'consent';

if ($errors) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'error' => 'validation', 'fields' => $errors]);
    exit;
}

// Sanitização para cabeçalhos (evita header injection)
$cleanSubject = preg_replace("/[\r\n]+/", ' ', $subject);
$cleanName    = preg_replace("/[\r\n]+/", ' ', $name);

// Destinatário — temporário até definirem o oficial
$to = 'carlosnetolima@gmail.com';

$mailSubject = '[Festival Crato] ' . $cleanSubject;
$mailBody    = "Assunto: {$cleanSubject}\n"
             . "Nome: {$cleanName}\n"
             . "Email: {$email}\n"
             . "Telefone: {$phone}\n\n"
             . "Mensagem:\n{$message}\n\n"
             . "---\n"
             . "Enviado em: " . date('Y-m-d H:i:s') . "\n"
             . "IP: {$ip}\n";

$headers  = "From: Festival Crato <no-reply@" . preg_replace('/[^a-z0-9\.\-]/i', '', $host) . ">\r\n";
$headers .= "Reply-To: {$cleanName} <{$email}>\r\n";
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
