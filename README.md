# Zee Couture — PHP Fashion Store

A responsive fashion storefront built with PHP, MySQL, HTML, and CSS. It includes a product catalogue, search/category filters, product details, a session-based shopping bag, checkout, and order storage. **There is no admin dashboard**: manage products directly in phpMyAdmin.

## Requirements
- XAMPP (Apache + MySQL + PHP 8.0+ recommended)
- A web browser

## Install with XAMPP
1. Extract the `zee_couture` folder into `C:\xampp\htdocs\`.
2. Start **Apache** and **MySQL** in the XAMPP Control Panel.
3. Open `http://localhost/phpmyadmin`.
4. Choose **Import**, select `database.sql`, then click **Import/Go**. The script creates the `zee_couture` database and tables.
5. Open `config.php` and set `DB_USER` / `DB_PASS` to match your local MySQL credentials. XAMPP's default is often `root` with an empty password.
6. Visit `http://localhost/zee_couture/`.

## Add products in phpMyAdmin (no admin page)
1. Open phpMyAdmin → `zee_couture` → `products` → **Insert**.
2. Fill in these fields:
   - `name`: product name, e.g. `Classic Black Dress`
   - `description`: product details
   - `price`: price in naira, e.g. `28000`
   - `image`: image filename, e.g. `black-dress.jpg` (upload that image into the project's `uploads` folder), or a trusted `https://` image URL
   - `category`: e.g. `Dresses`, `Gowns`, `Two-Piece`
   - `sizes`: comma-separated sizes, e.g. `S,M,L,XL`; leave blank if not applicable
   - `stock`: number available
   - `is_featured`: `1` to feature it, `0` otherwise
3. Click **Go**. The product will appear on the shop page if its stock is greater than zero.

For images, save the image file inside `uploads/` and put only its filename in the `image` column. If `image` is blank, a placeholder illustration is displayed.

## Orders
Checkout saves customer details to `orders` and each purchased item to `order_items`. You can view these tables in phpMyAdmin. Stock is reduced when an order is placed. The checkout currently records **Pay on delivery** or **Bank transfer** as the customer's preference; it does not integrate a payment gateway or send emails.

## Important before publishing online
- Change database credentials and use a strong database password.
- Enable HTTPS and configure production error logging.
- Add privacy/retention practices for customer information.
- Set up a real payment provider if online payment is needed.
- Review stock and delivery processes before accepting real orders.

## Project files
- `index.php` — storefront, search, category filter
- `product.php` — product details and add-to-bag
- `cart.php` — shopping bag
- `checkout.php` — customer details and order creation
- `order_success.php` — confirmation
- `config.php` — database connection and shared helpers
- `database.sql` — database/table schema
- `assets/css/style.css` — responsive fashion-store design
