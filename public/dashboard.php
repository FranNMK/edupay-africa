<?php
session_start();
require_once '../config/db.php';
require_once '../src/User.php';

// 1. Protection: If no session exists, redirect to login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userObj = new User($pdo);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - EduPay Africa</title>
</head>
<body>
    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>
    <p>Status: <strong><?php echo strtoupper($_SESSION['role']); ?></strong></p>
    
    <nav>
        <ul>
            <li><a href="logout.php">Logout</a></li>
            
            <?php if ($_SESSION['role'] === 'admin'): ?>
                <li><a href="add_institution.php">Manage Institutions</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <hr>

    <?php if ($_SESSION['role'] === 'parent'): ?>
        <h3>Your Registered Children</h3>
        <ul>
            <?php 
            $children = $userObj->getChildren($_SESSION['user_id']);
            if (empty($children)): ?>
                <li>No children linked to this account yet.</li>
            <?php else:
                foreach ($children as $child): 
            ?>
                <li>
                    <strong><?php echo htmlspecialchars($child['full_name']); ?></strong> 
                    (<?php echo htmlspecialchars($child['email']); ?>)
                    - <a href="view_fees.php?student_id=<?php echo $child['id']; ?>">View Fee Statement</a>
                </li>
            <?php 
                endforeach; 
            endif; 
            ?>
        </ul>
    <?php endif; ?>

    <?php if ($_SESSION['role'] === 'institution'): ?>
        <h3>School Management Panel</h3>
        <p>Manage your students and fee structures here.</p>
    <?php endif; ?>

</body>
</html>