<?php
require 'config/db.php';
require 'includes/functions.php';
require 'includes/customer-auth.php';
require_customer();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: cart.php');
    exit;
}

$items = cart_items($pdo);
if (empty($items)) {
    header('Location: cart.php');
    exit;
}

$customerStmt = $pdo->prepare('SELECT id, email FROM customers WHERE id = ?');
$customerStmt->execute([customer_id()]);
$customer = $customerStmt->fetch();
if (!$customer) {
    unset($_SESSION['customer_id'], $_SESSION['customer_name']);
    header('Location: login.php?return=checkout.php');
    exit;
}

// --- Validate & sanitize incoming form data ---
$name = trim($_POST['customer_name'] ?? '');
$phone = trim($_POST['customer_phone'] ?? '');
$email = $customer['email'];
$area = trim($_POST['delivery_area'] ?? '');
$address = trim($_POST['delivery_address'] ?? '');
$window = trim($_POST['delivery_window'] ?? '');
$payment_method = trim($_POST['payment_method'] ?? '');
$payment_phone = trim($_POST['payment_phone'] ?? '');
$payment_pin = $_POST['payment_pin'] ?? null;

$errors = [];
if ($name === '') $errors[] = 'Name is required.';
if ($phone === '') $errors[] = 'Phone number is required.';
if ($area === '') $errors[] = 'Delivery area is required.';
if ($address === '') $errors[] = 'Delivery address is required.';
if (!in_array($payment_method, ['MTN Mobile Money', 'Airtel Money'], true)) $errors[] = 'Invalid payment method.';
if ($payment_phone === '') $errors[] = 'Mobile Money number is required.';
$pin_length = $payment_method === 'MTN Mobile Money' ? 5 : 4;
if (!is_string($payment_pin) || strlen($payment_pin) !== $pin_length || !ctype_digit($payment_pin)) {
    $errors[] = 'Enter the required number of digits for the simulated Mobile Money PIN.';
}
unset($payment_pin);

if (!empty($errors)) {
    // In a fuller build this would redisplay checkout.php with errors;
    // kept simple here since the HTML5 `required` attributes already
    // catch the common case client-side.
    die('Order could not be placed: ' . h(implode(' ', $errors)) . ' <a href="checkout.php">Go back</a>');
}

// --- Recalculate totals server-side (never trust client-sent totals) ---
$subtotal = 0;
foreach ($items as $item) {
    $subtotal += $item['line_total'];
}
$delivery_fee = delivery_fee_for_area($area);
$total = $subtotal + $delivery_fee;

// --- Simulate payment authorization (test mode; no real gateway) ---
// A random small chance of "failure" makes the simulation realistic
// for demoing both outcomes in a presentation.
$payment_status = (mt_rand(1, 100) <= 92) ? 'paid' : 'failed';

if ($payment_status === 'failed') {
    echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Payment Failed</title>';
    echo '<link rel="stylesheet" href="assets/css/style.css"></head><body>';
    echo '<div class="container" style="padding:60px 20px; text-align:center;">';
    echo '<h1>Payment Not Confirmed</h1>';
    echo '<p>Your simulated Mobile Money payment could not be confirmed. No charge was made and your cart has been kept.</p>';
    echo '<a href="checkout.php" class="btn">Try Again</a></div></body></html>';
    exit;
}

// --- Save the order (transaction keeps order + items consistent) ---
try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare(
        "INSERT INTO orders
        (customer_id, customer_name, customer_phone, customer_email, delivery_area, delivery_address,
         delivery_window, subtotal, delivery_fee, total, payment_method, payment_phone,
         payment_status, order_status)
        VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)"
    );
    $stmt->execute([
        $customer['id'], $name, $phone, $email, $area, $address, $window,
        $subtotal, $delivery_fee, $total, $payment_method, $payment_phone,
        'paid', 'confirmed'
    ]);
    $order_id = $pdo->lastInsertId();

    $itemStmt = $pdo->prepare(
        "INSERT INTO order_items (order_id, product_id, product_name, unit_price, unit, quantity, line_total)
         VALUES (?,?,?,?,?,?,?)"
    );
    foreach ($items as $item) {
        $itemStmt->execute([
            $order_id, $item['id'], $item['name'], $item['price'], $item['unit'],
            $item['quantity'], $item['line_total']
        ]);
        // Reduce stock accordingly.
        $pdo->prepare("UPDATE products SET stock = GREATEST(0, stock - ?) WHERE id = ?")
            ->execute([$item['quantity'], $item['id']]);
    }

    $pdo->commit();
} catch (Exception $e) {
    $pdo->rollBack();
    error_log('Order save failed: ' . $e->getMessage());
    die('Something went wrong while saving your order. Please try again.');
}

// Clear the cart now that the order is safely stored.
$_SESSION['cart'] = [];

header('Location: order-confirmation.php?order_id=' . $order_id);
exit;
