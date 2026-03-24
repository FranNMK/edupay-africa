<?php

namespace EduPay;

class User
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function register($name, $email, $password, $role)
    {
        // Hash the password for security
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $sql = "INSERT INTO users (full_name, email, password_hash, role) VALUES (?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);

        try {
            return $stmt->execute([$name, $email, $hashedPassword, $role]);
        } catch (\PDOException $e) {
            // Handle duplicate emails
            return false;
        }
    }

    public function login($email, $password)
    {
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // Verify the user exists and the password matches the hash
        if ($user && password_verify($password, $user['password_hash'])) {
            // Start a session only if one is not already active.
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['institution_id'] = $user['institution_id'];

            return true;
        }
        return false;
    }



    public function getChildren($parentId)
    {
        $sql = "SELECT u.id, u.full_name, u.email 
            FROM users u
            JOIN parent_student_link psl ON u.id = psl.student_id
            WHERE psl.parent_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$parentId]);
        return $stmt->fetchAll();
    }
}
