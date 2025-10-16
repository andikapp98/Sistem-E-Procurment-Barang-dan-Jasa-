# 📚 Panduan Penggunaan - Sistem e-Procurement RSUD Ibnu Sina

<p align="center">
  <img src="public/images/logorsis.png" alt="RSUD Ibnu Sina" width="200"/>
</p>

<p align="center">
  <strong>Sistem e-Procurement</strong><br>
  Panduan Lengkap Penggunaan Aplikasi
</p>

---

## 📋 Daftar Isi

- [Login dan Autentikasi](#-login-dan-autentikasi)
- [Dashboard](#-dashboard)
- [Manajemen Permintaan](#-manajemen-permintaan)
- [Profil Pengguna](#-profil-pengguna)
- [FAQ](#-faq)
- [Tips & Trik](#-tips--trik)

---

## 🔐 Login dan Autentikasi

### Mengakses Aplikasi

1. Buka browser (Chrome, Firefox, Edge, atau Safari)
2. Ketik URL aplikasi: `http://localhost:8000` (development) atau `https://eprocurement.rsudibsnugresik.id` (production)
3. Anda akan diarahkan ke halaman login

### Login ke Sistem

1. **Halaman Login** akan menampilkan:
   - Logo RSUD Ibnu Sina Kabupaten Gresik
   - Tulisan "Sistem e-Procurement"
   - Form login (Email & Password)

2. **Masukkan Kredensial:**
   - **Email:** Alamat email yang terdaftar
   - **Password:** Kata sandi Anda
   - **Remember Me:** Centang jika ingin tetap login (opsional)

3. **Klik tombol "Log in"**

4. Jika berhasil, Anda akan diarahkan ke **Dashboard**

### Lupa Password

1. Klik link **"Forgot your password?"** di halaman login
2. Masukkan email yang terdaftar
3. Klik **"Email Password Reset Link"**
4. Cek email Anda untuk link reset password
5. Klik link di email
6. Masukkan password baru
7. Konfirmasi password baru
8. Klik **"Reset Password"**

### Logout

1. Klik nama Anda di pojok kanan atas
2. Pilih **"Log Out"** dari dropdown menu
3. Anda akan diarahkan kembali ke halaman login

---

## 📊 Dashboard

### Tampilan Dashboard

Dashboard menampilkan ringkasan data permintaan:

#### 📈 Statistik Cards

1. **Total Permintaan**
   - Menampilkan jumlah total semua permintaan
   - Icon: 📄 Dokumen

2. **Permintaan Diajukan**
   - Menampilkan permintaan dengan status "diajukan"
   - Icon: ⏰ Jam (berwarna kuning)

3. **Permintaan Proses**
   - Menampilkan permintaan yang sedang diproses
   - Icon: 🔄 Refresh (berwarna biru)

4. **Permintaan Disetujui**
   - Menampilkan permintaan yang sudah disetujui
   - Icon: ✅ Checklist (berwarna hijau)

#### 🎯 Welcome Message

Menampilkan pesan selamat datang dan deskripsi singkat aplikasi.

### Navigasi

**Sidebar Menu (Desktop):**
- 🏠 Dashboard
- 📝 Permintaan

**Mobile Menu:**
- Klik ikon hamburger (☰) di pojok kiri atas
- Menu akan muncul dari samping
- Pilih menu yang diinginkan

---

## 📝 Manajemen Permintaan

### Melihat Daftar Permintaan

1. Klik menu **"Permintaan"** di sidebar
2. Tabel akan menampilkan semua permintaan dengan kolom:
   - **ID:** Nomor unik permintaan
   - **Tanggal:** Tanggal permintaan dibuat
   - **Bidang:** Unit/Instalasi yang mengajukan
   - **User:** Nama pengguna yang mengajukan
   - **PIC Pimpinan:** Penanggung jawab
   - **No Nota Dinas:** Nomor referensi dokumen
   - **Deskripsi:** Detail permintaan
   - **Status:** Status permintaan (badge berwarna)
   - **Link Scan:** Link ke dokumen scan
   - **Aksi:** Tombol untuk View, Edit, Delete

#### Status Badge:
- 🟡 **Diajukan:** Kuning - Permintaan baru
- 🔵 **Proses:** Biru - Sedang diverifikasi
- 🟢 **Disetujui:** Hijau - Sudah disetujui

### Membuat Permintaan Baru

#### Langkah-langkah:

1. **Klik tombol "+ Buat Permintaan"** (pojok kanan atas atau bawah tabel)

2. **Isi Form Permintaan:**

   **a) Bidang** (Dropdown - Wajib)
   - Pilih unit/instalasi Anda dari dropdown
   - Contoh: Instalasi Gawat Darurat, Instalasi Farmasi, dll.

   **b) Deskripsi** (Textarea - Wajib)
   - Jelaskan detail permintaan dengan lengkap
   - Sertakan:
     - Nama barang/jasa yang diminta
     - Spesifikasi detail
     - Jumlah yang dibutuhkan
     - Alasan/justifikasi permintaan
     - Tingkat urgensi
   
   **Contoh:**
   ```
   Permintaan pengadaan alat kesehatan untuk ruang resusitasi IGD:
   1. Defibrillator portable 1 unit
   2. Oksigen konsentrator 2 unit
   3. Suction pump portable 3 unit
   
   Alat-alat di atas sangat mendesak mengingat peningkatan 
   kasus emergency dan kondisi beberapa alat existing yang 
   sudah tidak layak pakai.
   ```

   **c) Tanggal Permintaan** (Date Picker - Wajib)
   - Pilih tanggal permintaan dibuat
   - Format: YYYY-MM-DD

   **d) PIC Pimpinan** (Text Input - Wajib)
   - Masukkan nama lengkap pimpinan dengan gelar
   - Contoh: "Dr. Siti Nurhaliza, Sp.EM"

   **e) No Nota Dinas** (Text Input - Wajib)
   - Format: ND/[UNIT]/[TAHUN]/[NOMOR]/[BULAN]
   - Contoh: "ND/IGD/2025/001/X"

   **f) Link Scan Dokumen** (URL Input - Opsional)
   - Upload scan nota dinas ke Google Drive atau sistem dokumen
   - Copy paste link share-nya
   - Contoh: "https://drive.google.com/file/d/..."

   **g) Status** (Dropdown - Wajib)
   - Pilih status:
     - **Diajukan:** Untuk permintaan baru
     - **Proses:** Sedang diproses
     - **Disetujui:** Sudah disetujui

3. **Klik tombol "Buat"** untuk menyimpan

4. **Hasil:**
   - Jika berhasil: Notifikasi hijau muncul
   - Redirect ke halaman Daftar Permintaan
   - Permintaan baru muncul di tabel

### Melihat Detail Permintaan

1. Klik **ID Permintaan** (link biru) di tabel
   ATAU
   Klik tombol **👁️ View**

2. **Halaman Detail** menampilkan:
   - ID Permintaan
   - Nama User yang mengajukan
   - Bidang
   - Tanggal Permintaan
   - PIC Pimpinan
   - No Nota Dinas
   - Deskripsi lengkap
   - Status (badge berwarna)
   - Link Scan Dokumen (jika ada)

3. **Tombol Aksi:**
   - **Edit:** Untuk mengubah permintaan
   - **Hapus:** Untuk menghapus permintaan

### Mengedit Permintaan

1. **Dari halaman Daftar Permintaan:**
   - Klik tombol **✏️ Edit**
   
   **ATAU dari halaman Detail:**
   - Klik link **"Edit"**

2. **Form Edit** akan muncul dengan data yang sudah terisi

3. **Ubah field** yang ingin diubah

4. **Klik tombol "Simpan"**

5. **Hasil:**
   - Notifikasi sukses muncul
   - Data permintaan terupdate
   - Redirect ke halaman Daftar Permintaan

### Menghapus Permintaan

⚠️ **Perhatian:** Aksi ini tidak dapat dibatalkan!

1. **Dari halaman Daftar Permintaan:**
   - Klik tombol **🗑️ Hapus**
   
   **ATAU dari halaman Detail:**
   - Klik link **"Hapus"**

2. **Konfirmasi** akan muncul:
   - "Yakin ingin menghapus permintaan #[ID]?"

3. **Klik "OK"** untuk konfirmasi
   ATAU **"Cancel"** untuk membatalkan

4. **Hasil jika OK:**
   - Permintaan dihapus dari database
   - Notifikasi sukses muncul
   - Tabel diupdate

---

## 👤 Profil Pengguna

### Melihat & Mengubah Profil

1. **Klik nama Anda** di pojok kanan atas
2. Pilih **"Profile"** dari dropdown

3. **Halaman Profile** menampilkan:

   **a) Profile Information**
   - Name (Nama lengkap)
   - Email
   - Tombol "Save" untuk update

   **b) Update Password**
   - Current Password (Password saat ini)
   - New Password (Password baru)
   - Confirm Password (Konfirmasi password baru)
   - Tombol "Save" untuk update

   **c) Delete Account** (Jika diaktifkan)
   - Opsi untuk menghapus akun
   - Memerlukan konfirmasi password

### Mengubah Nama & Email

1. Edit field **Name** atau **Email**
2. Klik **"Save"**
3. Notifikasi "Saved." akan muncul jika berhasil

### Mengubah Password

1. Masukkan **Current Password** (password lama)
2. Masukkan **New Password** (password baru)
3. Masukkan **Confirm Password** (ulangi password baru)
4. Klik **"Save"**
5. Notifikasi "Saved." akan muncul jika berhasil

**Syarat Password:**
- Minimal 8 karakter
- Harus sama antara New Password dan Confirm Password

---

## ❓ FAQ (Frequently Asked Questions)

### 1. Siapa yang bisa mengakses sistem ini?

Hanya pengguna yang sudah terdaftar dan memiliki akun di sistem.

### 2. Bagaimana cara mendapatkan akun?

Hubungi administrator IT RSUD untuk registrasi akun baru.

### 3. Berapa lama permintaan diproses?

Tergantung pada jenis permintaan dan ketersediaan approval dari pimpinan. Pantau status permintaan Anda di menu Permintaan.

### 4. Apakah bisa mengedit permintaan yang sudah disetujui?

Tergantung pada hak akses Anda. Admin RS biasanya memiliki akses penuh, sedangkan user biasa hanya bisa mengedit permintaan milik sendiri dengan status tertentu.

### 5. Format file untuk scan dokumen apa saja yang didukung?

Upload ke Google Drive atau sistem dokumen RS terlebih dahulu, lalu copy paste link share-nya. Format yang umum: PDF, JPG, PNG.

### 6. Apa perbedaan status "Diajukan", "Proses", dan "Disetujui"?

- **Diajukan:** Permintaan baru yang baru saja dibuat
- **Proses:** Sedang dalam tahap verifikasi/approval
- **Disetujui:** Sudah mendapat persetujuan dan siap untuk diproses pengadaan

### 7. Bisa melihat permintaan dari bidang lain?

Ya, semua user bisa melihat permintaan dari semua bidang untuk transparansi.

### 8. Apakah ada notifikasi email?

Fitur ini tergantung konfigurasi sistem. Hubungi admin untuk info lebih lanjut.

### 9. Bagaimana jika lupa password?

Gunakan fitur "Forgot your password?" di halaman login.

### 10. Apakah bisa akses dari HP?

Ya, aplikasi responsive dan bisa diakses dari smartphone atau tablet.

---

## 💡 Tips & Trik

### 📝 Tips Mengisi Deskripsi Permintaan

**DO (Lakukan):**
✅ Tulis detail dan spesifik
✅ Gunakan penomoran untuk list item
✅ Sertakan justifikasi/alasan
✅ Cantumkan tingkat urgensi
✅ Gunakan format yang rapi

**DON'T (Jangan):**
❌ Terlalu singkat dan tidak jelas
❌ Tanpa penjelasan alasan
❌ Typo dan salah ketik
❌ Format yang berantakan

**Contoh BAIK:**
```
Permintaan pengadaan APD untuk IGD periode November 2025:

KEBUTUHAN:
1. Masker N95 @ 500 pcs
2. Face shield @ 100 pcs
3. Sarung tangan steril ukuran M @ 1000 pasang

JUSTIFIKASI:
APD sangat diperlukan untuk melindungi petugas kesehatan 
dalam menangani pasien dengan penyakit menular. Stok 
saat ini hampir habis dan perlu segera diisi ulang.

URGENSI: Tinggi - Stok tersisa untuk 2 minggu
```

**Contoh BURUK:**
```
butuh masker
```

### 📅 Tips Pengisian Tanggal

- Gunakan tanggal saat permintaan dibuat, bukan tanggal kebutuhan
- Jika permintaan mendesak, cantumkan di deskripsi
- Format: YYYY-MM-DD (otomatis dari date picker)

### 🔗 Tips Upload Dokumen

1. **Upload ke Google Drive:**
   - Buka Google Drive
   - Upload file scan nota dinas
   - Klik kanan file → Share → Copy link
   - Paste link ke field "Link Scan Dokumen"

2. **Pastikan Link Accessible:**
   - Set permission "Anyone with the link can view"
   - Test link sebelum submit

### 🎨 Tips Navigasi

**Keyboard Shortcuts (di beberapa browser):**
- `Tab` - Pindah ke field berikutnya
- `Shift + Tab` - Kembali ke field sebelumnya
- `Enter` - Submit form (di field input)
- `Esc` - Close modal/dropdown

**Mouse Shortcuts:**
- Klik nama di navbar untuk quick access ke Profile/Logout
- Klik ID di tabel untuk quick view detail

### 🔍 Tips Pencarian (Jika Tersedia)

- Gunakan filter status untuk mempercepat pencarian
- Bookmark halaman yang sering diakses
- Gunakan search di browser (Ctrl+F / Cmd+F)

### 📱 Tips Mobile

- Rotate HP ke landscape untuk tabel yang lebih lebar
- Gunakan hamburger menu (☰) untuk akses menu
- Zoom out jika tabel terlalu besar

### 🔒 Tips Keamanan

- **Jangan share password** dengan siapapun
- **Logout** setelah selesai menggunakan, terutama di komputer bersama
- **Gunakan password yang kuat:**
  - Minimal 8 karakter
  - Kombinasi huruf besar, kecil, angka, simbol
  - Jangan gunakan password yang sama dengan akun lain
- **Update password** secara berkala (3-6 bulan sekali)

### ⚡ Tips Performa

- Clear browser cache jika aplikasi terasa lambat
- Gunakan browser modern (Chrome, Firefox, Edge)
- Update browser ke versi terbaru
- Tutup tab yang tidak terpakai

---

## 📞 Bantuan & Support

### Kontak Support

**Tim IT RSUD Ibnu Sina Kabupaten Gresik**
- 📧 Email: it@rsudibsnugresik.id
- 📞 Telepon: (031) xxxx-xxxx
- 🏥 Lokasi: Ruang IT, RSUD Ibnu Sina

### Jam Operasional Support

- Senin - Jumat: 08:00 - 16:00 WIB
- Sabtu: 08:00 - 12:00 WIB
- Minggu & Libur: Off

### Pelaporan Bug/Error

Jika menemukan bug atau error:
1. Screenshot halaman error
2. Catat langkah-langkah yang menyebabkan error
3. Email ke tim IT dengan detail:
   - Screenshot
   - Waktu kejadian
   - Browser yang digunakan
   - Akun yang digunakan
   - Langkah reproduksi

---

## 📖 Dokumentasi Terkait

- [Panduan Instalasi](INSTALASI.md) - Untuk developer/administrator
- [Contoh Data IGD](CONTOH_DATA_IGD.md) - Template pengisian
- [README Contoh Data](README_CONTOH_DATA.md) - Panduan seed data

---

## 🔄 Update Log

**Versi 1.0 - Oktober 2025**
- ✅ Fitur Login/Logout
- ✅ Dashboard dengan statistik
- ✅ CRUD Permintaan (Create, Read, Update, Delete)
- ✅ Manajemen Profil User
- ✅ Filter berdasarkan status
- ✅ Responsive design untuk mobile

**Coming Soon (Versi 1.1):**
- 🔔 Notifikasi email
- 📊 Export ke Excel/PDF
- 🔍 Advanced search & filter
- 📈 Laporan & Analytics
- 📎 Upload file attachment langsung
- 💬 Sistem komentar/approval

---

**Versi Dokumentasi:** 1.0  
**Terakhir Diperbarui:** 16 Oktober 2025  
**Untuk:** Sistem e-Procurement RSUD Ibnu Sina Kabupaten Gresik

---

<p align="center">
  <strong>Terima kasih telah menggunakan Sistem e-Procurement RSUD Ibnu Sina</strong><br>
  Untuk pelayanan kesehatan yang lebih baik
</p>
