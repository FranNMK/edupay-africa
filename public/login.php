<?php
require_once '../config/db.php';
require_once '../src/User.php';

$userObj = new User($pdo);
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($userObj->login($_POST['email'], $_POST['password'])) {
        header("Location: dashboard.php"); // Redirect to dashboard on success
        exit();
    } else {
        $error = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>EduPay Africa - Login</title>
</head>
<body>
    <h1>Login to EduPay</h1>
    <?php if($error): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>

    <form method="POST">
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <button type="submit">Login</button>
    </form>
    <p>Don't have an account? <a href="index.php">Register here</a></p>
</body>
</html>