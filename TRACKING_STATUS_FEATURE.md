# 📊 Fitur Tracking Status Permintaan untuk Kepala Instalasi

Dokumentasi tentang implementasi fitur tracking timeline status permintaan pengadaan.

---

## 🎯 Tujuan

Memungkinkan **Kepala Instalasi** untuk:
- ✅ Melihat progress permintaan dari awal hingga selesai
- ✅ Mengetahui tahap mana yang sedang berlangsung
- ✅ Melihat riwayat lengkap setiap tahapan
- ✅ Monitoring durasi dan status setiap tahap

---

## 📋 Tahapan E-Procurement

### 8 Tahapan Proses Pengadaan:

1. **Permintaan** - Unit mengajukan permintaan
2. **Nota Dinas** - Kepala Instalasi membuat nota dinas
3. **Disposisi** - Disposisi ke bagian terkait
4. **Perencanaan** - Perencanaan pengadaan
5. **KSO** - Kerja Sama Operasional
6. **Pengadaan** - Proses pengadaan dari vendor
7. **Nota Penerimaan** - Penerimaan barang/jasa
8. **Serah Terima** - Serah terima ke unit

---

## 🛠️ Implementasi

### 1. Model Permintaan - Tracking Methods

**File:** `app/Models/Permintaan.php`

#### Method `getTimelineTracking()`

Mengembalikan array timeline lengkap yang sudah dilalui:

```php
$timeline = $permintaan->getTimelineTracking();

// Output:
[
    [
        'tahapan' => 'Permintaan',
        'tanggal' => '2025-10-15',
        'status' => 'diajukan',
        'keterangan' => 'Permintaan diajukan',
        'icon' => 'document',
        'completed' => true
    ],
    [
        'tahapan' => 'Nota Dinas',
        'tanggal' => '2025-10-16',
        'status' => 'proses',
        'keterangan' => 'Nota dinas ke: Direktur',
        'icon' => 'mail',
        'completed' => true
    ],
    // ... dst
]
```

#### Method `getProgressPercentage()`

Menghitung progress dalam persentase:

```php
$progress = $permintaan->getProgressPercentage();
// Output: 25 (jika sudah 2 dari 8 tahap = 25%)
```

#### Attribute `trackingStatus`

Mengembalikan tahap terakhir yang dicapai:

```php
$status = $permintaan->trackingStatus;
// Output: "Nota Dinas"
```

---

### 2. Controller Methods

**File:** `app/Http/Controllers/KepalaInstalasiController.php`

#### Method `show()` - Updated

Menambahkan timeline dan progress ke detail permintaan:

```php
public function show(Permintaan $permintaan)
{
    // ... validasi otorisasi
    
    $timeline = $permintaan->getTimelineTracking();
    $progress = $permintaan->getProgressPercentage();
    
    return Inertia::render('KepalaInstalasi/Show', [
        'permintaan' => $permintaan,
        'timeline' => $timeline,
        'progress' => $progress,
    ]);
}
```

#### Method `tracking()` - New

Dedicated page untuk tracking detail:

```php
public function tracking(Permintaan $permintaan)
{
    // ... validasi otorisasi
    
    $timeline = $permintaan->getTimelineTracking();
    $progress = $permintaan->getProgressPercentage();
    
    // Tahapan yang sudah dan belum dilalui
    $completedSteps = array_column($timeline, 'tahapan');
    $pendingSteps = array_diff($allSteps, $completedSteps);
    
    return Inertia::render('KepalaInstalasi/Tracking', [
        'permintaan' => $permintaan,
        'timeline' => $timeline,
        'progress' => $progress,
        'completedSteps' => $completedSteps,
        'pendingSteps' => $pendingSteps,
    ]);
}
```

#### Updated `dashboard()` dan `index()`

Menambahkan progress info ke list permintaan:

```php
->map(function($permintaan) {
    $permintaan->tracking_status = $permintaan->trackingStatus;
    $permintaan->progress = $permintaan->getProgressPercentage();
    $permintaan->timeline_count = count($permintaan->getTimelineTracking());
    return $permintaan;
})
```

---

### 3. Routes

**File:** `routes/web.php`

```php
Route::get('/permintaan/{permintaan}/tracking', 
    [KepalaInstalasiController::class, 'tracking'])
    ->name('tracking');
```

**Full URL:** `/kepala-instalasi/permintaan/{id}/tracking`

---

## 📊 Data Structure

### Timeline Array Structure

```php
[
    [
        'tahapan' => string,      // Nama tahapan
        'tanggal' => date,        // Tanggal tahapan
        'status' => string,       // Status (diajukan, proses, dll)
        'keterangan' => string,   // Keterangan detail
        'icon' => string,         // Icon identifier
        'completed' => boolean    // True jika sudah selesai
    ]
]
```

### Progress Calculation

```php
Total Steps = 8 tahapan
Completed Steps = jumlah tahapan yang sudah dilalui
Progress % = (Completed / Total) * 100
```

**Contoh:**
- Tahap 1-2 selesai → 2/8 = 25%
- Tahap 1-5 selesai → 5/8 = 62.5%
- Tahap 1-8 selesai → 8/8 = 100%

---

## 🎨 UI Components (Rekomendasi)

### Dashboard - List View

```jsx
<div className="tracking-info">
  <div className="progress-bar">
    <div style={{ width: `${progress}%` }}></div>
  </div>
  <span className="status-badge">{tracking_status}</span>
  <span className="timeline-count">{timeline_count}/8 tahap</span>
</div>
```

### Detail View - Timeline Vertical

```jsx
<div className="timeline-vertical">
  {timeline.map((item, index) => (
    <div key={index} className="timeline-item completed">
      <div className="timeline-icon">{item.icon}</div>
      <div className="timeline-content">
        <h4>{item.tahapan}</h4>
        <p className="date">{item.tanggal}</p>
        <p className="status">{item.status}</p>
        <p className="keterangan">{item.keterangan}</p>
      </div>
    </div>
  ))}
  
  {pendingSteps.map((step, index) => (
    <div key={index} className="timeline-item pending">
      <div className="timeline-icon">...</div>
      <div className="timeline-content">
        <h4>{step}</h4>
        <p className="status">Menunggu</p>
      </div>
    </div>
  ))}
</div>
```

### Tracking Page - Full Detail

```jsx
<div className="tracking-page">
  <div className="header">
    <h2>Tracking Permintaan #{permintaan.permintaan_id}</h2>
    <div className="progress-circle">{progress}%</div>
  </div>
  
  <div className="current-step">
    <p>Tahap Saat Ini:</p>
    <h3>{currentStep}</h3>
  </div>
  
  <div className="timeline-horizontal">
    {allSteps.map((step, index) => (
      <div className={completedSteps.includes(step) ? 'active' : 'pending'}>
        <div className="step-number">{index + 1}</div>
        <div className="step-label">{step}</div>
      </div>
    ))}
  </div>
  
  <div className="timeline-details">
    {/* Detail timeline vertical */}
  </div>
</div>
```

---

## 📱 Usage Examples

### Contoh 1: Lihat Progress di Dashboard

```php
// Controller
$permintaans = Permintaan::all()->map(function($p) {
    $p->progress = $p->getProgressPercentage();
    $p->current_step = $p->trackingStatus;
    return $p;
});

// View (React/Vue)
{permintaans.map(p => (
  <tr>
    <td>{p.permintaan_id}</td>
    <td>{p.deskripsi}</td>
    <td>
      <ProgressBar value={p.progress} />
      <span>{p.current_step}</span>
    </td>
  </tr>
))}
```

### Contoh 2: Detail Timeline

```javascript
// Fetch timeline
const timeline = permintaan.timeline;

// Tampilkan di UI
timeline.forEach(item => {
  console.log(`${item.tahapan} - ${item.tanggal}: ${item.status}`);
});

// Output:
// Permintaan - 2025-10-15: diajukan
// Nota Dinas - 2025-10-16: proses
// Disposisi - 2025-10-17: dalam_proses
```

### Contoh 3: Check Status Specific

```php
$timeline = $permintaan->getTimelineTracking();
$hasNotaDinas = collect($timeline)->contains('tahapan', 'Nota Dinas');
$hasPengadaan = collect($timeline)->contains('tahapan', 'Pengadaan');

if ($hasPengadaan) {
    $pengadaan = collect($timeline)->firstWhere('tahapan', 'Pengadaan');
    echo "Vendor: " . $pengadaan['keterangan'];
}
```

---

## 🔍 Query Performance

### Eager Loading

Untuk performa optimal, gunakan eager loading saat mengambil banyak permintaan:

```php
$permintaans = Permintaan::with([
    'notaDinas.disposisi.perencanaan.kso.pengadaan.notaPenerimaan.serahTerima'
])->get();
```

### Selective Loading

Jika hanya butuh tracking tanpa detail lengkap:

```php
$permintaans = Permintaan::with('notaDinas')->get()->map(function($p) {
    return [
        'id' => $p->permintaan_id,
        'progress' => $p->getProgressPercentage(),
        'current_step' => $p->trackingStatus,
    ];
});
```

---

## 📊 Status Values

### Permintaan
- `diajukan` - Permintaan diajukan
- `ditolak` - Permintaan ditolak
- `disetujui` - Permintaan disetujui
- `proses` - Dalam proses

### Nota Dinas
- `draft` - Draft nota dinas
- `proses` - Sedang diproses
- `dikirim` - Sudah dikirim
- `ditolak` - Ditolak
- `disetujui` - Disetujui

### Disposisi
- `menunggu` - Menunggu tindak lanjut
- `dalam_proses` - Dalam proses
- `ditolak` - Ditolak
- `disetujui` - Disetujui

### Perencanaan
- `draft` - Draft perencanaan
- `review` - Dalam review
- `revisi` - Perlu revisi
- `disetujui` - Disetujui

### KSO
- `draft` - Draft KSO
- `negosiasi` - Dalam negosiasi
- `proses_kontrak` - Proses kontrak
- `aktif` - KSO aktif
- `selesai` - Selesai

### Pengadaan
- `tender` - Proses tender
- `pembelian` - Proses pembelian
- `pengiriman` - Dalam pengiriman
- `diterima` - Diterima
- `ditolak` - Ditolak

### Nota Penerimaan
- `pending` - Menunggu
- `diterima_sebagian` - Diterima sebagian
- `diterima_lengkap` - Diterima lengkap
- `ditolak` - Ditolak

### Serah Terima
- `menunggu_penerima` - Menunggu penerima
- `diterima_unit` - Diterima unit
- `selesai` - Selesai

---

## 🎯 Benefits

### Untuk Kepala Instalasi:
1. **Transparansi** - Melihat progress real-time
2. **Monitoring** - Track semua permintaan unit
3. **Akuntabilitas** - Mengetahui siapa yang menangani
4. **Efisiensi** - Identifikasi bottleneck

### Untuk Sistem:
1. **Audit Trail** - Riwayat lengkap
2. **Reporting** - Data untuk laporan
3. **Analytics** - Analisis durasi per tahap
4. **Notification** - Trigger untuk notifikasi

---

## 📈 Advanced Features (Future)

### 1. Durasi per Tahap

```php
public function getDurasiPerTahap()
{
    $timeline = $this->getTimelineTracking();
    $durasi = [];
    
    for ($i = 0; $i < count($timeline) - 1; $i++) {
        $start = Carbon::parse($timeline[$i]['tanggal']);
        $end = Carbon::parse($timeline[$i + 1]['tanggal']);
        
        $durasi[$timeline[$i]['tahapan']] = $start->diffInDays($end);
    }
    
    return $durasi;
}
```

### 2. Estimasi Selesai

```php
public function getEstimasiSelesai()
{
    $rataRataDurasi = 30; // hari per tahap (dari historical data)
    $tahapSisa = 8 - count($this->getTimelineTracking());
    
    return now()->addDays($tahapSisa * $rataRataDurasi);
}
```

### 3. Alert untuk Delay

```php
public function isDelayed()
{
    $timeline = $this->getTimelineTracking();
    $lastUpdate = Carbon::parse(end($timeline)['tanggal']);
    
    return $lastUpdate->diffInDays(now()) > 7; // 7 hari tanpa update
}
```

---

## 🔗 Related Files

- `app/Models/Permintaan.php` - Model dengan tracking methods
- `app/Http/Controllers/KepalaInstalasiController.php` - Controller
- `routes/web.php` - Routes
- `VIEW_TRACKING_STATUS.sql` - Database view
- `QUERY_TRACKING_STATUS.sql` - Query tracking

---

## ✅ Testing

### Test Timeline

```php
$permintaan = Permintaan::find(1);
$timeline = $permintaan->getTimelineTracking();

dump($timeline);
// Expected: Array dengan minimal 1 item (Permintaan)
```

### Test Progress

```php
$permintaan = Permintaan::find(1);
$progress = $permintaan->getProgressPercentage();

dump($progress);
// Expected: Integer 0-100
```

### Test Tracking Page

```
URL: /kepala-instalasi/permintaan/1/tracking
Expected: Halaman tracking dengan timeline lengkap
```

---

**Version:** 1.2.0  
**Last Updated:** 17 Oktober 2025  
**Status:** ✅ Implemented
