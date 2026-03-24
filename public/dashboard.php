<?php
session_start();
require_once '../config/db.php';

use EduPay\Auth;
use EduPay\User;
use EduPay\DemoRequest;

Auth::requireLogin();

$userObj = new User($pdo);
$isAdmin = $_SESSION['role'] === 'admin';

$stats = [
    'demo_total' => 0,
    'demo_pending' => 0,
    'demo_approved' => 0,
    'institutions' => 0,
    'users' => 0,
];

if ($isAdmin) {
    try {
        $stats['demo_total'] = (int) $pdo->query("SELECT COUNT(*) FROM demo_requests")->fetchColumn();
        $stats['demo_pending'] = (int) $pdo->query("SELECT COUNT(*) FROM demo_requests WHERE approval_status = 'pending'")->fetchColumn();
        $stats['demo_approved'] = (int) $pdo->query("SELECT COUNT(*) FROM demo_requests WHERE approval_status = 'approved'")->fetchColumn();
        $stats['institutions'] = (int) $pdo->query("SELECT COUNT(*) FROM institutions")->fetchColumn();
        $stats['users'] = (int) $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    } catch (Throwable $e) {
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - EduPay Africa</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="images/logo.png">
    <style>
        :root {
            --primary: #003d82;
            --primary-dark: #002952;
            --bg: #f5f7fb;
            --text: #1f2937;
            --muted: #6b7280;
            --card: #ffffff;
            --border: #d7deea;
            --accent: #f39c12;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: Inter, Arial, sans-serif;
            background: var(--bg);
            color: var(--text);
        }

        .layout {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 260px 1fr;
        }

        .sidebar {
            background: linear-gradient(160deg, var(--primary), var(--primary-dark));
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
            padding: 22px;
        }

        .top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 18px;
            gap: 10px;
            flex-wrap: wrap;
        }

        .title {
            margin: 0;
            color: var(--primary);
            font-size: 1.55rem;
        }

        .sub {
            margin: 6px 0 0;
            color: var(--muted);
            font-size: 0.92rem;
        }

        .pill {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 999px;
            padding: 8px 12px;
            font-size: 0.84rem;
            font-weight: 700;
            color: var(--primary);
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 12px;
            margin-bottom: 18px;
        }

        .card {
            background: var(--card);
            border: 1px solid #e8edf5;
            border-radius: 12px;
            padding: 14px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.04);
        }

        .card .label { font-size: .78rem; color: var(--muted); text-transform: uppercase; letter-spacing: .3px; }
        .card .value { margin-top: 6px; font-size: 1.45rem; font-weight: 800; color: var(--primary); }

        .grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 14px;
        }

        .panel {
            background: #fff;
            border: 1px solid #e8edf5;
            border-radius: 12px;
            padding: 16px;
        }

        .panel h3 { margin: 0 0 10px; color: var(--primary); }
        .panel ul { margin: 0; padding-left: 18px; }
        .panel li { margin-bottom: 8px; }
        .panel a { color: var(--primary); font-weight: 600; text-decoration: none; }

        .section-list {
            background: #fff;
            border: 1px solid #e8edf5;
            border-radius: 12px;
            padding: 16px;
            margin-top: 14px;
        }

        @media (max-width: 1100px) {
            .cards { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .grid { grid-template-columns: 1fr; }
        }

        @media (max-width: 900px) {
            .layout { grid-template-columns: 1fr; }
            .sidebar { border-bottom: 1px solid rgba(255,255,255,0.15); }
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
                <a href="dashboard.php" class="active">Overview</a>

                <?php if ($isAdmin): ?>
                    <div class="menu-label">Requests</div>
                    <a href="admin_demo_requests.php">Demo Requests</a>

                    <div class="menu-label">Institution Management</div>
                    <a href="add_institution.php">Institutional Management</a>
                <?php endif; ?>

                <div class="menu-label">Account</div>
                <a href="logout.php">Logout</a>
            </nav>
        </aside>

        <main class="main">
            <div class="top">
                <div>
                    <h1 class="title">Welcome, <?php echo h($_SESSION['user_name']); ?></h1>
                    <p class="sub">Role: <?php echo strtoupper(h($_SESSION['role'])); ?></p>
                </div>
                <div class="pill">EduPay Africa Dashboard</div>
            </div>

            <?php if ($isAdmin): ?>
                <section class="cards">
                    <article class="card"><div class="label">Demo Requests</div><div class="value"><?php echo $stats['demo_total']; ?></div></article>
                    <article class="card"><div class="label">Pending Approval</div><div class="value"><?php echo $stats['demo_pending']; ?></div></article>
                    <article class="card"><div class="label">Approved</div><div class="value"><?php echo $stats['demo_approved']; ?></div></article>
                    <article class="card"><div class="label">Institutions</div><div class="value"><?php echo $stats['institutions']; ?></div></article>
                    <article class="card"><div class="label">Users</div><div class="value"><?php echo $stats['users']; ?></div></article>
                </section>

                <section class="grid">
                    <div class="panel">
                        <h3>Admin Management Areas</h3>
                        <ul>
                            <li><a href="admin_demo_requests.php">Lead Intake Management</a> – review and qualify inbound requests.</li>
                            <li><a href="admin_institution_approvals.php">Institution Approval Workflow</a> – approve/reject with onboarding token scaffolding.</li>
                            <li><a href="add_institution.php">Institution Registry</a> – add and maintain institution records.</li>
                        </ul>
                    </div>
                    <div class="panel">
                        <h3>Planned Automation (Next)</h3>
                        <ul>
                            <li>Send welcome email on approval.</li>
                            <li>Enable password reset completion for approved institutions.</li>
                            <li>Activate first-login access controls.</li>
                        </ul>
                    </div>
                </section>
            <?php endif; ?>

            <?php if ($_SESSION['role'] === 'parent'): ?>
                <section class="section-list">
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
                                <strong><?php echo h($child['full_name']); ?></strong>
                                (<?php echo h($child['email']); ?>)
                                - <a href="view_fees.php?student_id=<?php echo $child['id']; ?>">View Fee Statement</a>
                            </li>
                        <?php
                            endforeach;
                        endif;
                        ?>
                    </ul>
                </section>
            <?php endif; ?>

            <?php if ($_SESSION['role'] === 'institution'): ?>
                <section class="section-list">
                    <h3>School Management Panel</h3>
                    <p>Manage your students and fee structures here.</p>
                </section>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>