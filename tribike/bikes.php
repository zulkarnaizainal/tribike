<?php
require 'includes/db.php';
require 'includes/auth.php';
if (session_status() == PHP_SESSION_NONE) session_start();
$user = null; if (is_logged_in()) $user = current_user($pdo);
$stmt = $pdo->query('SELECT * FROM bikes ORDER BY id DESC'); $bikes = $stmt->fetchAll();
?>
<!doctype html><html><head><meta charset="utf-8"><title>Bikes - Tribike</title><link rel="stylesheet" href="assets/style.css"></head><body>
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
    <?php if ($user): ?><span>Logged in as <?=htmlspecialchars($user['name'])?> | <a href="index.php">Home</a> | <a href="logout.php">Logout</a></span><?php else: ?><a href="index.php">Home</a> | <a href="login.php">Login</a><?php endif; ?>
  </div>
</div>
<div class="container">
  <h2>🚲 Senarai Basikal</h2>
  <div style="margin:15px 0;">
    <a href="view_damaged_bikes.php" style="background:#e74c3c; color:white; padding:10px 20px; border-radius:6px; display:inline-block;">⚠️ Lihat Basikal Rosak</a>
    <?php if ($user): ?>
      <a href="report_damage.php" style="background:#c0392b; color:white; padding:10px 20px; border-radius:6px; display:inline-block; margin-left:10px;">📝 Laporkan Kerosakan</a>
    <?php endif; ?>
  </div>
  <table class="striped"><tr><th>ID</th><th>Model</th><th>Type</th><th>Status</th><th>Harga/jam</th><th>Tindakan</th></tr>
  <?php foreach($bikes as $bike): ?>
  <tr>
    <td><?=htmlspecialchars($bike['id'])?></td>
    <td><?=htmlspecialchars($bike['model'])?></td>
    <td><?=htmlspecialchars($bike['type'])?></td>
    <td>
      <?php 
      $statusClass = 'status-available';
      if ($bike['status'] === 'damaged') $statusClass = 'status-damaged';
      elseif ($bike['status'] === 'maintenance') $statusClass = 'status-maintenance';
      ?>
      <span class="<?=$statusClass?>"><?=htmlspecialchars($bike['status'])?></span>
      <?php if ($bike['status'] === 'damaged' && ($bike['latitude'] || $bike['longitude'])): ?>
        <br><a href="view_location.php?bike_id=<?=$bike['id']?>" style="font-size:12px;">📍 Lokasi</a>
      <?php endif; ?>
    </td>
    <td>RM <?=number_format($bike['price_per_hour'],2)?></td>
    <td>
      <?php if ($bike['status']==='available' && $user): ?>
        <a href="rent.php?bike_id=<?=$bike['id']?>">Sewa</a> | 
        <a href="booking.php?bike_id=<?=$bike['id']?>">Tempah</a>
      <?php elseif($bike['status']==='damaged' && ($bike['latitude'] || $bike['longitude'])): ?>
        <a href="view_location.php?bike_id=<?=$bike['id']?>">📍 Lihat Lokasi</a>
      <?php elseif(!$user): ?>
        <a href="login.php">Log masuk untuk sewa</a>
      <?php else: ?>
        N/A
      <?php endif; ?>
    </td>
  </tr>
  <?php endforeach; ?></table>
  
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

</div></body></html>