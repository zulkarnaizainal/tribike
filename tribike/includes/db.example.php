<?php
/**
 * Database Configuration Example
 * 
 * Copy this file to db.php and update with your credentials
 * For production, use environment variables
 */

// Option 1: Use environment variables (RECOMMENDED for production)
$host = $_ENV['DB_HOST'] ?? getenv('DB_HOST') ?? '127.0.0.1';
$db   = $_ENV['DB_NAME'] ?? getenv('DB_NAME') ?? 'tribike';
$user = $_ENV['DB_USER'] ?? getenv('DB_USER') ?? 'root';
$pass = $_ENV['DB_PASS'] ?? getenv('DB_PASS') ?? '';

// Option 2: Direct configuration (for localhost/testing)
// $host = '127.0.0.1';
// $db   = 'tribike';
// $user = 'root';
// $pass = '';

// Option 3: Online database (PlanetScale, Render, etc.)
// $host = 'your-database-host.com';
// $db   = 'tribike';
// $user = 'your-username';
// $pass = 'your-password';

$charset = 'utf8mb4';
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

// For PlanetScale or databases requiring SSL, uncomment:
// $dsn .= ";sslmode=REQUIRED";
// $options[PDO::MYSQL_ATTR_SSL_CA] = true;

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    echo "Database connection failed: " . htmlspecialchars($e->getMessage());
    exit;
}
?>


