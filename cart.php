<?php
require __DIR__ . '/config.php';
$cart = $_SESSION['cart'] ?? [];
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['remove'])) {
        unset($_SESSION['cart'][(string)$_POST['remove']]);
    } elseif (isset($_POST['update']) && is_array($_POST['qty'] ?? null)) {
        foreach ($_POST['qty'] as $key => $quantity) {
            if (!isset($_SESSION['cart'][$key])) continue;
            $quantity = max(1, (int)$quantity);
            $stmt = $pdo->prepare('SELECT stock FROM products WHERE id = ?');
            $stmt->execute([$_SESSION['cart'][$key]['product_id']]);
            $stock = (int)$stmt->fetchColumn();
            if ($stock < 1) unset($_SESSION['cart'][$key]);
            else $_SESSION['cart'][$key]['quantity'] = min($quantity, $stock);
        }
    }
    header('Location: cart.php'); exit;
}
$cart = $_SESSION['cart'] ?? [];
$total = array_sum(array_map(fn($item) => $item['price'] * $item['quantity'], $cart));
$cartCount = array_sum(array_map(fn($item) => (int)$item['quantity'], $cart));
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Your Bag | Zee Couture</title><link rel="stylesheet" href="assets/css/style.css"></head><body>
<header class="site-header"><a class="brand" href="index.php"><span class="brand-mark">Z</span><span>ZEE <b>COUTURE</b><small>WEAR YOUR CONFIDENCE</small></span></a><nav><a href="index.php">Shop</a><a href="index.php#about">Our Story</a><a href="index.php#contact">Contact</a><a class="cart-link active" href="cart.php">Bag <span><?= $cartCount ?></span></a></nav></header>
<main class="inner-page"><p class="eyebrow">YOUR SELECTION</p><h1 class="page-title">Your shopping <em>bag.</em></h1>
<?php if (isset($_GET['added'])): ?><p class="success-message">Added to your bag. Lovely choice!</p><?php endif; ?>
<?php if (!$cart): ?><div class="empty-state"><div class="empty-icon">✳</div><h3>Your bag is waiting for something lovely.</h3><p>Explore our collection and find a piece that feels like you.</p><a class="button button-dark" href="index.php">Explore the collection</a></div>
<?php else: ?><div class="cart-layout"><form method="post" class="cart-items">
<?php foreach ($cart as $key => $item): ?><article class="cart-item"><img src="<?= e(productImage($item['image'] ?? null)) ?>" alt="<?= e($item['name']) ?>"><div class="cart-item-info"><a href="product.php?id=<?= (int)$item['product_id'] ?>"><?= e($item['name']) ?></a><?php if (!empty($item['size'])): ?><span>Size: <?= e($item['size']) ?></span><?php endif; ?><span><?= money($item['price']) ?> each</span><button class="remove-button" type="submit" name="remove" value="<?= e($key) ?>">Remove</button></div><div class="cart-quantity"><label for="qty-<?= md5($key) ?>">Qty</label><input id="qty-<?= md5($key) ?>" type="number" name="qty[<?= e($key) ?>]" min="1" value="<?= (int)$item['quantity'] ?>"></div><strong><?= money($item['price'] * $item['quantity']) ?></strong></article><?php endforeach; ?>
<button class="button button-outline" type="submit" name="update" value="1">Update bag</button></form>
<aside class="order-summary"><p class="eyebrow">ORDER SUMMARY</p><div class="summary-line"><span>Subtotal</span><strong><?= money($total) ?></strong></div><div class="summary-line"><span>Delivery</span><span>Confirmed after order</span></div><div class="summary-total"><span>Total</span><strong><?= money($total) ?></strong></div><p class="summary-note">Delivery fees will be confirmed with you after checkout.</p><a class="button button-dark button-wide" href="checkout.php">Continue to checkout <span>↗</span></a><a class="continue-shopping" href="index.php">← Continue shopping</a></aside></div><?php endif; ?>
</main><footer><a class="footer-brand" href="index.php">ZEE COUTURE</a><span>Made for your moment.</span><span>© <?= date('Y') ?> Zee Couture</span></footer></body></html>
