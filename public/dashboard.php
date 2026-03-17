<?php
session_start();

// Protection: If no session exists, redirect to login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - EduPay Africa</title>
</head>
<body>
    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>
    <p>Role: <?php echo strtoupper($_SESSION['role']); ?></p>
    
    <nav>
        <ul>
            <?php if ($_SESSION['role'] === 'admin'): ?>
            <li><a href="add_institution.php">Manage Institutions</a></li>
            <?php endif; ?>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <div class="content">
        <p>This is your secure dashboard. Only logged-in users can see this.</p>
    </div>
</body>
</html>