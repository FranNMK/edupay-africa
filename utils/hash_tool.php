<?php
// utils/hash_tool.php

$generated_hash = "";
$test_result = "";

// Handle Generation
if (isset($_POST['generate'])) {
    $pass = $_POST['pass_to_hash'];
    $generated_hash = password_hash($pass, PASSWORD_BCRYPT);
}

// Handle Testing
if (isset($_POST['test'])) {
    $plain = $_POST['plain_pass'];
    $stored_hash = $_POST['hash_to_check'];
    
    if (password_verify($plain, $stored_hash)) {
        $test_result = "<b style='color:#27ae60;'>✅ MATCH!</b> This hash belongs to that password.";
    } else {
        $test_result = "<b style='color:#e74c3c;'>❌ NO MATCH.</b> Check your typing.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduPay Dev | Hash Utility</title>
    <style>
        :root { --primary: #003366; --accent: #ff9f43; }
        body { font-family: 'Segoe UI', sans-serif; padding: 40px; background: #f0f2f5; color: #333; }
        .container { max-width: 600px; margin: 0 auto; }
        .box { background: white; padding: 25px; border-radius: 12px; margin-bottom: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        h3 { margin-top: 0; color: var(--primary); border-bottom: 2px solid #eee; padding-bottom: 10px; }
        input { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; }
        button { padding: 12px 20px; cursor: pointer; background: var(--primary); color: white; border: none; border-radius: 6px; font-weight: bold; width: 100%; transition: 0.3s; }
        button:hover { opacity: 0.9; }
        .copy-btn { background: #27ae60; margin-top: 10px; }
        code { background: #f8f9fa; padding: 15px; display: block; margin-top: 10px; word-break: break-all; border-left: 4px solid var(--accent); border-radius: 4px; font-size: 0.9rem; }
        .success-msg { color: #27ae60; font-size: 0.8rem; margin-top: 5px; display: none; }
    </style>
</head>
<body>

<div class="container">
    <h2 style="text-align: center;">EduPay Dev Hash Utility</h2>

    <div class="box">
        <h3>1. Generate Hash</h3>
        <form method="POST">
            <input type="text" name="pass_to_hash" placeholder="Password to hash (e.g. 123)" required>
            <button type="submit" name="generate">Generate BCrypt Hash</button>
        </form>
        
        <?php if($generated_hash): ?>
            <p style="margin-top: 20px; font-weight: bold;">Resulting Hash:</p>
            <code id="hashCode"><?php echo $generated_hash; ?></code>
            <button class="copy-btn" onclick="copyHash()">Copy to Clipboard</button>
            <p id="copySuccess" class="success-msg">Copied successfully!</p>
        <?php endif; ?>
    </div>

    <div class="box">
        <h3>2. Verify Existing Hash</h3>
        <form method="POST">
            <input type="text" name="plain_pass" placeholder="Plain text password" required>
            <input type="text" name="hash_to_check" placeholder="Paste hash from DB ($2y$...)" required>
            <button type="submit" name="test" style="background: #555;">Verify Match</button>
        </form>
        <p style="text-align: center; margin-top: 15px;"><?php echo $test_result; ?></p>
    </div>
</div>

<script>
function copyHash() {
    const codeElement = document.getElementById('hashCode');
    const textArea = document.createElement('textarea');
    textArea.value = codeElement.innerText;
    document.body.appendChild(textArea);
    textArea.select();
    document.execCommand('copy');
    document.body.removeChild(textArea);
    
    // Show success message
    const msg = document.getElementById('copySuccess');
    msg.style.display = 'block';
    setTimeout(() => { msg.style.display = 'none'; }, 2000);
}
</script>

</body>
</html>