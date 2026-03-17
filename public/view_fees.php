<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$student_id = $_GET['student_id'] ?? null;

// Security: Check if this parent is actually linked to this student
$check_sql = "SELECT * FROM parent_student_link WHERE parent_id = ? AND student_id = ?";
$check_stmt = $pdo->prepare($check_sql);
$check_stmt->execute([$_SESSION['user_id'], $student_id]);

if (!$check_stmt->fetch() && $_SESSION['role'] !== 'admin') {
    die("Unauthorized access: You are not linked to this student.");
}

// Fetch Fee Details
$sql = "SELECT sf.*, fs.fee_name, fs.amount as total_amount 
        FROM student_fees sf
        JOIN fee_structures fs ON sf.fee_structure_id = fs.id
        WHERE sf.student_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$student_id]);
$fees = $stmt->fetchAll();

// Fetch Student Name
$name_stmt = $pdo->prepare("SELECT full_name FROM users WHERE id = ?");
$name_stmt->execute([$student_id]);
$student = $name_stmt->fetch();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Fee Statement - EduPay Africa</title>
</head>
<body>
    <h1>Fee Statement for <?php echo htmlspecialchars($student['full_name']); ?></h1>
    <a href="dashboard.php">Back to Dashboard</a>
    <hr>

    <table border="1" cellpadding="10">
        <thead>
            <tr>
                <th>Fee Description</th>
                <th>Total Required (KES)</th>
                <th>Amount Paid</th>
                <th>Balance</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $total_balance = 0;
            foreach ($fees as $fee): 
                $balance = $fee['total_amount'] - $fee['paid_amount'];
                $total_balance += $balance;
            ?>
            <tr>
                <td><?php echo htmlspecialchars($fee['fee_name']); ?></td>
                <td><?php echo number_format($fee['total_amount'], 2); ?></td>
                <td><?php echo number_format($fee['paid_amount'], 2); ?></td>
                <td style="color: red;"><?php echo number_format($balance, 2); ?></td>
                <td><strong><?php echo strtoupper($fee['status']); ?></strong></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h2>Total Outstanding Balance: KES <?php echo number_format($total_balance, 2); ?></h2>
    
    <button onclick="alert('Proceeding to M-Pesa Payment...')">Pay Now</button>
</body>
</html>