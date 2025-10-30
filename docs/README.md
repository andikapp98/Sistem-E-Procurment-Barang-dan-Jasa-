# 🏥 Sistem e-Procurement RSUD Ibnu Sina Kabupaten Gresik

<p align="center">
  <img src="public/images/logorsis.png" alt="RSUD Ibnu Sina" width="200"/>
</p>

<p align="center">
  <strong>Aplikasi Manajemen Permintaan Pengadaan Barang dan Jasa</strong>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=flat&logo=laravel" alt="Laravel">
  <img src="https://img.shields.io/badge/Vue.js-3.5-4FC08D?style=flat&logo=vue.js" alt="Vue.js">
  <img src="https://img.shields.io/badge/Inertia.js-2.0-9553E9?style=flat&logo=inertia" alt="Inertia.js">
  <img src="https://img.shields.io/badge/Tailwind-3.2-06B6D4?style=flat&logo=tailwind-css" alt="Tailwind">
  <img src="https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat&logo=php" alt="PHP">
</p>

---

## 📋 Tentang Aplikasi

Sistem e-Procurement RSUD Ibnu Sina adalah aplikasi web modern untuk mengelola seluruh alur pengadaan barang dan jasa rumah sakit, mulai dari pengajuan permintaan oleh unit kerja hingga serah terima barang ke instalasi. Aplikasi ini dibangun dengan teknologi terkini untuk memberikan pengalaman pengguna yang optimal dan proses bisnis yang terstruktur.

### ✨ Fitur Utama

- 🔐 **Multi-Role Access Control** - 9 role berbeda dengan hak akses spesifik
- 📊 **Dashboard Interaktif** - Statistik real-time berdasarkan role pengguna
- 📝 **Workflow Management** - Alur kerja lengkap dari permintaan hingga serah terima
- 📋 **Nota Dinas Digital** - Pembuatan dan tracking nota dinas
- 📄 **Disposisi & Perencanaan** - Manajemen disposisi dan scan berkas perencanaan
- 🏥 **Multi-Unit Support** - 24+ unit/instalasi rumah sakit
- 🔍 **Status Tracking** - Pelacakan status real-time di setiap tahapan
- 📱 **Responsive Design** - Akses dari desktop, tablet, dan smartphone
- 📎 **Document Management** - Upload dan manajemen dokumen pendukung
- 🎨 **Modern UI/UX** - Interface yang clean dan user-friendly

---

## 🚀 Tech Stack

**Backend:**
- Laravel 12.x - PHP Framework
- Inertia.js 2.0 - Modern monolith architecture
- MySQL 8.0+ - Relational database

**Frontend:**
- Vue.js 3.5 - Progressive JavaScript Framework
- Tailwind CSS 3.2 - Utility-first CSS framework
- Vite 7.0 - Next generation build tool
- Heroicons - Beautiful hand-crafted SVG icons

**Development Tools:**
- Laravel Breeze - Authentication scaffolding
- Laravel Pail - Real-time log viewer
- Laravel Pint - Code style fixer
- Ziggy - Route helper untuk JavaScript

---

## 🛠️ Instalasi

### Prasyarat

- PHP >= 8.2
- Composer >= 2.0
- Node.js >= 18.x & NPM >= 9.x
- MySQL >= 8.0
- Git

### Langkah Instalasi

```bash
# 1. Clone repository
git clone <repository-url>
cd pengadaan-app

# 2. Install PHP dependencies
composer install

# 3. Install JavaScript dependencies
npm install

# 4. Setup environment
cp .env.example .env
php artisan key:generate

# 5. Konfigurasi database di file .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pengadaan_app
DB_USERNAME=root
DB_PASSWORD=your_password

# 6. Buat database
mysql -u root -p
CREATE DATABASE pengadaan_app;
exit;

# 7. Migrasi database
php artisan migrate

# 8. Seed data demo (optional)
php artisan db:seed

# 9. Build assets untuk production
npm run build

# Atau untuk development dengan hot reload
npm run dev

# 10. Jalankan aplikasi
php artisan serve
```

Akses aplikasi di: **http://127.0.0.1:8000**

---

## 👥 User Roles & Akun Default

### Role yang Tersedia

1. **Admin** - Akses penuh ke seluruh sistem
2. **Unit Kerja** - Membuat dan mengelola permintaan
3. **Kepala Instalasi** - Review dan approval permintaan dari unit
4. **Kepala Bidang** - Review permintaan dari instalasi di bidangnya
5. **Wakil Direktur** - Approval tingkat wakil direktur
6. **Direktur** - Approval tertinggi
7. **Staff Perencanaan** - Mengelola perencanaan dan disposisi
8. **KSO** - Komite Standar & Obat
9. **Pengadaan** - Proses pengadaan barang/jasa
10. **Serah Terima** - Penerimaan dan serah terima barang

### Akun Default (Setelah Seeder)

```
Kepala Instalasi:
Email: kepala.instalasi@rsud.id
Password: password123

KSO:
Email: kso@rsud.id  
Password: password123

Staff Perencanaan:
Email: staff.perencanaan@rsud.id
Password: password123
```

---

## 🔄 Workflow Pengadaan Lengkap

```
1. Unit Kerja
   ↓ Buat Permintaan
   
2. Kepala Instalasi  
   ↓ Review & Buat Nota Dinas
   
3. Kepala Bidang
   ↓ Review & Approve
   
4. Wakil Direktur/Direktur
   ↓ Approval Final
   
5. Staff Perencanaan
   ↓ Buat Disposisi & Scan Berkas
   ↓ Input Data Perencanaan
   
6. KSO (Komite Standar & Obat)
   ↓ Review & Validasi
   
7. Bagian Pengadaan
   ↓ Proses Pengadaan
   
8. Nota Penerimaan
   ↓ Terima Barang
   
9. Serah Terima ke Kepala Instalasi
   ✓ Selesai
```

---

## 📊 Status & Tracking

### Status Permintaan

| Status | Deskripsi | Aksi Selanjutnya |
|--------|-----------|------------------|
| **diajukan** | Permintaan baru dari unit | Review oleh Kepala Instalasi |
| **proses** | Sedang dalam proses | Menunggu approval |
| **disetujui** | Sudah disetujui | Lanjut ke tahap berikutnya |
| **ditolak** | Ditolak dengan alasan | Revisi atau batal |
| **revisi** | Perlu diperbaiki | Perbaikan oleh pemohon |

### Tahapan Tracking

1. **Permintaan** - Pengajuan awal dari unit kerja
2. **Nota Dinas** - Pembuatan nota dinas oleh Kepala Instalasi
3. **Disposisi** - Disposisi dari Staff Perencanaan (Coming Soon)
4. **Perencanaan** - Input data perencanaan dan scan berkas
5. **KSO** - Review oleh Komite Standar & Obat
6. **Pengadaan** - Proses pengadaan oleh bagian pengadaan
7. **Nota Penerimaan** - Penerimaan barang
8. **Serah Terima** - Serah terima ke instalasi (selesai)

---

## 📱 Fitur per Role

### 1. Unit Kerja
- Dashboard dengan statistik permintaan sendiri
- Buat permintaan pengadaan baru
- Lihat dan edit permintaan yang masih draft
- Tracking status permintaan

**Routes:**
- `/dashboard` - Dashboard unit
- `/permintaan` - Daftar permintaan
- `/permintaan/create` - Buat permintaan baru

### 2. Kepala Instalasi
- Dashboard dengan statistik unit kerjanya
- Review permintaan dari unit di instalasinya
- Buat nota dinas
- Approve/Reject/Minta revisi permintaan

**Routes:**
- `/kepala-instalasi/dashboard` - Dashboard
- `/kepala-instalasi` - List permintaan
- `/kepala-instalasi/permintaan/{id}/nota-dinas/create` - Buat nota dinas

### 3. Kepala Bidang
- Dashboard dengan statistik bidangnya
- Review permintaan dari instalasi di bidangnya
- Approve/Reject permintaan

**Routes:**
- `/kepala-bidang/dashboard` - Dashboard
- `/kepala-bidang` - List permintaan untuk review

### 4. Wakil Direktur / Direktur
- Dashboard dengan statistik keseluruhan
- Review dan approval permintaan
- Monitoring seluruh proses

**Routes:**
- `/wakil-direktur/dashboard` - Dashboard Wakil Direktur
- `/direktur/dashboard` - Dashboard Direktur

### 5. Staff Perencanaan
- Dashboard dengan statistik perencanaan
- Buat disposisi untuk permintaan
- Upload scan berkas perencanaan
- Input data perencanaan lengkap

**Routes:**
- `/staff-perencanaan/dashboard` - Dashboard
- `/staff-perencanaan` - List permintaan
- `/staff-perencanaan/permintaan/{id}/perencanaan/create` - Input perencanaan
- `/staff-perencanaan/permintaan/{id}/scan-berkas` - Upload scan berkas

### 6. KSO (Komite Standar & Obat)
- Dashboard KSO
- Review permintaan yang masuk
- Validasi standar dan spesifikasi obat/alkes

**Routes:**
- `/kso/dashboard` - Dashboard KSO
- `/kso` - List permintaan untuk review

### 7. Bagian Pengadaan
- Dashboard pengadaan
- Proses pengadaan barang/jasa
- Update status pengadaan

**Routes:**
- `/pengadaan/dashboard` - Dashboard Pengadaan
- `/pengadaan` - List permintaan untuk diproses

### 8. Serah Terima
- Dashboard penerimaan barang
- Buat nota penerimaan
- Serah terima ke kepala instalasi

**Routes:**
- `/serah-terima/dashboard` - Dashboard
- `/serah-terima` - List penerimaan

---

## 🗂️ Struktur Database

### Tabel Utama

**`users`**
- Menyimpan data pengguna dengan role-based access
- Fields: user_id, nama, email, password, role, jabatan, unit_kerja

**`permintaan`**
- Menyimpan data permintaan pengadaan
- Fields: permintaan_id, user_id, bidang, tanggal_permintaan, deskripsi, status, pic_pimpinan

**`nota_dinas`**
- Menyimpan nota dinas yang dibuat
- Fields: nota_id, permintaan_id, dari, dari_jabatan, ke_jabatan, tanggal_nota, perihal, isi_nota

**`disposisi`**
- Menyimpan data disposisi (Coming Soon)
- Fields: disposisi_id, nota_id, dari, kepada, tanggal_disposisi, isi_disposisi, status

**`perencanaan`**
- Menyimpan data perencanaan pengadaan
- Fields: perencanaan_id, permintaan_id, jenis_pengadaan, metode_pengadaan, nilai_pagu, scan_berkas, catatan

**`kso`**
- Menyimpan data review KSO
- Fields: kso_id, permintaan_id, tanggal_review, hasil_review, catatan_kso

**`pengadaan`**
- Menyimpan data proses pengadaan
- Fields: pengadaan_id, permintaan_id, nomor_kontrak, tanggal_kontrak, vendor, nilai_kontrak

**`nota_penerimaan`**
- Menyimpan data penerimaan barang
- Fields: penerimaan_id, pengadaan_id, tanggal_terima, jumlah_diterima, kondisi

**`serah_terima`**
- Menyimpan data serah terima
- Fields: serah_terima_id, penerimaan_id, tanggal_serah_terima, penerima, bukti_terima

---

## 🎨 Struktur Project

```
pengadaan-app/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/                      # Authentication
│   │   │   ├── PermintaanController.php   # CRUD Permintaan
│   │   │   ├── KepalaInstalasiController.php
│   │   │   ├── KepalaBidangController.php
│   │   │   ├── DirekturController.php
│   │   │   ├── StaffPerencanaanController.php
│   │   │   ├── KsoController.php
│   │   │   ├── PengadaanController.php
│   │   │   └── SerahTerimaController.php
│   │   └── Middleware/
│   │       └── RedirectBasedOnRole.php    # Role redirect
│   └── Models/
│       ├── User.php
│       ├── Permintaan.php
│       ├── NotaDinas.php
│       ├── Disposisi.php
│       ├── Perencanaan.php
│       ├── Kso.php
│       ├── Pengadaan.php
│       └── SerahTerima.php
├── database/
│   ├── migrations/                        # Database migrations
│   └── seeders/                           # Data seeders
│       ├── DatabaseSeeder.php
│       ├── UserSeeder.php
│       ├── KsoSeeder.php
│       └── ...
├── resources/
│   ├── js/
│   │   ├── Components/                    # Vue components
│   │   ├── Layouts/
│   │   │   ├── AuthenticatedLayout.vue
│   │   │   └── GuestLayout.vue
│   │   └── Pages/
│   │       ├── Dashboard.vue
│   │       ├── Permintaan/
│   │       ├── KepalaInstalasi/
│   │       ├── KepalaBidang/
│   │       ├── Direktur/
│   │       ├── StaffPerencanaan/
│   │       ├── KSO/
│   │       ├── Pengadaan/
│   │       └── SerahTerima/
│   └── css/
│       └── app.css
└── routes/
    └── web.php                            # Web routes
```

---

## 💻 Development

### Menjalankan Development Server

```bash
# Terminal 1: Laravel server
php artisan serve

# Terminal 2: Vite (hot reload)
npm run dev
```

### Build untuk Production

```bash
npm run build
```

### Database Management

```bash
# Fresh migration
php artisan migrate:fresh

# Migrate dengan seeder
php artisan migrate:fresh --seed

# Seeder spesifik
php artisan db:seed --class=UserSeeder
php artisan db:seed --class=KsoSeeder
```

### Clear Cache

```bash
php artisan route:clear
php artisan view:clear
php artisan config:clear
php artisan cache:clear
```

---

## 🔒 Keamanan

- ✅ CSRF Protection
- ✅ Password Hashing (Bcrypt)
- ✅ SQL Injection Prevention (Eloquent ORM)
- ✅ XSS Protection
- ✅ Role-based Access Control (RBAC)
- ✅ Secure Session Management
- ✅ Input Validation & Sanitization

---

## 🐛 Troubleshooting

### Vite manifest error
```bash
npm run build
php artisan route:clear
```

### Route not found
```bash
php artisan route:clear
php artisan route:list  # Cek route yang tersedia
```

### Error 500 - Internal Server Error
```bash
# Cek log
php artisan pail

# Clear semua cache
php artisan optimize:clear
```

### Database connection refused
- Pastikan MySQL berjalan
- Cek kredensial di file `.env`
- Pastikan database sudah dibuat

---

## 📈 Roadmap

### Version 1.1 (Q4 2025)
- ✅ Workflow lengkap hingga serah terima
- ✅ Role-based access control
- ✅ Scan berkas perencanaan
- 🚧 Fitur disposisi (Coming Soon)
- [ ] Notifikasi email otomatis
- [ ] Export laporan (Excel/PDF)

### Version 1.2 (Q1 2026)
- [ ] Dashboard analytics lanjutan
- [ ] History & audit trail lengkap
- [ ] Sistem komentar antar role
- [ ] Upload file langsung (multi-upload)
- [ ] API untuk integrasi sistem lain

### Version 2.0 (Q2 2026)
- [ ] Mobile application (Android/iOS)
- [ ] Real-time notifications (WebSocket)
- [ ] Budget tracking & monitoring
- [ ] Vendor management system
- [ ] E-signature integration
- [ ] Advanced reporting & BI

---

## 📞 Support & Kontak

Untuk bantuan, pertanyaan, atau pelaporan bug:

- **Email IT:** it@rsudibsinugresik.id
- **Website:** https://rsudibsinugresik.id
- **Telepon:** (031) 3981718

---

## 📄 License

Proprietary Software - RSUD Ibnu Sina Kabupaten Gresik

Aplikasi ini dikembangkan khusus untuk RSUD Ibnu Sina Kabupaten Gresik.  
Hak cipta dilindungi undang-undang.

---

<p align="center">
  <strong>Developed with ❤️ for RSUD Ibnu Sina Kabupaten Gresik</strong><br>
  <em>Last Updated: October 19, 2025</em>
</p>
