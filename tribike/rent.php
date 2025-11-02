<?php
require 'includes/db.php';
require 'includes/auth.php';
if (session_status() == PHP_SESSION_NONE) session_start();
require_login();
$user = current_user($pdo);
if (!isset($_GET['bike_id'])) { header('Location: bikes.php'); exit; }
$bike_id = (int)$_GET['bike_id'];
$stmt = $pdo->prepare('SELECT * FROM bikes WHERE id = ? LIMIT 1'); $stmt->execute([$bike_id]); $bike = $stmt->fetch();
if (!$bike || $bike['status']!=='available') die('Bike not available');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hours = max(1, (int)($_POST['hours'] ?? 1));
    $total = $hours * $bike['price_per_hour'];
    $pdo->beginTransaction();
    $p1 = $pdo->prepare('INSERT INTO rentals (user_id,bike_id,start_time,end_time,total_price,status) VALUES (?,?,?,?,?,?)');
    $p1->execute([$user['id'],$bike_id,date('Y-m-d H:i:s'), date('Y-m-d H:i:s', strtotime("+$hours hour")), $total, 'finished']);
    $p2 = $pdo->prepare('UPDATE bikes SET status = ? WHERE id = ?'); $p2->execute(['available',$bike_id]);
    $pdo->commit();
    header('Location: bikes.php'); exit;
}
?>
<!doctype html><html><head><meta charset="utf-8"><title>Rent Bike - Tribike</title><link rel="stylesheet" href="assets/style.css"></head><body><div class="container">
  <h2>Rent Bike: <?=htmlspecialchars($bike['model'])?></h2>
  <form method="post"><label>Hours to rent<br><input name="hours" type="number" value="1" min="1"></label><br><p>Price/hr: <?=number_format($bike['price_per_hour'],2)?></p><button type="submit">Confirm Rent</button></form>
  <p><a href="bikes.php">Back</a></p></div></body></html>