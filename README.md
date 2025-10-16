# 🏥 Sistem e-Procurement RSUD Ibnu Sina Kabupaten Gresik

<p align="center">
  <img src="public/images/logorsis.png" alt="RSUD Ibnu Sina" width="200"/>
</p>

<p align="center">
  <strong>Aplikasi Manajemen Permintaan Pengadaan Barang dan Jasa</strong>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-11.x-FF2D20?style=flat&logo=laravel" alt="Laravel">
  <img src="https://img.shields.io/badge/Vue.js-3.5-4FC08D?style=flat&logo=vue.js" alt="Vue.js">
  <img src="https://img.shields.io/badge/Inertia.js-2.0-9553E9?style=flat&logo=inertia" alt="Inertia.js">
  <img src="https://img.shields.io/badge/Tailwind-3.2-06B6D4?style=flat&logo=tailwind-css" alt="Tailwind">
  <img src="https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat&logo=php" alt="PHP">
</p>

---

## 📋 Tentang Aplikasi

Sistem e-Procurement adalah aplikasi web untuk mengelola permintaan pengadaan barang dan jasa di RSUD Ibnu Sina Kabupaten Gresik. Aplikasi ini memudahkan proses pengajuan, review, dan approval permintaan dari berbagai unit/instalasi rumah sakit dengan alur bertingkat.

### ✨ Fitur Utama

- 🔐 **Multi-Role System** - Admin, Kepala Instalasi, dan Unit Kerja
- 📊 **Dashboard Interaktif** - Statistik real-time berdasarkan role
- 📝 **Manajemen Permintaan** - CRUD lengkap dengan workflow approval
- 📋 **Nota Dinas** - Pembuatan dan tracking nota dinas
- 🏥 **Multi-Unit Support** - 24+ unit/instalasi rumah sakit
- 📱 **Responsive Design** - Mobile-friendly
- 🔍 **Status Tracking** - Lacak status permintaan dari awal hingga serah terima
- 🎨 **Modern UI/UX** - Clean dan user-friendly dengan Tailwind CSS

---

## 🚀 Tech Stack

**Backend:**
- Laravel 11.x - PHP Framework
- Inertia.js 2.0 - Modern monolith approach
- MySQL - Database

**Frontend:**
- Vue.js 3.5 - Progressive JavaScript Framework
- Tailwind CSS 3.2 - Utility-first CSS
- Vite 7.0 - Build tool

**Authentication:**
- Laravel Breeze - Authentication scaffolding

---

## 🛠️ Instalasi

### Prasyarat

- PHP >= 8.2
- Composer >= 2.0
- Node.js >= 18.x & NPM >= 9.x
- MySQL >= 8.0

### Langkah Instalasi

```bash
# 1. Clone repository
git clone <repository-url>
cd pengadaan-app

# 2. Install dependencies
composer install
npm install

# 3. Setup environment
cp .env.example .env
php artisan key:generate

# 4. Konfigurasi database (.env)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pengadaan_app
DB_USERNAME=root
DB_PASSWORD=

# 5. Migrasi database
php artisan migrate

# 6. Seed data demo (optional)
php artisan db:seed --class=KepalaInstalasiDataSeeder

# 7. Build assets
npm run build

# 8. Jalankan aplikasi
php artisan serve
```

Akses aplikasi di: **http://127.0.0.1:8000**

---

## 📚 Dokumentasi Lengkap

| 📄 File | 📝 Deskripsi |
|---------|--------------|
| **[README.md](README.md)** | Overview project, fitur, dan quick start guide |
| **[INSTALASI.md](INSTALASI.md)** | Panduan instalasi lengkap untuk development & production |
| **[PENGGUNAAN.md](PENGGUNAAN.md)** | Panduan penggunaan aplikasi untuk end-user |
| **[SEEDER.md](SEEDER.md)** | Panduan data demo dan seeder |
| **[CHANGELOG.md](CHANGELOG.md)** | Riwayat perubahan dan roadmap |

---

## 👥 Akun Default

Setelah menjalankan seeder, gunakan akun berikut:

### Kepala Instalasi
- **Email:** kepala_instalasi@rsud.id
- **Password:** password123
- **Unit:** Instalasi Farmasi

### Staff Farmasi (Unit)
- **Email:** staff.farmasi@rsud.id
- **Password:** password123
- **Unit:** Instalasi Farmasi

---

## 📱 Fitur Berdasarkan Role

### 1. **Admin**
- Dashboard dengan statistik keseluruhan
- Kelola semua permintaan dari semua unit
- Monitoring status pengadaan

**Routes:**
- `/dashboard` - Dashboard admin
- `/permintaan` - Kelola permintaan

### 2. **Kepala Instalasi**
- Dashboard khusus dengan statistik unit
- Review permintaan dari unit kerjanya
- Approve/Reject/Revisi permintaan
- Membuat dan mengirim nota dinas
- Meneruskan ke Direktur/Bagian Pengadaan

**Routes:**
- `/kepala-instalasi/dashboard` - Dashboard
- `/kepala-instalasi` - Daftar permintaan untuk review
- `/kepala-instalasi/permintaan/{id}` - Detail & aksi permintaan
- `/kepala-instalasi/permintaan/{id}/nota-dinas/create` - Buat nota dinas

### 3. **Unit Kerja**
- Dashboard statistik permintaan sendiri
- Buat permintaan pengadaan baru
- Lihat status permintaan
- Edit/Hapus permintaan (draft)

**Routes:**
- `/dashboard` - Dashboard unit
- `/permintaan` - Daftar permintaan saya
- `/permintaan/create` - Buat permintaan baru

---

## 🔄 Workflow Pengadaan

```
┌─────────────────┐
│  Unit Kerja     │  1. Buat Permintaan
│  (Staff)        │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ Kepala Instalasi│  2. Review & Tindakan:
│                 │     - Approve langsung
└────────┬────────┘     - Buat Nota Dinas
         │              - Minta Revisi
         │              - Tolak
         ▼
    ┌────────┐
    │Direktur│  3. Approval
    └───┬────┘
        │
        ▼
┌───────────────┐
│Bagian Pengadaan│  4. Proses Pengadaan
└───────┬───────┘
        │
        ▼
  ┌──────────┐
  │Serah Terima│  5. Selesai
  └──────────┘
```

---

## 📊 Status Permintaan

| Status | Deskripsi |
|--------|-----------|
| **diajukan** | Permintaan baru menunggu review Kepala Instalasi |
| **proses** | Sedang diproses (sudah ada nota dinas) |
| **disetujui** | Disetujui dan diteruskan ke Bagian Pengadaan |
| **ditolak** | Ditolak dengan alasan |
| **revisi** | Perlu revisi dari pemohon |

## 🔍 Tracking Status

1. **Permintaan** - Status awal
2. **Nota Dinas** - Sudah dibuat nota dinas
3. **Disposisi** - Sudah didisposisi
4. **Perencanaan** - Masuk tahap perencanaan
5. **KSO** - Komite Standar & Obat
6. **Pengadaan** - Proses pengadaan
7. **Nota Penerimaan** - Barang diterima
8. **Serah Terima** - Selesai

---

## 🗂️ Struktur Database

### Tabel Utama

**`users`**
- `user_id` - Primary Key
- `nama` - Nama lengkap
- `email` - Email (unique)
- `password` - Password (hashed)
- `role` - admin / kepala_instalasi / unit
- `jabatan` - Jabatan user
- `unit_kerja` - Unit kerja

**`permintaan`**
- `permintaan_id` - Primary Key
- `user_id` - Foreign Key ke users
- `bidang` - Bidang pengadaan
- `tanggal_permintaan` - Tanggal pengajuan
- `deskripsi` - Detail permintaan
- `status` - diajukan / proses / disetujui / ditolak / revisi
- `pic_pimpinan` - Pimpinan yang menangani
- `no_nota_dinas` - Nomor nota dinas
- `link_scan` - Link scan dokumen

**`nota_dinas`**
- `nota_id` - Primary Key
- `permintaan_id` - Foreign Key ke permintaan
- `dari_unit` - Unit pembuat nota
- `ke_jabatan` - Tujuan nota (Direktur/Bagian Pengadaan/dll)
- `tanggal_nota` - Tanggal nota dinas
- `status` - Status nota

---

## 🎨 Struktur File

```
pengadaan-app/
├── app/
│   ├── Http/Controllers/
│   │   ├── Auth/                          # Authentication
│   │   ├── KepalaInstalasiController.php  # Kepala Instalasi
│   │   ├── PermintaanController.php       # CRUD Permintaan
│   │   └── ProfileController.php          # Profile
│   └── Models/
│       ├── User.php
│       ├── Permintaan.php
│       └── NotaDinas.php
├── resources/js/
│   ├── Layouts/
│   │   ├── AuthenticatedLayout.vue        # Layout dengan sidebar
│   │   └── GuestLayout.vue                # Layout login
│   ├── Pages/
│   │   ├── Dashboard.vue                  # Dashboard umum
│   │   ├── KepalaInstalasi/
│   │   │   ├── Dashboard.vue              # Dashboard Kepala Instalasi
│   │   │   ├── Index.vue                  # List permintaan
│   │   │   ├── Show.vue                   # Detail & aksi
│   │   │   └── CreateNotaDinas.vue        # Form nota dinas
│   │   └── Permintaan/
│   │       ├── Index.vue
│   │       ├── Create.vue
│   │       ├── Edit.vue
│   │       └── Show.vue
│   └── Components/                        # Reusable components
└── routes/
    └── web.php                            # Web routes
```

---

## 💻 Development

### Menjalankan Development Server

```bash
# Terminal 1: Laravel
php artisan serve

# Terminal 2: Vite (hot reload)
npm run dev
```

### Build untuk Production

```bash
npm run build
```

### Clear Cache

```bash
php artisan route:clear
php artisan view:clear
php artisan config:clear
```

### Database Commands

```bash
# Fresh migration
php artisan migrate:fresh

# Migrate + Seed
php artisan migrate:fresh --seed

# Seed khusus
php artisan db:seed --class=KepalaInstalasiDataSeeder
```

---

## 🐛 Troubleshooting

### Error: Route not found / Ziggy error
```bash
php artisan route:clear
npm run build
```

### Error: Layout tidak muncul
```bash
php artisan view:clear
npm run build
```

### Error: Database connection refused
- Pastikan MySQL berjalan
- Cek kredensial di `.env`
- Pastikan database sudah dibuat

### Halaman blank setelah login
- Buka Developer Tools (F12) → Console
- Lihat error yang muncul
- Biasanya karena route belum ada

---

## 🔒 Keamanan

- ✅ CSRF Protection
- ✅ Password Hashing (bcrypt)
- ✅ SQL Injection Prevention (Eloquent ORM)
- ✅ XSS Protection
- ✅ Role-based Access Control (RBAC)
- ✅ Secure Session Management

---

## 📈 Roadmap

### Version 1.1 (Coming Soon)
- [ ] Notifikasi email
- [ ] Export data (Excel/PDF)
- [ ] File upload langsung
- [ ] History perubahan

### Version 1.2
- [ ] Dashboard analytics lanjutan
- [ ] Sistem komentar
- [ ] Audit trail
- [ ] API untuk integrasi

### Version 2.0
- [ ] Mobile app
- [ ] Real-time notifications
- [ ] Budget tracking
- [ ] E-signature

---

## 📞 Support

Untuk bantuan atau pertanyaan:
- **Email:** it@rsudibsinugresik.id
- **Website:** https://rsudibsinugresik.id

---

## 📄 License

Proprietary - RSUD Ibnu Sina Kabupaten Gresik

---

<p align="center">
  <strong>Developed with ❤️ for RSUD Ibnu Sina Kabupaten Gresik</strong><br>
  <em>Last Updated: October 16, 2025</em>
</p>


### ✨ Fitur Utama

- 🔐 **Autentikasi & Autorisasi** - Sistem login yang aman dengan role-based access
- 📊 **Dashboard Interaktif** - Statistik real-time permintaan pengadaan
- 📝 **Manajemen Permintaan** - CRUD lengkap untuk permintaan pengadaan
- 🏥 **Multi-Unit Support** - Mendukung 24+ unit/instalasi RS
- 📱 **Responsive Design** - Dapat diakses dari desktop, tablet, dan smartphone
- 🔍 **Filter & Search** - Pencarian dan filter berdasarkan status, bidang, dll
- 👤 **Profil Management** - User dapat mengelola profil sendiri
- 📎 **Document Link** - Integrasi dengan Google Drive untuk scan dokumen
- 🎨 **Modern UI/UX** - Antarmuka yang clean dan user-friendly

### 🏗️ Tech Stack

**Backend:**
- Laravel 12.0 - PHP Framework
- Inertia.js 2.0 - Modern monolith approach
- SQLite/MySQL/PostgreSQL - Database

**Frontend:**
- Vue.js 3.5 - Progressive JavaScript Framework
- Tailwind CSS 3.2 - Utility-first CSS framework
- Vite 7.0 - Frontend build tool

**Development:**
- Laravel Breeze - Authentication scaffolding
- Laravel Pail - Log viewer
- Laravel Pint - Code style fixer

---

## 🚀 Quick Start

### Prerequisites

- PHP 8.2 atau lebih tinggi
- Composer 2.0+
- Node.js 18.x+ & NPM 9.x+
- Database (SQLite/MySQL/PostgreSQL)

### Instalasi Cepat

```bash
# Clone repository
git clone https://github.com/your-repo/pengadaan-app.git
cd pengadaan-app

# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Setup database
touch database/database.sqlite  # untuk SQLite
php artisan migrate

# Seed data (optional)
php artisan db:seed

# Build assets
npm run build

# Jalankan aplikasi
php artisan serve
```

Buka browser dan akses: **http://localhost:8000**

---

## 📚 Dokumentasi

Dokumentasi lengkap tersedia dalam file-file berikut:

### 📖 Dokumentasi Utama

| Dokumentasi | Deskripsi | Link |
|------------|-----------|------|
| **Instalasi** | Panduan instalasi lengkap untuk development & production | [INSTALASI.md](INSTALASI.md) |
| **Penggunaan** | Panduan penggunaan aplikasi untuk end-user | [PENGGUNAAN.md](PENGGUNAAN.md) |
| **Contoh Data IGD** | Template & contoh pengisian untuk IGD | [CONTOH_DATA_IGD.md](CONTOH_DATA_IGD.md) |
| **Panduan Seed Data** | Cara menggunakan sample data | [README_CONTOH_DATA.md](README_CONTOH_DATA.md) |

### 📂 Struktur Dokumentasi

```
pengadaan-app/
├── README.md                    ← File ini (Overview)
├── INSTALASI.md                 ← Panduan instalasi lengkap
├── PENGGUNAAN.md                ← Panduan penggunaan aplikasi
├── CONTOH_DATA_IGD.md           ← Contoh data Instalasi Gawat Darurat
├── README_CONTOH_DATA.md        ← Panduan seed data
└── CHANGELOG.md                 ← (Coming soon) Riwayat perubahan
```

---

## 🎯 Fitur Detail

### 1. Dashboard
- Statistik total permintaan
- Breakdown per status (Diajukan, Proses, Disetujui)
- Visualisasi data dengan cards yang intuitif

### 2. Manajemen Permintaan

**Create (Buat Permintaan):**
- Form lengkap dengan validasi
- Support untuk 24+ bidang/unit RS
- Upload link scan dokumen (Google Drive integration)

**Read (Lihat Permintaan):**
- Tabel interaktif dengan pagination
- Detail view untuk setiap permintaan
- Filter berdasarkan status dan bidang

**Update (Edit Permintaan):**
- Edit form dengan pre-filled data
- Real-time validation
- History tracking (coming soon)

**Delete (Hapus Permintaan):**
- Soft delete dengan konfirmasi
- Role-based authorization

### 3. Bidang/Unit yang Didukung

- Instalasi Gawat Darurat
- Instalasi Rawat Jalan
- Instalasi Rawat Inap
- Instalasi Bedah Sentral
- Instalasi Intensif Care
- Instalasi Farmasi
- Instalasi Laboratorium Patologi Klinik
- Instalasi Radiologi
- Instalasi Rehabilitasi Medik
- Instalasi Gizi
- Dan 14 bidang lainnya...

---

## 🗂️ Struktur Aplikasi

```
pengadaan-app/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/             # Authentication controllers
│   │   │   ├── PermintaanController.php
│   │   │   └── ProfileController.php
│   │   ├── Middleware/
│   │   └── Requests/
│   ├── Models/
│   │   ├── User.php
│   │   └── Permintaan.php
│   └── Providers/
├── database/
│   ├── migrations/               # Database migrations
│   ├── seeders/                  # Database seeders
│   │   ├── IGDPermintaanSeeder.php
│   │   └── DatabaseSeeder.php
│   └── database.sqlite          # SQLite database
├── resources/
│   ├── js/
│   │   ├── Components/          # Vue components
│   │   ├── Layouts/             # Layout components
│   │   │   ├── AppLayout.vue
│   │   │   ├── AuthenticatedLayout.vue
│   │   │   └── GuestLayout.vue
│   │   ├── Pages/               # Inertia pages
│   │   │   ├── Auth/
│   │   │   ├── Permintaan/
│   │   │   │   ├── Index.vue
│   │   │   │   ├── Create.vue
│   │   │   │   ├── Edit.vue
│   │   │   │   └── Show.vue
│   │   │   └── Dashboard.vue
│   │   └── app.js
│   ├── css/
│   │   └── app.css
│   └── views/
├── routes/
│   ├── web.php                  # Web routes
│   └── auth.php                 # Authentication routes
└── public/
    ├── images/
    │   └── logorsis.png
    └── index.php
```

---

## 🔒 Keamanan

- ✅ CSRF Protection
- ✅ Password Hashing (bcrypt)
- ✅ SQL Injection Prevention (Eloquent ORM)
- ✅ XSS Protection
- ✅ Role-based Access Control
- ✅ HTTPS Support (production)
- ✅ Secure Session Management

---

## 🧪 Testing

```bash
# Run tests
php artisan test

# Run specific test
php artisan test --filter PermintaanTest

# With coverage
php artisan test --coverage
```

---

## 🛠️ Development

### Menjalankan Development Server

```bash
# Terminal 1: Laravel development server
php artisan serve

# Terminal 2: Vite development server (with hot reload)
npm run dev
```

### Code Style

```bash
# Check code style
./vendor/bin/pint --test

# Fix code style
./vendor/bin/pint
```

### Database Management

```bash
# Fresh migration
php artisan migrate:fresh

# Migrate with seed
php artisan migrate:fresh --seed

# Seed specific seeder
php artisan db:seed --class=IGDPermintaanSeeder
```

---

## 📈 Roadmap

### Version 1.1 (Q4 2025)
- [ ] Notifikasi email
- [ ] Export data ke Excel/PDF
- [ ] Advanced search & filtering
- [ ] Approval workflow multi-level
- [ ] File upload langsung (tanpa Google Drive)

### Version 1.2 (Q1 2026)
- [ ] Dashboard analytics lanjutan
- [ ] Reporting & visualization
- [ ] Sistem komentar/catatan
- [ ] Audit trail lengkap
- [ ] API untuk integrasi eksternal

### Version 2.0 (Q2 2026)
- [ ] Mobile app (Android/iOS)
- [ ] Real-time notifications (WebSocket)
- [ ] Budget tracking
- [ ] Vendor management
- [ ] E-signature integration

---

## 🤝 Contributing

Kontribusi sangat dihargai! Silakan buat pull request atau laporkan bug melalui Issues.

### Guidelines
1. Fork repository
2. Buat branch baru (`git checkout -b feature/AmazingFeature`)
3. Commit perubahan (`git commit -m 'Add some AmazingFeature'`)
4. Push ke branch (`git push origin feature/AmazingFeature`)
5. Buat Pull Request

---

## 📄 License

Aplikasi ini dikembangkan untuk RSUD Ibnu Sina Kabupaten Gresik.

Powered by [Laravel](https://laravel.com)

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
