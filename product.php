<?php
require 'config/db.php';
require 'includes/functions.php';

$slug = $_GET['slug'] ?? '';
$stmt = $pdo->prepare("SELECT * FROM products WHERE slug = ? AND is_active = 1");
$stmt->execute([$slug]);
$product = $stmt->fetch();

if (!$product) {
    http_response_code(404);
    $page_title = 'Product Not Found';
    require 'includes/header.php';
    echo '<div class="container empty-state"><h1>Product not found</h1><p><a href="shop.php">Back to shop</a></p></div>';
    require 'includes/footer.php';
    exit;
}

$page_title = $product['name'];

// Handle "Add to cart" submission.
$added = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $qty = max(1, (int) ($_POST['quantity'] ?? 1));
    cart_add($product['id'], $qty);
    $added = true;
}

require 'includes/header.php';
?>

<section class="container" style="padding-top:40px;">
    <p><a href="shop.php">&larr; Back to Shop</a></p>

    <?php if ($added): ?>
        <div class="alert alert-success">Added <?= h($product['name']) ?> to your cart. <a href="cart.php">View cart &rarr;</a></div>
    <?php endif; ?>

    <div class="product-detail">
        <div class="thumb-large"><?= product_image($product['image'], $product['name']) ?></div>
        <div>
            <h1><?= h($product['name']) ?></h1>
            <p><?= h($product['description']) ?></p>

            <div class="trust-row">
                <span class="item">🚜 <?= h($product['farm_source']) ?></span>
                <span class="item">🕒 <?= freshness_label($product['harvest_date']) ?></span>
                <span class="item">📦 <?= $product['stock'] > 0 ? $product['stock'] . ' ' . h($product['unit']) . ' in stock' : 'Out of stock' ?></span>
            </div>

            <p style="font-size:1.6rem; font-family:'Fraunces',serif; color:var(--leaf-green-dark); margin:0;">
                <?= money($product['price']) ?> <span style="font-size:1rem; color:#77776b;">/ <?= h($product['unit']) ?></span>
            </p>

            <?php if ($product['stock'] > 0): ?>
            <form method="post" class="qty-form">
                <input type="number" name="quantity" value="1" min="1" max="<?= (int) $product['stock'] ?>">
                <button type="submit" name="add_to_cart" class="btn">Add to Cart</button>
            </form>
            <?php else: ?>
            <p class="field-error">This item is currently out of stock — check back soon or contact us for expected availability.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php require 'includes/footer.php'; ?>
