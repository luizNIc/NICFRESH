<?php
require 'config/db.php';
require 'includes/functions.php';
$page_title = 'Your Cart';

// Handle quantity updates / removals posted from this page.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_cart'])) {
        foreach ($_POST['qty'] as $product_id => $qty) {
            cart_set_qty($product_id, $qty);
        }
    } elseif (isset($_POST['remove'])) {
        cart_remove($_POST['remove']);
    }
    header('Location: cart.php');
    exit;
}

$items = cart_items($pdo);
$subtotal = cart_subtotal($pdo);

require 'includes/header.php';
?>

<section class="container" style="padding-top:40px;">
    <h1>Your Cart</h1>

    <?php if (empty($items)): ?>
        <div class="empty-state">
            <h3>Your cart is empty</h3>
            <p>Browse fresh produce and add something to your basket.</p>
            <a href="shop.php" class="btn">Shop Now</a>
        </div>
    <?php else: ?>
    <form method="post">
        <table class="cart-table">
            <thead>
                <tr><th>Product</th><th>Unit Price</th><th>Quantity</th><th>Line Total</th><th></th></tr>
            </thead>
            <tbody>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td><?= h($item['name']) ?><br><span class="unit">from <?= h($item['farm_source']) ?></span></td>
                    <td><?= money($item['price']) ?> / <?= h($item['unit']) ?></td>
                    <td><input type="number" name="qty[<?= $item['id'] ?>]" value="<?= $item['quantity'] ?>" min="0" max="<?= (int) $item['stock'] ?>"></td>
                    <td><?= money($item['line_total']) ?></td>
                    <td><button type="submit" name="remove" value="<?= $item['id'] ?>" class="btn btn-danger" style="padding:6px 12px; font-size:0.82rem;">Remove</button></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <button type="submit" name="update_cart" class="btn btn-secondary">Update Quantities</button>
    </form>

    <div class="cart-summary" style="margin-top:30px;">
        <div class="row"><span>Subtotal</span><span><?= money($subtotal) ?></span></div>
        <div class="row"><span>Delivery fee</span><span>Calculated at checkout</span></div>
        <div class="row total"><span>Total</span><span><?= money($subtotal) ?>+</span></div>
        <a href="checkout.php" class="btn btn-block" style="margin-top:14px;">Proceed to Checkout</a>
    </div>
    <?php endif; ?>
</section>

<?php require 'includes/footer.php'; ?>
