<?php

/**
 * Test Query Kabid - Debug disposisi dari Direktur
 * 
 * File ini untuk testing query Kabid menerima disposisi dari Direktur
 * 
 * Usage: php test_kabid_disposisi_direktur.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Permintaan;
use App\Models\User;
use App\Models\Disposisi;
use Illuminate\Support\Facades\DB;

echo "=================================================================\n";
echo "TEST QUERY: Kabid Menerima Disposisi dari Direktur\n";
echo "=================================================================\n\n";

// 1. Cek users Kabid
echo "1. USERS KABID:\n";
echo "-----------------------------------------------------------\n";
$kabidUsers = User::where('role', 'kepala_bidang')->get(['email', 'name', 'unit_kerja']);
foreach ($kabidUsers as $kabid) {
    echo "- {$kabid->email} | {$kabid->name} | {$kabid->unit_kerja}\n";
}
echo "\n";

// 2. Cek permintaan yang sudah di-approve Direktur
echo "2. PERMINTAAN YANG SUDAH DI-APPROVE DIREKTUR:\n";
echo "-----------------------------------------------------------\n";
$direkturApprovals = DB::table('permintaan as p')
    ->leftJoin('nota_dinas as nd', 'p.permintaan_id', '=', 'nd.permintaan_id')
    ->leftJoin('disposisi as d', 'nd.nota_id', '=', 'd.nota_id')
    ->where('d.catatan', 'LIKE', '%Disetujui oleh Direktur%')
    ->where('p.status', 'proses')
    ->select(
        'p.permintaan_id',
        'p.klasifikasi_permintaan',
        'p.kabid_tujuan',
        'p.pic_pimpinan',
        'p.status',
        'd.jabatan_tujuan',
        'd.catatan'
    )
    ->orderBy('p.permintaan_id', 'desc')
    ->limit(5)
    ->get();

if ($direkturApprovals->isEmpty()) {
    echo "❌ TIDAK ADA permintaan yang di-approve Direktur\n";
    echo "   Silakan jalankan: php artisan db:seed --class=DirekturWorkflowSeeder\n";
    echo "   Kemudian login sebagai Direktur dan approve permintaan\n";
} else {
    foreach ($direkturApprovals as $approval) {
        echo "Permintaan ID: {$approval->permintaan_id}\n";
        echo "  - Klasifikasi: {$approval->klasifikasi_permintaan}\n";
        echo "  - Kabid Tujuan: {$approval->kabid_tujuan}\n";
        echo "  - PIC: {$approval->pic_pimpinan}\n";
        echo "  - Status: {$approval->status}\n";
        echo "  - Disposisi Tujuan: {$approval->jabatan_tujuan}\n";
        echo "  - Catatan: " . substr($approval->catatan, 0, 80) . "...\n";
        echo "\n";
    }
}
echo "\n";

// 3. Test query untuk setiap Kabid
echo "3. TEST QUERY UNTUK SETIAP KABID:\n";
echo "-----------------------------------------------------------\n";

$kabidList = [
    [
        'email' => 'kabid.yanmed@rsud.id',
        'unit_kerja' => 'Bidang Pelayanan Medis',
        'klasifikasi' => ['Medis', 'medis']
    ],
    [
        'email' => 'kabid.penunjang@rsud.id',
        'unit_kerja' => 'Bidang Penunjang Medis',
        'klasifikasi' => ['Penunjang', 'penunjang_medis']
    ],
    [
        'email' => 'kabid.keperawatan@rsud.id',
        'unit_kerja' => 'Bidang Keperawatan',
        'klasifikasi' => ['Non Medis', 'non_medis']
    ]
];

foreach ($kabidList as $kabidInfo) {
    echo "\n👤 KABID: {$kabidInfo['email']}\n";
    echo "   Unit Kerja: {$kabidInfo['unit_kerja']}\n";
    echo "   Klasifikasi: " . implode(', ', $kabidInfo['klasifikasi']) . "\n";
    echo "\n";
    
    $user = User::where('email', $kabidInfo['email'])->first();
    
    if (!$user) {
        echo "   ❌ User tidak ditemukan\n";
        continue;
    }
    
    // Query seperti di controller
    $permintaans = Permintaan::with(['user', 'notaDinas.disposisi'])
        ->where('status', 'proses')
        ->where(function($q) use ($user, $kabidInfo) {
            // Kondisi 1: Permintaan baru dari Kepala Instalasi
            $q->where(function($subQ) use ($user, $kabidInfo) {
                $subQ->where('pic_pimpinan', 'LIKE', '%Kepala Bidang%');
                if ($kabidInfo['klasifikasi']) {
                    $subQ->whereIn('klasifikasi_permintaan', $kabidInfo['klasifikasi']);
                }
            })
            // Kondisi 2: Disposisi balik dari Direktur
            ->orWhere(function($subQ) use ($user) {
                $subQ->where('kabid_tujuan', 'LIKE', '%' . $user->unit_kerja . '%')
                     ->whereHas('notaDinas.disposisi', function($dispQ) use ($user) {
                         $dispQ->where('jabatan_tujuan', 'LIKE', '%' . $user->unit_kerja . '%')
                               ->where('catatan', 'LIKE', '%Disetujui oleh Direktur%');
                     });
            });
        })
        ->get();
    
    echo "   📊 HASIL QUERY:\n";
    echo "   Total: " . $permintaans->count() . " permintaan\n";
    
    if ($permintaans->isEmpty()) {
        echo "   ❌ Tidak ada permintaan ditemukan\n";
    } else {
        foreach ($permintaans as $p) {
            // Cek apakah ini dari Direktur
            $disposisiDariDirektur = $p->notaDinas()
                ->with('disposisi')
                ->get()
                ->pluck('disposisi')
                ->flatten()
                ->first(function($d) use ($user) {
                    return str_contains($d->jabatan_tujuan ?? '', $user->unit_kerja) &&
                           str_contains($d->catatan ?? '', 'Disetujui oleh Direktur');
                });
            
            $source = $disposisiDariDirektur ? '🔄 DARI DIREKTUR' : '📥 BARU';
            
            echo "   ✓ [{$source}] Permintaan #{$p->permintaan_id}\n";
            echo "     - Klasifikasi: {$p->klasifikasi_permintaan}\n";
            echo "     - Kabid Tujuan: {$p->kabid_tujuan}\n";
            echo "     - PIC: {$p->pic_pimpinan}\n";
            echo "     - Deskripsi: " . substr($p->deskripsi, 0, 50) . "...\n";
            
            if ($disposisiDariDirektur) {
                echo "     - Disposisi: {$disposisiDariDirektur->jabatan_tujuan}\n";
                echo "     - Catatan: " . substr($disposisiDariDirektur->catatan, 0, 60) . "...\n";
            }
            echo "\n";
        }
    }
}

echo "\n=================================================================\n";
echo "4. REKOMENDASI:\n";
echo "=================================================================\n";
echo "\n";

if ($direkturApprovals->isEmpty()) {
    echo "❌ Tidak ada data testing\n";
    echo "\n";
    echo "SOLUSI:\n";
    echo "1. Jalankan seeder:\n";
    echo "   php artisan db:seed --class=DirekturWorkflowSeeder\n";
    echo "\n";
    echo "2. Login sebagai Direktur:\n";
    echo "   Email: direktur@rsud.id\n";
    echo "   Password: password\n";
    echo "\n";
    echo "3. Approve salah satu permintaan\n";
    echo "\n";
    echo "4. Jalankan test ini lagi:\n";
    echo "   php test_kabid_disposisi_direktur.php\n";
} else {
    echo "✅ Ada data testing dari Direktur\n";
    echo "\n";
    echo "LANGKAH SELANJUTNYA:\n";
    echo "1. Login sebagai Kabid sesuai klasifikasi permintaan\n";
    echo "2. Cek dashboard/index - permintaan seharusnya muncul\n";
    echo "3. Jika tidak muncul, cek:\n";
    echo "   - Browser cache (Ctrl+Shift+R)\n";
    echo "   - Frontend assets (npm run build)\n";
    echo "   - Laravel cache (php artisan cache:clear)\n";
}

echo "\n=================================================================\n";
