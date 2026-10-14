<?php
require __DIR__ . '/config.php';
$cart = $_SESSION['cart'] ?? [];
if (!$cart) { header('Location: cart.php'); exit; }
$total = array_sum(array_map(fn($item) => $item['price'] * $item['quantity'], $cart));
$errors = [];
$values = ['customer_name'=>'','email'=>'','phone'=>'','address'=>'','city'=>'','notes'=>'','payment_method'=>'Pay on delivery'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($values as $key => $default) $values[$key] = trim((string)($_POST[$key] ?? $default));
    if ($values['customer_name'] === '') $errors[] = 'Enter your full name.';
    if (!filter_var($values['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Enter a valid email address.';
    if ($values['phone'] === '') $errors[] = 'Enter your phone number.';
    if ($values['address'] === '') $errors[] = 'Enter your delivery address.';
    if ($values['city'] === '') $errors[] = 'Enter your city.';
    if (!in_array($values['payment_method'], ['Pay on delivery', 'Bank transfer'], true)) $values['payment_method'] = 'Pay on delivery';

    // Recheck product availability and current prices before saving the order.
    $freshItems = [];
    $freshTotal = 0;
    foreach ($cart as $key => $item) {
        $stmt = $pdo->prepare('SELECT id, name, price, stock FROM products WHERE id = ?');
        $stmt->execute([$item['product_id']]);
        $fresh = $stmt->fetch();
        if (!$fresh || (int)$fresh['stock'] < (int)$item['quantity']) {
            $errors[] = 'A product in your bag is no longer available in the requested quantity. Please update your bag.';
            continue;
        }
        $item['name'] = $fresh['name'];
        $item['price'] = (float)$fresh['price'];
        $freshItems[$key] = $item;
        $freshTotal += $item['price'] * $item['quantity'];
    }
    if (!$errors) {
        try {
            $pdo->beginTransaction();
            $stmt = $pdo->prepare('INSERT INTO orders (customer_name,email,phone,address,city,notes,total,payment_method) VALUES (?,?,?,?,?,?,?,?)');
            $stmt->execute([$values['customer_name'],$values['email'],$values['phone'],$values['address'],$values['city'],$values['notes'],$freshTotal,$values['payment_method']]);
            $orderId = (int)$pdo->lastInsertId();
            $itemStmt = $pdo->prepare('INSERT INTO order_items (order_id,product_id,product_name,unit_price,quantity,size) VALUES (?,?,?,?,?,?)');
            $stockStmt = $pdo->prepare('UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?');
            foreach ($freshItems as $item) {
                $itemStmt->execute([$orderId,$item['product_id'],$item['name'],$item['price'],$item['quantity'],$item['size'] ?? null]);
                $stockStmt->execute([$item['quantity'],$item['product_id'],$item['quantity']]);
                if ($stockStmt->rowCount() !== 1) throw new RuntimeException('Stock changed during checkout.');
            }
            $pdo->commit();
            $_SESSION['cart'] = [];
            $_SESSION['last_order_id'] = $orderId;
            header('Location: order_success.php'); exit;
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            $errors[] = 'We could not complete your order right now. Please try again.';
        }
    }
}
$cartCount = array_sum(array_map(fn($item) => (int)$item['quantity'], $_SESSION['cart'] ?? []));
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Checkout | Zee Couture</title><link rel="stylesheet" href="assets/css/style.css"></head><body>
<header class="site-header"><a class="brand" href="index.php"><span class="brand-mark">Z</span><span>ZEE <b>COUTURE</b><small>WEAR YOUR CONFIDENCE</small></span></a><nav><a href="index.php">Shop</a><a href="index.php#about">Our Story</a><a href="index.php#contact">Contact</a><a class="cart-link" href="cart.php">Bag <span><?= $cartCount ?></span></a></nav></header>
<main class="inner-page"><p class="eyebrow">ALMOST YOURS</p><h1 class="page-title">Checkout <em>details.</em></h1>
<?php if ($errors): ?><div class="form-error"><ul><?php foreach (array_unique($errors) as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
<div class="checkout-layout"><form method="post" class="checkout-form"><h2>Delivery information</h2><div class="form-grid"><label>Full name<input name="customer_name" value="<?= e($values['customer_name']) ?>" required autocomplete="name"></label><label>Email address<input type="email" name="email" value="<?= e($values['email']) ?>" required autocomplete="email"></label><label>Phone number<input name="phone" value="<?= e($values['phone']) ?>" required autocomplete="tel"></label><label>City / town<input name="city" value="<?= e($values['city']) ?>" required autocomplete="address-level2"></label><label class="full-field">Delivery address<textarea name="address" rows="3" required autocomplete="street-address"><?= e($values['address']) ?></textarea></label><label class="full-field">Order note (optional)<textarea name="notes" rows="2"><?= e($values['notes']) ?></textarea></label><label class="full-field">Payment preference<select name="payment_method"><option <?= $values['payment_method']==='Pay on delivery'?'selected':'' ?>>Pay on delivery</option><option <?= $values['payment_method']==='Bank transfer'?'selected':'' ?>>Bank transfer</option></select></label></div><button class="button button-dark button-wide" type="submit">Place my order <span>↗</span></button><p class="small-note">This demo records your order. It does not process online card payments.</p></form>
<aside class="order-summary"><p class="eyebrow">YOUR ORDER</p><?php foreach ($cart as $item): ?><div class="checkout-product"><span><?= e($item['name']) ?> × <?= (int)$item['quantity'] ?><?php if (!empty($item['size'])): ?> <small>(<?= e($item['size']) ?>)</small><?php endif; ?></span><strong><?= money($item['price']*$item['quantity']) ?></strong></div><?php endforeach; ?><div class="summary-total"><span>Total</span><strong><?= money($total) ?></strong></div><p class="summary-note">Delivery fees, if any, will be confirmed with you.</p></aside></div></main><footer><a class="footer-brand" href="index.php">ZEE COUTURE</a><span>Made for your moment.</span><span>© <?= date('Y') ?> Zee Couture</span></footer></body></html>
