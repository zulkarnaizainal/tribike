<?php
require 'includes/db.php';
if (session_status() == PHP_SESSION_NONE) session_start();
$error='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    if (!$name || !$email || !$password) $error='All fields required';
    else {
        // Store password in plain text (NOT SECURE - for development only!)
        $stmt = $pdo->prepare('INSERT INTO users (name,email,password) VALUES (?,?,?)');
        try { $stmt->execute([$name,$email,$password]); header('Location: login.php'); exit; } catch (Exception $e) { $error = 'Error: '.$e->getMessage(); }
    }
}
?>
<!doctype html><html><head><meta charset="utf-8"><title>Register - Tribike</title><link rel="stylesheet" href="assets/style.css"></head><body><div class="container">
  <h2>Register</h2>
  <?php if ($error): ?><p class="error"><?=htmlspecialchars($error)?></p><?php endif; ?>
  <form method="post"><label>Name<br><input name="name" required></label><br><label>Email<br><input type="email" name="email" required></label><br><label>Password<br><input type="password" name="password" required></label><br><button type="submit">Register</button></form>
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