<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Permintaan;

echo "=== Updating Kabid Tujuan untuk Non Medis ===\n\n";

$permintaans = Permintaan::whereIn('klasifikasi_permintaan', ['Non Medis', 'non_medis'])
    ->where(function($q) {
        $q->where('kabid_tujuan', '!=', 'Bidang Umum & Keuangan')
          ->orWhereNull('kabid_tujuan');
    })
    ->get();

echo "Found " . $permintaans->count() . " permintaan(s) to update\n\n";

$updated = 0;
foreach ($permintaans as $permintaan) {
    echo "Updating Permintaan #{$permintaan->permintaan_id}:\n";
    echo "  Old kabid_tujuan: " . ($permintaan->kabid_tujuan ?? 'NULL') . "\n";
    
    $permintaan->kabid_tujuan = 'Bidang Umum & Keuangan';
    $permintaan->save();
    
    echo "  New kabid_tujuan: {$permintaan->kabid_tujuan}\n\n";
    $updated++;
}

echo "✅ Updated {$updated} permintaan(s)\n";
