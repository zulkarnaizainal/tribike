# Tribike - Sistem Sewa Basikal

**Slogan: Three Mind One Mission**

## Instalasi

1. Letakkan folder ini ke dalam `C:\xampp\htdocs\tribike\`
2. Import `tribike_db.sql` ke dalam phpMyAdmin untuk membuat database baharu
3. Jika anda sudah ada database, import `update_db.sql` untuk menambah field baharu
4. Buka pelayar dan akses `http://localhost/tribike/`

## Ciri-ciri

### 🚲 Asas
- **Logo & Slogan**: Logo basikal dan slogan "Three Mind One Mission" di semua halaman
- **Senarai Basikal**: Lihat semua basikal yang tersedia
- **Booking**: Tempah basikal untuk kegunaan masa depan
- **Sewa**: Sewa basikal secara langsung
- **Pembayaran**: Sistem invoice dan pembayaran (mock payment)
- **Profil Pengguna**: Urus profil pengguna
- **Admin Panel**: Urus pengguna dan basikal (untuk admin)

### ⚠️ Laporan Kerosakan & Lokasi
- **Laporkan Basikal Rosak**: Pengguna boleh laporkan basikal yang rosak dengan:
  - Pilih basikal yang rosak
  - Masukkan keterangan kerosakan
  - Dapatkan lokasi GPS secara automatik atau masukkan secara manual
  - Masukkan alamat lokasi
  
- **Lihat Basikal Rosak**: 
  - Senarai semua basikal yang rosak
  - Bilangan laporan kerosakan untuk setiap basikal
  - Status laporan (pending, resolved, dll)
  
- **Lihat Lokasi Basikal Rosak**:
  - Peta interaktif menunjukkan lokasi basikal yang rosak
  - Koordinat GPS
  - Link ke Google Maps
  - Alamat lokasi (jika ada)

## Struktur Database

### Jadual Utama:
- `users` - Maklumat pengguna
- `bikes` - Maklumat basikal (dengan lokasi: latitude, longitude, address)
- `rentals` - Rekod sewa basikal
- `bookings` - Rekod tempahan basikal
- `invoices` - Invois pembayaran
- `payments` - Rekod pembayaran
- `damage_reports` - Laporan kerosakan basikal dengan lokasi
- `feedback` - Maklumbalas pengguna

## Halaman Utama

- **index.php** - Laman utama dengan logo dan slogan
- **bikes.php** - Senarai semua basikal
- **report_damage.php** - Laporkan basikal rosak
- **view_damaged_bikes.php** - Lihat basikal rosak dan laporan
- **view_location.php** - Lihat lokasi basikal rosak di peta
- **invoices.php** - Invoices & Pembayaran
- **booking.php** - Tempah basikal
- **admin.php** - Panel admin

## Nota Penting

- Untuk menggunakan peta Google Maps, anda perlu menambah API key Google Maps di `view_location.php`
- Sistem pembayaran menggunakan mock payment untuk demonstrasi
- GPS location menggunakan Geolocation API browser (memerlukan HTTPS untuk production atau localhost sahaja)

## Default Admin Account
- Email: admin@example.com
- Password: (sila reset melalui database atau hubungi admin sistem)