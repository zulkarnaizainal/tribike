<?php
/**
 * Script to Reset Admin Password
 * Run this once to set a new admin password, then delete this file
 */
require 'includes/db.php';

$message = '';
$error = '';

// Default admin credentials from database
$admin_email = 'admin@example.com';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset_password'])) {
    $new_password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if (empty($new_password)) {
        $error = 'Sila masukkan password baharu';
    } elseif (strlen($new_password) < 6) {
        $error = 'Password mesti sekurang-kurangnya 6 aksara';
    } elseif ($new_password !== $confirm_password) {
        $error = 'Password dan pengesahan password tidak sepadan';
    } else {
        try {
            // Store password in plain text (NOT SECURE - for development only!)
            $stmt = $pdo->prepare('UPDATE users SET password = ? WHERE email = ? AND is_admin = 1');
            $stmt->execute([$new_password, $admin_email]);
            
            if ($stmt->rowCount() > 0) {
                $message = 'Password admin berjaya direset!';
            } else {
                // Try to create admin if doesn't exist
                $stmt = $pdo->prepare('INSERT INTO users (name, email, password, is_admin) VALUES (?, ?, ?, 1)');
                $stmt->execute(['Admin', $admin_email, $new_password]);
                $message = 'Account admin telah dicipta dengan password baharu!';
            }
        } catch (Exception $e) {
            $error = 'Ralat: ' . $e->getMessage();
        }
    }
}

// Check if admin exists
$stmt = $pdo->prepare('SELECT id, name, email FROM users WHERE email = ? AND is_admin = 1');
$stmt->execute([$admin_email]);
$admin = $stmt->fetch();
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Reset Admin Password - Tribike</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<header class="header">
  <div class="header-content">
    <div class="logo-container">
      <img src="assets/logo.svg" alt="Tribike Logo" class="logo">
      <div>
        <h1 class="site-title">Tribike</h1>
        <p class="slogan">Three Mind One Mission</p>
      </div>
    </div>
  </div>
</header>
<div class="container">
  <h2>🔐 Reset Admin Password</h2>
  
  <?php if ($admin): ?>
    <div style="background:#d1ecf1; color:#0c5460; padding:15px; border-radius:6px; margin:15px 0;">
      <p><strong>Admin Account Dijumpai:</strong></p>
      <ul>
        <li><strong>Nama:</strong> <?=htmlspecialchars($admin['name'])?></li>
        <li><strong>Email:</strong> <?=htmlspecialchars($admin['email'])?></li>
      </ul>
    </div>
  <?php else: ?>
    <div style="background:#fff3cd; color:#856404; padding:15px; border-radius:6px; margin:15px 0;">
      <p><strong>⚠️ Admin account tidak dijumpai. Akaun baharu akan dicipta.</strong></p>
    </div>
  <?php endif; ?>
  
  <?php if ($message): ?>
    <div class="success" style="background:#d4edda; color:#155724; padding:15px; border-radius:6px; margin:15px 0;">
      <h3>✅ <?=htmlspecialchars($message)?></h3>
      <p><strong>Maklumat Login Admin:</strong></p>
      <ul style="background:#fff; padding:15px; border-radius:4px; margin:10px 0;">
        <li><strong>Email:</strong> <?=htmlspecialchars($admin_email)?></li>
        <li><strong>Password:</strong> <span style="color:#27ae60; font-weight:bold;">(password yang anda baru masukkan)</span></li>
      </ul>
      <p><a href="login.php" style="background:#667eea; color:white; padding:10px 20px; border-radius:6px; display:inline-block; margin-top:10px;">→ Log Masuk Sekarang</a></p>
      <p style="margin-top:20px; color:#856404;"><strong>⚠️ Untuk keselamatan, sila padam fail ini (reset_admin_password.php) selepas reset password!</strong></p>
    </div>
  <?php endif; ?>
  
  <?php if ($error): ?>
    <div class="error" style="background:#f8d7da; color:#721c24; padding:12px; border-radius:6px; margin:15px 0;">
      <?=htmlspecialchars($error)?>
    </div>
  <?php endif; ?>
  
  <?php if (!$message): ?>
    <form method="post" style="max-width:500px;">
      <input type="hidden" name="reset_password" value="1">
      
      <div style="margin:15px 0;">
        <label><strong>Email Admin:</strong></label>
        <input type="email" value="<?=htmlspecialchars($admin_email)?>" disabled style="width:100%; padding:10px; background:#f5f5f5;">
        <small style="color:#666;">Email ini akan digunakan untuk login</small>
      </div>
      
      <div style="margin:15px 0;">
        <label><strong>Password Baharu *</strong></label>
        <input type="password" name="password" required minlength="6" placeholder="Minimum 6 aksara" style="width:100%; padding:10px;">
      </div>
      
      <div style="margin:15px 0;">
        <label><strong>Sahkan Password *</strong></label>
        <input type="password" name="confirm_password" required minlength="6" placeholder="Masukkan semula password" style="width:100%; padding:10px;">
      </div>
      
      <button type="submit" style="width:100%; padding:15px; font-size:16px; margin-top:20px;">🔐 Reset Password Admin</button>
    </form>
  <?php endif; ?>
  
  <p style="margin-top:30px; padding-top:20px; border-top:2px solid #eee;">
    <a href="login.php">← Kembali ke Login</a> | 
    <a href="index.php">Home</a>
  </p>
</div>
</body>
</html>

