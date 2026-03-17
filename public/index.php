<?php
require_once '../config/db.php';
require_once '../src/User.php';

$userObj = new User($pdo);
$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $success = $userObj->register($_POST['name'], $_POST['email'], $_POST['password'], 'admin');
    $message = $success ? "Account created successfully!" : "Error: Email might already exist.";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>EduPay Africa - Register</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h1>EduPay Africa System</h1>
    <p><?php echo $message; ?></p>

    <form method="POST">
        <input type="text" name="name" placeholder="Full Name" required><br>
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <button type="submit">Create Admin Account</button>
    </form>
</body>
</html>