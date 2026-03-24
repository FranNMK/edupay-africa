<?php
session_start();
require_once '../config/db.php';

use EduPay\User;

$userObj = new User($pdo);
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) {
        $error = 'Invalid request token. Please try again.';
    } else {
        if ($userObj->login($_POST['email'] ?? '', $_POST['password'] ?? '')) {
            header('Location: dashboard.php');
            exit();
        } else {
            $error = 'Invalid email or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduPay Africa - Login</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h1>Login to EduPay</h1>
    <?php if ($error): ?>
        <p style="color: red;"><?php echo h($error); ?></p>
    <?php endif; ?>

    <form method="POST">
        <?php echo csrf_field(); ?>
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <button type="submit">Login</button>
    </form>
    <p>Don't have an account? <a href="index.php">Register here</a></p>
</body>
</html>