<?php
require __DIR__ . '/config.php';

$q = trim((string)($_GET['q'] ?? ''));
$category = trim((string)($_GET['category'] ?? ''));

$sql = 'SELECT * FROM products WHERE stock > 0';
$params = [];
if ($q !== '') {
    $sql .= ' AND (name LIKE :q OR description LIKE :q OR category LIKE :q)';
    $params['q'] = '%' . $q . '%';
}
if ($category !== '') {
    $sql .= ' AND category = :category';
    $params['category'] = $category;
}
$sql .= ' ORDER BY is_featured DESC, created_at DESC';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

$categories = $pdo->query("SELECT DISTINCT category FROM products WHERE category IS NOT NULL AND category <> '' ORDER BY category")->fetchAll(PDO::FETCH_COLUMN);
$cartCount = array_sum(array_map(fn($item) => (int)$item['quantity'], $_SESSION['cart'] ?? []));
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Zee Couture | Wear Your Confidence</title>
    <meta name="description" content="Discover elegant fashion and timeless pieces at Zee Couture.">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header class="site-header">
    <a class="brand" href="index.php"><span class="brand-mark">Z</span><span>ZEE <b>COUTURE</b><small>WEAR YOUR CONFIDENCE</small></span></a>
    <nav><a class="active" href="index.php">Shop</a><a href="#about">Our Story</a><a href="#contact">Contact</a><a class="cart-link" href="cart.php">Bag <span><?= $cartCount ?></span></a></nav>
</header>
<main>
    <section class="hero">
        <div class="hero-copy">
            <p class="eyebrow">THE NEW SEASON EDIT</p>
            <h1>Style that speaks.<br><em>Elegance that lasts.</em></h1>
            <p>Discover thoughtfully selected pieces made for your confidence, your moments, and your everyday style.</p>
            <a class="button button-dark" href="#shop">Explore the collection <span>↗</span></a>
        </div>
        <div class="hero-art" aria-label="Fashion editorial artwork">
            <div class="hero-orbit"></div><div class="hero-label">NEW<br>ARRIVALS</div>
            <div class="hero-figure"><div class="figure-head"></div><div class="figure-hair"></div><div class="figure-body"></div><div class="figure-skirt"></div></div>
            <div class="hero-caption">CURATED FOR YOU · 2026</div>
        </div>
    </section>

    <section class="shop-section" id="shop">
        <div class="section-heading"><div><p class="eyebrow">THE ZEE EDIT</p><h2>Find your <em>signature look.</em></h2></div><p>Pieces you'll reach for, again and again.</p></div>
        <form class="filters" method="get" action="index.php">
            <label class="search-box"><span>⌕</span><input type="search" name="q" value="<?= e($q) ?>" placeholder="Search styles, pieces..."></label>
            <select name="category" aria-label="Filter by category">
                <option value="">All categories</option>
                <?php foreach ($categories as $cat): ?><option value="<?= e((string)$cat) ?>" <?= $category === $cat ? 'selected' : '' ?>><?= e((string)$cat) ?></option><?php endforeach; ?>
            </select>
            <button class="button button-outline" type="submit">Find pieces</button>
            <?php if ($q !== '' || $category !== ''): ?><a class="clear-filter" href="index.php">Clear</a><?php endif; ?>
        </form>

        <?php if (!$products): ?>
            <div class="empty-state"><div class="empty-icon">✳</div><h3>The collection is getting ready.</h3><p>No products match your search yet. Add products through phpMyAdmin or try another search.</p><a href="index.php" class="text-link">View all pieces ↗</a></div>
        <?php else: ?>
            <div class="product-grid">
                <?php foreach ($products as $product): ?>
                    <article class="product-card">
                        <a class="product-image" href="product.php?id=<?= (int)$product['id'] ?>">
                            <img src="<?= e(productImage($product['image'])) ?>" alt="<?= e($product['name']) ?>" loading="lazy">
                            <?php if ((int)$product['is_featured'] === 1): ?><span class="product-tag">FEATURED</span><?php endif; ?>
                            <span class="image-arrow">↗</span>
                        </a>
                        <div class="product-meta"><span><?= e($product['category'] ?: 'Zee Collection') ?></span><span><?= (int)$product['stock'] ?> left</span></div>
                        <a class="product-name" href="product.php?id=<?= (int)$product['id'] ?>"><?= e($product['name']) ?></a>
                        <div class="product-bottom"><strong><?= money($product['price']) ?></strong><a href="product.php?id=<?= (int)$product['id'] ?>">View item <span>↗</span></a></div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

    <section class="brand-story" id="about"><div class="story-symbol">Z<span>.</span></div><div><p class="eyebrow">A LITTLE ABOUT US</p><h2>Fashion is more than what you wear.</h2><p>At Zee Couture, we believe the right outfit helps you show up as your most confident self. Explore pieces selected with style, comfort, and individuality in mind.</p></div><a class="text-link" href="#shop">Shop the collection ↗</a></section>
    <section class="contact-strip" id="contact"><div><p class="eyebrow">NEED A HAND?</p><h2>We're happy to help.</h2></div><p>Questions about sizes, orders, or a look you love? Reach out and we'll help you find the right fit.</p><a class="button button-light" href="mailto:hello@zeecouture.example">Contact Zee Couture ↗</a></section>
</main>
<footer><a class="footer-brand" href="index.php">ZEE COUTURE</a><span>Made for your moment.</span><span>© <?= date('Y') ?> Zee Couture</span></footer>
</body>
</html>
