<?php
// src/Institution.php

class Institution {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function create($name, $short_name, $address, $email) {
        $sql = "INSERT INTO institutions (name, short_name, address, contact_email) VALUES (?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        try {
            return $stmt->execute([$name, $short_name, $address, $email]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM institutions ORDER BY name ASC");
        return $stmt->fetchAll();
    }
}