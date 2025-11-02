<?php
require 'includes/db.php';
require 'includes/auth.php';
if (session_status() == PHP_SESSION_NONE) session_start();
$user = null;
if (is_logged_in()) $user = current_user($pdo);

$latitude = null;
$longitude = null;
$address = '';
$title = 'Lokasi Basikal';
$bike_info = '';

if (isset($_GET['bike_id'])) {
    $bike_id = intval($_GET['bike_id']);
    $stmt = $pdo->prepare('SELECT * FROM bikes WHERE id = ?');
    $stmt->execute([$bike_id]);
    $bike = $stmt->fetch();
    if ($bike) {
        $latitude = $bike['latitude'];
        $longitude = $bike['longitude'];
        $address = $bike['address'];
        $title = 'Lokasi: ' . htmlspecialchars($bike['model']);
        $bike_info = htmlspecialchars($bike['model']) . ' (' . htmlspecialchars($bike['type']) . ')';
    }
} elseif (isset($_GET['report_id'])) {
    $report_id = intval($_GET['report_id']);
    $stmt = $pdo->prepare('SELECT dr.*, b.model, b.type FROM damage_reports dr JOIN bikes b ON b.id = dr.bike_id WHERE dr.id = ?');
    $stmt->execute([$report_id]);
    $report = $stmt->fetch();
    if ($report) {
        $latitude = $report['latitude'];
        $longitude = $report['longitude'];
        $address = $report['address'];
        $title = 'Lokasi Laporan: ' . htmlspecialchars($report['model']);
        $bike_info = htmlspecialchars($report['model']) . ' (' . htmlspecialchars($report['type']) . ')';
    }
}

if (!$latitude || !$longitude) {
    header('Location: view_damaged_bikes.php');
    exit;
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title><?=$title?> - Tribike</title>
<link rel="stylesheet" href="assets/style.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<style>
#map { 
  height: 400px; 
  width: 100%;
  border-radius: 8px;
  z-index: 1;
}
</style>
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
    <a href="index.php">Home</a> | <a href="view_damaged_bikes.php">← Kembali</a>
  </div>
</div>
<div class="container">
  <h2>🗺️ <?=$title?></h2>
  
  <?php if ($bike_info): ?>
    <p><strong>Basikal:</strong> <?=$bike_info?></p>
  <?php endif; ?>
  
  <?php if ($address): ?>
    <p><strong>Alamat:</strong> <?=htmlspecialchars($address)?></p>
  <?php endif; ?>
  
  <p><strong>Koordinat GPS:</strong> <?=htmlspecialchars($latitude)?>, <?=htmlspecialchars($longitude)?></p>
  
  <div id="map" class="map-container"></div>
  
  <p style="margin-top:20px;">
    <a href="https://www.google.com/maps?q=<?=urlencode($latitude)?>,<?=urlencode($longitude)?>" target="_blank" style="background:#3498db; color:white; padding:10px 20px; border-radius:6px; display:inline-block; margin-right:10px;">
      📍 Buka dalam Google Maps
    </a>
    <a href="https://www.openstreetmap.org/?mlat=<?=urlencode($latitude)?>&mlon=<?=urlencode($longitude)?>&zoom=15" target="_blank" style="background:#27ae60; color:white; padding:10px 20px; border-radius:6px; display:inline-block;">
      🗺️ Buka dalam OpenStreetMap
    </a>
  </p>
</div>

<script>
// Initialize map using Leaflet (OpenStreetMap - no API key needed)
var lat = <?=$latitude?>;
var lng = <?=$longitude?>;

// Create map
var map = L.map('map').setView([lat, lng], 15);

// Add OpenStreetMap tiles
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
  attribution: '© OpenStreetMap contributors',
  maxZoom: 19
}).addTo(map);

// Custom red icon for damaged bike
var redIcon = L.icon({
  iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png',
  shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
  iconSize: [25, 41],
  iconAnchor: [12, 41],
  popupAnchor: [1, -34],
  shadowSize: [41, 41]
});

// Add marker
var marker = L.marker([lat, lng], {icon: redIcon}).addTo(map);

// Add popup with bike information
var popupContent = '<div style="padding:5px;"><strong><?=addslashes($bike_info ?: "Lokasi Basikal")?></strong>';
<?php if($address): ?>
popupContent += '<br><?=addslashes(htmlspecialchars($address))?>';
<?php endif; ?>
popupContent += '<br><small>Koordinat: <?=htmlspecialchars($latitude)?>, <?=htmlspecialchars($longitude)?></small></div>';
marker.bindPopup(popupContent).openPopup();
</script>
</body>
</html>


