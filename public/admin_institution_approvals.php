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
    $token = $_POST['csrf_token'] ?? '';
    $requestId = isset($_POST['request_id']) ? (int) $_POST['request_id'] : 0;
    $approvalStatus = trim($_POST['approval_status'] ?? 'pending');
    $notes = trim($_POST['approval_notes'] ?? '');
    $allowedStatuses = ['pending', 'approved', 'rejected'];

    if (!hash_equals($_SESSION['csrf_token'], $token)) {
        $error = 'Invalid request token. Please refresh and try again.';
    } elseif ($requestId <= 0 || !in_array($approvalStatus, $allowedStatuses, true)) {
        $error = 'Invalid approval update payload.';
    } else {
        try {
            $updated = $demoRequestObj->updateApproval($requestId, $approvalStatus, (int) $_SESSION['user_id'], $notes);
            if ($updated) {
                $success = 'Institution request #' . $requestId . ' set to ' . strtoupper($approvalStatus) . '.';
            } else {
                $error = 'Unable to update institution approval.';
            }
        } catch (Throwable $e) {
            $error = 'Approval update failed. Please ensure the approval migration has been executed.';
        }
    }
}

try {
    $requests = $demoRequestObj->getAllForApproval();
} catch (Throwable $e) {
    $error = 'Unable to load approval data. Run database/demo_requests_approval_migration.sql. If you already ran an older version, run database/demo_requests_approval_patch_v2.sql as well.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Institution Approvals | EduPay Africa</title>
    <link rel="icon" type="image/png" href="images/logo.png">
    <style>
        :root {
            --primary: #003d82;
            --bg: #f5f7fb;
            --text: #1f2937;
            --muted: #6b7280;
            --border: #d7deea;
            --card: #fff;
            --success: #198754;
            --danger: #c0392b;
        }
        * { box-sizing: border-box; }

        body { margin: 0; font-family: Inter, Arial, sans-serif; background: var(--bg); color: var(--text); }

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

        .container { max-width: 1250px; margin: 0 auto; }
        .header { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 18px; }
        .title { margin: 0; color: var(--primary); }
        .sub { margin: 6px 0 0; color: var(--muted); }
        .actions { display: flex; gap: 10px; }
        .btn { text-decoration: none; border: 1px solid var(--border); border-radius: 8px; padding: 9px 12px; color: var(--primary); background: #fff; font-weight: 600; }
        .alert { padding: 12px 14px; border-radius: 10px; margin-bottom: 14px; }
        .alert.error { color: var(--danger); background: rgba(192,57,43,0.10); border: 1px solid rgba(192,57,43,0.25); }
        .alert.success { color: var(--success); background: rgba(25,135,84,0.10); border: 1px solid rgba(25,135,84,0.25); }
        .card { background: var(--card); border: 1px solid #e8edf5; border-radius: 12px; box-shadow: 0 10px 24px rgba(0,0,0,0.05); overflow: auto; }
        table { width: 100%; border-collapse: collapse; min-width: 1220px; }
        th, td { border-bottom: 1px solid #eef2f8; padding: 10px; vertical-align: top; font-size: 0.9rem; }
        th { text-align: left; background: #f8fbff; color: #334155; position: sticky; top: 0; }
        .badge { display: inline-block; border-radius: 999px; padding: 4px 9px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; }
        .badge.pending { background: rgba(243,156,18,0.13); color: #b26f00; }
        .badge.approved { background: rgba(25,135,84,0.13); color: #198754; }
        .badge.rejected { background: rgba(192,57,43,0.13); color: #c0392b; }
        .approve-form { display: flex; flex-direction: column; gap: 6px; }
        .approve-form select, .approve-form textarea { border: 1px solid var(--border); border-radius: 8px; padding: 6px 8px; font-size: 0.84rem; }
        .approve-form textarea { min-height: 70px; resize: vertical; }
        .approve-form button { border: 1px solid var(--border); background: #fff; color: var(--primary); border-radius: 8px; padding: 7px 10px; font-weight: 700; cursor: pointer; }
        .link { color: var(--primary); font-size: 0.82rem; }

        @media (max-width: 768px) {
            .layout { grid-template-columns: 1fr; }
            .sidebar { border-bottom: 1px solid rgba(255,255,255,0.15); }
            .main { padding: 14px; }
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
            <a href="add_institution.php">Institutional Management</a>

            <div class="menu-label">Account</div>
            <a href="logout.php">Logout</a>
        </nav>
    </aside>

    <main class="main">
<div class="container">
    <div class="header">
        <div>
            <h1 class="title">Institution Approval Management</h1>
            <p class="sub">Approve or reject institutions, and generate onboarding link scaffolding for later email automation.</p>
        </div>
        <div class="actions">
            <a class="btn" href="admin_demo_requests.php">Demo Requests</a>
        </div>
    </div>

    <?php if ($error !== ''): ?><div class="alert error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
    <?php if ($success !== ''): ?><div class="alert success"><?php echo htmlspecialchars($success); ?></div><?php endif; ?>

    <div class="card">
        <?php if (empty($requests)): ?>
            <div style="padding:16px;">No requests found.</div>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Institution</th>
                        <th>Contact</th>
                        <th>Type</th>
                        <th>Lead Stage</th>
                        <th>Approval</th>
                        <th>Institutions Record</th>
                        <th>Onboarding</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($requests as $request): ?>
                        <tr>
                            <td><?php echo (int) $request['id']; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($request['institution_name']); ?></strong><br>
                                <span style="color:#64748b"><?php echo htmlspecialchars($request['email']); ?></span><br>
                                <span style="color:#64748b"><?php echo htmlspecialchars($request['phone']); ?></span>
                            </td>
                            <td><?php echo htmlspecialchars($request['contact_name']); ?></td>
                            <td><?php echo htmlspecialchars($request['school_type']); ?></td>
                            <td><?php echo htmlspecialchars($request['status']); ?></td>
                            <td>
                                <span class="badge <?php echo htmlspecialchars($request['approval_status']); ?>">
                                    <?php echo htmlspecialchars($request['approval_status']); ?>
                                </span><br>
                                <small style="color:#6b7280; display:inline-block; margin-top:6px;">
                                    <?php echo $request['approved_at'] ? htmlspecialchars($request['approved_at']) : 'Not approved yet'; ?>
                                </small>
                            </td>
                            <td>
                                <?php if (!empty($request['institution_id'])): ?>
                                    <span style="color:#198754; font-weight:700;">Moved</span><br>
                                    <small style="color:#6b7280;">Institution ID: <?php echo (int) $request['institution_id']; ?></small>
                                <?php else: ?>
                                    <small style="color:#6b7280;">Not moved yet</small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($request['onboarding_token']) && !empty($request['onboarding_expires_at'])): ?>
                                    <a class="link" href="onboarding_set_password.php?token=<?php echo urlencode($request['onboarding_token']); ?>" target="_blank">Get Started Link</a><br>
                                    <small style="color:#6b7280;">Expires: <?php echo htmlspecialchars($request['onboarding_expires_at']); ?></small>
                                <?php else: ?>
                                    <small style="color:#6b7280;">Token not generated</small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <form method="POST" class="approve-form">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                                    <input type="hidden" name="request_id" value="<?php echo (int) $request['id']; ?>">
                                    <select name="approval_status">
                                        <?php foreach (['pending', 'approved', 'rejected'] as $opt): ?>
                                            <option value="<?php echo $opt; ?>" <?php echo $request['approval_status'] === $opt ? 'selected' : ''; ?>>
                                                <?php echo ucfirst($opt); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <textarea name="approval_notes" placeholder="Optional notes..."><?php echo htmlspecialchars((string) $request['approval_notes']); ?></textarea>
                                    <button type="submit">Save Approval</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
    </main>
</div>
</body>
</html>
