<?php
require __DIR__ . '/config.php';
$orderId = (int)($_SESSION['last_order_id'] ?? 0);
if ($orderId < 1) { header('Location: index.php'); exit; }
unset($_SESSION['last_order_id']);
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Order received | Zee Couture</title><link rel="stylesheet" href="assets/css/style.css"></head><body><header class="site-header"><a class="brand" href="index.php"><span class="brand-mark">Z</span><span>ZEE <b>COUTURE</b><small>WEAR YOUR CONFIDENCE</small></span></a><nav><a href="index.php">Shop</a></nav></header><main class="inner-page"><div class="success-card"><div class="success-check">✓</div><p class="eyebrow">ORDER RECEIVED</p><h1>Thank you for choosing <em>Zee Couture.</em></h1><p>Your order has been recorded successfully. Keep this order number for reference.</p><div class="order-number">ORDER #<?= $orderId ?></div><p>We'll use the contact details you provided to follow up on your order and delivery.</p><a class="button button-dark" href="index.php">Continue shopping ↗</a></div></main><footer><a class="footer-brand" href="index.php">ZEE COUTURE</a><span>Made for your moment.</span><span>© <?= date('Y') ?> Zee Couture</span></footer></body></html>
