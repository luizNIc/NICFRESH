<?php
require 'config/db.php';
require 'includes/functions.php';
require 'includes/customer-auth.php';
require_customer();

$page_title = 'My Account';
$stmt = $pdo->prepare('SELECT id, full_name, email, phone, analytics_consent FROM customers WHERE id = ?');
$stmt->execute([customer_id()]);
$customer = $stmt->fetch();

if (!$customer) {
    unset($_SESSION['customer_id'], $_SESSION['customer_name']);
    header('Location: login.php');
    exit;
}

$ordersStmt = $pdo->prepare('SELECT * FROM orders WHERE customer_id = ? ORDER BY created_at DESC');
$ordersStmt->execute([$customer['id']]);
$orders = $ordersStmt->fetchAll();
$analyticsVisitToken = $_SESSION['analytics_visit_token'] ??= bin2hex(random_bytes(32));

require 'includes/header.php';
?>
<section class="container" style="padding-top:40px;">
    <div class="section-head">
        <div>
            <h1>My Account</h1>
            <p>Welcome, <?= h($customer['full_name']) ?>.</p>
        </div>
        <a class="btn btn-outline" href="logout.php">Log Out</a>
    </div>

    <div class="order-summary-box" style="max-width:600px; margin-bottom:32px;">
        <h2>Account Details</h2>
        <p><strong>Name:</strong> <?= h($customer['full_name']) ?></p>
        <p><strong>Email:</strong> <?= h($customer['email']) ?></p>
        <p><strong>Phone:</strong> <?= h($customer['phone']) ?></p>
    </div>

    <div class="order-summary-box" style="max-width:600px; margin-bottom:32px;">
        <h2>Analytics privacy</h2>
        <p><?= (int) $customer['analytics_consent'] === 1
            ? 'You have allowed analytics, including named page visits while signed in.'
            : 'Analytics is off for your account. If enabled, your signed-in page visits and visit times are recorded for business analysis.' ?></p>
        <button type="button" class="btn" id="analyticsPreference">
            <?= (int) $customer['analytics_consent'] === 1 ? 'Withdraw analytics consent' : 'Allow analytics' ?>
        </button>
        <p id="analyticsPreferenceStatus" role="status"></p>
    </div>

    <h2>Your Orders</h2>
    <?php if (!$orders): ?>
        <div class="empty-state">
            <p>You have not placed any orders yet.</p>
            <a href="shop.php" class="btn">Shop Fresh Produce</a>
        </div>
    <?php else: ?>
        <table class="cart-table">
            <thead><tr><th>Order</th><th>Date</th><th>Total</th><th>Status</th></tr></thead>
            <tbody>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td>#<?= (int) $order['id'] ?></td>
                    <td><?= h(date('d M Y', strtotime($order['created_at']))) ?></td>
                    <td><?= money($order['total']) ?></td>
                    <td><?= h(ucfirst($order['order_status'])) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>
<script>
(function() {
    var analyticsAllowed = <?= (int) $customer['analytics_consent'] === 1 ? 'true' : 'false' ?>;
    var consentKey = 'nicfresh-analytics-consent-v2-customer-' + <?= (int) $customer['id'] ?>;
    var button = document.getElementById('analyticsPreference');
    var status = document.getElementById('analyticsPreferenceStatus');

    button.addEventListener('click', function() {
        button.disabled = true;
        fetch('analytics-consent.php', {
            method: 'POST',
            credentials: 'same-origin',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({
                accepted: !analyticsAllowed,
                token: <?= json_encode($analyticsVisitToken) ?>
            })
        }).then(function(response) {
            if (!response.ok) {
                throw new Error('Could not save analytics preference (' + response.status + ').');
            }
            localStorage.setItem(consentKey, analyticsAllowed ? 'declined' : 'accepted');
            window.location.reload();
        }).catch(function(error) {
            console.error(error);
            status.textContent = 'We could not save your preference. Please try again.';
            button.disabled = false;
        });
    });
})();
</script>
<?php require 'includes/footer.php'; ?>
