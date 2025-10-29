<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Checking KSO Access Issue ===" . PHP_EOL . PHP_EOL;

// Check permintaan 17
$permintaan = App\Models\Permintaan::find(17);
echo "Permintaan #17:" . PHP_EOL;
echo "  pic_pimpinan: " . $permintaan->pic_pimpinan . PHP_EOL;
echo "  status: " . $permintaan->status . PHP_EOL . PHP_EOL;

// Check all KSO users
echo "Users with role 'kso':" . PHP_EOL;
$ksoUsers = App\Models\User::where('role', 'kso')->get();
foreach ($ksoUsers as $user) {
    echo "  - " . $user->nama . " (Email: " . $user->email . ")" . PHP_EOL;
}
echo PHP_EOL;

// Recommendation
echo "=== Authorization Check ===" . PHP_EOL;
echo "Current logic: pic_pimpinan === 'Bagian KSO' OR pic_pimpinan === user->nama" . PHP_EOL;
echo "Permintaan 17: pic_pimpinan = '" . $permintaan->pic_pimpinan . "'" . PHP_EOL . PHP_EOL;

echo "✅ SHOULD ALLOW: Any user with role='kso' when pic_pimpinan='Bagian KSO'" . PHP_EOL;
echo "Current: Only allows if pic_pimpinan matches user->nama OR is 'Bagian KSO'" . PHP_EOL . PHP_EOL;

echo "=== Suggested Fix ===" . PHP_EOL;
echo "Change authorization to check user->role === 'kso' instead of user->nama" . PHP_EOL;
