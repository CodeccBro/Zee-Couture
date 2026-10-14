CREATE DATABASE IF NOT EXISTS zee_couture CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE zee_couture;

CREATE TABLE IF NOT EXISTS products (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(180) NOT NULL,
    description TEXT NULL,
    price DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    image VARCHAR(255) NULL,
    category VARCHAR(100) NULL,
    sizes VARCHAR(150) NULL,
    stock INT UNSIGNED NOT NULL DEFAULT 0,
    is_featured TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_products_category (category),
    INDEX idx_products_featured (is_featured),
    INDEX idx_products_created (created_at)
);

CREATE TABLE IF NOT EXISTS orders (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(150) NOT NULL,
    email VARCHAR(190) NOT NULL,
    phone VARCHAR(40) NOT NULL,
    address TEXT NOT NULL,
    city VARCHAR(100) NOT NULL,
    notes TEXT NULL,
    total DECIMAL(12,2) NOT NULL,
    payment_method VARCHAR(40) NOT NULL DEFAULT 'Pay on delivery',
    status VARCHAR(30) NOT NULL DEFAULT 'Pending',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS order_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id BIGINT UNSIGNED NOT NULL,
    product_id INT UNSIGNED NULL,
    product_name VARCHAR(180) NOT NULL,
    unit_price DECIMAL(12,2) NOT NULL,
    quantity INT UNSIGNED NOT NULL,
    size VARCHAR(30) NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
);

-- Optional sample products. Uncomment these rows if you want demo products.
-- INSERT INTO products (name, description, price, image, category, sizes, stock, is_featured)
-- VALUES
-- ('Classic Black Dress', 'A timeless dress for special occasions.', 28000, NULL, 'Dresses', 'S,M,L,XL', 12, 1),
-- ('Elegant Two-Piece Set', 'A stylish two-piece outfit for everyday elegance.', 35000, NULL, 'Two-Piece', 'S,M,L,XL', 8, 1),
-- ('Luxury Evening Gown', 'An elegant gown made for memorable nights.', 65000, NULL, 'Gowns', 'S,M,L', 5, 0);
