<?php require 'includes/db.php'; require 'includes/auth.php'; if (session_status()==PHP_SESSION_NONE) session_start(); require_login(); $user=current_user($pdo); $error=''; $success=''; $bikes = $pdo->query('SELECT * FROM bikes WHERE status="available"')->fetchAll(); if ($_SERVER['REQUEST_METHOD']==='POST'){ $bike_id=(int)($_POST['bike_id']??0); $start=$_POST['start_datetime']??''; $end=$_POST['end_datetime']??''; if($bike_id && $start && $end && strtotime($end)>strtotime($start)){ $stmt=$pdo->prepare('INSERT INTO bookings (user_id,bike_id,start_datetime,end_datetime,status) VALUES (?,?,?,?,?)'); $stmt->execute([$user['id'],$bike_id,$start,$end,'booked']); $booking_id=$pdo->lastInsertId(); $bike=$pdo->prepare('SELECT price_per_hour FROM bikes WHERE id=? LIMIT 1'); $bike->execute([$bike_id]); $b=$bike->fetch(); $hours=max(1, ceil((strtotime($end)-strtotime($start))/3600)); $amount=$hours * floatval($b['price_per_hour']); $inv=$pdo->prepare('INSERT INTO invoices (booking_id,user_id,amount,status) VALUES (?,?,?,?)'); $inv->execute([$booking_id,$user['id'],$amount,'unpaid']); $success='Booking created. Please check your invoices.'; } else $error='Invalid input or end must be after start'; } ?><!doctype html><html><head><meta charset="utf-8"><title>Booking - Tribike</title><link rel="stylesheet" href="assets/style.css"></head><body><div class="container"><h2>Advance Booking</h2><?php if($error):?><p class="error"><?=htmlspecialchars($error)?></p><?php endif;?><?php if($success):?><p class="success"><?=htmlspecialchars($success)?></p><?php endif; ?><form method="post"><label>Bike<br><select name="bike_id" required><?php foreach($bikes as $bk):?><option value="<?=$bk['id']?>"><?=htmlspecialchars($bk['model'])?> (<?=htmlspecialchars($bk['type'])?>) - <?=number_format($bk['price_per_hour'],2)?></option><?php endforeach;?></select></label><br><label>Start (YYYY-MM-DD HH:MM)<br><input name="start_datetime" required></label><br><label>End (YYYY-MM-DD HH:MM)<br><input name="end_datetime" required></label><br><button type="submit">Book</button></form><p><a href="bikes.php">Back</a></p></div>
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