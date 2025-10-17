# 🔒 Akses Data Kepala Instalasi - Isolasi Berdasarkan Bagian

Dokumentasi tentang implementasi kontrol akses untuk Kepala Instalasi agar hanya dapat melihat dan mengelola data dari bagian/unit kerjanya sendiri.

---

## 📋 Daftar Isi

- [Konsep](#konsep)
- [Implementasi](#implementasi)
- [Cara Kerja Filter](#cara-kerja-filter)
- [Testing](#testing)
- [Contoh Skenario](#contoh-skenario)

---

## 🎯 Konsep

### Prinsip Isolasi Data

Setiap Kepala Instalasi **HANYA** dapat:

1. ✅ Melihat permintaan yang ditujukan untuk bagian/unit kerjanya
2. ✅ Menyetujui/menolak permintaan dari bagiannya sendiri
3. ✅ Membuat nota dinas untuk permintaan bagiannya
4. ❌ **TIDAK DAPAT** melihat permintaan dari bagian lain

### Filter Berdasarkan `bidang`

Filter utama menggunakan kolom:
- **`permintaan.bidang`** - Bagian/unit tujuan permintaan
- **`users.unit_kerja`** - Bagian/unit kerja Kepala Instalasi

**Aturan:** 
```
Kepala Instalasi dapat melihat permintaan HANYA jika:
  permintaan.bidang == user.unit_kerja
  ATAU
  permintaan.pic_pimpinan == user.nama
```

---

## 🛠️ Implementasi

### 1. Filter di Dashboard

**File:** `app/Http/Controllers/KepalaInstalasiController.php`

```php
public function dashboard()
{
    $user = Auth::user();
    
    // Filter berdasarkan bidang yang sesuai dengan unit_kerja
    $permintaans = Permintaan::with(['user', 'notaDinas'])
        ->where(function($query) use ($user) {
            if ($user->unit_kerja) {
                $query->where('bidang', $user->unit_kerja);
            }
            $query->orWhere('pic_pimpinan', $user->nama);
        })
        ->get();
    
    // ... statistik dan return
}
```

### 2. Filter di Index (Daftar Permintaan)

```php
public function index()
{
    $user = Auth::user();
    
    $permintaans = Permintaan::with(['user', 'notaDinas'])
        ->where(function($query) use ($user) {
            if ($user->unit_kerja) {
                $query->where('bidang', $user->unit_kerja);
            }
            $query->orWhere('pic_pimpinan', $user->nama);
        })
        ->orderByDesc('permintaan_id')
        ->get();
    
    // ... mapping dan return
}
```

### 3. Otorisasi di Method Show/Update

Setiap method yang menampilkan atau memodifikasi permintaan memiliki pengecekan otorisasi:

```php
public function show(Permintaan $permintaan)
{
    $user = Auth::user();
    
    // Cek otorisasi
    if ($user->unit_kerja && 
        $permintaan->bidang !== $user->unit_kerja && 
        $permintaan->pic_pimpinan !== $user->nama) {
        abort(403, 'Anda tidak memiliki akses untuk melihat permintaan ini.');
    }
    
    // ... return view
}
```

Method yang dilindungi:
- ✅ `show()` - Melihat detail
- ✅ `createNotaDinas()` - Buat nota dinas
- ✅ `storeNotaDinas()` - Simpan nota dinas
- ✅ `approve()` - Setujui permintaan
- ✅ `reject()` - Tolak permintaan
- ✅ `requestRevision()` - Minta revisi

---

## 🔍 Cara Kerja Filter

### Scenario 1: Kepala Instalasi Farmasi Login

**Data User:**
```
nama: Dr. Siti Rahayu, S.Farm., Apt
unit_kerja: Instalasi Farmasi
role: kepala_instalasi
```

**Permintaan yang TERLIHAT:**
| ID | Bidang | Status | Aksi |
|----|--------|--------|------|
| 1  | Instalasi Farmasi | Diajukan | ✅ Bisa lihat & kelola |
| 2  | Instalasi Farmasi | Diajukan | ✅ Bisa lihat & kelola |
| 3  | Instalasi Farmasi | Proses | ✅ Bisa lihat & kelola |
| 4  | Instalasi Farmasi | Disetujui | ✅ Bisa lihat & kelola |
| 5  | Instalasi Farmasi | Ditolak | ✅ Bisa lihat & kelola |

**Permintaan yang TIDAK TERLIHAT:**
| ID | Bidang | Status | Aksi |
|----|--------|--------|------|
| 6  | Instalasi IGD | Diajukan | ❌ Tidak bisa lihat |
| 7  | Instalasi Radiologi | Diajukan | ❌ Tidak bisa lihat |

### Scenario 2: Kepala Instalasi IGD Login

**Data User:**
```
nama: Dr. Ahmad Fauzi, Sp.EM
unit_kerja: Instalasi IGD
role: kepala_instalasi
```

**Permintaan yang TERLIHAT:**
| ID | Bidang | Status | Aksi |
|----|--------|--------|------|
| 6  | Instalasi IGD | Diajukan | ✅ Bisa lihat & kelola |

**Permintaan yang TIDAK TERLIHAT:**
| ID | Bidang | Status | Aksi |
|----|--------|--------|------|
| 1-5 | Instalasi Farmasi | - | ❌ Tidak bisa lihat |

---

## 🧪 Testing

### 1. Testing Isolasi Data

**Jalankan Seeder:**
```bash
php artisan db:seed --class=KepalaInstalasiDataSeeder
```

Seeder akan membuat:
- 2 Kepala Instalasi (Farmasi & IGD)
- 2 Staff (Farmasi & IGD)  
- 6 Permintaan (5 Farmasi + 1 IGD)

### 2. Test Login Kepala Instalasi Farmasi

**Login:**
```
Email: kepala_instalasi@rsud.id
Password: password123
```

**Hasil yang Diharapkan:**
- Dashboard menampilkan **5 permintaan** (Farmasi saja)
- Statistik: Diajukan: 2, Proses: 1, Disetujui: 1, Ditolak: 1
- **TIDAK** terlihat permintaan IGD

### 3. Test Login Kepala Instalasi IGD

**Login:**
```
Email: kepala_igd@rsud.id
Password: password123
```

**Hasil yang Diharapkan:**
- Dashboard menampilkan **1 permintaan** (IGD saja)
- Statistik: Diajukan: 1
- **TIDAK** terlihat permintaan Farmasi

### 4. Test Akses Langsung ke URL

**Scenario:** Kepala Farmasi mencoba akses permintaan IGD

```
URL: /kepala-instalasi/permintaan/6
(Permintaan ID 6 adalah permintaan IGD)
```

**Hasil yang Diharapkan:**
```
403 Forbidden
"Anda tidak memiliki akses untuk melihat permintaan ini."
```

---

## 📝 Contoh Skenario

### Skenario 1: Admin Kirim Permintaan ke Farmasi

**Flow:**
1. Admin membuat permintaan baru
2. Mengisi field `bidang` = "Instalasi Farmasi"
3. Status = "diajukan"

**Hasil:**
- Kepala Instalasi **Farmasi** ✅ DAPAT melihat
- Kepala Instalasi **IGD** ❌ TIDAK DAPAT melihat
- Kepala Instalasi **Radiologi** ❌ TIDAK DAPAT melihat

### Skenario 2: Kepala Farmasi Setujui Permintaan

**Flow:**
1. Kepala Instalasi Farmasi login
2. Melihat permintaan dengan bidang = "Instalasi Farmasi"
3. Klik tombol "Setujui"
4. Sistem update status → "disetujui"
5. Buat nota dinas otomatis ke "Bagian Pengadaan"

**Validasi:**
- ✅ Hanya bisa setujui jika `bidang` == `unit_kerja` user
- ❌ Error 403 jika coba setujui permintaan bagian lain

### Skenario 3: Kepala Farmasi Coba Akses Data IGD

**Flow:**
1. Kepala Instalasi Farmasi login
2. Mendapat URL permintaan IGD (misal dari share)
3. Akses: `/kepala-instalasi/permintaan/6`

**Hasil:**
```
HTTP 403 Forbidden
Anda tidak memiliki akses untuk melihat permintaan ini.
```

---

## 🔐 Keamanan

### Perlindungan yang Diterapkan

1. **Filter Query Level**
   - Semua query sudah difilter di controller
   - Tidak mungkin fetch data bagian lain

2. **Otorisasi Method Level**  
   - Setiap method show/update/delete ada pengecekan
   - Abort 403 jika tidak berhak

3. **Route Protection**
   - Semua route kepala-instalasi butuh auth
   - Middleware `['auth', 'verified']`

### Best Practice

✅ **DO:**
- Selalu set field `bidang` sesuai unit tujuan
- Pastikan `unit_kerja` user terisi dengan benar
- Filter berdasarkan `bidang`, bukan `user.unit_kerja` pembuat

❌ **DON'T:**
- Jangan filter berdasarkan user pembuat permintaan
- Jangan skip validasi otorisasi
- Jangan hardcode nama bagian/unit

---

## 📊 Database Schema

### Tabel `users`

| Column | Type | Keterangan |
|--------|------|------------|
| user_id | INT | Primary key |
| nama | VARCHAR | Nama lengkap |
| email | VARCHAR | Email (unique) |
| role | VARCHAR | 'kepala_instalasi', 'unit', dll |
| unit_kerja | VARCHAR | **Bagian/Unit kerja** |
| jabatan | VARCHAR | Jabatan |

### Tabel `permintaan`

| Column | Type | Keterangan |
|--------|------|------------|
| permintaan_id | INT | Primary key |
| user_id | INT | User pembuat |
| bidang | VARCHAR | **Bagian/Unit tujuan** |
| status | VARCHAR | Status permintaan |
| pic_pimpinan | VARCHAR | PIC yang menangani |

**KEY POINT:**
- Filter menggunakan `permintaan.bidang` ✅
- Bukan `users.unit_kerja` dari pembuat ❌

---

## 🚀 Implementasi di Bagian Lain

### Untuk Membuat Kontrol Akses Serupa:

1. **Pastikan ada kolom `bidang` di tabel master**
2. **Pastikan user punya `unit_kerja`**
3. **Filter query dengan:**
   ```php
   ->where('bidang', $user->unit_kerja)
   ```
4. **Tambahkan otorisasi di setiap method:**
   ```php
   if ($model->bidang !== $user->unit_kerja) {
       abort(403);
   }
   ```

### Template Controller Method

```php
public function index()
{
    $user = Auth::user();
    
    $data = Model::query()
        ->where(function($query) use ($user) {
            if ($user->unit_kerja) {
                $query->where('bidang', $user->unit_kerja);
            }
            $query->orWhere('pic', $user->nama);
        })
        ->get();
        
    return view('index', compact('data'));
}

public function show(Model $model)
{
    $user = Auth::user();
    
    if ($user->unit_kerja && 
        $model->bidang !== $user->unit_kerja && 
        $model->pic !== $user->nama) {
        abort(403, 'Akses ditolak');
    }
    
    return view('show', compact('model'));
}
```

---

## 📚 Referensi File

**Controller:**
- `app/Http/Controllers/KepalaInstalasiController.php`

**Model:**
- `app/Models/User.php`
- `app/Models/Permintaan.php`

**Seeder:**
- `database/seeders/KepalaInstalasiDataSeeder.php`

**Migration:**
- `2025_10_17_100000_fix_users_table_role.php` (role, unit_kerja)
- `2025_10_16_044500_add_bidang_to_permintaan_table.php` (bidang)

---

## ❓ FAQ

### Q: Bagaimana jika kepala instalasi pindah bagian?

**A:** Update `unit_kerja` di tabel users. Filter otomatis akan berubah sesuai unit baru.

### Q: Bagaimana jika permintaan butuh disetujui 2 kepala instalasi?

**A:** Gunakan field `pic_pimpinan`. Permintaan yang sudah di-assign ke PIC tertentu akan tetap terlihat meski beda bagian.

### Q: Bagaimana cara admin melihat semua permintaan?

**A:** Buat controller terpisah untuk admin tanpa filter `unit_kerja`, atau cek `role == 'admin'` sebelum apply filter.

### Q: Apakah bisa 1 kepala instalasi punya 2 bagian?

**A:** Ya, bisa. Simpan sebagai array JSON di `unit_kerja` atau buat tabel relasi `user_units`. Lalu ubah filter:
```php
->whereIn('bidang', json_decode($user->unit_kerja))
```

---

<p align="center">
  <strong>Implementasi Berhasil! ✅</strong><br>
  <em>Kepala Instalasi kini hanya dapat melihat dan mengelola data bagiannya sendiri</em>
</p>

---

**Last Updated:** October 17, 2025  
**Version:** 1.0.0
