<?php 
require 'includes/db.php'; 
require 'includes/auth.php'; 
if (session_status()==PHP_SESSION_NONE) session_start(); 
require_login(); 
$user=current_user($pdo); 
if(!$user['is_admin']) die('Access denied'); 

$message = '';
$error = '';

if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['update_status'])){
    $bike_id=(int)$_POST['bike_id']; 
    $status=$_POST['status']??'available'; 
    $stmt=$pdo->prepare('UPDATE bikes SET status=? WHERE id=?'); 
    $stmt->execute([$status,$bike_id]); 
    $message = 'Status basikal berjaya dikemaskini!';
} 

$bikes=$pdo->query('SELECT * FROM bikes ORDER BY id DESC')->fetchAll(); 
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Urus Basikal - Tribike</title>
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
    <span>Welcome, <?=htmlspecialchars($user['name'])?> | <a href="admin.php">Admin Panel</a> | <a href="add_bike.php">Tambah Basikal</a> | <a href="index.php">Home</a> | <a href="logout.php">Logout</a></span>
  </div>
</div>
<div class="container">
  <h2>⚙️ Urus Basikal</h2>
  
  <?php if ($message): ?>
    <div class="success" style="background:#d4edda; color:#155724; padding:12px; border-radius:6px; margin:15px 0;">
      <?=htmlspecialchars($message)?>
    </div>
  <?php endif; ?>
  
  <p><a href="add_bike.php" style="background:#27ae60; color:white; padding:10px 20px; border-radius:6px; display:inline-block; margin-bottom:15px;">➕ Tambah Basikal Baharu</a></p>
  
  <table class="striped">
    <tr>
      <th>ID</th>
      <th>Model</th>
      <th>Jenis</th>
      <th>Status</th>
      <th>Harga/jam</th>
      <th>Ubah Status</th>
    </tr>
    <?php foreach($bikes as $b): ?>
    <tr>
      <td><?=$b['id']?></td>
      <td><strong><?=htmlspecialchars($b['model'])?></strong></td>
      <td><?=htmlspecialchars($b['type'])?></td>
      <td>
        <?php 
        $statusClass = 'status-available';
        if ($b['status'] === 'damaged') $statusClass = 'status-damaged';
        elseif ($b['status'] === 'maintenance') $statusClass = 'status-maintenance';
        elseif ($b['status'] === 'rented') $statusClass = 'status-rented';
        ?>
        <span class="<?=$statusClass?>"><?=htmlspecialchars($b['status'])?></span>
      </td>
      <td>RM <?=number_format($b['price_per_hour'],2)?></td>
      <td>
        <form method="post" style="display:inline">
          <input type="hidden" name="bike_id" value="<?=$b['id']?>">
          <select name="status" style="padding:5px; margin-right:5px;">
            <option value="available" <?=$b['status']=='available'?'selected':''?>>Available</option>
            <option value="rented" <?=$b['status']=='rented'?'selected':''?>>Rented</option>
            <option value="maintenance" <?=$b['status']=='maintenance'?'selected':''?>>Maintenance</option>
            <option value="damaged" <?=$b['status']=='damaged'?'selected':''?>>Damaged</option>
          </select>
          <button name="update_status" type="submit" style="padding:5px 15px;">Update</button>
        </form>
      </td>
    </tr>
    <?php endforeach; ?>
  </table>
  
  <p style="margin-top:20px;">
    <a href="admin.php">← Kembali ke Admin Panel</a> | 
    <a href="bikes.php">Lihat Senarai Basikal (User View)</a>
  </p>
</div>
</body>
</html>