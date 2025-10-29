<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== Checking kabid_tujuan Column ===\n\n";

// Check if column exists
if (Schema::hasColumn('permintaan', 'kabid_tujuan')) {
    echo "✅ Column 'kabid_tujuan' EXISTS in permintaan table\n\n";
    
    // Get column info
    $columns = DB::select("SHOW COLUMNS FROM permintaan WHERE Field = 'kabid_tujuan'");
    
    if (!empty($columns)) {
        $col = $columns[0];
        echo "Column Details:\n";
        echo "  - Field: " . $col->Field . "\n";
        echo "  - Type: " . $col->Type . "\n";
        echo "  - Null: " . $col->Null . "\n";
        echo "  - Key: " . ($col->Key ?: 'None') . "\n";
        echo "  - Default: " . ($col->Default ?: 'NULL') . "\n";
        echo "  - Extra: " . ($col->Extra ?: 'None') . "\n";
    }
    
    // Sample data
    echo "\n=== Sample Data ===\n";
    $sample = DB::table('permintaan')
        ->select('permintaan_id', 'klasifikasi_permintaan', 'kabid_tujuan')
        ->whereNotNull('kabid_tujuan')
        ->limit(5)
        ->get();
    
    if ($sample->count() > 0) {
        foreach ($sample as $row) {
            echo "ID: {$row->permintaan_id} | Klasifikasi: {$row->klasifikasi_permintaan} | Kabid: {$row->kabid_tujuan}\n";
        }
    } else {
        echo "No data with kabid_tujuan set\n";
    }
    
} else {
    echo "❌ Column 'kabid_tujuan' DOES NOT EXIST in permintaan table\n";
    echo "Need to run migration to add this column\n";
}

echo "\n=== All Permintaan Columns ===\n";
$allColumns = DB::select("SHOW COLUMNS FROM permintaan");
foreach ($allColumns as $col) {
    echo "  - {$col->Field} ({$col->Type})\n";
}
