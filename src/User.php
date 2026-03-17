<?php
// src/User.php

class User {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function register($name, $email, $password, $role) {
        // Hash the password for security
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $sql = "INSERT INTO users (full_name, email, password_hash, role) VALUES (?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        
        try {
            return $stmt->execute([$name, $email, $hashedPassword, $role]);
        } catch (PDOException $e) {
            // Handle duplicate emails
            return false;
        }
    }
}