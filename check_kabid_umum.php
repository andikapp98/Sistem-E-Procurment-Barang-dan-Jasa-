<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Checking Kabid Umum User ===\n\n";

// Check by email
$kabidUmum = DB::table('users')->where('email', 'kabid.umum@rsud.id')->first();

if ($kabidUmum) {
    echo "✅ Kabid Umum FOUND!\n";
    echo "ID: " . $kabidUmum->id . "\n";
    echo "Name: " . $kabidUmum->name . "\n";
    echo "Email: " . $kabidUmum->email . "\n";
    echo "Role: " . $kabidUmum->role . "\n";
    echo "Jabatan: " . ($kabidUmum->jabatan ?? 'NULL') . "\n";
    echo "Unit Kerja: " . ($kabidUmum->unit_kerja ?? 'NULL') . "\n";
} else {
    echo "❌ Kabid Umum NOT FOUND!\n";
}

echo "\n=== All Kepala Bidang Users ===\n\n";

$allKabid = DB::table('users')->where('role', 'kepala_bidang')->get();

if ($allKabid->count() > 0) {
    foreach ($allKabid as $kabid) {
        echo "- ID: {$kabid->id}, Name: {$kabid->name}, Email: {$kabid->email}\n";
        echo "  Jabatan: " . ($kabid->jabatan ?? 'NULL') . ", Unit: " . ($kabid->unit_kerja ?? 'NULL') . "\n\n";
    }
} else {
    echo "No kepala_bidang users found.\n";
}

echo "\n=== Check for Duplicates ===\n\n";

$duplicates = DB::table('users')
    ->select('email', DB::raw('count(*) as count'))
    ->where('role', 'kepala_bidang')
    ->groupBy('email')
    ->having('count', '>', 1)
    ->get();

if ($duplicates->count() > 0) {
    echo "⚠️ DUPLICATES FOUND:\n";
    foreach ($duplicates as $dup) {
        echo "- Email: {$dup->email} (Count: {$dup->count})\n";
    }
} else {
    echo "✅ No duplicates found.\n";
}
