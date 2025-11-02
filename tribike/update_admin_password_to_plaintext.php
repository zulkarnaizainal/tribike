<?php
/**
 * Script to update existing admin password from hash to plain text
 * Run this once after changing to plain text system
 */
require 'includes/db.php';

$message = '';
$error = '';

// Default admin email
$admin_email = 'admin@example.com';
$new_password = 'admin123';

try {
    // Update admin password to plain text
    $stmt = $pdo->prepare('UPDATE users SET password = ? WHERE email = ? AND is_admin = 1');
    $stmt->execute([$new_password, $admin_email]);
    
    if ($stmt->rowCount() > 0) {
        $message = 'Password admin berjaya dikemaskini ke plain text!';
    } else {
        // Check if admin exists
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$admin_email]);
        $user = $stmt->fetch();
        
        if ($user) {
            $error = 'User wujud tetapi bukan admin. Sila check database.';
        } else {
            // Create admin if doesn't exist
            $stmt = $pdo->prepare('INSERT INTO users (name, email, password, is_admin) VALUES (?, ?, ?, 1)');
            $stmt->execute(['Admin', $admin_email, $new_password]);
            $message = 'Account admin telah dicipta dengan password plain text!';
        }
    }
} catch (Exception $e) {
    $error = 'Ralat: ' . $e->getMessage();
}

// Check current admin status
$stmt = $pdo->prepare('SELECT id, name, email, password FROM users WHERE email = ? AND is_admin = 1');
$stmt->execute([$admin_email]);
$admin = $stmt->fetch();
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Update Admin Password - Tribike</title>
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
  <h2>🔐 Update Admin Password ke Plain Text</h2>
  
  <?php if ($message): ?>
    <div class="success" style="background:#d4edda; color:#155724; padding:15px; border-radius:6px; margin:15px 0;">
      <h3>✅ <?=htmlspecialchars($message)?></h3>
      <div style="background:#fff; padding:15px; border-radius:4px; margin:15px 0; border:2px solid #27ae60;">
        <p><strong>Maklumat Login Admin:</strong></p>
        <ul style="margin:10px 0; padding-left:20px;">
          <li><strong>Email:</strong> <?=htmlspecialchars($admin_email)?></li>
          <li><strong>Password:</strong> <span style="color:#27ae60; font-weight:bold; font-size:18px;"><?=htmlspecialchars($new_password)?></span></li>
        </ul>
      </div>
      <p><a href="login.php" style="background:#667eea; color:white; padding:12px 24px; border-radius:6px; display:inline-block; margin-top:10px;">→ Log Masuk Sekarang</a></p>
      <p style="margin-top:20px; color:#856404; background:#fff3cd; padding:12px; border-radius:6px;">
        <strong>⚠️ Untuk keselamatan, sila padam fail ini selepas digunakan!</strong>
      </p>
    </div>
  <?php endif; ?>
  
  <?php if ($error): ?>
    <div class="error" style="background:#f8d7da; color:#721c24; padding:12px; border-radius:6px; margin:15px 0;">
      <strong>❌ <?=htmlspecialchars($error)?></strong>
    </div>
  <?php endif; ?>
  
  <?php if ($admin): ?>
    <div style="background:#d1ecf1; color:#0c5460; padding:15px; border-radius:6px; margin:15px 0;">
      <p><strong>Status Admin Semasa:</strong></p>
      <ul>
        <li><strong>ID:</strong> <?=$admin['id']?></li>
        <li><strong>Nama:</strong> <?=htmlspecialchars($admin['name'])?></li>
        <li><strong>Email:</strong> <?=htmlspecialchars($admin['email'])?></li>
        <li><strong>Password (terpapar):</strong> <?=htmlspecialchars(substr($admin['password'], 0, 20))?>...</li>
      </ul>
    </div>
  <?php else: ?>
    <div style="background:#fff3cd; color:#856404; padding:15px; border-radius:6px; margin:15px 0;">
      <p><strong>⚠️ Admin tidak dijumpai. Script akan cuba cipta account admin baharu.</strong></p>
    </div>
  <?php endif; ?>
  
  <?php if (!$message && !$error): ?>
    <form method="post" style="max-width:500px;">
      <input type="hidden" name="update_password" value="1">
      <p>Script ini akan update password admin ke: <strong>admin123</strong> (plain text)</p>
      <button type="submit" style="width:100%; padding:15px; font-size:16px; margin-top:20px;">🔄 Update Password Admin</button>
    </form>
  <?php endif; ?>
  
  <p style="margin-top:30px; padding-top:20px; border-top:2px solid #eee;">
    <a href="login.php">← Kembali ke Login</a> | 
    <a href="index.php">Home</a> | 
    <a href="admin.php">Admin Panel</a>
  </p>
</div>
</body>
</html>


