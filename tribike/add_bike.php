<?php
require 'includes/db.php';
require 'includes/auth.php';
if (session_status() == PHP_SESSION_NONE) session_start();
require_login();
$user = current_user($pdo);
if (!$user['is_admin']) die('Access denied');

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_bike'])) {
    $model = trim($_POST['model'] ?? '');
    $type = trim($_POST['type'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $status = $_POST['status'] ?? 'available';
    $latitude = !empty($_POST['latitude']) ? floatval($_POST['latitude']) : null;
    $longitude = !empty($_POST['longitude']) ? floatval($_POST['longitude']) : null;
    $address = trim($_POST['address'] ?? '');
    
    if (empty($model)) {
        $error = 'Sila masukkan model basikal';
    } elseif (empty($type)) {
        $error = 'Sila masukkan jenis basikal';
    } elseif ($price <= 0) {
        $error = 'Sila masukkan harga yang sah';
    } else {
        try {
            $stmt = $pdo->prepare('INSERT INTO bikes (model, type, price_per_hour, status, latitude, longitude, address) VALUES (?, ?, ?, ?, ?, ?, ?)');
            $stmt->execute([$model, $type, $price, $status, $latitude, $longitude, $address]);
            $message = 'Basikal berjaya ditambah!';
            // Reset form
            $_POST = [];
        } catch (Exception $e) {
            $error = 'Ralat: ' . $e->getMessage();
        }
    }
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Tambah Basikal - Tribike</title>
<link rel="stylesheet" href="assets/style.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<style>
#map-picker { 
  height: 300px; 
  width: 100%;
  border: 2px solid #ddd;
  border-radius: 8px;
  margin: 15px 0;
  cursor: crosshair;
}
.form-group { margin: 15px 0; }
.form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
.form-group input, .form-group select, .form-group textarea { width: 100%; padding: 10px; }
.two-column { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
@media (max-width: 600px) {
  .two-column { grid-template-columns: 1fr; }
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
    <span>Welcome, <?=htmlspecialchars($user['name'])?> | <a href="admin.php">Admin Panel</a> | <a href="index.php">Home</a> | <a href="logout.php">Logout</a></span>
  </div>
</div>
<div class="container">
  <h2>🚲 Tambah Basikal Baharu</h2>
  
  <?php if ($message): ?>
    <div class="success" style="background:#d4edda; color:#155724; padding:12px; border-radius:6px; margin:15px 0;">
      <?=htmlspecialchars($message)?> | <a href="admin.php">← Kembali ke Admin Panel</a>
    </div>
  <?php endif; ?>
  
  <?php if ($error): ?>
    <div class="error" style="background:#f8d7da; color:#721c24; padding:12px; border-radius:6px; margin:15px 0;">
      <?=htmlspecialchars($error)?>
    </div>
  <?php endif; ?>
  
  <form method="post" style="max-width:800px;">
    <input type="hidden" name="add_bike" value="1">
    
    <div class="two-column">
      <div class="form-group">
        <label>Model Basikal *</label>
        <input type="text" name="model" required placeholder="Contoh: Polygon Xtrada 5" value="<?=htmlspecialchars($_POST['model'] ?? '')?>">
      </div>
      
      <div class="form-group">
        <label>Jenis Basikal *</label>
        <select name="type" required>
          <option value="">-- Pilih Jenis --</option>
          <option value="mountain" <?=($_POST['type'] ?? '')=='mountain'?'selected':''?>>Mountain Bike</option>
          <option value="road" <?=($_POST['type'] ?? '')=='road'?'selected':''?>>Road Bike</option>
          <option value="city" <?=($_POST['type'] ?? '')=='city'?'selected':''?>>City Bike</option>
          <option value="hybrid" <?=($_POST['type'] ?? '')=='hybrid'?'selected':''?>>Hybrid Bike</option>
          <option value="electric" <?=($_POST['type'] ?? '')=='electric'?'selected':''?>>Electric Bike</option>
        </select>
      </div>
    </div>
    
    <div class="two-column">
      <div class="form-group">
        <label>Harga per Jam (RM) *</label>
        <input type="number" name="price" step="0.01" min="0" required placeholder="0.00" value="<?=htmlspecialchars($_POST['price'] ?? '')?>">
      </div>
      
      <div class="form-group">
        <label>Status *</label>
        <select name="status" required>
          <option value="available" <?=($_POST['status'] ?? 'available')=='available'?'selected':''?>>Available</option>
          <option value="maintenance" <?=($_POST['status'] ?? '')=='maintenance'?'selected':''?>>Maintenance</option>
          <option value="damaged" <?=($_POST['status'] ?? '')=='damaged'?'selected':''?>>Damaged</option>
        </select>
      </div>
    </div>
    
    <div class="form-group">
      <label>Alamat Lokasi (opsional)</label>
      <input type="text" name="address" placeholder="Contoh: Jalan Bukit Bintang, Kuala Lumpur" value="<?=htmlspecialchars($_POST['address'] ?? '')?>">
    </div>
    
    <div class="form-group">
      <label>Koordinat GPS (opsional - klik pada peta untuk tetapkan lokasi)</label>
      <div style="margin:10px 0;">
        <button type="button" onclick="getCurrentLocation()" style="background:#3498db;">📍 Dapatkan Lokasi Saya</button>
        <button type="button" onclick="clearLocation()" style="background:#95a5a6;">🗑️ Kosongkan Lokasi</button>
      </div>
      <div class="two-column">
        <input type="number" step="any" name="latitude" id="latitude" placeholder="Latitude" value="<?=htmlspecialchars($_POST['latitude'] ?? '')?>">
        <input type="number" step="any" name="longitude" id="longitude" placeholder="Longitude" value="<?=htmlspecialchars($_POST['longitude'] ?? '')?>">
      </div>
      <div id="map-picker"></div>
      <small style="color:#666;">💡 Klik pada peta untuk tetapkan lokasi basikal atau gunakan butang "Dapatkan Lokasi Saya"</small>
    </div>
    
    <button type="submit" style="width:100%; margin-top:20px; padding:15px; font-size:16px;">✅ Tambah Basikal</button>
  </form>
  
  <p style="margin-top:30px;">
    <a href="admin.php">← Kembali ke Admin Panel</a> | 
    <a href="bikes.php">Lihat Senarai Basikal</a>
  </p>
</div>

<script>
var map;
var marker;
var defaultLat = 3.1390; // Default: Kuala Lumpur
var defaultLng = 101.6869;

// Initialize map
function initMap() {
  map = L.map('map-picker').setView([defaultLat, defaultLng], 13);
  
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors',
    maxZoom: 19
  }).addTo(map);
  
  // Get existing coordinates if any
  var lat = parseFloat(document.getElementById('latitude').value) || defaultLat;
  var lng = parseFloat(document.getElementById('longitude').value) || defaultLng;
  
  if (lat && lng) {
    map.setView([lat, lng], 15);
    addMarker(lat, lng);
  }
  
  // Add click handler to map
  map.on('click', function(e) {
    var lat = e.latlng.lat;
    var lng = e.latlng.lng;
    document.getElementById('latitude').value = lat.toFixed(8);
    document.getElementById('longitude').value = lng.toFixed(8);
    addMarker(lat, lng);
  });
}

function addMarker(lat, lng) {
  if (marker) {
    map.removeLayer(marker);
  }
  marker = L.marker([lat, lng]).addTo(map);
  marker.bindPopup('Lokasi Basikal<br>' + lat.toFixed(6) + ', ' + lng.toFixed(6)).openPopup();
}

function getCurrentLocation() {
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function(position) {
      var lat = position.coords.latitude;
      var lng = position.coords.longitude;
      document.getElementById('latitude').value = lat.toFixed(8);
      document.getElementById('longitude').value = lng.toFixed(8);
      map.setView([lat, lng], 15);
      addMarker(lat, lng);
    }, function(error) {
      alert('Tidak dapat mendapatkan lokasi: ' + error.message);
    });
  } else {
    alert('Geolocation tidak disokong oleh pelayar anda.');
  }
}

function clearLocation() {
  document.getElementById('latitude').value = '';
  document.getElementById('longitude').value = '';
  if (marker) {
    map.removeLayer(marker);
    marker = null;
  }
  map.setView([defaultLat, defaultLng], 13);
}

// Initialize map when page loads
window.onload = initMap;
</script>
</body>
</html>


