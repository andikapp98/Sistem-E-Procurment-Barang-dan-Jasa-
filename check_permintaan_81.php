<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Checking Permintaan ID 81 ===\n\n";

$permintaan = DB::table('permintaan')->where('permintaan_id', 81)->first();

if ($permintaan) {
    echo "✅ Permintaan #81 FOUND!\n\n";
    echo "ID: " . $permintaan->permintaan_id . "\n";
    echo "Bidang: " . ($permintaan->bidang ?? 'NULL') . "\n";
    echo "Klasifikasi: " . ($permintaan->klasifikasi_permintaan ?? 'NULL') . "\n";
    echo "Kabid Tujuan: " . ($permintaan->kabid_tujuan ?? 'NULL') . "\n";
    echo "Status: " . ($permintaan->status ?? 'NULL') . "\n";
    echo "User ID: " . ($permintaan->user_id ?? 'NULL') . "\n\n";
    
    // Check access for Kabid Umum
    $kabidUmum = DB::table('users')->where('email', 'kabid.umum@rsud.id')->first();
    
    if ($kabidUmum) {
        echo "=== Access Check for Kabid Umum ===\n\n";
        echo "Kabid Unit Kerja: " . $kabidUmum->unit_kerja . "\n\n";
        
        $shouldAccess = false;
        $reasons = [];
        
        // Check klasifikasi
        if (in_array($permintaan->klasifikasi_permintaan, ['Non Medis', 'non_medis'])) {
            echo "✅ Klasifikasi cocok: " . $permintaan->klasifikasi_permintaan . "\n";
            $shouldAccess = true;
        } else {
            echo "❌ Klasifikasi tidak cocok: " . ($permintaan->klasifikasi_permintaan ?? 'NULL') . "\n";
            $reasons[] = "Klasifikasi bukan Non Medis";
        }
        
        // Check kabid_tujuan
        if ($permintaan->kabid_tujuan && str_contains($permintaan->kabid_tujuan, 'Umum')) {
            echo "✅ Kabid Tujuan cocok: " . $permintaan->kabid_tujuan . "\n";
            $shouldAccess = true;
        } else {
            echo "❌ Kabid Tujuan tidak cocok: " . ($permintaan->kabid_tujuan ?? 'NULL') . "\n";
            $reasons[] = "Kabid Tujuan bukan untuk Bidang Umum";
        }
        
        echo "\n";
        if ($shouldAccess) {
            echo "✅ SHOULD HAVE ACCESS\n";
        } else {
            echo "❌ 403 FORBIDDEN - CORRECT!\n";
            echo "Alasan:\n";
            foreach ($reasons as $reason) {
                echo "  - " . $reason . "\n";
            }
        }
    }
    
    // Suggest which Kabid should access
    echo "\n=== Routing Suggestion ===\n\n";
    
    $klasifikasi = $permintaan->klasifikasi_permintaan;
    if (in_array($klasifikasi, ['Medis', 'medis'])) {
        echo "➜ Permintaan ini untuk: Kabid Pelayanan Medis (kabid.yanmed@rsud.id)\n";
    } elseif (in_array($klasifikasi, ['Penunjang', 'penunjang_medis'])) {
        echo "➜ Permintaan ini untuk: Kabid Penunjang Medis (kabid.penunjang@rsud.id)\n";
    } elseif (in_array($klasifikasi, ['Non Medis', 'non_medis'])) {
        echo "➜ Permintaan ini untuk: Kabid Umum (kabid.umum@rsud.id)\n";
        echo "   Tapi kabid_tujuan perlu diupdate ke: Bidang Umum & Keuangan\n";
    } else {
        echo "➜ Klasifikasi tidak dikenali: " . ($klasifikasi ?? 'NULL') . "\n";
    }
    
} else {
    echo "❌ Permintaan #81 NOT FOUND!\n";
}

echo "\n=== All Permintaan for Kabid Umum ===\n\n";

$permintaansForKabidUmum = DB::table('permintaan')
    ->whereIn('klasifikasi_permintaan', ['Non Medis', 'non_medis'])
    ->orWhere('kabid_tujuan', 'LIKE', '%Umum%')
    ->orderBy('permintaan_id', 'DESC')
    ->limit(10)
    ->get(['permintaan_id', 'bidang', 'klasifikasi_permintaan', 'kabid_tujuan', 'status']);

if ($permintaansForKabidUmum->count() > 0) {
    echo "Permintaan yang bisa diakses Kabid Umum:\n\n";
    foreach ($permintaansForKabidUmum as $p) {
        echo "ID: {$p->permintaan_id} | {$p->bidang} | {$p->klasifikasi_permintaan} | ";
        echo "Kabid: " . ($p->kabid_tujuan ?? 'NULL') . " | Status: {$p->status}\n";
    }
} else {
    echo "Tidak ada permintaan untuk Kabid Umum\n";
}
