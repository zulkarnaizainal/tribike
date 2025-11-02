<?php
require 'includes/db.php';
require 'includes/auth.php';
if (session_status() == PHP_SESSION_NONE) session_start();
require_login();
$user = current_user($pdo);
if (!$user['is_admin']) die('Access denied');
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_bike'])) {
    $model = $_POST['model'] ?? ''; $type = $_POST['type'] ?? ''; $price = floatval($_POST['price'] ?? 0);
    $stmt = $pdo->prepare('INSERT INTO bikes (model,type,price_per_hour) VALUES (?,?,?)'); $stmt->execute([$model,$type,$price]); header('Location: admin.php'); exit;
}
$users = $pdo->query('SELECT id,name,email,is_admin FROM users ORDER BY id DESC')->fetchAll();
$bikes = $pdo->query('SELECT * FROM bikes ORDER BY id DESC')->fetchAll();
?>
<!doctype html><html><head><meta charset="utf-8"><title>Admin - Tribike</title><link rel="stylesheet" href="assets/style.css"></head><body>
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
    <span>Welcome, <?=htmlspecialchars($user['name'])?> | <a href="index.php">Home</a> | <a href="logout.php">Logout</a></span>
  </div>
</div>
<div class="container">
  <h2>⚙️ Admin Panel</h2>
  <p><a href="view_damaged_bikes.php">View Damage Reports</a> | <a href="report_damage.php">Report Damage</a></p>
  <h3>Users</h3>
  <table class="striped"><tr><th>ID</th><th>Name</th><th>Email</th><th>Admin</th></tr><?php foreach($users as $u): ?><tr><td><?=$u['id']?></td><td><?=htmlspecialchars($u['name'])?></td><td><?=htmlspecialchars($u['email'])?></td><td><?=$u['is_admin']?'Yes':'No'?></td></tr><?php endforeach; ?></table>
  <h3>Basikal</h3>
  <p><a href="add_bike.php" style="background:#27ae60; color:white; padding:10px 20px; border-radius:6px; display:inline-block; margin-bottom:15px;">➕ Tambah Basikal Baharu</a></p>
  <table class="striped">
    <tr>
      <th>ID</th>
      <th>Model</th>
      <th>Jenis</th>
      <th>Status</th>
      <th>Harga/jam</th>
      <th>Lokasi</th>
      <th>Tindakan</th>
    </tr>
    <?php foreach($bikes as $b): ?>
    <tr>
      <td><?=$b['id']?></td>
      <td><?=htmlspecialchars($b['model'])?></td>
      <td><?=htmlspecialchars($b['type'])?></td>
      <td>
        <?php 
        $statusClass = 'status-available';
        if ($b['status'] === 'damaged') $statusClass = 'status-damaged';
        elseif ($b['status'] === 'maintenance') $statusClass = 'status-maintenance';
        ?>
        <span class="<?=$statusClass?>"><?=htmlspecialchars($b['status'])?></span>
      </td>
      <td>RM <?=number_format($b['price_per_hour'],2)?></td>
      <td>
        <?php if ($b['latitude'] && $b['longitude']): ?>
          <a href="view_location.php?bike_id=<?=$b['id']?>" style="font-size:12px;">📍 Lihat</a>
        <?php else: ?>
          <span style="color:#999; font-size:12px;">-</span>
        <?php endif; ?>
      </td>
      <td>
        <a href="admin_bikes.php?id=<?=$b['id']?>">Edit</a>
      </td>
    </tr>
    <?php endforeach; ?>
  </table>
  <p style="margin-top:20px;">
    <a href="admin_bikes.php">Urus Status Basikal</a> | 
    <a href="bikes.php">Lihat Senarai Basikal (User View)</a>
  </p>
  <p><a href="report.php">Reports & Export</a></p>
</div></body></html>