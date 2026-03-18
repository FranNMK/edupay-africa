<?php
session_start();
require_once '../config/db.php';
require_once '../src/DemoRequest.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo 'Access denied. Admins only.';
    exit();
}

$demoRequestObj = new DemoRequest($pdo);
$error = '';
$success = '';
$requests = [];

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $allowedStatuses = ['new', 'contacted', 'qualified', 'closed'];
    $requestId = isset($_POST['request_id']) ? (int) $_POST['request_id'] : 0;
    $newStatus = trim($_POST['status'] ?? '');
    $token = $_POST['csrf_token'] ?? '';

    if (!hash_equals($_SESSION['csrf_token'], $token)) {
        $error = 'Invalid request token. Please refresh and try again.';
    } elseif ($requestId <= 0 || !in_array($newStatus, $allowedStatuses, true)) {
        $error = 'Invalid status update payload.';
    } else {
        try {
            $updated = $demoRequestObj->updateStatus($requestId, $newStatus);
            if ($updated) {
                $success = 'Request #' . $requestId . ' updated to "' . $newStatus . '".';
            } else {
                $error = 'Status update failed. Please try again.';
            }
        } catch (Throwable $e) {
            $error = 'Unable to update request status right now.';
        }
    }
}

try {
    $requests = $demoRequestObj->getAll();
} catch (Throwable $e) {
    $error = 'Unable to load demo requests. Confirm the demo_requests table exists and your database is up to date.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Demo Requests | EduPay Africa</title>
    <link rel="icon" type="image/png" href="images/logo.png">
    <style>
        :root {
            --primary: #003d82;
            --bg: #f5f7fb;
            --text: #1f2937;
            --muted: #6b7280;
            --card: #ffffff;
            --border: #d7deea;
            --success: #198754;
            --warning: #f39c12;
            --danger: #c0392b;
            --info: #0d6efd;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: Inter, Arial, sans-serif;
            color: var(--text);
            background: var(--bg);
        }

        .layout {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 260px 1fr;
        }

        .sidebar {
            background: linear-gradient(160deg, var(--primary), #002952);
            color: #fff;
            padding: 20px 16px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 22px;
            font-weight: 800;
            letter-spacing: -0.3px;
        }

        .brand img {
            width: 38px;
            height: 38px;
            border-radius: 8px;
            background: #fff;
            padding: 3px;
        }

        .menu {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .menu-label {
            margin-top: 12px;
            margin-bottom: 4px;
            padding: 0 6px;
            font-size: 0.72rem;
            letter-spacing: 0.6px;
            text-transform: uppercase;
            color: #b8ccf7;
            font-weight: 700;
        }

        .menu a {
            color: #e4edff;
            text-decoration: none;
            padding: 10px 12px;
            border-radius: 10px;
            font-size: 0.92rem;
            font-weight: 600;
            transition: background .2s ease;
        }

        .menu a:hover,
        .menu a.active {
            background: rgba(255,255,255,0.12);
            color: #fff;
        }

        .main {
            padding: 24px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .title {
            margin: 0;
            color: var(--primary);
            font-size: 1.75rem;
        }

        .sub {
            color: var(--muted);
            margin-top: 6px;
        }

        .actions {
            display: flex;
            gap: 10px;
        }

        .btn {
            text-decoration: none;
            border-radius: 8px;
            padding: 10px 14px;
            font-weight: 600;
            font-size: 0.92rem;
            border: 1px solid var(--border);
            background: #fff;
            color: var(--primary);
        }

        .card {
            background: var(--card);
            border: 1px solid #e8edf5;
            border-radius: 12px;
            box-shadow: 0 10px 24px rgba(0,0,0,0.05);
            overflow: hidden;
        }

        .table-wrap {
            overflow: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1050px;
        }

        th, td {
            text-align: left;
            padding: 12px;
            border-bottom: 1px solid #eef2f8;
            vertical-align: top;
            font-size: 0.92rem;
        }

        th {
            background: #f8fbff;
            color: #334155;
            font-weight: 700;
            position: sticky;
            top: 0;
        }

        .status {
            display: inline-block;
            border-radius: 999px;
            padding: 4px 10px;
            font-size: 0.78rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.2px;
        }

        .status.new { background: rgba(13,110,253,0.14); color: var(--info); }
        .status.contacted { background: rgba(243,156,18,0.14); color: var(--warning); }
        .status.qualified { background: rgba(25,135,84,0.14); color: var(--success); }
        .status.closed { background: rgba(192,57,43,0.14); color: var(--danger); }

        .empty, .error, .success {
            padding: 18px;
            margin: 0;
        }

        .error {
            color: var(--danger);
            background: rgba(192,57,43,0.08);
            border: 1px solid rgba(192,57,43,0.2);
            border-radius: 10px;
            margin-bottom: 16px;
        }

        .success {
            color: var(--success);
            background: rgba(25,135,84,0.10);
            border: 1px solid rgba(25,135,84,0.24);
            border-radius: 10px;
            margin-bottom: 16px;
        }

        .status-form {
            display: flex;
            gap: 6px;
            align-items: center;
            margin-top: 6px;
        }

        .status-form select {
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 5px 8px;
            font-size: 0.78rem;
            background: #fff;
        }

        .status-form button {
            border: 1px solid var(--border);
            border-radius: 8px;
            background: #fff;
            color: var(--primary);
            font-size: 0.76rem;
            padding: 5px 8px;
            font-weight: 700;
            cursor: pointer;
        }

        @media (max-width: 768px) {
            .layout { grid-template-columns: 1fr; }
            .sidebar { border-bottom: 1px solid rgba(255,255,255,0.15); }
            .main { padding: 14px; }
            .title { font-size: 1.4rem; }
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
            <a href="admin_demo_requests.php" class="active">Demo Requests</a>

            <div class="menu-label">Institution Management</div>
            <a href="add_institution.php">Institutional Management</a>

            <div class="menu-label">Account</div>
            <a href="logout.php">Logout</a>
        </nav>
    </aside>

    <main class="main">
    <div class="container">
        <div class="header">
            <div>
                <h1 class="title">Submitted Demo Requests</h1>
                <p class="sub">Review active inbound demo requests (approved schools are moved to Institutions).</p>
            </div>
            <div class="actions">
                <a class="btn" href="index.php">Go to Landing</a>
            </div>
        </div>

    <?php if ($error !== ''): ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <?php if ($success !== ''): ?>
        <p class="success"><?php echo htmlspecialchars($success); ?></p>
    <?php endif; ?>

    <?php if ($error === '' || !empty($requests)): ?>
        <div class="card">
            <?php if (empty($requests)): ?>
                <p class="empty">No demo requests submitted yet.</p>
            <?php else: ?>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Institution</th>
                                <th>Contact</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Type</th>
                                <th>Students</th>
                                <th>Preferred Contact</th>
                                <th>Message</th>
                                <th>Status</th>
                                <th>Submitted At</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($requests as $request): ?>
                                <tr>
                                    <td><?php echo (int) $request['id']; ?></td>
                                    <td><?php echo htmlspecialchars($request['institution_name']); ?></td>
                                    <td><?php echo htmlspecialchars($request['contact_name']); ?></td>
                                    <td><?php echo htmlspecialchars($request['email']); ?></td>
                                    <td><?php echo htmlspecialchars($request['phone']); ?></td>
                                    <td><?php echo htmlspecialchars($request['school_type']); ?></td>
                                    <td><?php echo $request['student_count'] !== null ? (int) $request['student_count'] : '-'; ?></td>
                                    <td><?php echo htmlspecialchars($request['preferred_contact']); ?></td>
                                    <td><?php echo nl2br(htmlspecialchars((string) $request['message'])); ?></td>
                                    <td>
                                        <span class="status <?php echo htmlspecialchars($request['status']); ?>">
                                            <?php echo htmlspecialchars($request['status']); ?>
                                        </span>
                                        <form method="POST" class="status-form">
                                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                                            <input type="hidden" name="request_id" value="<?php echo (int) $request['id']; ?>">
                                            <select name="status" aria-label="Update status for request <?php echo (int) $request['id']; ?>">
                                                <?php foreach (['new', 'contacted', 'qualified', 'closed'] as $statusOption): ?>
                                                    <option value="<?php echo $statusOption; ?>" <?php echo $request['status'] === $statusOption ? 'selected' : ''; ?>>
                                                        <?php echo ucfirst($statusOption); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <button type="submit">Save</button>
                                        </form>
                                    </td>
                                    <td><?php echo htmlspecialchars($request['created_at']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    </div>
    </main>
</div>
</body>
</html>
