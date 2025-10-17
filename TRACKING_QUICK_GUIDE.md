# 🚀 Tracking Status - Quick Reference

**Kepala Instalasi sekarang dapat melihat progress permintaan secara real-time!**

---

## ⚡ Fitur Utama

### 1. Timeline Tracking
Melihat riwayat lengkap 8 tahapan e-procurement:
```
1. Permintaan
2. Nota Dinas
3. Disposisi
4. Perencanaan
5. KSO
6. Pengadaan
7. Nota Penerimaan
8. Serah Terima
```

### 2. Progress Percentage
Otomatis menghitung progress: `(tahap selesai / 8) × 100%`

### 3. Current Status
Menampilkan tahap terakhir yang dicapai

---

## 📍 Cara Menggunakan

### Di Dashboard
```
✅ Progress bar untuk setiap permintaan
✅ Label tahap saat ini
✅ Jumlah tahap selesai (contoh: 3/8)
```

### Di Detail Permintaan
```
URL: /kepala-instalasi/permintaan/{id}
✅ Timeline vertikal
✅ Progress percentage
✅ Tombol "Lihat Tracking Lengkap"
```

### Tracking Detail Page
```
URL: /kepala-instalasi/permintaan/{id}/tracking
✅ Timeline horizontal dengan step indicator
✅ Timeline vertikal dengan detail lengkap
✅ Tahapan yang sudah dan belum dilalui
✅ Progress circle
```

---

## 💻 Code Usage

### Get Timeline
```php
$permintaan = Permintaan::find(1);
$timeline = $permintaan->getTimelineTracking();

// Output: Array dengan struktur:
// [
//   ['tahapan' => 'Permintaan', 'tanggal' => '2025-10-15', ...],
//   ['tahapan' => 'Nota Dinas', 'tanggal' => '2025-10-16', ...],
// ]
```

### Get Progress
```php
$progress = $permintaan->getProgressPercentage();
// Output: 25 (jika 2 dari 8 tahap selesai)
```

### Get Current Status
```php
$currentStatus = $permintaan->trackingStatus;
// Output: "Nota Dinas"
```

---

## 📊 Data yang Ditampilkan

Setiap tahap menampilkan:
- ✅ Nama tahapan
- ✅ Tanggal eksekusi
- ✅ Status (diajukan, proses, disetujui, dll)
- ✅ Keterangan detail (PIC, vendor, tracking number, dll)
- ✅ Icon identifier

---

## 🎨 UI Components (Rekomendasi)

### Progress Bar
```jsx
<div className="w-full bg-gray-200 rounded-full h-2">
  <div 
    className="bg-blue-600 h-2 rounded-full" 
    style={{ width: `${progress}%` }}
  />
</div>
<span className="text-sm">{progress}% selesai</span>
```

### Timeline Item
```jsx
<div className="timeline-item">
  <div className="timeline-marker">✓</div>
  <div className="timeline-content">
    <h4>{tahapan}</h4>
    <p className="text-gray-500">{tanggal}</p>
    <p className="text-sm">{keterangan}</p>
    <span className="badge">{status}</span>
  </div>
</div>
```

---

## 🔍 Contoh Skenario

### Scenario 1: Permintaan Baru
```
Progress: 12.5% (1/8)
Current: Permintaan
Timeline:
  ✓ Permintaan (2025-10-15) - diajukan
```

### Scenario 2: Sedang Proses Nota Dinas
```
Progress: 25% (2/8)
Current: Nota Dinas
Timeline:
  ✓ Permintaan (2025-10-15) - disetujui
  ✓ Nota Dinas (2025-10-16) - proses
```

### Scenario 3: Sudah di Pengadaan
```
Progress: 75% (6/8)
Current: Pengadaan
Timeline:
  ✓ Permintaan (2025-10-15) - disetujui
  ✓ Nota Dinas (2025-10-16) - disetujui
  ✓ Disposisi (2025-10-17) - disetujui
  ✓ Perencanaan (2025-10-18) - disetujui
  ✓ KSO (2025-10-19) - aktif
  ✓ Pengadaan (2025-10-20) - pembelian
    Vendor: PT Meditek | Tracking: JNE123456
```

---

## ⚙️ Integration dengan Isolasi Data

**Penting:** Tracking tetap mengikuti aturan isolasi data!

```php
// Kepala Farmasi HANYA bisa tracking permintaan Farmasi
// Kepala IGD HANYA bisa tracking permintaan IGD

// Validasi otomatis di controller:
if ($permintaan->bidang !== $user->unit_kerja) {
    abort(403); // Forbidden
}
```

---

## 📈 Benefits

### Untuk Kepala Instalasi:
- 🎯 **Transparansi** - Tahu progress real-time
- ⏱️ **Monitoring** - Track semua permintaan unit
- 📊 **Visibility** - Lihat bottleneck
- 🔔 **Proactive** - Tindak cepat jika stuck

### Untuk Manajemen:
- 📑 **Reporting** - Data untuk laporan
- 🔍 **Audit** - Riwayat lengkap
- 📉 **Analytics** - Analisis durasi
- ⚡ **Efficiency** - Optimasi proses

---

## 🐛 Troubleshooting

**Q: Timeline kosong/hanya 1 item?**  
A: Normal jika permintaan masih tahap awal. Timeline akan bertambah saat ada progress.

**Q: Progress stuck di 12.5%?**  
A: Permintaan masih di tahap Permintaan, belum ada nota dinas yang dibuat.

**Q: Error 403 saat akses tracking?**  
A: Kepala Instalasi hanya bisa tracking permintaan dari bagiannya sendiri.

---

## 📚 Dokumentasi Lengkap

- **TRACKING_STATUS_FEATURE.md** - Dokumentasi teknis lengkap
- **CHANGELOG.md** - Riwayat versi 1.2.0
- **API Reference** - Method di Model Permintaan

---

## ✅ Next Steps

1. **Testing** - Test dengan data real
2. **UI** - Implement frontend components
3. **Notification** - Alert jika ada delay
4. **Report** - Export tracking ke PDF/Excel

---

**Version:** 1.2.0  
**Feature:** Timeline Tracking  
**Status:** ✅ Ready to Use
