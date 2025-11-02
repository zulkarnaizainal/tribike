# Render.com Deployment - Troubleshooting

## ⚠️ Error: "Dockerfile not found"

Render sedang cuba gunakan Docker walaupun kita nak PHP runtime. Ini biasanya berlaku jika:

1. **Render tidak detect `runtime: php` dengan betul**
2. **Blueprint configuration tidak betul**

## ✅ Solution

### Option 1: Delete dan Recreate Service (Recommended)

1. **Delete service yang error** di Render dashboard
2. **Create service baru secara manual:**

   - Klik "New +" → **"Web Service"** (bukan Blueprint!)
   - Connect GitHub repository
   - Settings:
     - **Name:** tribike
     - **Runtime:** PHP
     - **Region:** Oregon (atau pilihan anda)
     - **Branch:** main
     - **Build Command:** (leave empty)
     - **Start Command:** `php -S 0.0.0.0:$PORT`
     - **Plan:** Free
   
3. **Add Database:**
   - Klik "New +" → **"PostgreSQL"** (Render free tier uses PostgreSQL, not MySQL!)
   - Name: tribike-db
   - Plan: Free
   - Note: Anda perlu update code untuk PostgreSQL, atau gunakan MySQL add-on (paid)

### Option 2: Fix Blueprint (Jika tetap nak guna Blueprint)

1. Pastikan `render.yaml` ada di **root folder**
2. Pastikan content betul (sudah saya update)
3. Delete service lama
4. Create Blueprint baru
5. Render akan detect `runtime: php` dari `render.yaml`

## 🔧 Important Note: MySQL vs PostgreSQL

**Render Free Tier menggunakan PostgreSQL, bukan MySQL!**

Anda ada 2 options:

### Option A: Convert ke PostgreSQL (Recommended untuk free)

1. Update `includes/db.php` untuk support PostgreSQL
2. Update `tribike_db.sql` ke PostgreSQL syntax

### Option B: Gunakan MySQL (Perlu upgrade/paid)

1. Render MySQL add-on memerlukan paid plan
2. Atau gunakan external MySQL (PlanetScale, etc.)

## 📝 Quick Fix: Update untuk PostgreSQL

Update `includes/db.php`:

```php
<?php
// Support both MySQL and PostgreSQL
$host = $_ENV['DB_HOST'] ?? getenv('DB_HOST') ?: '127.0.0.1';
$port = $_ENV['DB_PORT'] ?? getenv('DB_PORT') ?: '5432'; // PostgreSQL default
$db   = $_ENV['DB_NAME'] ?? getenv('DB_NAME') ?: 'tribike';
$user = $_ENV['DB_USER'] ?? getenv('DB_USER') ?: 'postgres';
$pass = $_ENV['DB_PASS'] ?? getenv('DB_PASS') ?: '';

// Detect database type from host or use env
$db_type = $_ENV['DB_TYPE'] ?? 'mysql';

if ($db_type === 'postgresql' || strpos($host, 'postgres') !== false) {
    // PostgreSQL
    $dsn = "pgsql:host=$host;port=$port;dbname=$db";
} else {
    // MySQL
    $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";
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
```

## 🚀 Recommended: Manual Service Creation

Bukan guna Blueprint, tapi create service secara manual:

1. **Web Service:**
   - Runtime: PHP
   - Build Command: (empty)
   - Start Command: `php -S 0.0.0.0:$PORT`

2. **Database:**
   - Type: PostgreSQL (free) atau gunakan external MySQL
   - Name: tribike-db

3. **Environment Variables:**
   - Auto-set dari database connection

## 📚 Resources

- Render PHP Docs: https://render.com/docs/php
- Render Blueprint Docs: https://render.com/docs/blueprint-spec


