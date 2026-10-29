<?php
require 'config/db.php';
require 'includes/functions.php';
require 'includes/customer-auth.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

if (customer_id() <= 0) {
    http_response_code(401);
    exit;
}

$payload = json_decode(file_get_contents('php://input'), true);
$page = is_array($payload) ? ($payload['page'] ?? '') : '';
$token = is_array($payload) ? ($payload['token'] ?? '') : '';
$sessionToken = $_SESSION['analytics_visit_token'] ?? '';

if (
    !is_string($page)
    || !preg_match('/^[a-zA-Z0-9_-]+\.php$/', $page)
    || !is_string($token)
    || $sessionToken === ''
    || !hash_equals($sessionToken, $token)
) {
    http_response_code(403);
    exit;
}

try {
    $consentStmt = $pdo->prepare('SELECT analytics_consent FROM customers WHERE id = ?');
    $consentStmt->execute([customer_id()]);
    if (!(int) $consentStmt->fetchColumn()) {
        http_response_code(403);
        exit;
    }

    $stmt = $pdo->prepare(
        'INSERT INTO customer_page_visits (customer_id, page) VALUES (?, ?)'
    );
    $stmt->execute([customer_id(), $page]);
    http_response_code(204);
} catch (PDOException $e) {
    error_log('Customer visit tracking failed: ' . $e->getMessage());
    http_response_code(500);
}
