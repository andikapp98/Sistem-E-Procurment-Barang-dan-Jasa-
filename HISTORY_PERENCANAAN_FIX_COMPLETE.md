# Fix History Perencanaan - Complete

## Tanggal: 2025-11-05

## Summary Perbaikan

Telah dilakukan perbaikan lengkap untuk sistem history/logging aktivitas Staff Perencanaan dengan menyesuaikan struktur log ke tabel `user_activity_logs` yang sebenarnya.

---

## 1. Perbaikan Struktur UserActivityLog

### ❌ Struktur LAMA (Salah):
```php
UserActivityLog::create([
    'user_id' => $user->id,
    'activity_type' => 'create_dpp',  // ❌ Kolom tidak ada
    'description' => '...',
    'related_model' => 'Perencanaan',  // ❌ Kolom tidak ada
    'related_id' => $id,
    'ip_address' => request()->ip(),
    'user_agent' => request()->userAgent(),
]);
```

### ✅ Struktur BARU (Benar):
```php
UserActivityLog::create([
    'user_id' => $user->id,
    'role' => $user->role ?? 'Staff Perencanaan',  // ✅ Required
    'action' => 'create',                           // ✅ Required
    'module' => 'perencanaan',                      // ✅ Required
    'description' => '...',
    'related_type' => 'Perencanaan',                // ✅ Benar (bukan related_model)
    'related_id' => $id,
    'ip_address' => request()->ip(),
    'user_agent' => request()->userAgent(),
]);
```

---

## 2. Kolom-kolom di Tabel `user_activity_logs`

| Kolom | Type | Nullable | Keterangan |
|-------|------|----------|------------|
| `id` | bigint | NO | Primary key |
| `user_id` | bigint | YES | FK ke users |
| `role` | varchar(50) | NO | Role user (REQUIRED) |
| `action` | varchar(100) | NO | login, create, update, delete, approve, dll (REQUIRED) |
| `module` | varchar(50) | NO | permintaan, perencanaan, hps, dll (REQUIRED) |
| `description` | varchar(255) | YES | Deskripsi detail |
| `url` | varchar(255) | YES | URL yang diakses |
| `method` | varchar(10) | YES | GET, POST, PUT, DELETE |
| `ip_address` | varchar(45) | YES | IP Address |
| `user_agent` | text | YES | Browser/Device info |
| `request_data` | json | YES | Data request (filtered) |
| `response_data` | json | YES | Response data (optional) |
| `status_code` | int | YES | HTTP status code |
| `related_id` | bigint | YES | ID dari record terkait |
| `related_type` | varchar(255) | YES | Type dari record (Permintaan, Perencanaan, Hps, dll) |
| `duration` | decimal(8,2) | YES | Durasi eksekusi dalam detik |
| `created_at` | timestamp | YES | Auto timestamp |
| `updated_at` | timestamp | YES | Auto timestamp |

---

## 3. Aktivitas yang Tercatat

### ✅ Aktivitas Staff Perencanaan yang Ter-log:

1. **Create DPP (Dokumen Persiapan Pengadaan)**
   - `action`: create
   - `module`: perencanaan
   - `related_type`: Perencanaan
   - Description: "Membuat DPP untuk permintaan #X: [nama_paket]"

2. **Update DPP**
   - `action`: update
   - `module`: perencanaan
   - `related_type`: Perencanaan
   - Description: "Mengupdate DPP untuk permintaan #X: [nama_paket]"

3. **Create HPS (Harga Perkiraan Satuan)**
   - `action`: create
   - `module`: hps
   - `related_type`: Hps
   - Description: "Membuat HPS untuk permintaan #X dengan N item, total: Rp [total]"

4. **Update HPS**
   - `action`: update
   - `module`: hps
   - `related_type`: Hps
   - Description: "Mengupdate HPS untuk permintaan #X dengan N item"

5. **Create Spesifikasi Teknis**
   - `action`: create
   - `module`: spesifikasi_teknis
   - `related_type`: SpesifikasiTeknis
   - Description: "Membuat Spesifikasi Teknis untuk permintaan #X: [jenis_barang_jasa]"

6. **Update Spesifikasi Teknis**
   - `action`: update
   - `module`: spesifikasi_teknis
   - `related_type`: SpesifikasiTeknis
   - Description: "Mengupdate Spesifikasi Teknis untuk permintaan #X: [jenis_barang_jasa]"

7. **Forward to Pengadaan**
   - `action`: forward
   - `module`: permintaan
   - `related_type`: Permintaan
   - Description: "Mengirim permintaan #X ke Bagian Pengadaan dengan semua dokumen lengkap (Nota Dinas, DPP, HPS, Nota Dinas Pembelian, Spesifikasi Teknis)"

---

## 4. Routes untuk History

### Route baru yang ditambahkan:
```php
Route::get('/permintaan/{permintaan}/history', [StaffPerencanaanController::class, 'showHistory'])
    ->name('staff-perencanaan.history');
```

**URL**: `/staff-perencanaan/permintaan/{id}/history`

---

## 5. Method di Controller

### `show()` Method
Sudah include `activityHistory` yang akan di-pass ke Inertia component:

```php
'activityHistory' => $activityHistory,
```

### `showHistory()` Method (Baru)
Method khusus untuk menampilkan halaman history dengan semua aktivitas:

```php
public function showHistory(Permintaan $permintaan)
{
    // Get comprehensive activity history
    $activityHistory = UserActivityLog::where(function($query) use (...) {
        // Query untuk Permintaan
        $query->where('related_type', 'Permintaan')
              ->where('related_id', $permintaan->permintaan_id);
        
        // Query untuk Perencanaan (DPP)
        if ($perencanaan) {
            $query->orWhere(function($q) use ($perencanaan) {
                $q->where('related_type', 'Perencanaan')
                  ->where('related_id', $perencanaan->perencanaan_id);
            });
        }
        
        // Query untuk HPS
        if ($hps) {
            $query->orWhere(function($q) use ($hps) {
                $q->where('related_type', 'Hps')
                  ->where('related_id', $hps->hps_id);
            });
        }
        
        // Query untuk Spesifikasi Teknis
        if ($spesifikasiTeknis) {
            $query->orWhere(function($q) use ($spesifikasiTeknis) {
                $q->where('related_type', 'SpesifikasiTeknis')
                  ->where('related_id', $spesifikasiTeknis->spesifikasi_id);
            });
        }
    })
    ->with('user')
    ->orderBy('created_at', 'desc')
    ->get();
    
    return Inertia::render('StaffPerencanaan/History', [
        'permintaan' => $permintaan,
        'activityHistory' => $activityHistory,
        'userLogin' => $user,
    ]);
}
```

---

## 6. Perbaikan di Seeder

### CompleteStaffPerencanaanSeeder.php

#### Fix #1: `jangka_waktu_pelaksanaan`
**BEFORE**: `'30 hari kalender'` (string) ❌  
**AFTER**: `30` (integer) ✅

Kolom `jangka_waktu_pelaksanaan` adalah `integer` yang menyimpan jumlah hari.

#### Fix #2: `metode_pengadaan`
**BEFORE**: `'E-Catalog'` ❌  
**AFTER**: `'E-Purchasing'` ✅

Valid enum values:
- `'E-Purchasing'`
- `'Tender'`
- `'Penunjukan Langsung'`
- `'Lelang'`

#### Fix #3: HPS Table Structure
**BEFORE**:
```php
Hps::create([
    'metode_pengadaan' => 'E-Purchasing',  // ❌ Kolom tidak ada
    'total_hps' => 82000000,                // ❌ Kolom tidak ada
    'keterangan' => '...',                  // ❌ Kolom tidak ada
]);
```

**AFTER**:
```php
Hps::create([
    'ppk' => 'Dr. Ahmad Yani, Sp.PD',                // ✅
    'surat_penawaran_harga' => 'SPH/IGD/2025/001',   // ✅
    'grand_total' => 82000000,                       // ✅
]);
```

#### Fix #4: HPS Items Structure
**BEFORE**:
```php
HpsItem::create([
    'spesifikasi' => '...',  // ❌ Kolom tidak ada
    'kuantitas' => 2,        // ❌ Kolom tidak ada
    'total_harga' => 80000000, // ❌ Kolom tidak ada
]);
```

**AFTER**:
```php
HpsItem::create([
    'volume' => 2,           // ✅ Benar (bukan kuantitas)
    'total' => 80000000,     // ✅ Benar (bukan total_harga)
    'type' => 'Alat Medis',  // ✅ Ada
    'merk' => 'Philips',     // ✅ Ada
]);
```

#### Fix #5: Spesifikasi Teknis Structure
**BEFORE**:
```php
SpesifikasiTeknis::create([
    'nama_barang' => '...',   // ❌ Kolom tidak ada
    'spesifikasi' => '...',   // ❌ Kolom tidak ada
    'kuantitas' => 2,         // ❌ Kolom tidak ada
    'satuan' => 'Unit',       // ❌ Kolom tidak ada
]);
```

**AFTER**:
```php
SpesifikasiTeknis::create([
    // Section 1: Latar Belakang & Tujuan
    'latar_belakang' => '...',      // ✅
    'maksud_tujuan' => '...',       // ✅
    'target_sasaran' => '...',      // ✅
    
    // Section 2: Pejabat & Anggaran
    'pejabat_pengadaan' => '...',   // ✅
    'sumber_dana' => '...',         // ✅
    'perkiraan_biaya' => '...',     // ✅
    
    // Section 3: Detail Barang/Jasa
    'jenis_barang_jasa' => '...',   // ✅
    'fungsi_manfaat' => '...',      // ✅
    'kegiatan_rutin' => 'Ya',       // ✅
    
    // Section 4: Waktu & Tenaga
    'jangka_waktu' => '30 hari kalender',  // ✅
    'estimasi_waktu_datang' => '...',      // ✅
    'tenaga_diperlukan' => '...',          // ✅
    
    // Section 5: Pelaku Usaha & Konsolidasi
    'pelaku_usaha' => '...',                  // ✅
    'pengadaan_sejenis' => 'Ya',              // ✅
    'pengadaan_sejenis_keterangan' => '...',  // ✅
    'indikasi_konsolidasi' => 'Tidak',        // ✅
    'indikasi_konsolidasi_keterangan' => null, // ✅
]);
```

---

## 7. Testing

### Cara Test History:

1. **Login sebagai Staff Perencanaan**
   - Email: `perencanaan@rsud.id`
   - Password: `password`

2. **Buat DPP untuk permintaan**
   - Akses: `/staff-perencanaan/permintaan/{id}/dpp/create`
   - Submit form DPP
   - ✅ Log akan tercatat dengan action=create, module=perencanaan

3. **Buat HPS**
   - Akses: `/staff-perencanaan/permintaan/{id}/hps/create`
   - Submit form HPS
   - ✅ Log akan tercatat dengan action=create, module=hps

4. **Buat Spesifikasi Teknis**
   - Akses: `/staff-perencanaan/permintaan/{id}/spesifikasi-teknis/create`
   - Submit form
   - ✅ Log akan tercatat dengan action=create, module=spesifikasi_teknis

5. **Forward ke Pengadaan**
   - Klik tombol "Kirim ke Bagian Pengadaan"
   - ✅ Log akan tercatat dengan action=forward, module=permintaan

6. **Lihat History**
   - Akses: `/staff-perencanaan/permintaan/{id}`
   - Scroll ke section "Activity History"
   - Atau akses: `/staff-perencanaan/permintaan/{id}/history`
   - ✅ Semua aktivitas akan tampil dengan detail lengkap

---

## 8. Query untuk Verifikasi

```sql
-- Lihat semua log aktivitas untuk permintaan tertentu
SELECT 
    ual.id,
    ual.role,
    ual.action,
    ual.module,
    ual.description,
    ual.related_type,
    ual.related_id,
    u.nama as user_name,
    ual.created_at
FROM user_activity_logs ual
LEFT JOIN users u ON ual.user_id = u.id
WHERE ual.related_type = 'Permintaan' 
  AND ual.related_id = 16
ORDER BY ual.created_at DESC;

-- Lihat semua log untuk perencanaan
SELECT * FROM user_activity_logs 
WHERE module = 'perencanaan' 
ORDER BY created_at DESC;

-- Lihat semua log untuk HPS
SELECT * FROM user_activity_logs 
WHERE module = 'hps' 
ORDER BY created_at DESC;
```

---

## 9. Frontend Component (Info)

Frontend component perlu menampilkan `activityHistory` dengan struktur:

```javascript
// Props dari backend
const { activityHistory } = props;

// Format data
activityHistory.forEach(log => {
    console.log({
        user: log.user?.nama,
        role: log.role,
        action: log.action,        // create, update, forward, delete
        module: log.module,        // perencanaan, hps, spesifikasi_teknis
        description: log.description,
        related_type: log.related_type,
        related_id: log.related_id,
        created_at: log.created_at,
        ip_address: log.ip_address,
    });
});
```

---

## ✅ STATUS: COMPLETE

Semua perbaikan telah selesai:
- [x] Fix struktur UserActivityLog (related_model → related_type)
- [x] Fix struktur UserActivityLog (activity_type → role, action, module)
- [x] Fix query history di show() method
- [x] Fix query history di showHistory() method
- [x] Fix seeder: jangka_waktu_pelaksanaan (string → integer)
- [x] Fix seeder: metode_pengadaan ('E-Catalog' → 'E-Purchasing')
- [x] Fix seeder: HPS table structure
- [x] Fix seeder: HPS Items structure
- [x] Fix seeder: Spesifikasi Teknis structure
- [x] Add route untuk history page
- [x] Add logging untuk semua operasi create/update
- [x] Add logging untuk forward to pengadaan

**History akan muncul ketika:**
1. Staff Perencanaan membuat DPP
2. Staff Perencanaan membuat/update HPS
3. Staff Perencanaan membuat/update Spesifikasi Teknis
4. Staff Perencanaan mengirim ke Bagian Pengadaan

Semua aktivitas tercatat dengan lengkap untuk audit trail! 🎉
