# 📌 Quick Reference - Sistem e-Procurement

Panduan cepat untuk akses referensi kilat.

---

## 🚀 Command Cheat Sheet

### Development

```bash
# Start development
php artisan serve                    # Run Laravel server (localhost:8000)
npm run dev                          # Run Vite with hot reload

# Database
php artisan migrate                  # Run migrations
php artisan migrate:fresh            # Fresh migration (DROP all tables)
php artisan migrate:fresh --seed    # Fresh migration + seed data
php artisan db:seed                  # Seed data
php artisan db:seed --class=IGDPermintaanSeeder  # Seed specific seeder

# Clear cache
php artisan cache:clear             # Clear application cache
php artisan config:clear            # Clear config cache
php artisan route:clear             # Clear route cache
php artisan view:clear              # Clear compiled views
php artisan optimize:clear          # Clear all caches

# Code quality
./vendor/bin/pint                   # Fix code style
./vendor/bin/pint --test            # Check code style
php artisan test                    # Run tests
```

### Production

```bash
# Optimize for production
composer install --optimize-autoloader --no-dev
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Permissions (Linux)
sudo chmod -R 775 storage bootstrap/cache
sudo chown -R www-data:www-data storage bootstrap/cache
```

---

## 🔗 URL Routes

| Route | Method | Description |
|-------|--------|-------------|
| `/` | GET | Home/Welcome |
| `/login` | GET/POST | Login page |
| `/register` | GET/POST | Register (if enabled) |
| `/forgot-password` | GET/POST | Forgot password |
| `/dashboard` | GET | Dashboard |
| `/permintaan` | GET | List permintaan |
| `/permintaan/create` | GET | Form create permintaan |
| `/permintaan` | POST | Store permintaan |
| `/permintaan/{id}` | GET | Show detail permintaan |
| `/permintaan/{id}/edit` | GET | Form edit permintaan |
| `/permintaan/{id}` | PUT/PATCH | Update permintaan |
| `/permintaan/{id}` | DELETE | Delete permintaan |
| `/profile` | GET/PATCH | User profile |
| `/logout` | POST | Logout |

---

## 📊 Status Permintaan

| Status | Badge | Deskripsi |
|--------|-------|-----------|
| `diajukan` | 🟡 Kuning | Permintaan baru yang diajukan |
| `proses` | 🔵 Biru | Sedang dalam proses verifikasi |
| `disetujui` | 🟢 Hijau | Sudah disetujui |

---

## 🏥 Bidang/Unit (24 Unit)

1. Instalasi Gawat Darurat
2. Instalasi Rawat Jalan
3. Instalasi Rawat Inap
4. Instalasi Bedah Sentral
5. Instalasi Intensif Care
6. Instalasi Farmasi
7. Instalasi Laboratorium Patologi Klinik
8. Instalasi Radiologi
9. Instalasi Rehabilitasi Medik
10. Instalasi Gizi
11. Instalasi Kedokteran Forensik dan Medikolegal
12. Unit Hemodialisa
13. Unit Bank Darah Rumah Sakit
14. Unit Laboratorium Patologi Anatomi
15. Unit Sterilisasi Sentral
16. Unit Endoskopi
17. Unit Pemasaran dan Promosi Kesehatan Rumah Sakit
18. Unit Rekam Medik
19. Instalasi Pendidikan dan Penelitian
20. Instalasi Pemeliharaan Sarana
21. Instalasi Penyehatan Lingkungan
22. Unit Teknologi Informasi
23. Unit Keselamatan dan Kesehatan Kerja Rumah Sakit
24. Unit Pengadaan
25. Unit Aset & Logistik
26. Unit Penjaminan
27. Unit Pengaduan

---

## 📝 Template Deskripsi Permintaan

### Format Standar

```
Permintaan pengadaan [nama barang/jasa] untuk [unit/keperluan]:

DAFTAR BARANG/JASA:
1. [Nama item 1] - [spesifikasi] @ [jumlah] [satuan]
2. [Nama item 2] - [spesifikasi] @ [jumlah] [satuan]
3. [dst...]

JUSTIFIKASI/ALASAN:
[Penjelasan mengapa barang/jasa ini diperlukan]

URGENSI: [Tinggi/Sedang/Rendah]
DEADLINE: [jika ada]
```

### Contoh Lengkap

```
Permintaan pengadaan alat kesehatan untuk ruang resusitasi IGD:

DAFTAR ALAT:
1. Defibrillator portable - Merk X, 360J @ 1 unit
2. Oksigen konsentrator - 10L/min @ 2 unit
3. Suction pump portable - 40L/min @ 3 unit
4. Ventilator transport - CPAP/BiPAP @ 1 unit
5. Monitor vital sign - 5 parameter @ 2 unit

JUSTIFIKASI:
Alat-alat di atas sangat mendesak mengingat:
- Peningkatan kasus emergency 35% (3 bulan terakhir)
- 2 unit defibrillator existing sudah tidak layak pakai
- Kebutuhan mobilitas tinggi untuk ambulans

URGENSI: Tinggi
DEADLINE: 30 November 2025
```

---

## 🔢 Format Nomor Nota Dinas

**Pattern:** `ND/[UNIT]/[TAHUN]/[NOMOR]/[BULAN]`

**Contoh:**
- `ND/IGD/2025/001/X` - IGD, tahun 2025, nomor 1, bulan Oktober
- `ND/FARMASI/2025/015/XI` - Farmasi, tahun 2025, nomor 15, bulan November

**Kode Bulan Romawi:**
- I = Januari, II = Februari, III = Maret
- IV = April, V = Mei, VI = Juni
- VII = Juli, VIII = Agustus, IX = September
- X = Oktober, XI = November, XII = Desember

---

## 🗄️ Database Schema

### Tabel `users`
```
- user_id (PK)
- nama
- email (unique)
- password
- role
- created_at
- updated_at
```

### Tabel `permintaan`
```
- permintaan_id (PK)
- user_id (FK → users)
- bidang
- tanggal_permintaan
- deskripsi
- status (diajukan/proses/disetujui)
- pic_pimpinan
- no_nota_dinas
- link_scan
- created_at
- updated_at
```

---

## 🔐 Default Credentials (Development)

**Admin:**
```
Email: admin@rsudibsnugresik.id
Password: password
```

⚠️ **PENTING:** Ganti password default di production!

---

## 🌐 Environment Variables (.env)

### Development
```env
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=sqlite
```

### Production
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://eprocurement.rsudibsnugresik.id

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pengadaan_production
DB_USERNAME=pengadaan_user
DB_PASSWORD=strong_password_here

CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

---

## 📂 Important Files & Directories

```
pengadaan-app/
├── .env                        # Environment config (DO NOT COMMIT!)
├── .env.example                # Template environment
├── database/database.sqlite    # SQLite database file
├── storage/logs/laravel.log    # Application logs
├── public/                     # Public web root
│   └── images/logorsis.png    # Logo RSUD
├── resources/js/Pages/         # Inertia pages (Vue)
├── app/Http/Controllers/       # Controllers
│   └── PermintaanController.php
├── routes/web.php              # Web routes
└── database/
    ├── migrations/             # Database migrations
    └── seeders/               # Database seeders
```

---

## 🆘 Quick Troubleshooting

### Error: Class not found
```bash
composer dump-autoload
```

### Error: Permission denied
```bash
# Linux/Mac
chmod -R 775 storage bootstrap/cache

# Windows: Right-click folder → Properties → Security → Full Control
```

### Error: Vite manifest not found
```bash
npm run build
```

### Error: No encryption key
```bash
php artisan key:generate
```

### Error: Database connection failed
```bash
# Check .env configuration
# Make sure database exists
# Test connection:
php artisan tinker
>>> DB::connection()->getPdo();
```

### Clear ALL caches
```bash
php artisan optimize:clear
composer dump-autoload
npm run build
```

---

## 📞 Quick Contact

**Tim IT RSUD Ibnu Sina**
- 📧 Email: it@rsudibsnugresik.id
- 📱 Phone: (031) xxxx-xxxx
- 🏥 Lokasi: Ruang IT, RSUD Ibnu Sina Gresik

**Jam Support:**
- Senin - Jumat: 08:00 - 16:00 WIB
- Sabtu: 08:00 - 12:00 WIB

---

## 🔗 Useful Links

- [Dokumentasi Lengkap](README.md)
- [Panduan Instalasi](INSTALASI.md)
- [Panduan Penggunaan](PENGGUNAAN.md)
- [Contoh Data IGD](CONTOH_DATA_IGD.md)
- [Laravel Docs](https://laravel.com/docs)
- [Vue.js Docs](https://vuejs.org)
- [Inertia.js Docs](https://inertiajs.com)
- [Tailwind CSS Docs](https://tailwindcss.com)

---

**Last Updated:** 16 Oktober 2025  
**Version:** 1.0
