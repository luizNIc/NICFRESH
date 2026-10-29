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
$accepted = is_array($payload) ? ($payload['accepted'] ?? null) : null;
$token = is_array($payload) ? ($payload['token'] ?? '') : '';
$sessionToken = $_SESSION['analytics_visit_token'] ?? '';

if (
    !is_bool($accepted)
    || !is_string($token)
    || $sessionToken === ''
    || !hash_equals($sessionToken, $token)
) {
    http_response_code(403);
    exit;
}

try {
    $pdo->beginTransaction();
    $stmt = $pdo->prepare('UPDATE customers SET analytics_consent = ? WHERE id = ?');
    $stmt->execute([$accepted ? 1 : 0, customer_id()]);

    if (!$accepted) {
        $stmt = $pdo->prepare('DELETE FROM customer_page_visits WHERE customer_id = ?');
        $stmt->execute([customer_id()]);
    }
    $pdo->commit();
    $_SESSION['analytics_consent'] = $accepted;
    unset($_SESSION['customer_entry_pending']);
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log('Customer analytics consent update failed: ' . $e->getMessage());
    http_response_code(500);
}
