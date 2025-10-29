<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Checking Permintaan ID 84 ===\n\n";

// Check permintaan 84
$permintaan = DB::table('permintaan')->where('permintaan_id', 84)->first();

if ($permintaan) {
    echo "✅ Permintaan #84 FOUND!\n\n";
    echo "ID: " . $permintaan->permintaan_id . "\n";
    echo "Bidang: " . ($permintaan->bidang ?? 'NULL') . "\n";
    echo "Klasifikasi: " . ($permintaan->klasifikasi_permintaan ?? 'NULL') . "\n";
    echo "Kabid Tujuan: " . ($permintaan->kabid_tujuan ?? 'NULL') . "\n";
    echo "Status: " . ($permintaan->status ?? 'NULL') . "\n";
    echo "Deskripsi: " . substr($permintaan->deskripsi ?? '', 0, 100) . "...\n";
    echo "User ID: " . ($permintaan->user_id ?? 'NULL') . "\n";
    
    // Check user
    if ($permintaan->user_id) {
        $user = DB::table('users')->where('id', $permintaan->user_id)->first();
        if ($user) {
            echo "\nPemohon:\n";
            echo "  - Nama: " . $user->name . "\n";
            echo "  - Email: " . $user->email . "\n";
            echo "  - Unit: " . ($user->unit_kerja ?? 'NULL') . "\n";
        }
    }
    
    // Check nota dinas
    $notaDinas = DB::table('nota_dinas')->where('permintaan_id', 84)->get();
    if ($notaDinas->count() > 0) {
        echo "\nNota Dinas:\n";
        foreach ($notaDinas as $nota) {
            echo "  - ID: {$nota->nota_id}, Dari: {$nota->dari}, Kepada: {$nota->kepada}\n";
        }
    }
    
} else {
    echo "❌ Permintaan #84 NOT FOUND!\n";
}

echo "\n=== Checking Kabid Umum Access ===\n\n";

$kabidUmum = DB::table('users')->where('email', 'kabid.umum@rsud.id')->first();

if ($kabidUmum) {
    echo "✅ Kabid Umum User Found\n";
    echo "Unit Kerja: " . $kabidUmum->unit_kerja . "\n\n";
    
    // Check if permintaan should be accessible
    if ($permintaan) {
        $shouldAccess = false;
        
        // Check by klasifikasi
        if (in_array($permintaan->klasifikasi_permintaan, ['Non Medis', 'non_medis'])) {
            echo "✅ Klasifikasi cocok (Non Medis)\n";
            $shouldAccess = true;
        } else {
            echo "❌ Klasifikasi tidak cocok: " . ($permintaan->klasifikasi_permintaan ?? 'NULL') . "\n";
        }
        
        // Check by kabid_tujuan
        if ($permintaan->kabid_tujuan && str_contains($permintaan->kabid_tujuan, 'Umum')) {
            echo "✅ Kabid Tujuan cocok: " . $permintaan->kabid_tujuan . "\n";
            $shouldAccess = true;
        } else {
            echo "❌ Kabid Tujuan tidak cocok: " . ($permintaan->kabid_tujuan ?? 'NULL') . "\n";
        }
        
        echo "\n";
        if ($shouldAccess) {
            echo "✅ KABID UMUM BERHAK AKSES PERMINTAAN INI\n";
        } else {
            echo "❌ KABID UMUM TIDAK BERHAK AKSES PERMINTAAN INI (403 Expected)\n";
        }
    }
}
