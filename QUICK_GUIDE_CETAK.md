# ✅ FITUR CETAK NOTA DINAS - QUICK GUIDE

## 🎯 Fitur Baru

Fitur cetak nota dinas dalam format profesional siap cetak (A4).

## 🖨️ Cara Menggunakan

### Dari Halaman Detail Permintaan:

**Ada 2 Tombol Cetak:**

1. **Di Header** (pojok kanan atas - hijau)
   - Klik "🖨️ Cetak Nota Dinas"
   
2. **Di Card Nota Dinas** (dalam card - hijau)
   - Scroll ke section "Nota Dinas"
   - Klik "Cetak Nota Dinas"

### Di Halaman Cetak:

- **Tombol "← Kembali"**: Kembali ke detail permintaan
- **Tombol "🖨️ Cetak"**: Print dokumen
- **Keyboard**: 
  - `Ctrl+P` = Print
  - `ESC` = Kembali

## 📄 Isi Dokumen Cetak

### Header:
```
RUMAH SAKIT UMUM DAERAH
RSUD Dr. Soetomo
Alamat & Kontak
─────────────────
NOTA DINAS
```

### Informasi:
- Kepada
- Dari
- Tanggal (format: 23 Oktober 2025)
- Nomor
- Sifat (dengan badge warna)
- Lampiran (link)
- Perihal

### Isi:
- Detail Permintaan
- Deskripsi
- Info Disposisi (tujuan, wadir, catatan)

### Tanda Tangan:
- Mengetahui (Kepala Instalasi) - kiri
- Pembuat (dari nota) - kanan

### Footer:
- ID Permintaan
- Waktu cetak

## 🎨 Fitur Cetak

### Badge Sifat (Warna):
- **Sangat Segera**: 🔴 Merah
- **Segera**: 🟠 Merah Muda
- **Rahasia**: ⚫ Hitam
- **Biasa**: ⚪ Abu-abu

### Print Settings:
- Ukuran: A4
- Orientasi: Portrait
- Margin: 2cm (atas/bawah), 2.5cm (kiri/kanan)
- Font: Times New Roman, 12pt

### Elemen yang TIDAK tercetak:
- Tombol "Kembali"
- Tombol "Cetak"
- Watermark "RSUD"

## 💾 Export Options

1. **Cetak ke Printer**:
   - Klik tombol Cetak
   - Pilih printer
   - Print

2. **Simpan sebagai PDF**:
   - Klik tombol Cetak (Ctrl+P)
   - Pilih "Save as PDF"
   - Save

## 📋 Yang Ditambahkan

### Backend:
- ✅ Route: `permintaan/{id}/cetak-nota-dinas`
- ✅ Controller method: `cetakNotaDinas()`
- ✅ Template: `resources/views/cetak/nota-dinas.blade.php`

### Frontend:
- ✅ Tombol cetak di header (Show.vue)
- ✅ Tombol cetak di card Nota Dinas (Show.vue)

## 🎉 Keunggulan

- ✅ Format profesional sesuai standar surat dinas
- ✅ Print-ready (optimized untuk A4)
- ✅ Badge sifat dengan warna
- ✅ Keyboard shortcuts
- ✅ Bisa print atau save PDF
- ✅ Auto-format tanggal Indonesia
- ✅ Info lengkap & terstruktur

## 📄 Dokumentasi Lengkap

Lihat: `FITUR_CETAK_NOTA_DINAS.md`

---
**Status**: ✅ READY TO USE
**Route**: `/permintaan/{id}/cetak-nota-dinas`
