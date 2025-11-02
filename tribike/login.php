<?php
require 'includes/db.php';
require 'includes/auth.php';
if (session_status() == PHP_SESSION_NONE) session_start();
$error='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $stmt = $pdo->prepare('SELECT id,password FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $row = $stmt->fetch();
    // Plain text password comparison (NOT SECURE - for development only!)
    if ($row && $row['password'] === $password) {
        $_SESSION['user_id'] = $row['id'];
        header('Location: index.php'); exit;
    } else $error='Invalid credentials';
}
?>
<!doctype html><html><head><meta charset="utf-8"><title>Login - Tribike</title><link rel="stylesheet" href="assets/style.css"></head><body>
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
<div class="container">
  <h2>Login</h2>
  <?php if ($error): ?><p class="error"><?=htmlspecialchars($error)?></p><?php endif; ?>
  <form method="post"><label>Email<br><input type="email" name="email" required></label><br><label>Password<br><input type="password" name="password" required></label><br><button type="submit">Login</button></form>
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

  <p><a href="register.php">Register</a></p></div></body></html>