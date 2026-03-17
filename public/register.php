<?php
require_once '../config/db.php';
require_once '../src/User.php';

$userObj = new User($pdo);
$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $success = $userObj->register($_POST['name'], $_POST['email'], $_POST['password'], 'parent');
    $message = $success ? "Account created! <a href='login.php'>Login here</a>" : "Error creating account.";
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join EduPay Africa</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body style="background: #f0f2f5;">

<div class="form-container">
    <h2 style="text-align: center; color: var(--secondary-blue);">Create Your Account</h2>
    <p style="text-align: center; color: green;"><?php echo $message; ?></p>
    
    <form method="POST">
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