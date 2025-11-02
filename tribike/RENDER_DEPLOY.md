# Panduan Deploy ke Render.com

## 🚀 Langkah-langkah Deploy

### 1. Upload ke GitHub

```bash
# Initialize git (jika belum)
git init

# Add all files
git add .

# Commit
git commit -m "Initial commit - Tribike app"

# Create repository di GitHub, kemudian:
git remote add origin https://github.com/USERNAME/tribike.git
git branch -M main
git push -u origin main
```

### 2. Deploy ke Render.com

1. **Sign up/Login ke Render**
   - Pergi ke https://render.com
   - Sign up dengan GitHub account

2. **Create Blueprint**
   - Klik "New +" → "Blueprint"
   - Connect GitHub repository anda
   - Render akan auto-detect `render.yaml`

3. **Review Configuration**
   - Render akan show preview of services
   - Pastikan ada:
     - Web Service (tribike)
     - Database (tribike-db)
   - Klik "Apply"

4. **Wait for Deploy**
   - Render akan build dan deploy
   - Ambil masa ~5-10 minit

### 3. Import Database

Selepas deploy berjaya:

1. **Dapatkan Database Connection Info**
   - Render Dashboard → Databases → tribike-db
   - Klik "Connect"
   - Copy connection details

2. **Import Database Schema**
   
   **Option A: Via Render Dashboard (Recommended)**
   - Klik pada database → "Connect"
   - Gunakan MySQL client atau Render Shell
   - Import `tribike_db.sql`

   **Option B: Via Command Line**
   ```bash
   # Dapatkan connection string dari Render dashboard
   mysql -h <host> -u <user> -p <database> < tribike_db.sql
   ```

   **Option C: Via phpMyAdmin Alternative**
   - Install MySQL client locally
   - Connect ke Render database
   - Import SQL file

### 4. Verify Deployment

1. **Check Web Service**
   - Buka URL yang diberikan Render
   - Test: `https://your-app.onrender.com`

2. **Check Database Connection**
   - Login ke aplikasi
   - Test semua features

## 📋 Database Setup Details

### Connection Info dari Render

Render akan auto-set environment variables:
- `DB_HOST` - Database host
- `DB_PORT` - Database port (usually 3306)
- `DB_NAME` - Database name (tribike)
- `DB_USER` - Database username
- `DB_PASS` - Database password

**Nota:** Semua ini sudah configured dalam `render.yaml`!

### Manual Import SQL

Jika perlu import manually:

1. **Via Render Shell**
   ```bash
   # Access Render Shell dari dashboard
   mysql -u tribike_user -p tribike < tribike_db.sql
   ```

2. **Via External MySQL Client**
   - Install MySQL Workbench atau DBeaver
   - Connect menggunakan credentials dari Render
   - Run SQL commands dari `tribike_db.sql`

## 🔧 Troubleshooting

### Error: "Database connection failed"

1. Check environment variables di Render dashboard
2. Verify database service is running
3. Check database credentials
4. Ensure database is accessible from web service

### Error: "Dockerfile not found"

- **Solution:** Render.com tidak memerlukan Dockerfile untuk PHP
- Pastikan `render.yaml` ada dan betul
- Remove Dockerfile jika ada (optional)

### Website tidak load

1. Check build logs di Render dashboard
2. Verify `startCommand` dalam render.yaml
3. Check PHP syntax errors
4. Verify `index.php` exists

### Database import failed

1. Check SQL syntax
2. Verify user permissions
3. Try importing table by table
4. Check database size limits (free tier has limits)

## 📝 Environment Variables (Auto-set oleh Render)

Render akan auto-set ini dari database connection:

```
DB_HOST=dpg-xxxxx.oregon-postgres.render.com
DB_PORT=5432
DB_NAME=tribike
DB_USER=tribike_user
DB_PASS=xxxxxxxxxxxxx
```

**Tidak perlu set manually!** Semua sudah dalam `render.yaml`.

## ✅ Checklist

- [ ] Code pushed ke GitHub
- [ ] Render account created
- [ ] Blueprint created dan connected
- [ ] Services deployed successfully
- [ ] Database created
- [ ] Database schema imported
- [ ] Test login (admin@example.com / admin123)
- [ ] Test all features
- [ ] Custom domain setup (optional)

## 🔗 Useful Links

- Render Dashboard: https://dashboard.render.com
- Render Docs: https://render.com/docs
- Support: https://render.com/docs/getting-support

## 💡 Tips

1. **Free Tier Limits:**
   - Services spin down after 15 min inactivity
   - First request may be slow (cold start)
   - Consider upgrading untuk production

2. **Database:**
   - Free tier: 90 days retention
   - Backup automatically
   - Can upgrade to paid for persistence

3. **Custom Domain:**
   - Free tier supports custom domains
   - Setup in Render dashboard
   - Free SSL included


