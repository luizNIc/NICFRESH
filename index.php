<?php
require 'config/db.php';
require 'includes/functions.php';
$page_title = 'Home';

// Featured products: the 4 most recently added active items.
$featured = $pdo->query(
    "SELECT * FROM products WHERE is_active = 1 ORDER BY created_at DESC LIMIT 4"
)->fetchAll();

$marketCrops = $pdo->query(
    "SELECT * FROM products
     WHERE is_active = 1 AND stock > 0
       AND slug IN ('fresh-nakati', 'dodo-amaranth', 'egg-plant', 'green-pepper', 'ground-nuts', 'row-banana')
     ORDER BY FIELD(slug, 'fresh-nakati', 'dodo-amaranth', 'egg-plant', 'green-pepper', 'ground-nuts', 'row-banana')"
)->fetchAll();

require 'includes/header.php';
?>

<section class="hero">
    <div class="hero-inner">
        <div>
            <span class="tag">Farm-to-door in Masaka</span>
            <h1>Fresh produce, straight from the farm to your kitchen.</h1>
            <p>We work directly with a small network of trusted farms around Masaka so you get vegetables, fruits and staples that were still in the ground yesterday — no middlemen, no markups from months of storage.</p>
            <a href="shop.php" class="btn">Shop Fresh Produce</a>
            <a href="about.php" class="btn btn-outline">Meet Our Farms</a>
        </div>
        <div class="hero-card">
            <h3>Why households choose us</h3>
            <ul>
                <li><strong>Same-day or next-day delivery</strong> across Masaka town and nearby areas</li>
                <li><strong>Named source farms</strong> on every product — know exactly where your food came from</li>
                <li><strong>Harvest-dated</strong> produce, never sitting in a warehouse for weeks</li>
                <li><strong>Mobile Money checkout</strong> — MTN &amp; Airtel</li>
            </ul>
        </div>
    </div>
</section>

<?php if ($marketCrops): ?>
<section class="container crop-market-section" aria-labelledby="cropMarketTitle">
    <div class="section-head">
        <div>
            <h2 id="cropMarketTitle">From Masaka farms to market</h2>
            <p>Fresh crops available now from our partner farmers.</p>
        </div>
        <a href="shop.php" class="btn btn-secondary">Shop all produce</a>
    </div>

    <div class="crop-market-banner">
        <div class="crop-market-track" role="list" aria-label="Available crops from our farmers">
            <div class="crop-market-group">
                <?php foreach ($marketCrops as $crop): ?>
                <a class="crop-market-card" role="listitem" href="product.php?slug=<?= urlencode($crop['slug']) ?>">
                    <?= product_image($crop['image'], $crop['name']) ?>
                    <span class="crop-market-caption">
                        <strong><?= h($crop['name']) ?></strong>
                        <span>From <?= h($crop['farm_source']) ?></span>
                    </span>
                </a>
                <?php endforeach; ?>
            </div>
            <div class="crop-market-group" aria-hidden="true">
                <?php foreach ($marketCrops as $crop): ?>
                <a class="crop-market-card" tabindex="-1" href="product.php?slug=<?= urlencode($crop['slug']) ?>">
                    <?= product_image($crop['image'], $crop['name']) ?>
                    <span class="crop-market-caption">
                        <strong><?= h($crop['name']) ?></strong>
                        <span>From <?= h($crop['farm_source']) ?></span>
                    </span>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="container">
    <div class="section-head">
        <div>
            <h2>Freshly Listed</h2>
            <p>Recently added by our partner farms.</p>
        </div>
        <a href="shop.php" class="btn btn-secondary">View All</a>
    </div>

    <div class="product-grid">
        <?php foreach ($featured as $p): ?>
        <div class="product-card">
            <div class="thumb"><?= product_image($p['image'], $p['name']) ?></div>
            <div class="body">
                <h3><?= h($p['name']) ?></h3>
                <div class="farm">From <?= h($p['farm_source']) ?></div>
                <div class="fresh"><?= freshness_label($p['harvest_date']) ?></div>
                <div class="price-row">
                    <div>
                        <span class="price"><?= money($p['price']) ?></span>
                        <span class="unit">/ <?= h($p['unit']) ?></span>
                    </div>
                    <a href="product.php?slug=<?= urlencode($p['slug']) ?>" class="btn btn-secondary">View</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<section class="container">
    <div class="info-cards">
        <div class="info-card">
            <h3>🚚 Fast Delivery</h3>
            <p>Order before our daily cut-off and get same-day delivery within Masaka town.</p>
        </div>
        <div class="info-card">
            <h3>🌱 Direct from Farms</h3>
            <p>Every product lists its exact source farm — full traceability, fair prices for growers.</p>
        </div>
        <div class="info-card">
            <h3>📱 Mobile Money</h3>
            <p>Pay securely with MTN or Airtel Mobile Money at checkout.</p>
        </div>
    </div>
</section>

<?php require 'includes/footer.php'; ?>
