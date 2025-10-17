# 📝 Changelog

Semua perubahan penting pada project ini akan didokumentasikan di file ini.

Format berdasarkan [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
dan project ini mengikuti [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---

## [1.3.0] - 2025-10-17

### 🔄 Feature - Workflow Approval Bertingkat (Kepala Bidang)

#### Added
- **Kepala Bidang Role** - Level approval ke-2 setelah Kepala Instalasi
- **KepalaBidangController** - Controller lengkap untuk Kepala Bidang
- **Workflow 2-Level** - Admin → Kepala Instalasi → Kepala Bidang → Bagian Terkait
- **Disposisi Management** - Kepala Bidang dapat membuat disposisi

#### Workflow Flow
```
Admin → Buat Permintaan
  ↓
Kepala Instalasi → Approve/Reject
  ↓ (jika approve)
Kepala Bidang → Approve/Reject
  ↓ (jika approve)
Bagian Perencanaan/Pengadaan
```

#### Routes Added
- **GET /kepala-bidang/dashboard** - Dashboard Kepala Bidang
- **GET /kepala-bidang** - List permintaan
- **GET /kepala-bidang/permintaan/{id}** - Detail permintaan
- **POST /kepala-bidang/permintaan/{id}/approve** - Approve & teruskan
- **POST /kepala-bidang/permintaan/{id}/reject** - Reject permintaan
- **POST /kepala-bidang/permintaan/{id}/disposisi** - Buat disposisi
- **POST /kepala-bidang/permintaan/{id}/revisi** - Request revisi

#### Controller Methods
- **dashboard()** - Stats & recent permintaan
- **index()** - List all permintaan untuk Kepala Bidang
- **show()** - Detail permintaan dengan tracking
- **approve()** - Approve & teruskan ke bagian terkait
- **reject()** - Reject & kembalikan ke pemohon
- **createDisposisi()** - Form buat disposisi
- **storeDisposisi()** - Save disposisi
- **requestRevision()** - Request revisi dari pemohon

#### Changed
- **KepalaInstalasiController::approve()** - Sekarang teruskan ke Kepala Bidang (bukan langsung Pengadaan)
- **KepalaInstalasiController::reject()** - Update message lebih jelas
- **AuthenticatedSessionController** - Auto-redirect Kepala Bidang ke dashboardnya

#### Database/Seeder
- **Seeder** - Tambah user Kepala Bidang (kepala_bidang@rsud.id)
- **Test Data** - Permintaan #3 sudah di tahap Kepala Bidang
- **Nota Dinas** - Updated ke_jabatan = 'Kepala Bidang'

### Technical Details

**Authorization:**
```php
// Kepala Instalasi - filter by unit_kerja
->where('bidang', $user->unit_kerja)

// Kepala Bidang - filter by PIC
->where('pic_pimpinan', 'Kepala Bidang')
```

**Approval Flow:**
1. Kepala Instalasi approve → `pic_pimpinan = 'Kepala Bidang'`
2. Kepala Bidang approve → `pic_pimpinan = {tujuan}`, `status = 'disetujui'`

### Documentation
- **WORKFLOW_APPROVAL.md** - Dokumentasi lengkap workflow 2-level
- **SEEDER.md** - Updated dengan akun Kepala Bidang
- **CHANGELOG.md** - Added version 1.3.0

### Testing
- ✅ Routes registered (8 routes)
- ✅ Controller syntax valid
- ✅ Seeder creates Kepala Bidang user
- ✅ Test data: Permintaan #3 at Kepala Bidang level

---

## [1.2.0] - 2025-10-17

### 📊 Feature - Tracking Status Timeline

#### Added
- **Timeline Tracking** - Kepala Instalasi dapat melihat progress permintaan dari awal hingga selesai
- **8 Tahapan E-Procurement** - Tracking lengkap: Permintaan → Nota Dinas → Disposisi → Perencanaan → KSO → Pengadaan → Nota Penerimaan → Serah Terima
- **Progress Percentage** - Perhitungan otomatis progress dalam persentase
- **Tracking Detail Page** - Halaman khusus untuk melihat tracking lengkap
- **Timeline Data** - Array lengkap dengan tanggal, status, keterangan per tahap

#### Methods Added
- **Permintaan::getTimelineTracking()** - Return array timeline lengkap
- **Permintaan::getProgressPercentage()** - Hitung progress 0-100%
- **KepalaInstalasiController::tracking()** - Dedicated tracking page
- **Updated show()** - Menambahkan timeline dan progress ke detail view

#### Routes
- **GET /kepala-instalasi/permintaan/{id}/tracking** - Halaman tracking detail

#### Models Updated
- **Permintaan** - Tambah methods tracking dan update trackingStatus attribute
- Relations sudah lengkap untuk semua 8 tahapan

### Technical Details

**Timeline Structure:**
```php
[
    'tahapan' => string,
    'tanggal' => date,
    'status' => string,
    'keterangan' => string,
    'icon' => string,
    'completed' => boolean
]
```

**Dashboard Enhancement:**
- List permintaan menampilkan progress bar
- Menampilkan tahap saat ini
- Menampilkan jumlah tahap yang sudah dilalui (X/8)

### Documentation
- **TRACKING_STATUS_FEATURE.md** - Dokumentasi lengkap fitur tracking
- Updated CHANGELOG dengan fitur baru

---

## [1.1.0] - 2025-10-17

### 🔒 Security - Data Isolation for Kepala Instalasi

#### Added
- **Data Access Control** - Kepala Instalasi hanya dapat melihat data bagian/unit kerjanya sendiri
- **Authorization Checks** - Validasi otorisasi di semua method controller
- **Cross-Department Prevention** - Mencegah akses data antar bagian yang berbeda
- **403 Forbidden Response** - Error handling untuk akses tidak sah

#### Changed
- **KepalaInstalasiController::dashboard()** - Filter berdasarkan `bidang` permintaan sesuai `unit_kerja` user
- **KepalaInstalasiController::index()** - Filter permintaan hanya untuk bagian sendiri
- **KepalaInstalasiController::show()** - Tambah validasi otorisasi
- **KepalaInstalasiController::createNotaDinas()** - Tambah validasi otorisasi
- **KepalaInstalasiController::storeNotaDinas()** - Tambah validasi otorisasi
- **KepalaInstalasiController::approve()** - Tambah validasi otorisasi
- **KepalaInstalasiController::reject()** - Tambah validasi otorisasi
- **KepalaInstalasiController::requestRevision()** - Tambah validasi otorisasi

#### Fixed
- **KepalaInstalasiDataSeeder** - Perbaiki field `bidang` dari "Farmasi" menjadi "Instalasi Farmasi"
- **Filter Logic** - Ubah dari filter berdasarkan user pembuat ke filter berdasarkan bidang tujuan

#### Documentation
- **KEPALA_INSTALASI_AKSES.md** - Dokumentasi lengkap tentang isolasi data dan kontrol akses
- **TESTING_ISOLASI_DATA.md** - Panduan testing fitur isolasi data
- **PERUBAHAN_ISOLASI_DATA.md** - Ringkasan perubahan implementasi
- **SEEDER.md** - Update dokumentasi seeder dengan data testing isolasi

#### Testing Data
- Tambah user Kepala Instalasi IGD untuk testing isolasi
- Tambah user Staff IGD
- Tambah permintaan IGD (ID 6) yang tidak terlihat oleh Kepala Farmasi
- Total data seeder: 4 users, 6 permintaan (5 Farmasi + 1 IGD)

### Technical Details

**Filter Query:**
```php
->where(function($query) use ($user) {
    if ($user->unit_kerja) {
        $query->where('bidang', $user->unit_kerja);
    }
    $query->orWhere('pic_pimpinan', $user->nama);
})
```

**Authorization Validation:**
```php
if ($user->unit_kerja && 
    $permintaan->bidang !== $user->unit_kerja && 
    $permintaan->pic_pimpinan !== $user->nama) {
    abort(403, 'Anda tidak memiliki akses...');
}
```

### Test Scenarios

**Kepala Instalasi Farmasi:**
- ✅ Dapat melihat 5 permintaan Farmasi
- ❌ TIDAK dapat melihat permintaan IGD
- ❌ Error 403 jika akses URL permintaan IGD

**Kepala Instalasi IGD:**
- ✅ Dapat melihat 1 permintaan IGD
- ❌ TIDAK dapat melihat permintaan Farmasi
- ❌ Error 403 jika akses URL permintaan Farmasi

---

## [1.0.0] - 2025-10-16

### ✨ Added
- **Multi-Role System** - Admin, Kepala Instalasi, dan Unit Kerja
- **Dashboard Kepala Instalasi** dengan statistik real-time
- **Manajemen Permintaan** - CRUD lengkap untuk pengadaan
- **Workflow Approval** - Approve, Reject, Revisi permintaan
- **Nota Dinas** - Pembuatan dan tracking nota dinas
- **Auto-redirect** setelah login berdasarkan role
- **Status Tracking** - Lacak permintaan dari awal hingga selesai
- **Responsive Design** - Mobile-friendly UI

### 🎨 UI/UX
- Layout dengan sidebar navigasi
- Dashboard cards dengan statistik
- Modal konfirmasi untuk aksi penting
- Status badges dengan color-coding
- Clean dan modern design dengan Tailwind CSS

### 📱 Pages
- **Dashboard.vue** - Dashboard umum
- **KepalaInstalasi/Dashboard.vue** - Dashboard khusus
- **KepalaInstalasi/Index.vue** - List permintaan untuk review
- **KepalaInstalasi/Show.vue** - Detail dan aksi permintaan
- **KepalaInstalasi/CreateNotaDinas.vue** - Form nota dinas
- **Permintaan/Index.vue** - Daftar permintaan
- **Permintaan/Create.vue** - Buat permintaan baru
- **Permintaan/Edit.vue** - Edit permintaan
- **Permintaan/Show.vue** - Detail permintaan

### 🗄️ Database
- **users** table - Multi-role user management
- **permintaan** table - Permintaan pengadaan
- **nota_dinas** table - Nota dinas tracking

### 🔧 Backend
- **KepalaInstalasiController** - 8 methods untuk workflow
- **PermintaanController** - CRUD permintaan
- **ProfileController** - User profile management
- **AuthenticatedSessionController** - Auto-redirect by role

### 🌱 Seeders
- **KepalaInstalasiDataSeeder** - Data demo lengkap
- 2 Users (Kepala + Staff)
- 5 Permintaan dengan berbagai status
- 3 Nota Dinas

### 📚 Documentation
- **README.md** - Overview dan quick start
- **INSTALASI.md** - Panduan instalasi lengkap
- **PENGGUNAAN.md** - Panduan penggunaan aplikasi
- **SEEDER.md** - Panduan data demo

### 🔒 Security
- CSRF Protection
- Password Hashing (bcrypt)
- SQL Injection Prevention (Eloquent ORM)
- XSS Protection
- Role-based Access Control

### 🐛 Fixed
- Route error (Ziggy): nota-dinas.index, users.index
- Layout blank issue saat login
- NotaDinas model timestamps error
- Query optimization untuk dashboard
- Tracking status accessor error

### 🗑️ Removed
- 23 file Markdown redundan
- 19 file .bat yang tidak perlu
- Debug console.log dari production build

---

## [Unreleased]

### 🎯 Planned Features

#### Version 1.1 (Coming Soon)
- [ ] Notifikasi email
- [ ] Export data (Excel/PDF)
- [ ] File upload langsung (tanpa Google Drive)
- [ ] History perubahan permintaan
- [ ] Advanced search & filtering

#### Version 1.2
- [ ] Dashboard analytics lanjutan
- [ ] Sistem komentar/catatan
- [ ] Audit trail lengkap
- [ ] API untuk integrasi eksternal
- [ ] Bulk operations

#### Version 2.0
- [ ] Mobile app (Android/iOS)
- [ ] Real-time notifications (WebSocket)
- [ ] Budget tracking
- [ ] Vendor management
- [ ] E-signature integration
- [ ] Multi-language support

---

## 📌 Notes

### Versi Numbering
- **MAJOR** version - Breaking changes
- **MINOR** version - New features (backward compatible)
- **PATCH** version - Bug fixes

### Tipe Perubahan
- **Added** - Fitur baru
- **Changed** - Perubahan pada fitur yang ada
- **Deprecated** - Fitur yang akan dihapus
- **Removed** - Fitur yang dihapus
- **Fixed** - Bug fixes
- **Security** - Perbaikan keamanan

---

<p align="center">
  <strong>Developed for RSUD Ibnu Sina Kabupaten Gresik</strong><br>
  <em>Last Updated: October 16, 2025</em>
</p>
