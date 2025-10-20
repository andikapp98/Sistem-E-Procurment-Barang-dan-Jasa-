<?php

use App\Models\User;

echo "=== ALL USERS ===\n\n";
$users = User::select('nama', 'email', 'jabatan', 'role')->get();

foreach ($users as $u) {
    echo "{$u->nama} - {$u->email}\n";
    echo "  Jabatan: {$u->jabatan}\n";
    echo "  Role: {$u->role}\n\n";
}

echo "\n=== KEPALA BIDANG ===\n\n";
$kabid = User::where('role', 'kepala_bidang')->get();
echo "Found " . $kabid->count() . " users\n\n";

foreach ($kabid as $k) {
    echo "{$k->nama} - {$k->email}\n";
    echo "  Jabatan: {$k->jabatan}\n";
    echo "  Role: {$k->role}\n\n";
}
