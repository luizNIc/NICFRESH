<?php
require 'config/db.php';
require 'includes/functions.php';
require 'includes/customer-auth.php';
$page_title = 'Checkout';

$items = cart_items($pdo);
if (empty($items)) {
    header('Location: cart.php');
    exit;
}
require_customer();

$customerStmt = $pdo->prepare('SELECT full_name, email, phone FROM customers WHERE id = ?');
$customerStmt->execute([customer_id()]);
$customer = $customerStmt->fetch();
if (!$customer) {
    unset($_SESSION['customer_id'], $_SESSION['customer_name']);
    header('Location: login.php?return=checkout.php');
    exit;
}
$subtotal = cart_subtotal($pdo);

// Recalculate the delivery fee live if an area was already chosen
// (used only to pre-fill the summary; process-order.php recalculates
// it again server-side as the authoritative figure).
$selected_area = $_GET['area'] ?? '';
$delivery_fee = $selected_area ? delivery_fee_for_area($selected_area) : 0;

$areas = ['Nyendo', 'Kyabakuza', 'Ndegeya', 'Nyabikungu', 'Katwe (Masaka)', 'Other (within Masaka)'];

require 'includes/header.php';
?>

<section class="container" style="padding-top:40px;">
    <h1>Checkout</h1>

    <div class="checkout-layout">
        <div>
            <form method="post" action="process-order.php" id="checkoutForm">
                <h3>Delivery Details</h3>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="customer_name" value="<?= h($customer['full_name']) ?>" autocomplete="name" required>
                    </div>
                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="tel" name="customer_phone" value="<?= h($customer['phone']) ?>" placeholder="07XXXXXXXX" autocomplete="tel" required>
                    </div>
                    <div class="form-group full">
                        <label>Account Email</label>
                        <input type="email" value="<?= h($customer['email']) ?>" autocomplete="email" disabled>
                    </div>
                    <div class="form-group">
                        <label>Delivery Area</label>
                        <select name="delivery_area" id="deliveryArea" required>
                            <option value="">Select area</option>
                            <?php foreach ($areas as $a): ?>
                            <option value="<?= h($a) ?>" <?= $a===$selected_area?'selected':'' ?>><?= h($a) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Preferred Delivery Window</label>
                        <select name="delivery_window" required>
                            <option value="Today evening">Today evening (order before 1pm)</option>
                            <option value="Tomorrow morning">Tomorrow morning</option>
                            <option value="Tomorrow evening">Tomorrow evening</option>
                        </select>
                    </div>
                    <div class="form-group full">
                        <label>Delivery Address (landmark / directions)</label>
                        <textarea name="delivery_address" required placeholder="e.g. Near Masaka Regional Hospital, blue gate"></textarea>
                    </div>
                </div>

                <h3>Payment</h3>
                <p class="helper-text" style="margin-bottom:14px;">This is a <strong>test-mode simulation</strong> — MTN accepts a simulated 5-digit PIN and Airtel a simulated 4-digit PIN. Never enter your real Mobile Money PIN; no real money moves and no live network is contacted.</p>

                <div class="payment-methods">
                    <label><input type="radio" name="payment_method" value="MTN Mobile Money" checked><span>MTN Mobile Money</span></label>
                    <label><input type="radio" name="payment_method" value="Airtel Money"><span>Airtel Money</span></label>
                </div>

                <div class="form-group">
                    <label>Mobile Money Number</label>
                    <input type="tel" name="payment_phone" placeholder="07XXXXXXXX" required>
                </div>

                <input type="hidden" name="payment_pin" id="paymentPinField">
                <button type="submit" class="btn btn-block">Pay & Place Order</button>
            </form>
        </div>

        <div class="order-summary-box">
            <h3>Order Summary</h3>
            <?php foreach ($items as $item): ?>
            <div class="line">
                <span><?= h($item['name']) ?> &times; <?= $item['quantity'] ?></span>
                <span><?= money($item['line_total']) ?></span>
            </div>
            <?php endforeach; ?>
            <div class="line" style="border-top:1px solid var(--line); padding-top:10px; margin-top:10px;">
                <span>Subtotal</span><span><?= money($subtotal) ?></span>
            </div>
            <div class="line" id="deliveryFeeLine">
                <span>Delivery fee</span><span id="deliveryFeeAmount">Select area</span>
            </div>
            <div class="line" style="font-weight:700; font-family:'Fraunces',serif; font-size:1.1rem;">
                <span>Total</span><span id="totalAmount"><?= money($subtotal) ?></span>
            </div>
        </div>
    </div>
</section>

<!-- Simulated Mobile Money PIN confirmation modal -->
<div class="modal-overlay" id="paymentModal">
    <div class="modal-box" id="modalStepPin">
        <span class="badge-sim">SIMULATED PAYMENT — TEST MODE</span>
        <h3>Confirm Simulated Payment</h3>
        <p id="modalPromptText">Enter a simulated Mobile Money PIN. Never enter your real Mobile Money PIN.</p>
        <input type="password" id="pinInput" maxlength="5" placeholder="*****" inputmode="numeric" autocomplete="off">
        <p class="field-error" id="pinError" style="display:none;"></p>
        <button class="btn btn-block" id="confirmPinBtn" style="margin-top:14px;">Confirm</button>
        <button class="btn btn-outline btn-block" id="cancelPayBtn" style="margin-top:10px; color:var(--ink); border-color:var(--line);">Cancel</button>
    </div>
    <div class="modal-box" id="modalStepProcessing" style="display:none;">
        <div class="spinner"></div>
        <p>Processing your payment&hellip;</p>
    </div>
</div>

<script src="assets/js/checkout.js"></script>

<?php require 'includes/footer.php'; ?>
