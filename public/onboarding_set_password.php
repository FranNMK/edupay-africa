<?php
require_once '../config/db.php';
require_once '../src/DemoRequest.php';

$demoRequestObj = new DemoRequest($pdo);
$token = trim($_GET['token'] ?? '');
$error = '';
$success = '';
$request = null;

if ($token === '') {
    $error = 'Invalid onboarding link.';
} else {
    try {
        $request = $demoRequestObj->findByOnboardingToken($token);
        if (!$request) {
            $error = 'Onboarding request not found.';
        } elseif ($request['approval_status'] !== 'approved') {
            $error = 'This onboarding link is not yet approved.';
        } elseif (!empty($request['onboarding_expires_at']) && strtotime($request['onboarding_expires_at']) < time()) {
            $error = 'This onboarding link has expired. Please request a new one from EduPay support.';
        }
    } catch (Throwable $e) {
        $error = 'Unable to process onboarding token right now.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $error === '' && $request) {
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match.';
    } else {
        $success = 'Password setup workflow scaffold is ready. Account activation and login wiring will be completed in the next phase.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set Password | EduPay Africa</title>
    <link rel="icon" type="image/png" href="images/logo.png">
    <style>
        :root {
            --primary: #003d82;
            --bg: #f4f7fb;
            --text: #1f2937;
            --muted: #6b7280;
            --success: #198754;
            --danger: #c0392b;
            --border: #d7deea;
        }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Inter, Arial, sans-serif; background: var(--bg); color: var(--text); padding: 24px 16px; }
        .card { max-width: 540px; margin: 30px auto; background: #fff; border: 1px solid #e8edf5; border-radius: 14px; box-shadow: 0 10px 24px rgba(0,0,0,0.06); overflow: hidden; }
        .header { background: linear-gradient(135deg, #003d82, #002952); color: #fff; padding: 22px; }
        .header h1 { margin: 0; font-size: 1.45rem; }
        .header p { margin: 8px 0 0; opacity: 0.92; }
        .body { padding: 20px; }
        .msg { padding: 11px 12px; border-radius: 9px; margin-bottom: 14px; font-size: 0.92rem; }
        .msg.error { color: var(--danger); background: rgba(192,57,43,0.10); border: 1px solid rgba(192,57,43,0.2); }
        .msg.success { color: var(--success); background: rgba(25,135,84,0.10); border: 1px solid rgba(25,135,84,0.2); }
        .field { margin-bottom: 12px; }
        label { display: block; font-size: 0.9rem; font-weight: 600; margin-bottom: 6px; color: #30445e; }
        input { width: 100%; border: 1px solid var(--border); border-radius: 10px; padding: 10px 11px; }
        button { margin-top: 8px; border: none; border-radius: 10px; background: #f39c12; color: #fff; padding: 11px 14px; font-weight: 700; cursor: pointer; }
        .meta { color: var(--muted); font-size: 0.85rem; margin-bottom: 12px; }
    </style>
</head>
<body>
    <div class="card">
        <div class="header">
            <h1>Get Started with EduPay Africa</h1>
            <p>Create your secure password to continue onboarding.</p>
        </div>
        <div class="body">
            <?php if ($error !== ''): ?><div class="msg error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
            <?php if ($success !== ''): ?><div class="msg success"><?php echo htmlspecialchars($success); ?></div><?php endif; ?>

            <?php if ($request && $error === ''): ?>
                <p class="meta">
                    Institution: <strong><?php echo htmlspecialchars($request['institution_name']); ?></strong><br>
                    Contact: <?php echo htmlspecialchars($request['contact_name']); ?> (<?php echo htmlspecialchars($request['email']); ?>)
                </p>
                <form method="POST">
                    <div class="field">
                        <label for="password">New Password</label>
                        <input id="password" type="password" name="password" required minlength="8">
                    </div>
                    <div class="field">
                        <label for="confirm_password">Confirm Password</label>
                        <input id="confirm_password" type="password" name="confirm_password" required minlength="8">
                    </div>
                    <button type="submit">Set Password</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
