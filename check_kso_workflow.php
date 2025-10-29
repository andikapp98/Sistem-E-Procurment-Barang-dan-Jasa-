<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Checking Workflow Chain for Permintaan ID 17 ===" . PHP_EOL . PHP_EOL;

// Full chain query
$result = DB::select("
    SELECT 
        p.permintaan_id,
        p.deskripsi,
        p.pic_pimpinan,
        p.status,
        nd.nota_id,
        nd.no_nota,
        d.disposisi_id,
        pr.perencanaan_id,
        pr.nama_paket,
        k.kso_id,
        k.no_kso
    FROM permintaan p
    LEFT JOIN nota_dinas nd ON p.permintaan_id = nd.permintaan_id
    LEFT JOIN disposisi d ON nd.nota_id = d.nota_id
    LEFT JOIN perencanaan pr ON d.disposisi_id = pr.disposisi_id
    LEFT JOIN kso k ON pr.perencanaan_id = k.perencanaan_id
    WHERE p.permintaan_id = 17
");

echo "Full Chain:" . PHP_EOL;
echo json_encode($result, JSON_PRETTY_PRINT) . PHP_EOL . PHP_EOL;

// Count checks
$notaCount = DB::selectOne("SELECT COUNT(*) as count FROM nota_dinas WHERE permintaan_id = 17");
echo "Nota Dinas Count: " . $notaCount->count . PHP_EOL;

$disposisiCount = DB::selectOne("
    SELECT COUNT(*) as count 
    FROM disposisi d
    JOIN nota_dinas nd ON d.nota_id = nd.nota_id
    WHERE nd.permintaan_id = 17
");
echo "Disposisi Count: " . $disposisiCount->count . PHP_EOL;

$perencanaanCount = DB::selectOne("
    SELECT COUNT(*) as count
    FROM perencanaan pr
    JOIN disposisi d ON pr.disposisi_id = d.disposisi_id
    JOIN nota_dinas nd ON d.nota_id = nd.nota_id
    WHERE nd.permintaan_id = 17
");
echo "Perencanaan Count: " . $perencanaanCount->count . PHP_EOL;

// Test using the controller's UPDATED method logic
echo PHP_EOL . "=== Testing UPDATED Controller Logic ===" . PHP_EOL;

$permintaan = App\Models\Permintaan::find(17);
echo "Permintaan found: " . ($permintaan ? "YES" : "NO") . PHP_EOL;

if ($permintaan) {
    $notaDinas = $permintaan->notaDinas()->latest('tanggal_nota')->first();
    echo "Nota Dinas: " . ($notaDinas ? $notaDinas->nota_id : "NULL") . PHP_EOL;
    
    if ($notaDinas) {
        // UPDATED: Cari disposisi yang sudah punya perencanaan
        $disposisi = $notaDinas->disposisi()
            ->whereHas('perencanaan')
            ->latest('tanggal_disposisi')
            ->first();
        echo "Disposisi (with perencanaan): " . ($disposisi ? $disposisi->disposisi_id : "NULL") . PHP_EOL;
        
        if ($disposisi) {
            $perencanaan = $disposisi->perencanaan()->first();
            echo "Perencanaan: " . ($perencanaan ? $perencanaan->perencanaan_id : "NULL") . PHP_EOL;
            
            if ($perencanaan) {
                $kso = $perencanaan->kso()->first();
                echo "KSO: " . ($kso ? $kso->kso_id : "NULL") . PHP_EOL;
            }
        }
    }
}
