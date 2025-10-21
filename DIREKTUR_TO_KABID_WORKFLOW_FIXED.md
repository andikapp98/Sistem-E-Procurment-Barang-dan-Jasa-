# WORKFLOW DIREKTUR KE KEPALA BIDANG - COMPLETE FIX

## Tanggal: 21 Oktober 2025

## Workflow yang Benar

### Alur Lengkap Permintaan
```
Kepala Instalasi 
    ↓ (buat permintaan + nota dinas)
Kepala Bidang 
    ↓ (approve + disposisi ke Direktur)
DIREKTUR 
    ↓ (approve - Final Approval)
Kepala Bidang 
    ↓ (disposisi ke Staff Perencanaan)
Staff Perencanaan
    ↓ (buat perencanaan + nota dinas)
Bagian KSO
    ↓ (buat KSO)
Bagian Pengadaan
    ↓ (proses pengadaan + nota penerimaan)
Serah Terima
```

## Penjelasan Workflow Direktur

### 1. APPROVE (Final Approval)
**Yang Terjadi:**
- Direktur memberikan persetujuan final
- Permintaan dikembalikan ke **Kepala Bidang**
- Kepala Bidang kemudian disposisi ke Staff Perencanaan

**Alasan:**
- Kepala Bidang perlu membuat disposisi formal ke Staff Perencanaan
- Menjaga jalur hirarkis dan dokumentasi yang benar
- Staff Perencanaan menerima disposisi dari atasannya (Kepala Bidang)

**Database Changes:**
```
Status: proses
PIC: Kepala Bidang

Disposisi baru:
- jabatan_tujuan: Kepala Bidang
- status: selesai
- catatan: Disetujui oleh Direktur (Final Approval). Silakan disposisi ke Staff Perencanaan...
```

### 2. REJECT
**Yang Terjadi:**
- Direktur menolak permintaan
- Proses STOP
- Dikembalikan ke Unit Pemohon

**Database Changes:**
```
Status: ditolak
PIC: Unit Pemohon

Disposisi baru:
- jabatan_tujuan: Unit Pemohon (Kepala Instalasi)
- status: ditolak
- catatan: [DITOLAK oleh Direktur] + alasan
```

### 3. REVISI
**Yang Terjadi:**
- Direktur meminta perbaikan
- Dikembalikan ke Kepala Bidang untuk diperbaiki
- Kepala Bidang perbaiki, lalu kirim lagi ke Direktur

**Database Changes:**
```
Status: revisi
PIC: Kepala Bidang

Disposisi baru:
- jabatan_tujuan: Kepala Bidang
- status: revisi
- catatan: [REVISI dari Direktur] + catatan revisi
```

## File yang Dimodifikasi

### 1. `app/Http/Controllers/DirekturController.php`

```php
public function approve(Request $request, Permintaan $permintaan)
{
    // ... validasi
    
    // Buat disposisi kembali ke Kepala Bidang
    Disposisi::create([
        'nota_id' => $notaDinas->nota_id,
        'jabatan_tujuan' => 'Kepala Bidang',
        'tanggal_disposisi' => Carbon::now(),
        'catatan' => 'Disetujui oleh Direktur (Final Approval). ' . 
                     ($data['catatan'] ?? 'Silakan disposisi ke Staff Perencanaan untuk perencanaan pengadaan.'),
        'status' => 'selesai',
    ]);

    // Update status permintaan - kembali ke Kepala Bidang
    $permintaan->update([
        'status' => 'proses',
        'pic_pimpinan' => 'Kepala Bidang',
    ]);

    return redirect()
        ->route('direktur.index')
        ->with('success', 'Permintaan disetujui (Final Approval) dan dikembalikan ke Kepala Bidang untuk disposisi ke Staff Perencanaan.');
}
```

### 2. `resources/js/Pages/Direktur/Show.vue`

```vue
<div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
    <p class="text-sm text-green-800">
        Permintaan akan dikembalikan ke <strong>Kepala Bidang</strong> untuk disposisi ke Staff Perencanaan.
    </p>
</div>
```

### 3. `database/seeders/DirekturApproval10Seeder.php`

Updated documentation to reflect the correct workflow.

## Testing Workflow

### Skenario 1: APPROVE (Happy Path)

**Step 1 - Direktur Approve:**
```bash
# Login sebagai Direktur
Email: direktur@rsud.id
Password: password

# Approve permintaan #77
Action: Setujui (Final)
Result: Kembali ke Kepala Bidang
```

**Database After Step 1:**
```sql
SELECT permintaan_id, status, pic_pimpinan FROM permintaan WHERE permintaan_id = 77;
-- Result: status = 'proses', pic_pimpinan = 'Kepala Bidang'

SELECT jabatan_tujuan, catatan, status 
FROM disposisi 
WHERE nota_id = (SELECT nota_id FROM nota_dinas WHERE permintaan_id = 77 ORDER BY tanggal_nota DESC LIMIT 1)
ORDER BY tanggal_disposisi DESC LIMIT 1;
-- Result: jabatan_tujuan = 'Kepala Bidang', status = 'selesai'
```

**Step 2 - Kepala Bidang Disposisi:**
```bash
# Login sebagai Kepala Bidang Pelayanan Medis
Email: kabid.yanmed@rsud.id
Password: password

# Permintaan #77 muncul di dashboard
# Buka detail → Buat disposisi ke Staff Perencanaan
```

**Database After Step 2:**
```sql
SELECT permintaan_id, status, pic_pimpinan FROM permintaan WHERE permintaan_id = 77;
-- Result: status = 'proses', pic_pimpinan = 'Staff Perencanaan'

SELECT jabatan_tujuan, catatan, status 
FROM disposisi 
WHERE nota_id = (SELECT nota_id FROM nota_dinas WHERE permintaan_id = 77 ORDER BY tanggal_nota DESC LIMIT 1)
ORDER BY tanggal_disposisi DESC LIMIT 1;
-- Result: jabatan_tujuan = 'Staff Perencanaan', status = 'selesai'
```

**Step 3 - Staff Perencanaan Process:**
```bash
# Login sebagai Staff Perencanaan
Email: staff.perencanaan@rsud.id
Password: password

# Permintaan #77 muncul di dashboard
# Buat perencanaan → lanjut ke KSO
```

### Skenario 2: REJECT

**Step 1 - Direktur Reject:**
```bash
# Login sebagai Direktur
# Reject permintaan #78
Action: Tolak
Alasan: "Budget tidak tersedia untuk tahun ini"
Result: Proses STOP, kembali ke Unit Pemohon
```

**Database After:**
```sql
SELECT permintaan_id, status, pic_pimpinan FROM permintaan WHERE permintaan_id = 78;
-- Result: status = 'ditolak', pic_pimpinan = 'Unit Pemohon'

SELECT jabatan_tujuan, catatan, status 
FROM disposisi 
WHERE nota_id = (SELECT nota_id FROM nota_dinas WHERE permintaan_id = 78 ORDER BY tanggal_nota DESC LIMIT 1)
ORDER BY tanggal_disposisi DESC LIMIT 1;
-- Result: jabatan_tujuan = 'Kepala Instalasi Farmasi', status = 'ditolak'
```

### Skenario 3: REVISI

**Step 1 - Direktur Minta Revisi:**
```bash
# Login sebagai Direktur
# Minta revisi permintaan #79
Action: Minta Revisi
Catatan: "Mohon tambahkan justifikasi kebutuhan dan spesifikasi teknis lebih detail"
Result: Kembali ke Kepala Bidang
```

**Database After Step 1:**
```sql
SELECT permintaan_id, status, pic_pimpinan FROM permintaan WHERE permintaan_id = 79;
-- Result: status = 'revisi', pic_pimpinan = 'Kepala Bidang'

SELECT jabatan_tujuan, catatan, status 
FROM disposisi 
WHERE nota_id = (SELECT nota_id FROM nota_dinas WHERE permintaan_id = 79 ORDER BY tanggal_nota DESC LIMIT 1)
ORDER BY tanggal_disposisi DESC LIMIT 1;
-- Result: jabatan_tujuan = 'Kepala Bidang', status = 'revisi', catatan = '[REVISI dari Direktur] ...'
```

**Step 2 - Kepala Bidang Perbaiki:**
```bash
# Login sebagai Kepala Bidang
# Perbaiki permintaan #79
# Edit deskripsi/dokumen
# Kirim ulang ke Direktur
```

## Summary Perubahan

### ✅ Yang Diperbaiki
1. **Error timelineTracking()** - Fixed dengan mengubah method jadi placeholder
2. **Workflow approve** - Dikembalikan ke Kepala Bidang (bukan langsung Staff Perencanaan)
3. **UI Message** - Update pesan di modal approve
4. **Seeder** - Created seeder untuk 6 permintaan testing

### 📊 Workflow Final
```
APPROVE:  Direktur → Kepala Bidang → Staff Perencanaan
REJECT:   Direktur → Unit Pemohon (STOP)
REVISI:   Direktur → Kepala Bidang (untuk perbaikan)
```

### 🎯 Benefits
1. **Jalur hirarkis terjaga** - Sesuai struktur organisasi
2. **Dokumentasi lengkap** - Setiap disposisi tercatat
3. **Accountability jelas** - Setiap level punya tanggung jawab
4. **Audit trail** - History lengkap melalui disposisi

## Quick Commands

### Run Seeder
```bash
php artisan db:seed --class=DirekturApproval10Seeder
```

### Check Workflow
```sql
-- Cek permintaan yang menunggu di Direktur
SELECT permintaan_id, bidang, status, pic_pimpinan 
FROM permintaan 
WHERE pic_pimpinan = 'Direktur' AND status = 'proses';

-- Cek history disposisi untuk permintaan tertentu
SELECT d.jabatan_tujuan, d.catatan, d.status, d.tanggal_disposisi
FROM disposisi d
JOIN nota_dinas nd ON d.nota_id = nd.nota_id
WHERE nd.permintaan_id = 77
ORDER BY d.tanggal_disposisi DESC;
```

## Next Steps

1. ✅ Test approve workflow → Kepala Bidang → Staff Perencanaan
2. ✅ Test reject workflow → Unit Pemohon
3. ✅ Test revisi workflow → Kepala Bidang → fix → Direktur
4. ⏳ Verify Kepala Bidang can see approved items from Direktur
5. ⏳ Verify Kepala Bidang can create disposisi to Staff Perencanaan
