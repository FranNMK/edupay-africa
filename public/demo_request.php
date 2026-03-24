<?php
session_start();
require_once '../config/db.php';

use EduPay\DemoRequest;

$demoRequest = new DemoRequest($pdo);
$message = '';
$messageType = '';

$formData = [
    'institution_name' => '',
    'contact_name' => '',
    'email' => '',
    'phone' => '',
    'school_type' => 'Other',
    'student_count' => '',
    'preferred_contact' => 'Email',
    'message' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) {
        $message = 'Invalid request token. Please refresh and try again.';
        $messageType = 'error';
    } else {
    $formData['institution_name'] = trim($_POST['institution_name'] ?? '');
    $formData['contact_name'] = trim($_POST['contact_name'] ?? '');
    $formData['email'] = trim($_POST['email'] ?? '');
    $formData['phone'] = trim($_POST['phone'] ?? '');
    $formData['school_type'] = trim($_POST['school_type'] ?? 'Other');
    $formData['student_count'] = trim($_POST['student_count'] ?? '');
    $formData['preferred_contact'] = trim($_POST['preferred_contact'] ?? 'Email');
    $formData['message'] = trim($_POST['message'] ?? '');

    $allowedSchoolTypes = ['Primary', 'Secondary', 'College', 'University', 'Other'];
    $allowedContactOptions = ['Phone', 'Email', 'WhatsApp'];

    $errors = [];

    if ($formData['institution_name'] === '') {
        $errors[] = 'Institution name is required.';
    }
    if ($formData['contact_name'] === '') {
        $errors[] = 'Contact person name is required.';
    }
    if (!filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'A valid email address is required.';
    }
    if ($formData['phone'] === '') {
        $errors[] = 'Phone number is required.';
    }

    if (!in_array($formData['school_type'], $allowedSchoolTypes, true)) {
        $formData['school_type'] = 'Other';
    }

    if (!in_array($formData['preferred_contact'], $allowedContactOptions, true)) {
        $formData['preferred_contact'] = 'Email';
    }

    if ($formData['student_count'] === '') {
        $formData['student_count'] = null;
    } elseif (!ctype_digit($formData['student_count'])) {
        $errors[] = 'Student count must be a whole number.';
    } else {
        $formData['student_count'] = (int) $formData['student_count'];
    }

    if (empty($errors)) {
        try {
            $saved = $demoRequest->create($formData);
            if ($saved) {
                $message = 'Demo request submitted successfully. Our team will contact you shortly.';
                $messageType = 'success';
                $formData = [
                    'institution_name' => '',
                    'contact_name' => '',
                    'email' => '',
                    'phone' => '',
                    'school_type' => 'Other',
                    'student_count' => '',
                    'preferred_contact' => 'Email',
                    'message' => '',
                ];
            } else {
                $message = 'Unable to submit your request at the moment. Please try again.';
                $messageType = 'error';
            }
        } catch (Throwable $e) {
            $message = 'Submission failed. Ensure the demo_requests table exists by re-running database/schema.sql.';
            $messageType = 'error';
        }
    } else {
        $message = implode(' ', $errors);
        $messageType = 'error';
    }
    } // end csrf_verify else
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book a Demo Request | EduPay Africa</title>
    <link rel="icon" type="image/png" href="images/logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #003d82;
            --primary-dark: #002952;
            --accent: #f39c12;
            --bg: #f4f7fb;
            --card: #ffffff;
            --text: #1f2937;
            --muted: #6b7280;
            --success: #198754;
            --error: #c0392b;
            --border: #d7deea;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: Inter, Arial, sans-serif;
            background: linear-gradient(135deg, #f4f7fb, #eef3f9);
            color: var(--text);
            min-height: 100vh;
            padding: 32px 16px;
        }

        .wrapper {
            max-width: 900px;
            margin: 0 auto;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            gap: 12px;
            flex-wrap: wrap;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: var(--primary);
            font-weight: 800;
            letter-spacing: -0.3px;
        }

        .brand img {
            width: 42px;
            height: 42px;
            object-fit: contain;
            border-radius: 8px;
            background: #fff;
            padding: 3px;
        }

        .back-link {
            text-decoration: none;
            color: var(--primary);
            font-weight: 600;
        }

        .card {
            background: var(--card);
            border-radius: 16px;
            box-shadow: 0 12px 30px rgba(0, 41, 82, 0.10);
            border: 1px solid #e8edf5;
            overflow: hidden;
        }

        .card-header {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: #fff;
            padding: 26px 24px;
        }

        .card-header h1 {
            font-size: 1.8rem;
            margin-bottom: 8px;
        }

        .card-header p {
            opacity: 0.92;
            line-height: 1.6;
        }

        .card-body {
            padding: 24px;
        }

        .alert {
            margin-bottom: 18px;
            border-radius: 10px;
            padding: 12px 14px;
            font-size: 0.95rem;
            font-weight: 500;
        }

        .alert.success {
            background: rgba(25, 135, 84, 0.10);
            color: var(--success);
            border: 1px solid rgba(25, 135, 84, 0.35);
        }

        .alert.error {
            background: rgba(192, 57, 43, 0.10);
            color: var(--error);
            border: 1px solid rgba(192, 57, 43, 0.35);
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
        }

        .field {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .field.full {
            grid-column: 1 / -1;
        }

        label {
            font-size: 0.92rem;
            font-weight: 600;
            color: #30445e;
        }

        input, select, textarea {
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 11px 12px;
            font-size: 0.95rem;
            outline: none;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        textarea {
            min-height: 120px;
            resize: vertical;
        }

        input:focus, select:focus, textarea:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(0, 61, 130, 0.12);
        }

        .actions {
            margin-top: 22px;
            display: flex;
            justify-content: flex-end;
        }

        button {
            border: none;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--accent), #e67e22);
            color: #fff;
            font-weight: 700;
            font-size: 0.95rem;
            padding: 12px 18px;
            cursor: pointer;
        }

        button:hover {
            filter: brightness(0.97);
        }

        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }

            .card-header h1 {
                font-size: 1.45rem;
            }
        }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="topbar">
        <a href="index.php" class="brand">
            <img src="images/logo.png" alt="EduPay Africa logo">
            <span>EDUPAY AFRICA</span>
        </a>
        <a class="back-link" href="index.php"><i class="fas fa-arrow-left"></i> Back to Home</a>
    </div>

    <div class="card">
        <div class="card-header">
            <h1>Book a Demo Request</h1>
            <p>Tell us about your institution. Our team will contact you with a guided product demo and onboarding plan.</p>
        </div>

        <div class="card-body">
            <?php if ($message !== ''): ?>
                <div class="alert <?= h($messageType) ?>"><?= h($message) ?></div>
            <?php endif; ?>

            <form method="POST" novalidate>
                <?php echo csrf_field(); ?>
                <div class="form-grid">
                    <div class="field">
                        <label for="institution_name">Institution Name *</label>
                        <input id="institution_name" name="institution_name" type="text" required value="<?= h($formData['institution_name']) ?>" placeholder="e.g. Greenfields Academy">
                    </div>

                    <div class="field">
                        <label for="contact_name">Contact Person *</label>
                        <input id="contact_name" name="contact_name" type="text" required value="<?= h($formData['contact_name']) ?>" placeholder="e.g. Jane Wambui">
                    </div>

                    <div class="field">
                        <label for="email">Email Address *</label>
                        <input id="email" name="email" type="email" required value="<?= h($formData['email']) ?>" placeholder="name@school.ac.ke">
                    </div>

                    <div class="field">
                        <label for="phone">Phone Number *</label>
                        <input id="phone" name="phone" type="text" required value="<?= h($formData['phone']) ?>" placeholder="+254 7XX XXX XXX">
                    </div>

                    <div class="field">
                        <label for="school_type">Institution Type</label>
                        <select id="school_type" name="school_type">
                            <option value="Primary" <?= $formData['school_type'] === 'Primary' ? 'selected' : '' ?>>Primary</option>
                            <option value="Secondary" <?= $formData['school_type'] === 'Secondary' ? 'selected' : '' ?>>Secondary</option>
                            <option value="College" <?= $formData['school_type'] === 'College' ? 'selected' : '' ?>>College</option>
                            <option value="University" <?= $formData['school_type'] === 'University' ? 'selected' : '' ?>>University</option>
                            <option value="Other" <?= $formData['school_type'] === 'Other' ? 'selected' : '' ?>>Other</option>
                        </select>
                    </div>

                    <div class="field">
                        <label for="student_count">Approx. Student Count</label>
                        <input id="student_count" name="student_count" type="number" min="0" value="<?= h((string) $formData['student_count']) ?>" placeholder="e.g. 1200">
                    </div>

                    <div class="field full">
                        <label for="preferred_contact">Preferred Contact Method</label>
                        <select id="preferred_contact" name="preferred_contact">
                            <option value="Email" <?= $formData['preferred_contact'] === 'Email' ? 'selected' : '' ?>>Email</option>
                            <option value="Phone" <?= $formData['preferred_contact'] === 'Phone' ? 'selected' : '' ?>>Phone</option>
                            <option value="WhatsApp" <?= $formData['preferred_contact'] === 'WhatsApp' ? 'selected' : '' ?>>WhatsApp</option>
                        </select>
                    </div>

                    <div class="field full">
                        <label for="message">Notes</label>
                        <textarea id="message" name="message" placeholder="Tell us anything important about your institution needs..."><?= h($formData['message']) ?></textarea>
                    </div>
                </div>

                <div class="actions">
                    <button type="submit"><i class="fas fa-paper-plane"></i> Submit Demo Request</button>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>
