<?php
require 'config/db.php';
require 'includes/functions.php';
$page_title = 'Shop';

$categories = get_categories($pdo);

// Read & sanitize filters from the query string.
$category_slug = $_GET['category'] ?? '';
$search = trim($_GET['q'] ?? '');

// Build the query dynamically but SAFELY: only bound parameters are
// ever interpolated into the SQL, never raw user input.
$sql = "SELECT p.*, c.slug AS cat_slug FROM products p
        JOIN categories c ON c.id = p.category_id
        WHERE p.is_active = 1";
$params = [];

if ($category_slug !== '') {
    $sql .= " AND c.slug = ?";
    $params[] = $category_slug;
}
if ($search !== '') {
    $sql .= " AND p.name LIKE ?";
    $params[] = '%' . $search . '%';
}
$sql .= " ORDER BY p.name";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

require 'includes/header.php';
?>

<section class="container" style="padding-top:40px;">
    <div class="section-head">
        <div>
            <h1>Shop Fresh Produce</h1>
            <p>Browse by category or search for what you need.</p>
        </div>
    </div>

    <form method="get" style="margin-bottom:24px; max-width:420px;">
        <?php if ($category_slug): ?><input type="hidden" name="category" value="<?= h($category_slug) ?>"><?php endif; ?>
        <input type="search" name="q" placeholder="Search products, e.g. tomatoes" value="<?= h($search) ?>">
    </form>

    <div class="category-pills">
        <a href="shop.php" class="<?= $category_slug=='' ? 'active' : '' ?>">All</a>
        <?php foreach ($categories as $c): ?>
        <a href="shop.php?category=<?= urlencode($c['slug']) ?>" class="<?= $category_slug==$c['slug'] ? 'active' : '' ?>"><?= h($c['name']) ?></a>
        <?php endforeach; ?>
    </div>

    <?php if (empty($products)): ?>
        <div class="empty-state">
            <h3>No products match your search</h3>
            <p>Try a different keyword or browse all categories.</p>
        </div>
    <?php else: ?>
    <div class="product-grid">
        <?php foreach ($products as $p): ?>
        <div class="product-card">
            <div class="thumb"><?= product_image($p['image'], $p['name']) ?></div>
            <div class="body">
                <h3><?= h($p['name']) ?></h3>
                <div class="farm">From <?= h($p['farm_source']) ?></div>
                <div class="fresh"><?= freshness_label($p['harvest_date']) ?></div>
                <?php if ($p['stock'] <= 0): ?>
                    <span class="stock-out">Out of stock</span>
                <?php endif; ?>
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
    <?php endif; ?>
</section>

<?php require 'includes/footer.php'; ?>
