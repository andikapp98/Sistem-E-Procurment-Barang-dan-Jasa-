# QUICK GUIDE: IRJA Department Routing

## ✅ Perubahan yang Telah Dilakukan

Sistem telah dikonfigurasi agar semua permintaan dari 10 departemen di bawah IRJA otomatis diarahkan ke **Kepala IRJA**, bukan ke Kepala IGD.

## 📋 Departemen IRJA (10 Departemen)

Semua permintaan dari departemen berikut akan ke **Kepala IRJA (Dr. Putri Handayani, Sp.PD)**:

1. Poli Bedah
2. Poli Gigi
3. Poli Kulit Kelamin
4. Poli Penyakit Dalam
5. Poli Jiwa
6. Poli Psikologi
7. Poli Mata
8. Klinik Gizi
9. Laboratorium
10. Apotek

## 🔄 Workflow Permintaan

```
[Staff di Poli Bedah]
        ↓ (membuat permintaan dengan bidang="Poli Bedah")
[Sistem mencocokkan]
        ↓
[Kepala IRJA - Dr. Putri Handayani]
        ↓ (approve)
[Kepala Bidang yang sesuai]
```

**BUKAN** ke Kepala IGD!

## 📝 Cara Membuat Permintaan dari Departemen IRJA

Ketika membuat permintaan, pastikan field `bidang` diisi dengan nama departemen yang tepat:

```php
// Contoh: Permintaan dari Poli Bedah
[
    'bidang' => 'Poli Bedah',
    'deskripsi' => 'Permintaan alat bedah...',
    // ... field lainnya
]
```

## ✅ Verifikasi

Jalankan script verifikasi untuk memastikan konfigurasi bekerja:

```bash
php verify_irja_routing.php
```

Output yang diharapkan:
```
✓ Kepala IRJA ditemukan
✓ Routing logic BENAR
✓ Sistem siap digunakan!
```

## 📁 File yang Dimodifikasi

- `app/Http/Controllers/KepalaInstalasiController.php`
  - Menambahkan method `getIRJADepartments()`
  - Menambahkan method `getIGDDepartments()`
  - Memodifikasi method `getBidangVariations()`

## 🚀 Testing

Login sebagai **Kepala IRJA** (Dr. Putri Handayani):
- Email: `kepala.rajal@rsud.id`
- Dashboard akan menampilkan permintaan dari semua 10 departemen IRJA

Login sebagai **Kepala IGD** (Dr. Ahmad Yani):
- Email: `kepala.igd@rsud.id`
- Dashboard hanya menampilkan permintaan dari IGD/Gawat Darurat

## 📚 Dokumentasi Lengkap

Lihat: `IRJA_ROUTING_CONFIGURATION.md`

---

**Status**: ✅ Implemented & Tested
**Tanggal**: 31 Oktober 2025
