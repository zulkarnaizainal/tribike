<?php
/**
 * Installation/Update Script for Tribike
 * Run this once to create missing tables and columns
 */
require 'includes/db.php';

$errors = [];
$success = [];

try {
    // Check if damage_reports table exists, if not create it
    $stmt = $pdo->query("SHOW TABLES LIKE 'damage_reports'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("CREATE TABLE IF NOT EXISTS damage_reports (
            id INT AUTO_INCREMENT PRIMARY KEY,
            bike_id INT NOT NULL,
            user_id INT NOT NULL,
            description TEXT NOT NULL,
            latitude DECIMAL(10, 8) DEFAULT NULL,
            longitude DECIMAL(11, 8) DEFAULT NULL,
            address TEXT DEFAULT NULL,
            status VARCHAR(20) DEFAULT 'pending',
            reported_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (bike_id) REFERENCES bikes(id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        $success[] = "Table 'damage_reports' created successfully";
    } else {
        $success[] = "Table 'damage_reports' already exists";
    }
} catch (Exception $e) {
    $errors[] = "Error creating damage_reports table: " . $e->getMessage();
}

// Check if bikes table has location columns
try {
    $stmt = $pdo->query("SHOW COLUMNS FROM bikes LIKE 'latitude'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE bikes ADD COLUMN latitude DECIMAL(10, 8) DEFAULT NULL");
        $success[] = "Added 'latitude' column to bikes table";
    } else {
        $success[] = "Column 'latitude' already exists in bikes table";
    }
} catch (Exception $e) {
    $errors[] = "Error adding latitude column: " . $e->getMessage();
}

try {
    $stmt = $pdo->query("SHOW COLUMNS FROM bikes LIKE 'longitude'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE bikes ADD COLUMN longitude DECIMAL(11, 8) DEFAULT NULL");
        $success[] = "Added 'longitude' column to bikes table";
    } else {
        $success[] = "Column 'longitude' already exists in bikes table";
    }
} catch (Exception $e) {
    $errors[] = "Error adding longitude column: " . $e->getMessage();
}

try {
    $stmt = $pdo->query("SHOW COLUMNS FROM bikes LIKE 'address'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE bikes ADD COLUMN address TEXT DEFAULT NULL");
        $success[] = "Added 'address' column to bikes table";
    } else {
        $success[] = "Column 'address' already exists in bikes table";
    }
} catch (Exception $e) {
    $errors[] = "Error adding address column: " . $e->getMessage();
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Database Update - Tribike</title>
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
<div class="container">
  <h2>Database Update</h2>
  
  <?php if (!empty($success)): ?>
    <div style="background:#d4edda; color:#155724; padding:15px; border-radius:6px; margin:15px 0;">
      <h3>✅ Success:</h3>
      <ul>
        <?php foreach($success as $msg): ?>
          <li><?=htmlspecialchars($msg)?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>
  
  <?php if (!empty($errors)): ?>
    <div style="background:#f8d7da; color:#721c24; padding:15px; border-radius:6px; margin:15px 0;">
      <h3>❌ Errors:</h3>
      <ul>
        <?php foreach($errors as $msg): ?>
          <li><?=htmlspecialchars($msg)?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>
  
  <?php if (empty($errors)): ?>
    <div style="background:#d1ecf1; color:#0c5460; padding:15px; border-radius:6px; margin:15px 0;">
      <p><strong>✅ Database update completed successfully!</strong></p>
      <p>You can now use all features of Tribike including damage reporting and location tracking.</p>
      <p style="margin-top:20px;"><strong>⚠️ For security, please delete this file (install_update.php) after running it once.</strong></p>
    </div>
  <?php endif; ?>
  
  <p style="margin-top:30px;">
    <a href="index.php">← Back to Home</a>
  </p>
</div>
</body>
</html>


