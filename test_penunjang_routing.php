<?php

/**
 * Test Routing Kabid Penunjang Medis
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Permintaan;
use App\Models\User;
use Illuminate\Support\Facades\DB;

echo "=================================================================\n";
echo "TEST: Routing Kabid Penunjang Medis\n";
echo "=================================================================\n\n";

// 1. Cek user Kabid Penunjang
echo "1. USER KABID PENUNJANG MEDIS:\n";
echo "-----------------------------------------------------------\n";
$kabidPenunjang = User::where('email', 'kabid.penunjang@rsud.id')->first();
if ($kabidPenunjang) {
    echo "✓ Email: {$kabidPenunjang->email}\n";
    echo "  Nama: {$kabidPenunjang->name}\n";
    echo "  Unit Kerja: {$kabidPenunjang->unit_kerja}\n";
} else {
    echo "❌ User tidak ditemukan!\n";
    echo "   Jalankan: php artisan db:seed --class=UserSeeder\n";
    exit(1);
}
echo "\n";

// 2. Cek permintaan dengan klasifikasi penunjang
echo "2. PERMINTAAN DENGAN KLASIFIKASI PENUNJANG:\n";
echo "-----------------------------------------------------------\n";
$permintaanPenunjang = Permintaan::where(function($q) {
    $q->where('klasifikasi_permintaan', 'Penunjang')
      ->orWhere('klasifikasi_permintaan', 'penunjang_medis')
      ->orWhere('klasifikasi_permintaan', 'LIKE', '%penunjang%');
})->get();

if ($permintaanPenunjang->isEmpty()) {
    echo "❌ TIDAK ADA permintaan dengan klasifikasi penunjang\n";
    echo "\n";
    echo "SOLUSI:\n";
    echo "1. Buat permintaan baru dari Laboratorium/Radiologi\n";
    echo "2. Set klasifikasi_permintaan = 'penunjang_medis'\n";
    echo "3. Atau jalankan seeder dengan permintaan penunjang\n";
} else {
    echo "✓ Total: {$permintaanPenunjang->count()} permintaan\n\n";
    foreach ($permintaanPenunjang as $p) {
        echo "Permintaan ID: {$p->permintaan_id}\n";
        echo "  - Klasifikasi: {$p->klasifikasi_permintaan}\n";
        echo "  - Kabid Tujuan: {$p->kabid_tujuan}\n";
        echo "  - PIC: {$p->pic_pimpinan}\n";
        echo "  - Status: {$p->status}\n";
        echo "\n";
    }
}
echo "\n";

// 3. Cek disposisi dari Direktur ke Penunjang
echo "3. DISPOSISI DARI DIREKTUR KE PENUNJANG:\n";
echo "-----------------------------------------------------------\n";
$disposisiPenunjang = DB::table('permintaan as p')
    ->join('nota_dinas as nd', 'p.permintaan_id', '=', 'nd.permintaan_id')
    ->join('disposisi as d', 'nd.nota_id', '=', 'd.nota_id')
    ->where('d.jabatan_tujuan', 'LIKE', '%Penunjang%')
    ->where('d.catatan', 'LIKE', '%Disetujui oleh Direktur%')
    ->select('p.*', 'd.jabatan_tujuan', 'd.catatan')
    ->get();

if ($disposisiPenunjang->isEmpty()) {
    echo "❌ TIDAK ADA disposisi dari Direktur ke Penunjang\n";
    echo "\n";
    echo "CEK:\n";
    echo "1. Apakah ada permintaan penunjang yang sudah di-approve Direktur?\n";
    echo "2. Apakah mapping klasifikasi sudah benar?\n";
} else {
    echo "✓ Total: {$disposisiPenunjang->count()} disposisi\n\n";
    foreach ($disposisiPenunjang as $d) {
        echo "Permintaan ID: {$d->permintaan_id}\n";
        echo "  - Jabatan Tujuan: {$d->jabatan_tujuan}\n";
        echo "  - Catatan: " . substr($d->catatan, 0, 60) . "...\n";
        echo "\n";
    }
}
echo "\n";

// 4. Test query Kabid Penunjang
if ($kabidPenunjang) {
    echo "4. TEST QUERY KABID PENUNJANG:\n";
    echo "-----------------------------------------------------------\n";
    
    $klasifikasiArray = ['Penunjang', 'penunjang_medis'];
    
    $permintaans = Permintaan::with(['user', 'notaDinas.disposisi'])
        ->where('status', 'proses')
        ->where(function($q) use ($kabidPenunjang, $klasifikasiArray) {
            // Kondisi 1: Permintaan baru dari Kepala Instalasi
            $q->where(function($subQ) use ($kabidPenunjang, $klasifikasiArray) {
                $subQ->where('pic_pimpinan', 'LIKE', '%Kepala Bidang%');
                if ($klasifikasiArray) {
                    $subQ->whereIn('klasifikasi_permintaan', $klasifikasiArray);
                }
            })
            // Kondisi 2: Disposisi balik dari Direktur
            ->orWhere(function($subQ) use ($kabidPenunjang) {
                $subQ->where('kabid_tujuan', 'LIKE', '%' . $kabidPenunjang->unit_kerja . '%')
                     ->whereHas('notaDinas.disposisi', function($dispQ) use ($kabidPenunjang) {
                         $dispQ->where('jabatan_tujuan', 'LIKE', '%' . $kabidPenunjang->unit_kerja . '%')
                               ->where('catatan', 'LIKE', '%Disetujui oleh Direktur%');
                     });
            });
        })
        ->get();
    
    echo "Unit Kerja: {$kabidPenunjang->unit_kerja}\n";
    echo "Klasifikasi: " . implode(', ', $klasifikasiArray) . "\n";
    echo "Total: {$permintaans->count()} permintaan\n";
    echo "\n";
    
    if ($permintaans->isEmpty()) {
        echo "❌ Tidak ada permintaan untuk Kabid Penunjang\n";
        echo "\n";
        echo "KEMUNGKINAN PENYEBAB:\n";
        echo "1. Tidak ada permintaan dengan klasifikasi 'Penunjang' atau 'penunjang_medis'\n";
        echo "2. Direktur belum approve permintaan penunjang\n";
        echo "3. kabid_tujuan tidak di-set dengan benar\n";
    } else {
        foreach ($permintaans as $p) {
            echo "✓ Permintaan #{$p->permintaan_id}\n";
            echo "  - Klasifikasi: {$p->klasifikasi_permintaan}\n";
            echo "  - Kabid Tujuan: {$p->kabid_tujuan}\n";
            echo "  - PIC: {$p->pic_pimpinan}\n";
            echo "  - Deskripsi: " . substr($p->deskripsi, 0, 50) . "...\n";
            echo "\n";
        }
    }
}

echo "\n";
echo "=================================================================\n";
echo "REKOMENDASI:\n";
echo "=================================================================\n";
echo "\n";

if ($permintaanPenunjang->isEmpty()) {
    echo "📝 LANGKAH UNTUK MEMBUAT PERMINTAAN PENUNJANG:\n";
    echo "\n";
    echo "1. Login sebagai Kepala Instalasi Laboratorium:\n";
    echo "   Email: kepala.lab@rsud.id\n";
    echo "   Password: password\n";
    echo "\n";
    echo "2. Buat permintaan baru untuk:\n";
    echo "   - Reagen laboratorium\n";
    echo "   - Film radiologi\n";
    echo "   - Alat penunjang medis lainnya\n";
    echo "\n";
    echo "3. Saat approve, pastikan klasifikasi_permintaan = 'penunjang_medis'\n";
    echo "\n";
    echo "4. Workflow selanjutnya:\n";
    echo "   - Kabid Penunjang review & approve\n";
    echo "   - Direktur approve → routing ke 'Bidang Penunjang Medis'\n";
    echo "   - Kabid Penunjang terima kembali\n";
    echo "   - Kabid kirim ke Staff Perencanaan\n";
} else {
    echo "✓ Ada permintaan penunjang di database\n";
    echo "\n";
    echo "LANGKAH SELANJUTNYA:\n";
    echo "\n";
    echo "1. Login Direktur → Approve permintaan penunjang\n";
    echo "   Email: direktur@rsud.id\n";
    echo "\n";
    echo "2. Login Kabid Penunjang → Cek dashboard\n";
    echo "   Email: kabid.penunjang@rsud.id\n";
    echo "   Expected: Permintaan muncul setelah Direktur approve\n";
    echo "\n";
    echo "3. Jika tidak muncul:\n";
    echo "   - Clear cache: php artisan cache:clear\n";
    echo "   - Hard refresh browser: Ctrl+Shift+R\n";
    echo "   - Cek query di database\n";
}

echo "\n=================================================================\n";
