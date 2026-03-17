<?php
// Database configuration details
$host = 'localhost';
$db   = 'edupay_africa';
$user = 'root';
$pass = ''; // Default XAMPP password is blank
$charset = 'utf8mb4';

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