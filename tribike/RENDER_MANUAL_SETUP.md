# 📋 Panduan Manual Setup di Render.com

## Langkah 1: Delete Service Lama (jika ada)

1. Pergi ke Render Dashboard
2. Cari service "tribike" yang error
3. Klik pada service
4. Settings → Scroll ke bawah → "Delete Service"

## Langkah 2: Create Web Service (Manual)

### Step 2.1: Create Web Service

1. Klik **"New +"** (butang hijau di atas)
2. Pilih **"Web Service"** (bukan Blueprint!)
3. Connect GitHub repository:
   - Pilih **"zulkarnaizainal/tribike1"**
   - Klik **"Connect"**

### Step 2.2: Configure Settings

Isi maklumat berikut:

```
Name: tribike
Region: Oregon (atau Singapore jika available)
Branch: main
Root Directory: (biarkan kosong)
Runtime: PHP
Build Command: (biarkan kosong)
Start Command: php -S 0.0.0.0:$PORT
Plan: Free
```

### Step 2.3: Create Database (PostgreSQL)

**PENTING:** Render Free Tier guna **PostgreSQL**, bukan MySQL!

1. Klik **"New +"** → **"PostgreSQL"**
2. Settings:
   ```
   Name: tribike-db
   Database: tribike
   User: tribike_user
   Region: Oregon (sama dengan web service)
   Plan: Free
   ```
3. Klik **"Create Database"**

### Step 2.4: Link Database ke Web Service

1. Klik pada Web Service "tribike"
2. Pergi ke **"Environment"** tab
3. Klik **"Add Environment Variable"**
4. Add variables berikut (atau auto-link dari database):

   ```
   DB_HOST = [dari database connection info]
   DB_PORT = 5432
   DB_NAME = tribike
   DB_USER = tribike_user
   DB_PASS = [dari database connection info]
   ```

**ATAU lebih mudah:**
- Klik **"Link Database"** dalam web service
- Pilih "tribike-db"
- Render akan auto-set environment variables!

## Langkah 3: Update Code untuk PostgreSQL

Render guna PostgreSQL, jadi kita perlu update code. Saya akan sediakan versi yang support kedua-dua.

## Langkah 4: Deploy

1. Klik **"Create Web Service"**
2. Render akan:
   - Clone repository
   - Deploy aplikasi
   - Ambil masa ~5-10 minit
3. Tunggu sehingga status **"Live"**

## Langkah 5: Import Database

### Option A: Convert SQL ke PostgreSQL

Anda perlu convert `tribike_db.sql` dari MySQL ke PostgreSQL syntax.

### Option B: Gunakan External MySQL (Lebih Mudah!)

Guna PlanetScale atau MySQL hosting lain untuk kekalkan MySQL syntax.

## ✅ Checklist

- [ ] Service lama deleted
- [ ] Web Service created
- [ ] Database created (PostgreSQL)
- [ ] Database linked ke web service
- [ ] Environment variables set
- [ ] Service deployed successfully
- [ ] Database imported
- [ ] Test application


