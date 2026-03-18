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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="images/logo.png">
    <style>
        :root {
            --primary: #003d82;
            --bg: #f5f7fb;
            --text: #1f2937;
            --muted: #6b7280;
            --border: #d7deea;
            --success: #198754;
        }

        * { box-sizing: border-box; }
        body { margin: 0; font-family: Inter, Arial, sans-serif; background: var(--bg); color: var(--text); }

        .layout { min-height: 100vh; display: grid; grid-template-columns: 260px 1fr; }
        .sidebar { background: linear-gradient(160deg, var(--primary), #002952); color: #fff; padding: 20px 16px; }
        .brand { display: flex; align-items: center; gap: 10px; margin-bottom: 22px; font-weight: 800; letter-spacing: -0.3px; }
        .brand img { width: 38px; height: 38px; border-radius: 8px; background: #fff; padding: 3px; }
        .menu { display: flex; flex-direction: column; gap: 8px; }
        .menu-label { margin-top: 12px; margin-bottom: 4px; padding: 0 6px; font-size: 0.72rem; letter-spacing: 0.6px; text-transform: uppercase; color: #b8ccf7; font-weight: 700; }
        .menu a { color: #e4edff; text-decoration: none; padding: 10px 12px; border-radius: 10px; font-size: 0.92rem; font-weight: 600; transition: background .2s ease; }
        .menu a:hover, .menu a.active { background: rgba(255,255,255,0.12); color: #fff; }

        .main { padding: 24px; }
        .container { max-width: 1050px; margin: 0 auto; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; flex-wrap: wrap; gap: 10px; }
        .title { margin: 0; color: var(--primary); }
        .sub { margin: 6px 0 0; color: var(--muted); }

        .alert { margin: 12px 0; padding: 12px; border-radius: 8px; background: rgba(25,135,84,0.10); color: var(--success); border: 1px solid rgba(25,135,84,0.2); }

        .panel { background: #fff; border: 1px solid #e8edf5; border-radius: 12px; padding: 16px; box-shadow: 0 8px 16px rgba(0,0,0,0.04); }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
        .field { display: flex; flex-direction: column; gap: 6px; margin-bottom: 10px; }
        .field.full { grid-column: 1 / -1; }
        label { font-size: 0.88rem; font-weight: 700; color: #30445e; }
        input, textarea { border: 1px solid var(--border); border-radius: 8px; padding: 10px; font-size: 0.92rem; }
        textarea { min-height: 80px; resize: vertical; }
        button { border: none; border-radius: 9px; background: #f39c12; color: #fff; padding: 10px 14px; font-weight: 700; cursor: pointer; }

        .table-panel { margin-top: 14px; background: #fff; border: 1px solid #e8edf5; border-radius: 12px; overflow: auto; }
        table { width: 100%; border-collapse: collapse; min-width: 760px; }
        th, td { border-bottom: 1px solid #eef2f8; padding: 10px; font-size: 0.9rem; text-align: left; }
        th { background: #f8fbff; color: #334155; }

        @media (max-width: 900px) {
            .layout { grid-template-columns: 1fr; }
            .sidebar { border-bottom: 1px solid rgba(255,255,255,0.15); }
            .main { padding: 14px; }
            .grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="layout">
        <aside class="sidebar">
            <div class="brand">
                <img src="images/logo.png" alt="EduPay Africa logo">
                <span>EDUPAY ADMIN</span>
            </div>
            <nav class="menu">
                <div class="menu-label">Dashboard</div>
                <a href="dashboard.php">Overview</a>

                <div class="menu-label">Requests</div>
                <a href="admin_demo_requests.php">Demo Requests</a>

                <div class="menu-label">Institution Management</div>
                <a href="add_institution.php" class="active">Institutional Management</a>

                <div class="menu-label">Account</div>
                <a href="logout.php">Logout</a>
            </nav>
        </aside>

        <main class="main">
            <div class="container">
                <div class="header">
                    <div>
                        <h1 class="title">Institutional Management</h1>
                        <p class="sub">Register and view all approved institutions.</p>
                    </div>
                </div>

                <?php if ($message): ?>
                    <div class="alert"><?php echo htmlspecialchars($message); ?></div>
                <?php endif; ?>

                <section class="panel">
                    <h3>Add New School</h3>
                    <form method="POST">
                        <div class="grid">
                            <div class="field">
                                <label for="name">School Full Name</label>
                                <input id="name" type="text" name="name" required>
                            </div>
                            <div class="field">
                                <label for="short_name">Short Name</label>
                                <input id="short_name" type="text" name="short_name" placeholder="e.g. AGH-001" required>
                            </div>
                            <div class="field full">
                                <label for="address">Physical Address</label>
                                <textarea id="address" name="address"></textarea>
                            </div>
                            <div class="field full">
                                <label for="email">Contact Email</label>
                                <input id="email" type="email" name="email">
                            </div>
                        </div>
                        <button type="submit">Register Institution</button>
                    </form>
                </section>

                <section class="table-panel">
                    <table>
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
                </section>
            </div>
        </main>
    </div>
</body>
</html>