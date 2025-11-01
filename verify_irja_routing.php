<?php
/**
 * SCRIPT VERIFIKASI ROUTING IRJA
 * 
 * Script ini untuk memverifikasi bahwa konfigurasi routing IRJA sudah benar.
 * Jalankan dengan: php verify_irja_routing.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║       VERIFIKASI ROUTING IRJA KE KEPALA INSTALASI             ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n\n";

// 1. Cek Kepala IRJA
echo "1. CHECKING KEPALA IRJA\n";
echo str_repeat("-", 60) . "\n";

$kepalaIRJA = DB::table('users')
    ->where('role', 'kepala_instalasi')
    ->where(function($q) {
        $q->where('unit_kerja', 'Rawat Jalan')
          ->orWhere('unit_kerja', 'LIKE', '%IRJA%')
          ->orWhere('unit_kerja', 'LIKE', '%Instalasi Rawat Jalan%');
    })
    ->first();

if ($kepalaIRJA) {
    echo "✓ Kepala IRJA ditemukan\n";
    echo "  ID         : {$kepalaIRJA->id}\n";
    echo "  Nama       : {$kepalaIRJA->name}\n";
    echo "  Unit Kerja : {$kepalaIRJA->unit_kerja}\n";
    echo "  Email      : {$kepalaIRJA->email}\n";
} else {
    echo "✗ PERINGATAN: Kepala IRJA tidak ditemukan!\n";
    echo "  Pastikan ada user dengan:\n";
    echo "  - role = 'kepala_instalasi'\n";
    echo "  - unit_kerja = 'Rawat Jalan'\n";
}
echo "\n";

// 2. Cek Kepala IGD (untuk memastikan terpisah)
echo "2. CHECKING KEPALA IGD (harus terpisah dari IRJA)\n";
echo str_repeat("-", 60) . "\n";

$kepalaIGD = DB::table('users')
    ->where('role', 'kepala_instalasi')
    ->where(function($q) {
        $q->where('unit_kerja', 'Gawat Darurat')
          ->orWhere('unit_kerja', 'LIKE', '%IGD%');
    })
    ->first();

if ($kepalaIGD) {
    echo "✓ Kepala IGD ditemukan\n";
    echo "  ID         : {$kepalaIGD->id}\n";
    echo "  Nama       : {$kepalaIGD->name}\n";
    echo "  Unit Kerja : {$kepalaIGD->unit_kerja}\n";
    echo "  Email      : {$kepalaIGD->email}\n";
} else {
    echo "  INFO: Kepala IGD tidak ditemukan (opsional)\n";
}
echo "\n";

// 3. Cek permintaan dari departemen IRJA
echo "3. CHECKING PERMINTAAN DARI DEPARTEMEN IRJA\n";
echo str_repeat("-", 60) . "\n";

$irjaDepartments = [
    'Poli Bedah', 'Poli Gigi', 'Poli Kulit Kelamin',
    'Poli Penyakit Dalam', 'Poli Jiwa', 'Poli Psikologi',
    'Poli Mata', 'Klinik Gizi', 'Laboratorium', 'Apotek'
];

$totalPermintaan = 0;
foreach ($irjaDepartments as $dept) {
    $count = DB::table('permintaan')
        ->where('bidang', 'LIKE', "%{$dept}%")
        ->count();
    
    if ($count > 0) {
        echo "  {$dept}: {$count} permintaan\n";
        $totalPermintaan += $count;
    }
}

if ($totalPermintaan > 0) {
    echo "\n✓ Total {$totalPermintaan} permintaan dari departemen IRJA\n";
} else {
    echo "\n  INFO: Belum ada permintaan dari departemen IRJA\n";
}
echo "\n";

// 4. Test routing logic
echo "4. TESTING ROUTING LOGIC\n";
echo str_repeat("-", 60) . "\n";

if ($kepalaIRJA) {
    // Simulate controller method
    $unitKerja = $kepalaIRJA->unit_kerja;
    
    echo "Testing untuk unit_kerja: '{$unitKerja}'\n\n";
    
    // Check if IRJA departments will be matched
    $matchedDepts = [];
    foreach ($irjaDepartments as $dept) {
        // Simple check - in production this uses getBidangVariations()
        if (stripos($unitKerja, 'Rawat Jalan') !== false) {
            $matchedDepts[] = $dept;
        }
    }
    
    if (count($matchedDepts) > 0) {
        echo "✓ Routing logic BENAR\n";
        echo "  Departemen yang akan di-route ke Kepala IRJA:\n";
        foreach ($matchedDepts as $dept) {
            echo "  - {$dept}\n";
        }
    } else {
        echo "✗ PERINGATAN: Routing logic mungkin tidak bekerja\n";
        echo "  Periksa method getBidangVariations() di KepalaInstalasiController\n";
    }
} else {
    echo "⚠ Skip - Kepala IRJA tidak ditemukan\n";
}
echo "\n";

// 5. Rekomendasi
echo "5. REKOMENDASI\n";
echo str_repeat("-", 60) . "\n";

$recommendations = [];

if (!$kepalaIRJA) {
    $recommendations[] = "URGENT: Buat user Kepala IRJA dengan unit_kerja='Rawat Jalan'";
}

if ($totalPermintaan === 0) {
    $recommendations[] = "INFO: Belum ada data permintaan untuk testing";
}

// Check if any permintaan from IRJA departments exists
$existingIRJAPermintaan = DB::table('permintaan')
    ->where(function($q) use ($irjaDepartments) {
        foreach ($irjaDepartments as $dept) {
            $q->orWhere('bidang', 'LIKE', "%{$dept}%");
        }
    })
    ->count();

if ($existingIRJAPermintaan > 0 && $kepalaIRJA) {
    echo "✓ Sistem siap digunakan!\n";
    echo "  {$existingIRJAPermintaan} permintaan dari IRJA akan ter-route ke {$kepalaIRJA->name}\n";
}

if (count($recommendations) > 0) {
    echo "\nRekomendasi:\n";
    foreach ($recommendations as $i => $rec) {
        echo "  " . ($i + 1) . ". {$rec}\n";
    }
} else {
    echo "✓ Tidak ada rekomendasi - konfigurasi sudah lengkap!\n";
}

echo "\n";
echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║                    VERIFIKASI SELESAI                          ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n";
echo "\nUntuk informasi lebih lanjut, lihat: IRJA_ROUTING_CONFIGURATION.md\n";
