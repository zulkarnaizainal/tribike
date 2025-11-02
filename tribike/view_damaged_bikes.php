<?php
require 'includes/db.php';
require 'includes/auth.php';
if (session_status() == PHP_SESSION_NONE) session_start();
$user = null;
if (is_logged_in()) $user = current_user($pdo);

// Get damaged bikes with their locations
$stmt = $pdo->query('SELECT b.*, 
  (SELECT COUNT(*) FROM damage_reports dr WHERE dr.bike_id = b.id) as report_count
  FROM bikes b 
  WHERE b.status = "damaged" OR b.id IN (SELECT bike_id FROM damage_reports)
  ORDER BY b.id DESC');
$damaged_bikes = $stmt->fetchAll();

// Get all damage reports
$reports = $pdo->query('SELECT dr.*, b.model, b.type, u.name as reporter_name 
  FROM damage_reports dr 
  JOIN bikes b ON b.id = dr.bike_id 
  LEFT JOIN users u ON u.id = dr.user_id 
  ORDER BY dr.reported_at DESC')->fetchAll();
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Basikal Rosak - Tribike</title>
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
<div class="nav-menu">
  <div style="max-width:1200px; margin:0 auto; padding:0 20px;">
    <?php if ($user): ?>
      <span>Logged in as <?=htmlspecialchars($user['name'])?> | <a href="index.php">Home</a> | <a href="logout.php">Logout</a></span>
    <?php else: ?>
      <a href="index.php">Home</a> | <a href="login.php">Login</a>
    <?php endif; ?>
  </div>
</div>
<div class="container">
  <h2>📍 Basikal Rosak & Lokasi</h2>
  
  <?php if (empty($damaged_bikes)): ?>
    <p style="padding:20px; background:#e8f5e9; border-radius:6px; color:#2e7d32;">✅ Tiada basikal rosak pada masa ini.</p>
  <?php else: ?>
    <table class="striped">
      <tr>
        <th>ID</th>
        <th>Model</th>
        <th>Type</th>
        <th>Status</th>
        <th>Lokasi</th>
        <th>Bil. Laporan</th>
        <th>Action</th>
      </tr>
      <?php foreach($damaged_bikes as $bike): ?>
      <tr>
        <td><?=htmlspecialchars($bike['id'])?></td>
        <td><?=htmlspecialchars($bike['model'])?></td>
        <td><?=htmlspecialchars($bike['type'])?></td>
        <td><span class="status-damaged"><?=htmlspecialchars($bike['status'])?></span></td>
        <td>
          <?php if ($bike['latitude'] && $bike['longitude']): ?>
            <a href="view_location.php?bike_id=<?=$bike['id']?>" style="color:#3498db;">📍 Lihat Peta</a><br>
            <small style="color:#666;"><?=htmlspecialchars($bike['address'] ?: $bike['latitude'].', '.$bike['longitude'])?></small>
          <?php else: ?>
            <span style="color:#999;">Tiada lokasi</span>
          <?php endif; ?>
        </td>
        <td><?=$bike['report_count']?></td>
        <td>
          <?php if ($bike['latitude'] && $bike['longitude']): ?>
            <a href="view_location.php?bike_id=<?=$bike['id']?>">🗺️ Peta</a>
          <?php endif; ?>
        </td>
      </tr>
      <?php endforeach; ?>
    </table>
  <?php endif; ?>
  
  <h3 style="margin-top:40px;">📋 Senarai Laporan Kerosakan</h3>
  <?php if (empty($reports)): ?>
    <p>Tiada laporan kerosakan.</p>
  <?php else: ?>
    <table class="striped">
      <tr>
        <th>Tarikh</th>
        <th>Basikal</th>
        <th>Dilaporkan Oleh</th>
        <th>Keterangan</th>
        <th>Status</th>
        <th>Lokasi</th>
      </tr>
      <?php foreach($reports as $report): ?>
      <tr>
        <td><?=date('d/m/Y H:i', strtotime($report['reported_at']))?></td>
        <td><?=htmlspecialchars($report['model'])?> (<?=htmlspecialchars($report['type'])?>)</td>
        <td><?=htmlspecialchars($report['reporter_name'])?></td>
        <td><?=htmlspecialchars($report['description'])?></td>
        <td><span class="damage-badge"><?=htmlspecialchars($report['status'])?></span></td>
        <td>
          <?php if ($report['latitude'] && $report['longitude']): ?>
            <a href="view_location.php?report_id=<?=$report['id']?>">📍 Lihat</a>
          <?php else: ?>
            <span style="color:#999;">-</span>
          <?php endif; ?>
        </td>
      </tr>
      <?php endforeach; ?>
    </table>
  <?php endif; ?>
  
  <p style="margin-top:30px;">
    <a href="bikes.php">← Senarai Semua Basikal</a> | 
    <a href="report_damage.php">Laporkan Basikal Rosak</a>
  </p>
</div>
</body>
</html>



