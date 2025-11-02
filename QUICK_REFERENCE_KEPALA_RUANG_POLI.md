# Quick Reference: Kepala Ruang & Kepala Poli

## Akses Login

### Kepala Ruang (IRNA)
- **Role Database:** `kepala_ruang`
- **Akses:** Permintaan dari ruangan IRNA mereka saja
- **Dashboard:** `/kepala-ruang/dashboard`
- **Input Baru:** `/kepala-ruang/create`

### Kepala Poli (IRJA)
- **Role Database:** `kepala_poli`
- **Akses:** Permintaan dari poli IRJA mereka saja
- **Dashboard:** `/kepala-poli/dashboard`
- **Input Baru:** `/kepala-poli/create`

## Kemampuan (Sama dengan Admin)

### ✅ Yang BISA Dilakukan:
1. **Input Permintaan Baru** - CREATE
   - Isi form permintaan
   - Isi nota dinas
   - Auto-set status "diajukan"
   - Auto-set bidang dari unit kerja

2. **Lihat Permintaan** - READ
   - Dashboard dengan statistik
   - List semua permintaan dari unit kerja mereka
   - Filter & search
   - Detail permintaan
   - Tracking status

3. **Edit Permintaan** - UPDATE
   - Edit permintaan yang sudah dibuat
   - Update data permintaan

4. **Hapus Permintaan** - DELETE
   - Hapus permintaan yang sudah dibuat

5. **Cetak & Dokumen**
   - Cetak Nota Dinas
   - Lihat lampiran
   - Download dokumen

### ❌ Yang TIDAK BISA Dilakukan:
- Lihat permintaan unit lain (hanya unit kerja sendiri)
- Approve/reject permintaan (bukan reviewer)
- Akses menu Admin
- Ubah role atau permission

## Contoh Unit Kerja

### Kepala Ruang IRNA:
- Ruang Anggrek
- Ruang Bougenville
- Ruang Cempaka
- Ruang Dahlia
- Ruang Edelweiss
- Ruang Flamboyan
- Ruang Gardena
- Ruang Heliconia
- Ruang Ixia

### Kepala Poli IRJA:
- Poli Bedah
- Poli Gigi
- Poli Kulit Kelamin
- Poli Penyakit Dalam
- Poli Jiwa
- Poli Psikologi
- Poli Mata
- Klinik Gizi
- Laboratorium
- Apotek

## Workflow Input Permintaan

```
1. Login sebagai Kepala Ruang/Poli
   ↓
2. Redirect ke dashboard mereka
   ↓
3. Klik "Buat Permintaan Baru" atau akses /create
   ↓
4. Isi form:
   - Klasifikasi: Medis/Non Medis/Penunjang
   - Deskripsi permintaan
   - Disposisi tujuan
   - Kabid tujuan (jika perlu)
   - Data Nota Dinas:
     * Kepada
     * Dari
     * Tanggal
     * No Nota
     * Perihal
     * Detail
   ↓
5. Submit form
   ↓
6. Auto-fill oleh system:
   - user_id → dari login
   - bidang → dari unit_kerja
   - tanggal_permintaan → hari ini
   - status → "diajukan"
   ↓
7. Permintaan tersimpan
   ↓
8. Redirect ke index dengan notif sukses
```

## Security

### Filter by Unit Kerja
Semua query permintaan di-filter:
```php
->whereHas('user', function($q) use ($user) {
    $q->where('unit_kerja', $user->unit_kerja);
});
```

### Validasi Akses
Sebelum show/edit/delete, cek unit kerja:
```php
if ($permintaan->user->unit_kerja !== $user->unit_kerja) {
    abort(403);
}
```

## Testing Checklist

### Kepala Ruang
- [ ] Login dengan role `kepala_ruang`
- [ ] Redirect ke `/kepala-ruang/dashboard`
- [ ] Dashboard tampil dengan statistik
- [ ] Klik "Buat Permintaan"
- [ ] Isi form dan submit
- [ ] Permintaan tersimpan dengan `bidang` = unit kerja
- [ ] Hanya lihat permintaan dari ruang sendiri
- [ ] Bisa edit permintaan sendiri
- [ ] Bisa hapus permintaan sendiri
- [ ] Tidak bisa akses permintaan ruang lain (403)

### Kepala Poli
- [ ] Login dengan role `kepala_poli`
- [ ] Redirect ke `/kepala-poli/dashboard`
- [ ] Dashboard tampil dengan statistik
- [ ] Klik "Buat Permintaan"
- [ ] Isi form dan submit
- [ ] Permintaan tersimpan dengan `bidang` = unit kerja
- [ ] Hanya lihat permintaan dari poli sendiri
- [ ] Bisa edit permintaan sendiri
- [ ] Bisa hapus permintaan sendiri
- [ ] Tidak bisa akses permintaan poli lain (403)

## SQL untuk Test Users

```sql
-- Insert Kepala Ruang Anggrek
INSERT INTO users (name, email, password, role, jabatan, unit_kerja, email_verified_at)
VALUES (
    'Dr. Siti Kepala Ruang Anggrek',
    'kepala.anggrek@hospital.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password
    'kepala_ruang',
    'Kepala Ruang',
    'Anggrek',
    NOW()
);

-- Insert Kepala Poli Bedah
INSERT INTO users (name, email, password, role, jabatan, unit_kerja, email_verified_at)
VALUES (
    'Dr. Budi Kepala Poli Bedah',
    'kepala.bedah@hospital.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password
    'kepala_poli',
    'Kepala Poli',
    'Poli Bedah',
    NOW()
);
```

**Default Password:** `password`

## Quick Commands

```bash
# Lihat routes Kepala Ruang
php artisan route:list --name=kepala-ruang

# Lihat routes Kepala Poli
php artisan route:list --name=kepala-poli

# Test controller
php artisan tinker
>>> $user = User::where('role', 'kepala_ruang')->first();
>>> $user->unit_kerja;

# Clear cache
php artisan cache:clear
php artisan route:clear
php artisan config:clear
```

## Troubleshooting

### Error 404 Not Found
- Pastikan routes sudah didefinisikan di `web.php`
- Jalankan `php artisan route:clear`

### Error 403 Forbidden
- User mencoba akses permintaan unit lain
- Ini normal, working as intended

### Redirect tidak ke dashboard yang benar
- Check middleware `RedirectBasedOnRole`
- Pastikan role user sudah sesuai

### Form tidak bisa submit
- Check validasi di controller
- Pastikan semua required field terisi
- Check console browser untuk error

## File Locations

```
Backend (✅ Done):
├── app/Http/Controllers/
│   ├── KepalaRuangController.php
│   └── KepalaPoliController.php
├── app/Http/Middleware/
│   └── RedirectBasedOnRole.php (updated)
└── routes/
    └── web.php (updated)

Frontend (⏳ TODO):
└── resources/js/Pages/
    ├── KepalaRuang/
    │   ├── Dashboard.jsx
    │   ├── Index.jsx
    │   ├── Create.jsx
    │   ├── Edit.jsx
    │   ├── Show.jsx
    │   ├── Tracking.jsx
    │   ├── CetakNotaDinas.jsx
    │   └── LampiranNotaDinas.jsx
    └── KepalaPoli/
        ├── Dashboard.jsx
        ├── Index.jsx
        ├── Create.jsx
        ├── Edit.jsx
        ├── Show.jsx
        ├── Tracking.jsx
        ├── CetakNotaDinas.jsx
        └── LampiranNotaDinas.jsx
```

## Status

**Backend:** ✅ Complete  
**Frontend:** ⏳ Pending  
**Testing:** ⏳ Pending  
**Documentation:** ✅ Complete
