# KONFIGURASI ROUTING IRJA KE KEPALA INSTALASI

## Ringkasan Perubahan

Sistem telah dikonfigurasi agar semua permintaan dari departemen di bawah IRJA (Instalasi Rawat Jalan) otomatis diarahkan ke **Kepala IRJA**, BUKAN ke Kepala IGD.

## Departemen di Bawah IRJA

Departemen-departemen berikut sekarang otomatis akan diarahkan ke Kepala IRJA:

1. **Poli Bedah**
2. **Poli Gigi**
3. **Poli Kulit Kelamin**
4. **Poli Penyakit Dalam**
5. **Poli Jiwa**
6. **Poli Psikologi**
7. **Poli Mata**
8. **Klinik Gizi**
9. **Laboratorium**
10. **Apotek**

## Cara Kerja Sistem

### 1. Kepala IRJA
- **User**: Dr. Putri Handayani, Sp.PD (ID: 9)
- **Unit Kerja**: `Rawat Jalan`
- **Role**: `kepala_instalasi`

Ketika Kepala IRJA login, sistem akan menampilkan semua permintaan dari:
- `Rawat Jalan`
- `IRJ`
- `IRJA`
- `Instalasi Rawat Jalan`
- Dan semua 10 departemen di atas

### 2. Kepala IGD (Terpisah)
- **User**: Dr. Ahmad Yani, Sp.PD (ID: 3)
- **Unit Kerja**: `Gawat Darurat`
- **Role**: `kepala_instalasi`

Kepala IGD hanya akan melihat permintaan dari:
- `IGD`
- `Gawat Darurat`
- `Instalasi Gawat Darurat`
- Sub-departemen IGD (UGD, Triase, Observasi, Ruang Tindakan IGD)

## File yang Dimodifikasi

### `app/Http/Controllers/KepalaInstalasiController.php`

#### Metode yang ditambahkan/dimodifikasi:

1. **`getIRJADepartments()`** - Baru
   ```php
   private function getIRJADepartments()
   {
       return [
           'Poli Bedah',
           'Poli Gigi',
           'Poli Kulit Kelamin',
           'Poli Penyakit Dalam',
           'Poli Jiwa',
           'Poli Psikologi',
           'Poli Mata',
           'Klinik Gizi',
           'Laboratorium',
           'Apotek',
       ];
   }
   ```

2. **`getIGDDepartments()`** - Baru
   ```php
   private function getIGDDepartments()
   {
       return [
           'UGD',
           'Triase',
           'Observasi',
           'Ruang Tindakan IGD',
       ];
   }
   ```

3. **`getBidangVariations()`** - Dimodifikasi
   - Menambahkan special case untuk Kepala IRJA
   - Menambahkan special case untuk Kepala IGD
   - Otomatis menambahkan semua sub-departemen ke variations

## Panduan Penggunaan

### Membuat Permintaan dari Departemen IRJA

Ketika membuat permintaan baru, pastikan field `bidang` diisi dengan salah satu dari:
- `Poli Bedah`
- `Poli Gigi`
- `Poli Kulit Kelamin`
- `Poli Penyakit Dalam`
- `Poli Jiwa`
- `Poli Psikologi`
- `Poli Mata`
- `Klinik Gizi`
- `Laboratorium`
- `Apotek`
- Atau `Rawat Jalan` / `IRJ` / `IRJA`

Sistem akan otomatis mengarahkan permintaan tersebut ke Kepala IRJA.

### Membuat User untuk Kepala Departemen IRJA

Jika ingin membuat user untuk kepala departemen di bawah IRJA (misalnya Kepala Poli Bedah), gunakan:

```php
DB::table('users')->insert([
    'name' => 'Dr. Nama Dokter',
    'email' => 'email@example.com',
    'password' => bcrypt('password'),
    'role' => 'kepala_instalasi', // atau role lain sesuai kebutuhan
    'unit_kerja' => 'Poli Bedah', // Nama department
    'jabatan' => 'Kepala Poli Bedah',
]);
```

**PENTING**: Jika kepala departemen ini hanya mengelola department-nya sendiri, gunakan `unit_kerja` sesuai nama department. Jika mengelola seluruh IRJA, gunakan `unit_kerja` = `Rawat Jalan`.

## Workflow Permintaan

1. **Staff membuat permintaan** dengan `bidang` = salah satu departemen IRJA (misal: "Poli Bedah")

2. **Sistem mencocokkan** permintaan dengan Kepala Instalasi:
   - Cari kepala instalasi dengan `unit_kerja` = "Rawat Jalan"
   - Check apakah "Poli Bedah" ada di variations
   - Hasil: ✓ Match dengan Kepala IRJA

3. **Kepala IRJA melihat permintaan** di dashboard-nya

4. **Kepala IRJA approve** → Otomatis diteruskan ke Kepala Bidang yang sesuai

## Testing

Jalankan script test untuk verifikasi:

```bash
php test_irja_routing.php
```

Output yang diharapkan:
```
✓ YA - Poli Bedah
✓ YA - Poli Gigi
✓ YA - Poli Kulit Kelamin
... (semua departemen IRJA)
```

## Troubleshooting

### Masalah: Permintaan dari Poli Bedah tidak muncul di dashboard Kepala IRJA

**Solusi**:
1. Cek field `bidang` di tabel `permintaan`:
   ```sql
   SELECT permintaan_id, bidang, status 
   FROM permintaan 
   WHERE bidang LIKE '%Bedah%';
   ```

2. Pastikan Kepala IRJA memiliki `unit_kerja` = `Rawat Jalan`:
   ```sql
   SELECT id, name, unit_kerja, role 
   FROM users 
   WHERE role = 'kepala_instalasi' 
   AND unit_kerja LIKE '%Rawat Jalan%';
   ```

### Masalah: Permintaan muncul di Kepala IGD padahal dari IRJA

**Solusi**:
Periksa field `bidang` pada permintaan. Pastikan tidak mengandung kata "IGD" atau "Gawat Darurat".

## Penambahan Departemen IRJA Baru

Jika ada departemen baru di bawah IRJA, tambahkan ke method `getIRJADepartments()`:

```php
private function getIRJADepartments()
{
    return [
        'Poli Bedah',
        'Poli Gigi',
        // ... departemen lainnya
        'Departemen Baru', // Tambahkan di sini
    ];
}
```

## Catatan Penting

1. **Unit Kerja harus konsisten**: Pastikan field `unit_kerja` di tabel `users` dan field `bidang` di tabel `permintaan` menggunakan nama yang sama.

2. **Case-insensitive matching**: Sistem menggunakan `stripos()` untuk matching yang case-insensitive, jadi "Poli Bedah" sama dengan "poli bedah".

3. **Partial matching**: Sistem juga mendukung partial matching dengan `LIKE`, jadi "Instalasi Rawat Jalan - Poli Bedah" akan tetap match dengan "Poli Bedah".

---

**Tanggal Implementasi**: 31 Oktober 2025
**Developer**: GitHub Copilot CLI
**Status**: ✅ Implemented & Tested
