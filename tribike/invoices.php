<?php require 'includes/db.php'; require 'includes/auth.php'; if (session_status()==PHP_SESSION_NONE) session_start(); require_login(); $user=current_user($pdo); if(isset($_GET['pay']) && is_numeric($_GET['pay'])){ $id=(int)$_GET['pay']; $stmt=$pdo->prepare('UPDATE invoices SET status=? WHERE id=? AND user_id=?'); $stmt->execute(['paid',$id,$user['id']]); header('Location: invoices.php'); exit; } $invoices=$pdo->prepare('SELECT i.*, bk.bike_id, b.model AS bike_model FROM invoices i LEFT JOIN bookings bk ON bk.id=i.booking_id LEFT JOIN bikes b ON b.id=bk.bike_id WHERE i.user_id=? ORDER BY i.issued_at DESC'); $invoices->execute([$user['id']]); $invoices=$invoices->fetchAll(); ?><!doctype html><html><head><meta charset="utf-8"><title>Invoices - Tribike</title><link rel="stylesheet" href="assets/style.css"></head><body>
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
    <span>Logged in as <?=htmlspecialchars($user['name'])?> | <a href="index.php">Home</a> | <a href="logout.php">Logout</a></span>
  </div>
</div>
<div class="container"><h2>💰 Invoices & Pembayaran</h2><table><tr><th>ID</th><th>Bike</th><th>Amount</th><th>Status</th><th>Action</th></tr><?php foreach($invoices as $inv): ?><tr><td><?=$inv['id']?></td><td><?=htmlspecialchars($inv['bike_model']?:'-')?></td><td><?=number_format($inv['amount'],2)?></td><td><?=htmlspecialchars($inv['status'])?></td><td><?php if($inv['status']!='paid'): ?><a href="invoices.php?pay=<?=$inv['id']?>">Pay (mock)</a> | <a href="invoice.php?id=<?=$inv['id']?>">View</a><?php else: ?>Paid | <a href="invoice.php?id=<?=$inv['id']?>">View</a><?php endif; ?></td></tr><?php endforeach; ?></table><p><a href="index.php">Back</a></p></div>
<!-- Navigation Buttons -->
<div class="nav-buttons">
  <?php
  // Tentukan halaman semasa
  $currentFile = basename($_SERVER['PHP_SELF']);

  // Tentukan link "Home"
  // Jika fail berada dalam folder admin, pergi ke dashboard.php
  // Kalau bukan (user biasa), pergi ke index.php
  $homeLink = (strpos($_SERVER['PHP_SELF'], 'admin/') !== false) ? '../dashboard.php' : 'index.php';

  // Tentukan link "Back" berdasarkan halaman
  switch ($currentFile) {
    case 'bikes.php':
      $backLink = 'index.php';
      break;
    case 'register.php':
      $backLink = 'login.php';
      break;
    case 'add_bike.php':
    case 'edit_bike.php':
      $backLink = 'bikes.php';
      break;
    case 'rentals.php':
      $backLink = 'dashboard.php';
      break;
    case 'dashboard.php':
      $backLink = 'index.php';
      break;
    case 'users.php':
      $backLink = 'dashboard.php';
      break;
    default:
      $backLink = 'index.php';
  }
  ?>
  <a href="<?php echo $homeLink; ?>" class="btn-nav">🏠 Home</a>
  <a href="<?php echo $backLink; ?>" class="btn-nav">⬅ Back</a>
</div>

<style>
.nav-buttons {
  margin-top: 20px;
  text-align: center;
}
.btn-nav {
  display: inline-block;
  background-color: #007bff;
  color: white;
  padding: 8px 16px;
  border-radius: 8px;
  text-decoration: none;
  font-weight: bold;
  margin: 8px;
  transition: background-color 0.3s;
}
.btn-nav:hover {
  background-color: #0056b3;
}
</style>

</body></html>