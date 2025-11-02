# Panduan Deploy Tribike ke Online

## ⚠️ Penting: Netlify Tidak Menyokong PHP

Netlify adalah untuk static websites sahaja. Untuk aplikasi PHP seperti Tribike, anda perlu gunakan hosting yang menyokong PHP.

## 🚀 Pilihan Hosting untuk PHP

### 1. **Render.com** (DISYORKAN - Percuma)
- ✅ Support PHP secara percuma
- ✅ Built-in MySQL database
- ✅ Auto deploy dari GitHub
- ✅ HTTPS included

### 2. **Heroku** (Bayar selepas trial)
- ✅ Support PHP
- ✅ Add-ons untuk MySQL
- ⚠️ Harga: ~$7/bulan

### 3. **Railway.app** (Percuma untuk permulaan)
- ✅ Support PHP
- ✅ MySQL included
- ✅ Easy deployment

### 4. **DigitalOcean App Platform**
- ✅ Support PHP
- ✅ Managed MySQL database
- 💰 ~$5-12/bulan

### 5. **000webhost / InfinityFree** (Percuma)
- ✅ PHP support
- ✅ MySQL database percuma
- ⚠️ Terhad features

## 📦 Panduan Deploy ke Render.com

### Langkah 1: Sediakan GitHub Repository

1. Buat account GitHub jika belum ada
2. Upload semua fail Tribike ke GitHub:
   ```bash
   git init
   git add .
   git commit -m "Initial commit"
   git remote add origin https://github.com/USERNAME/tribike.git
   git push -u origin main
   ```

### Langkah 2: Buat render.yaml

Buat fail `render.yaml` di root folder:

```yaml
services:
  - type: web
    name: tribike
    runtime: php
    buildCommand: ""
    startCommand: php -S 0.0.0.0:$PORT
    envVars:
      - key: PHP_VERSION
        value: 8.1
    healthCheckPath: /index.php

databases:
  - name: tribike-db
    databaseName: tribike
    user: tribike_user
    plan: free
```

### Langkah 3: Deploy ke Render

1. Pergi ke https://render.com
2. Sign up dengan GitHub
3. Klik "New +" → "Blueprint"
4. Connect repository GitHub anda
5. Render akan detect render.yaml dan setup secara automatik

### Langkah 4: Setup Database

1. Selepas deploy, database akan auto-created
2. Dapatkan connection string dari Render dashboard:
   - Internal Database URL: `mysql://user:pass@host/dbname`
   - External connection (optional)

3. Update `includes/db.php` dengan connection details:

```php
<?php
// Update these with Render database credentials
$host = 'your-database-host.onrender.com'; // From Render dashboard
$db   = 'tribike'; // Database name
$user = 'tribike_user'; // Database user
$pass = 'your-password'; // From Render dashboard
$charset = 'utf8mb4';
// ... rest of code
```

### Langkah 5: Import Database

1. Export database lokal anda (phpMyAdmin → Export)
2. Import ke Render database melalui:
   - Render dashboard → Database → Connect
   - Atau gunakan MySQL client

## 🌐 Alternatif: Online MySQL Database (Bebas Hosting)

Anda boleh gunakan online MySQL database sahaja tanpa ubah hosting:

### Pilihan Database Online:

1. **PlanetScale** (Disyorkan - Percuma)
   - MySQL-compatible
   - Free tier: 5GB storage
   - https://planetscale.com

2. **Supabase** (Percuma)
   - PostgreSQL (boleh convert dari MySQL)
   - https://supabase.com

3. **AWS RDS** (Bayar)
   - Managed MySQL
   - ~$15/bulan minimum

4. **Aiven** (Free trial)
   - Managed MySQL
   - https://aiven.io

## 📝 Setup dengan PlanetScale

1. Sign up di https://planetscale.com
2. Create database baru
3. Dapatkan connection string
4. Update `includes/db.php`:

```php
<?php
// PlanetScale connection
$host = 'your-planetscale-host.planetscale.app';
$db   = 'your-database-name';
$user = 'your-username';
$pass = 'your-password';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset&ssl-mode=REQUIRED";
// Note: PlanetScale requires SSL
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
    PDO::MYSQL_ATTR_SSL_CA => true,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    echo "Database connection failed: " . htmlspecialchars($e->getMessage());
    exit;
}
?>
```

## 🔧 Update Database Connection untuk Production

Buat fail `includes/db.production.php` atau update `includes/db.php`:

```php
<?php
// Production database settings
// Use environment variables for security

$host = $_ENV['DB_HOST'] ?? getenv('DB_HOST') ?? '127.0.0.1';
$db   = $_ENV['DB_NAME'] ?? getenv('DB_NAME') ?? 'tribike';
$user = $_ENV['DB_USER'] ?? getenv('DB_USER') ?? 'root';
$pass = $_ENV['DB_PASS'] ?? getenv('DB_PASS') ?? '';

$charset = 'utf8mb4';
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("Database connection failed: " . htmlspecialchars($e->getMessage()));
}
?>
```

## 🛡️ Keselamatan untuk Production

1. **Jangan commit credentials** - gunakan environment variables
2. **Gunakan HTTPS** - kebanyakan hosting moden include HTTPS
3. **Update passwords** - guna password yang kuat
4. **Backup database** - setup automatic backups
5. **Remove debug files** - padam `install_update.php`, `check_admin_password.php`, dll

## 📁 Fail yang Perlu Diabaikan (.gitignore)

Buat `.gitignore`:

```
# Sensitive files
/includes/db.php
config.php

# Install/update scripts
install_update.php
update_admin_password_to_plaintext.php
check_admin_password.php
reset_admin_password.php

# Database dumps
*.sql
!tribike_db.sql
!update_db.sql

# IDE
.vscode/
.idea/

# OS
.DS_Store
Thumbs.db
```

## ✅ Checklist Sebelum Deploy

- [ ] Test semua fungsi dalam localhost
- [ ] Backup database
- [ ] Remove/test files
- [ ] Update database connection untuk production
- [ ] Setup environment variables
- [ ] Test connection ke online database
- [ ] Import database schema dan data
- [ ] Test login dan semua features
- [ ] Setup custom domain (optional)
- [ ] Enable HTTPS/SSL

## 🔗 Resources

- Render.com: https://render.com
- PlanetScale: https://planetscale.com
- Railway: https://railway.app
- DigitalOcean: https://digitalocean.com


