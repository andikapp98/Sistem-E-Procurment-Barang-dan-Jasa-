<?php

// Test akses ke index method langsung
use App\Http\Controllers\KepalaBidangController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Login as kepala bidang
$user = \App\Models\User::where('jabatan', 'Kepala Bidang')->first();
if (!$user) {
    echo "ERROR: User Kepala Bidang tidak ditemukan!\n";
    exit;
}

Auth::login($user);
echo "Logged in as: {$user->nama} ({$user->jabatan})\n\n";

// Test controller method
$controller = new KepalaBidangController();
$request = Request::create('/kepala-bidang/index', 'GET');

try {
    $response = $controller->index($request);
    $data = $response->getData();
    
    echo "Response successful!\n";
    echo "Props keys: " . implode(', ', array_keys($data['props'])) . "\n\n";
    
    if (isset($data['props']['permintaans'])) {
        $permintaans = $data['props']['permintaans'];
        if (is_object($permintaans)) {
            echo "Permintaans type: Object (paginated)\n";
            echo "Total: " . ($permintaans->total ?? 'N/A') . "\n";
            echo "Current page: " . ($permintaans->currentPage ?? 'N/A') . "\n";
            echo "Per page: " . ($permintaans->perPage ?? 'N/A') . "\n";
            echo "Data count: " . count($permintaans->items() ?? []) . "\n";
        } else {
            echo "Permintaans type: " . gettype($permintaans) . "\n";
        }
    } else {
        echo "ERROR: permintaans not found in props!\n";
    }
    
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
