<?php
require __DIR__ . '/config.php';
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$stmt = $pdo->prepare('SELECT * FROM products WHERE id = ? AND stock > 0');
$stmt->execute([$id ?: 0]);
$product = $stmt->fetch();
if (!$product) { http_response_code(404); }
$cartCount = array_sum(array_map(fn($item) => (int)$item['quantity'], $_SESSION['cart'] ?? []));
$error = '';
if ($product && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT) ?: 1;
    $size = trim((string)($_POST['size'] ?? ''));
    $allowedSizes = array_filter(array_map('trim', explode(',', (string)$product['sizes'])));
    if ($quantity < 1 || $quantity > (int)$product['stock']) {
        $error = 'Please choose a quantity within the available stock.';
    } elseif ($allowedSizes && !in_array($size, $allowedSizes, true)) {
        $error = 'Please select a size.';
    } else {
        if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
        $key = $product['id'] . ':' . $size;
        $existing = (int)($_SESSION['cart'][$key]['quantity'] ?? 0);
        $_SESSION['cart'][$key] = ['product_id' => (int)$product['id'], 'name' => $product['name'], 'price' => (float)$product['price'], 'image' => $product['image'], 'quantity' => min($existing + $quantity, (int)$product['stock']), 'size' => $size];
        header('Location: cart.php?added=1'); exit;
    }
}
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title><?= $product ? e($product['name']) . ' | Zee Couture' : 'Product not found | Zee Couture' ?></title><link rel="stylesheet" href="assets/css/style.css"></head><body>
<header class="site-header"><a class="brand" href="index.php"><span class="brand-mark">Z</span><span>ZEE <b>COUTURE</b><small>WEAR YOUR CONFIDENCE</small></span></a><nav><a href="index.php">Shop</a><a href="index.php#about">Our Story</a><a href="index.php#contact">Contact</a><a class="cart-link" href="cart.php">Bag <span><?= $cartCount ?></span></a></nav></header>
<main class="inner-page">
<?php if (!$product): ?><div class="empty-state"><h2>We couldn't find that piece.</h2><a class="button button-dark" href="index.php">Back to shop</a></div>
<?php else: ?>
<a class="back-link" href="index.php">← Back to collection</a><div class="product-detail">
<div class="detail-image"><img src="<?= e(productImage($product['image'])) ?>" alt="<?= e($product['name']) ?>"></div>
<div class="detail-copy"><p class="eyebrow"><?= e($product['category'] ?: 'ZEE COLLECTION') ?></p><h1><?= e($product['name']) ?></h1><p class="detail-price"><?= money($product['price']) ?></p><p class="detail-description"><?= nl2br(e($product['description'] ?: 'A thoughtfully selected piece from Zee Couture.')) ?></p><p class="stock-note"><?= (int)$product['stock'] ?> available</p>
<?php if ($error): ?><p class="form-error"><?= e($error) ?></p><?php endif; ?>
<form method="post" class="add-form">
<?php $sizes = array_filter(array_map('trim', explode(',', (string)$product['sizes']))); if ($sizes): ?><label for="size">Choose your size</label><select id="size" name="size" required><option value="">Select size</option><?php foreach ($sizes as $size): ?><option value="<?= e($size) ?>"><?= e($size) ?></option><?php endforeach; ?></select><?php endif; ?>
<label for="quantity">Quantity</label><input id="quantity" type="number" name="quantity" value="1" min="1" max="<?= (int)$product['stock'] ?>" required><button class="button button-dark button-wide" type="submit">Add to bag <span>↗</span></button>
</form><p class="detail-footnote">Secure your selection today. Payment options are confirmed during checkout.</p></div></div>
<?php endif; ?></main><footer><a class="footer-brand" href="index.php">ZEE COUTURE</a><span>Made for your moment.</span><span>© <?= date('Y') ?> Zee Couture</span></footer></body></html>
