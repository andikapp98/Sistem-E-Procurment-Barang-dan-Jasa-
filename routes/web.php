<?php

use App\Http\Controllers\ProfileController;
use App\Models\Permintaan;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    $totalPermintaan = Permintaan::count();
    $permintaanDiajukan = Permintaan::where('status', 'diajukan')->count();
    $permintaanProses = Permintaan::where('status', 'proses')->count();
    $permintaanDisetujui = Permintaan::where('status', 'disetujui')->count();

    return Inertia::render('Dashboard', [
        'totalPermintaan' => $totalPermintaan,
        'permintaanDiajukan' => $permintaanDiajukan,
        'permintaanProses' => $permintaanProses,
        'permintaanDisetujui' => $permintaanDisetujui,
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

use App\Http\Controllers\PermintaanController;

Route::resource('permintaan', PermintaanController::class)
    ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'])
    ->middleware(['auth', 'verified']);

require __DIR__.'/auth.php';
