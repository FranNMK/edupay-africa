<?php
require_once '../config/db.php';
require_once '../src/Institution.php';
session_start();

// Security Check: Only allow logged-in Admins
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$instObj = new Institution($pdo);
$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $success = $instObj->create($_POST['name'], $_POST['short_name'], $_POST['address'], $_POST['email']);
    $message = $success ? "Institution added successfully!" : "Error: Short name must be unique.";
}

$all_schools = $instObj->getAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Institutions - EduPay Africa</title>
</head>
<body>
    <h1>Manage Institutions</h1>
    <a href="dashboard.php">Back to Dashboard</a>
    <hr>

    <h3>Add New School</h3>
    <p style="color: blue;"><?php echo $message; ?></p>
    <form method="POST">
        <input type="text" name="name" placeholder="School Full Name" required><br>
        <input type="text" name="short_name" placeholder="Short Name (e.g. AGH-001)" required><br>
        <textarea name="address" placeholder="Physical Address"></textarea><br>
        <input type="email" name="email" placeholder="Contact Email"><br>
        <button type="submit">Register Institution</button>
    </form>

    <hr>
    <h3>Existing Institutions</h3>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Code</th>
            <th>Email</th>
        </tr>
        <?php foreach ($all_schools as $school): ?>
        <tr>
            <td><?php echo $school['id']; ?></td>
            <td><?php echo htmlspecialchars($school['name']); ?></td>
            <td><?php echo htmlspecialchars($school['short_name']); ?></td>
            <td><?php echo htmlspecialchars($school['contact_email']); ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>