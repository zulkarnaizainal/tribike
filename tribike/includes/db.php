<?php
// Database configuration - supports MySQL and PostgreSQL
// Render.com automatically provides these via environment variables

$host = $_ENV['DB_HOST'] ?? getenv('DB_HOST') ?: '127.0.0.1';
$port = $_ENV['DB_PORT'] ?? getenv('DB_PORT') ?: '3306';
$db   = $_ENV['DB_NAME'] ?? getenv('DB_NAME') ?: 'tribike';
$user = $_ENV['DB_USER'] ?? getenv('DB_USER') ?: 'root';
$pass = $_ENV['DB_PASS'] ?? getenv('DB_PASS') ?: '';

// Detect database type (PostgreSQL if port 5432 or host contains 'postgres')
$is_postgres = ($port == 5432 || strpos($host, 'postgres') !== false || strpos($host, 'dpg-') !== false);

if ($is_postgres) {
    // PostgreSQL (Render.com free tier)
    $dsn = "pgsql:host=$host;port=$port;dbname=$db";
} else {
    // MySQL (localhost or external MySQL like PlanetScale)
    $charset = 'utf8mb4';
    $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";
}

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    echo "Database connection failed: " . htmlspecialchars($e->getMessage());
    exit;
}
?>