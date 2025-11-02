<?php
require 'includes/db.php';
require 'includes/auth.php';
if (session_status() == PHP_SESSION_NONE) session_start();
$user = null;
if (isset($_SESSION['user_id'])) $user = current_user($pdo);
?>
<!doctype html><html><head><meta charset="utf-8"><title>Tribike</title><link rel="stylesheet" href="assets/style.css"></head><body>
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
<div class="nav-menu">
  <div style="max-width:1200px; margin:0 auto; padding:0 20px;">
    <?php if ($user): ?>
      <span>Hello, <?= htmlspecialchars($user['name']) ?> | <a href="logout.php">Logout</a></span>
    <?php else: ?>
      <a href="login.php">Login</a> | <a href="register.php">Register</a>
    <?php endif; ?>
  </div>
</div>
<div class="container">
  <h2>Welcome to Tribike</h2>
  <p><strong>Sistem Sewa Basikal Terbaik</strong></p>
  <div style="margin:20px 0;">
    <a href="bikes.php" style="display:inline-block; background:#667eea; color:white; padding:12px 24px; border-radius:6px; margin:5px;">🚲 Lihat Basikal</a>
    <a href="report_damage.php" style="display:inline-block; background:#e74c3c; color:white; padding:12px 24px; border-radius:6px; margin:5px;">⚠️ Laporkan Basikal Rosak</a>
    <?php if ($user): ?>
      <a href="booking.php" style="display:inline-block; background:#27ae60; color:white; padding:12px 24px; border-radius:6px; margin:5px;">📅 Booking</a>
      <a href="invoices.php" style="display:inline-block; background:#f39c12; color:white; padding:12px 24px; border-radius:6px; margin:5px;">💰 Invoices & Pembayaran</a>
      <a href="profile.php" style="display:inline-block; background:#3498db; color:white; padding:12px 24px; border-radius:6px; margin:5px;">👤 Profile</a>
      <a href="feedback.php" style="display:inline-block; background:#9b59b6; color:white; padding:12px 24px; border-radius:6px; margin:5px;">💬 Feedback</a>
    <?php endif; ?>
    <?php if ($user && $user['is_admin']): ?>
      <a href="admin.php" style="display:inline-block; background:#2c3e50; color:white; padding:12px 24px; border-radius:6px; margin:5px;">⚙️ Admin Panel</a>
    <?php endif; ?>
  </div>
</div></body></html>