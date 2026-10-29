<?php
require 'config/db.php';
require 'includes/functions.php';
require 'includes/customer-auth.php';
require_customer();
$page_title = 'Order Confirmed';

$order_id = (int) ($_GET['order_id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND customer_id = ?");
$stmt->execute([$order_id, customer_id()]);
$order = $stmt->fetch();

if (!$order) {
    header('Location: index.php');
    exit;
}

$itemStmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
$itemStmt->execute([$order_id]);
$items = $itemStmt->fetchAll();

require 'includes/header.php';
?>

<section class="container" style="padding-top:40px; max-width:700px;">
    <div class="alert alert-success">
        <strong>Order #<?= $order['id'] ?> confirmed!</strong> Payment received via <?= h($order['payment_method']) ?>.
    </div>

    <h1>Thank you, <?= h($order['customer_name']) ?>!</h1>
    <p>Your order will be delivered to <strong><?= h($order['delivery_area']) ?></strong> during your chosen window: <strong><?= h($order['delivery_window']) ?></strong>.</p>

    <div class="order-summary-box" style="margin:24px 0;">
        <h3>Order Items</h3>
        <?php foreach ($items as $item): ?>
        <div class="line">
            <span><?= h($item['product_name']) ?> &times; <?= $item['quantity'] ?> <?= h($item['unit']) ?></span>
            <span><?= money($item['line_total']) ?></span>
        </div>
        <?php endforeach; ?>
        <div class="line" style="border-top:1px solid var(--line); padding-top:10px; margin-top:10px;">
            <span>Subtotal</span><span><?= money($order['subtotal']) ?></span>
        </div>
        <div class="line"><span>Delivery fee</span><span><?= money($order['delivery_fee']) ?></span></div>
        <div class="line" style="font-weight:700; font-family:'Fraunces',serif; font-size:1.1rem;">
            <span>Total Paid</span><span><?= money($order['total']) ?></span>
        </div>
    </div>

    <h3>Delivery Address</h3>
    <p><?= nl2br(h($order['delivery_address'])) ?></p>
    <p>We'll contact you on <strong><?= h($order['customer_phone']) ?></strong> before delivery. Questions? <a href="contact.php">Reach our support team</a>.</p>

    <a href="shop.php" class="btn">Continue Shopping</a>
</section>

<?php require 'includes/footer.php'; ?>
