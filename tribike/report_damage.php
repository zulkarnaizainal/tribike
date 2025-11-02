<?php
require 'includes/db.php';
require 'includes/auth.php';
if (session_status() == PHP_SESSION_NONE) session_start();
require_login();
$user = current_user($pdo);
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['report_damage'])) {
    $bike_id = intval($_POST['bike_id'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $latitude = !empty($_POST['latitude']) ? floatval($_POST['latitude']) : null;
    $longitude = !empty($_POST['longitude']) ? floatval($_POST['longitude']) : null;
    $address = trim($_POST['address'] ?? '');
    
    if ($bike_id <= 0) {
        $error = 'Sila pilih basikal';
    } elseif (empty($description)) {
        $error = 'Sila masukkan keterangan kerosakan';
    } else {
        // Insert damage report
        $stmt = $pdo->prepare('INSERT INTO damage_reports (bike_id, user_id, description, latitude, longitude, address) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([$bike_id, $user['id'], $description, $latitude, $longitude, $address]);
        
        // Update bike status to damaged
        $stmt = $pdo->prepare('UPDATE bikes SET status = ?, latitude = ?, longitude = ?, address = ? WHERE id = ?');
        $stmt->execute(['damaged', $latitude, $longitude, $address, $bike_id]);
        
        $message = 'Laporan kerosakan telah dihantar. Terima kasih!';
    }
}

// Get all bikes for dropdown
$bikes = $pdo->query('SELECT id, model, type FROM bikes ORDER BY model')->fetchAll();
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Laporkan Basikal Rosak - Tribike</title>
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
    <span>Logged in as <?=htmlspecialchars($user['name'])?> | <a href="index.php">Home</a> | <a href="logout.php">Logout</a></span>
  </div>
</div>
<div class="container">
  <h2>⚠️ Laporkan Basikal Rosak</h2>
  
  <?php if ($message): ?>
    <div class="success" style="background:#d4edda; color:#155724; padding:12px; border-radius:6px; margin:15px 0;"><?=htmlspecialchars($message)?></div>
  <?php endif; ?>
  
  <?php if ($error): ?>
    <div class="error" style="background:#f8d7da; color:#721c24; padding:12px; border-radius:6px; margin:15px 0;"><?=htmlspecialchars($error)?></div>
  <?php endif; ?>
  
  <form method="post" style="max-width:600px;">
    <input type="hidden" name="report_damage" value="1">
    
    <label><strong>Pilih Basikal:</strong>
      <select name="bike_id" required style="width:100%; padding:10px;">
        <option value="">-- Pilih Basikal --</option>
        <?php foreach($bikes as $bike): ?>
          <option value="<?=$bike['id']?>"><?=htmlspecialchars($bike['model'])?> (<?=htmlspecialchars($bike['type'])?>)</option>
        <?php endforeach; ?>
      </select>
    </label><br>
    
    <label><strong>Keterangan Kerosakan:</strong>
      <textarea name="description" required style="width:100%; padding:10px; min-height:100px;" placeholder="Terangkan kerosakan yang berlaku..."></textarea>
    </label><br>
    
    <label><strong>Alamat Lokasi (jika berbeza):</strong>
      <input type="text" name="address" style="width:100%; padding:10px;" placeholder="Contoh: Jalan Bukit Bintang, Kuala Lumpur">
    </label><br>
    
    <label><strong>Koordinat GPS (opsional - klik butang untuk dapatkan lokasi anda):</strong>
      <div style="margin:10px 0;">
        <button type="button" onclick="getLocation()" style="background:#3498db;">📍 Dapatkan Lokasi Saya</button>
      </div>
      <input type="number" step="any" name="latitude" id="latitude" placeholder="Latitude" style="width:48%; padding:10px; margin-right:2%;">
      <input type="number" step="any" name="longitude" id="longitude" placeholder="Longitude" style="width:48%; padding:10px;">
    </label><br>
    
    <button type="submit" style="width:100%; margin-top:15px;">Hantar Laporan</button>
  </form>
  
  <p style="margin-top:30px;"><a href="bikes.php">← Kembali ke Senarai Basikal</a> | <a href="view_damaged_bikes.php">Lihat Basikal Rosak</a></p>
</div>

<script>
function getLocation() {
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function(position) {
      document.getElementById('latitude').value = position.coords.latitude;
      document.getElementById('longitude').value = position.coords.longitude;
      alert('Lokasi GPS telah diambil: ' + position.coords.latitude + ', ' + position.coords.longitude);
    }, function(error) {
      alert('Tidak dapat mendapatkan lokasi: ' + error.message);
    });
  } else {
    alert('Geolocation tidak disokong oleh pelayar anda.');
  }
}
</script>
</body>
</html>



