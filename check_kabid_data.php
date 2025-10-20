<?php

use App\Models\Permintaan;

// Cek total permintaan
$totalAll = Permintaan::count();
echo "Total semua permintaan: {$totalAll}\n";

// Cek dengan status proses/disetujui
$withStatus = Permintaan::whereIn('status', ['proses', 'disetujui'])->count();
echo "Permintaan dengan status proses/disetujui: {$withStatus}\n";

// Cek dengan disposisi ke Kepala Bidang
$withDisposisi = Permintaan::whereHas('notaDinas.disposisi', function($q) {
    $q->where('jabatan_tujuan', 'Kepala Bidang')
      ->where('status', 'pending');
})->count();
echo "Dengan disposisi ke Kepala Bidang (pending): {$withDisposisi}\n";

// Cek dengan pic_pimpinan
$withPic = Permintaan::where('pic_pimpinan', 'Kepala Bidang')->count();
echo "Dengan pic_pimpinan = 'Kepala Bidang': {$withPic}\n";

// Cek kombinasi (seperti di controller)
$combined = Permintaan::where(function($q) {
    $q->where('pic_pimpinan', 'Kepala Bidang')
      ->orWhereHas('notaDinas.disposisi', function($query) {
          $query->where('jabatan_tujuan', 'Kepala Bidang')
                ->where('status', 'pending');
      });
})
->whereIn('status', ['proses', 'disetujui'])
->count();
echo "Kombinasi query (seperti controller): {$combined}\n";

// Sample data
echo "\nSample data:\n";
$samples = Permintaan::with(['notaDinas.disposisi'])
    ->whereIn('status', ['proses', 'disetujui'])
    ->limit(5)
    ->get(['permintaan_id', 'bidang', 'status', 'pic_pimpinan']);

foreach ($samples as $p) {
    echo "- ID: {$p->permintaan_id}, Bidang: {$p->bidang}, Status: {$p->status}, PIC: {$p->pic_pimpinan}\n";
    if ($p->notaDinas) {
        foreach ($p->notaDinas as $nd) {
            if ($nd->disposisi) {
                foreach ($nd->disposisi as $d) {
                    echo "  Disposisi -> {$d->jabatan_tujuan} (status: {$d->status})\n";
                }
            }
        }
    }
}
