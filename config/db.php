<?php
// Load environment variables from .env when the file exists (local/dev).
// In production, set the variables directly in the server environment instead.
$vendorAutoload = __DIR__ . '/../vendor/autoload.php';
if (file_exists($vendorAutoload)) {
    require_once $vendorAutoload;
}

$dotenvPath = __DIR__ . '/..';
if (class_exists(\Dotenv\Dotenv::class) && file_exists($dotenvPath . '/.env')) {
    \Dotenv\Dotenv::createImmutable($dotenvPath)->safeLoad();
}

// Database configuration — values come from environment variables with safe fallbacks.
$host    = $_ENV['DB_HOST']    ?? getenv('DB_HOST')    ?: 'localhost';
$db      = $_ENV['DB_NAME']    ?? getenv('DB_NAME')    ?: 'edupay_africa';
$user    = $_ENV['DB_USER']    ?? getenv('DB_USER')    ?: 'root';
$pass    = $_ENV['DB_PASS']    ?? getenv('DB_PASS')    ?: '';
$charset = $_ENV['DB_CHARSET'] ?? getenv('DB_CHARSET') ?: 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
     PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     // If this fails, make sure you created the 'edupay_africa' database in phpMyAdmin
     throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
?>