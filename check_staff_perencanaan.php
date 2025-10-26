<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Permintaan;

echo "Checking Staff Perencanaan Data\n";
echo "================================\n\n";

// 1. Check User
echo "1. Checking User Account:\n";
$user = User::where('role', 'staff_perencanaan')->first();
if ($user) {
    echo "   ✅ Found: {$user->name}\n";
    echo "   Email: {$user->email}\n";
    echo "   Jabatan: {$user->jabatan}\n";
    echo "   Unit Kerja: {$user->unit_kerja}\n\n";
} else {
    echo "   ❌ NOT FOUND - No staff_perencanaan user exists\n\n";
}

// 2. Check Permintaan
echo "2. Checking Permintaan Data:\n";

$allPermintaans = Permintaan::count();
echo "   Total Permintaan: {$allPermintaans}\n";

$disetujuiPermintaans = Permintaan::where('status', 'disetujui')->count();
echo "   Status 'disetujui': {$disetujuiPermintaans}\n";

$prosesPermintaans = Permintaan::where('status', 'proses')->count();
echo "   Status 'proses': {$prosesPermintaans}\n";

// Check pic_pimpinan
$picStaffPerencanaan = Permintaan::where('pic_pimpinan', 'Staff Perencanaan')->count();
echo "   PIC 'Staff Perencanaan': {$picStaffPerencanaan}\n";

// Check permintaan dengan nota dinas
$withNotaDinas = Permintaan::has('notaDinas')->count();
echo "   Dengan Nota Dinas: {$withNotaDinas}\n\n";

// 3. Check specific queries
echo "3. Checking Specific Queries for Staff Perencanaan:\n";

$query1 = Permintaan::whereIn('status', ['disetujui', 'proses'])
    ->where('pic_pimpinan', 'Staff Perencanaan')
    ->count();
echo "   Status disetujui/proses + PIC Staff Perencanaan: {$query1}\n";

$query2 = Permintaan::whereIn('status', ['disetujui', 'proses'])
    ->whereHas('notaDinas', function($q) {
        $q->where(function($query) {
            $query->where('dari', 'like', '%Direktur%')
                  ->orWhere('dari', 'like', '%Wakil Direktur%')
                  ->orWhere('kepada', 'like', '%Staff Perencanaan%');
        });
    })
    ->count();
echo "   Status disetujui/proses + Nota Dinas dari Direktur/Wadir: {$query2}\n\n";

// 4. Show sample data
echo "4. Sample Permintaan Data:\n";
$samples = Permintaan::with(['user', 'notaDinas'])
    ->orderBy('permintaan_id', 'desc')
    ->limit(3)
    ->get();

foreach ($samples as $p) {
    echo "   ID: {$p->permintaan_id}\n";
    echo "   Bidang: {$p->bidang}\n";
    echo "   Status: {$p->status}\n";
    echo "   PIC Pimpinan: {$p->pic_pimpinan}\n";
    echo "   Nota Dinas Count: " . $p->notaDinas->count() . "\n";
    if ($p->notaDinas->count() > 0) {
        $nota = $p->notaDinas->first();
        echo "   Nota Dari: {$nota->dari}\n";
        echo "   Nota Kepada: {$nota->kepada}\n";
    }
    echo "   ---\n";
}

echo "\n================================\n";
echo "Check completed!\n";
