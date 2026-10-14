<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('DB_HOST', 'localhost');
define('DB_NAME', 'zee_couture');
define('DB_USER', 'root');
define('DB_PASS', ''); // Change this if your MySQL account has a password.

try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    exit('Database connection failed. Check config.php and make sure you imported database.sql.');
}

function e(?string $value): string {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function money(float|int|string $amount): string {
    return '₦' . number_format((float)$amount, 2);
}

function productImage(?string $image): string {
    $image = trim((string)$image);
    if ($image === '') return 'assets/images/placeholder.svg';
    // Product image values should be a relative path or a trusted HTTPS image URL.
    if (preg_match('~^https://~i', $image)) return $image;
    return 'uploads/' . rawurlencode(basename($image));
}
