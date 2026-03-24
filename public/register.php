<?php
session_start();
require_once '../config/db.php';

use EduPay\User;

$userObj = new User($pdo);
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) {
        $message = 'Invalid request token. Please try again.';
        $messageType = 'error';
    } else {
        $name     = trim($_POST['name'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $errors = [];
        if ($name === '') {
            $errors[] = 'Full name is required.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'A valid email address is required.';
        }
        if (strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters.';
        }

        if ($errors) {
            $message = implode(' ', $errors);
            $messageType = 'error';
        } else {
            $success = $userObj->register($name, $email, $password, 'parent');
            if ($success) {
                $message = 'Account created! <a href="login.php">Login here</a>';
                $messageType = 'success';
            } else {
                $message = 'Error creating account. The email may already be registered.';
                $messageType = 'error';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join EduPay Africa</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body style="background: #f0f2f5;">

<div class="form-container">
    <h2 style="text-align: center; color: var(--secondary-blue);">Create Your Account</h2>
    <?php if ($message): ?>
        <p style="text-align: center; color: <?php echo $messageType === 'success' ? 'green' : 'red'; ?>;">
            <?php echo $message; ?>
        </p>
    <?php endif; ?>

    <form method="POST">
        <?php echo csrf_field(); ?>
        <label>Full Name</label>
        <input type="text" name="name" placeholder="John Doe" required>

        <label>Email Address</label>
        <input type="email" name="email" placeholder="john@example.com" required>

        <label>Password</label>
        <input type="password" name="password" placeholder="Min. 8 characters" required>

        <button type="submit" class="btn-nav" style="width: 100%; border:none; cursor:pointer; margin-top:20px;">Register as Parent</button>
    </form>

    <p style="text-align: center; margin-top: 20px;">Already have an account? <a href="login.php">Login</a></p>
</div>

</body>
</html>