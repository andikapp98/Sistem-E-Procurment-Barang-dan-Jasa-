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
use App\Http\Controllers\KepalaInstalasiController;
use App\Http\Controllers\KepalaBidangController;

Route::resource('permintaan', PermintaanController::class)
    ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'])
    ->middleware(['auth', 'verified']);

// Routes untuk Kepala Instalasi
Route::middleware(['auth', 'verified'])->prefix('kepala-instalasi')->name('kepala-instalasi.')->group(function () {
    Route::get('/dashboard', [KepalaInstalasiController::class, 'dashboard'])->name('dashboard');
    Route::get('/', [KepalaInstalasiController::class, 'index'])->name('index');
    Route::get('/permintaan/{permintaan}', [KepalaInstalasiController::class, 'show'])->name('show');
    Route::get('/permintaan/{permintaan}/tracking', [KepalaInstalasiController::class, 'tracking'])->name('tracking');
    Route::get('/permintaan/{permintaan}/nota-dinas/create', [KepalaInstalasiController::class, 'createNotaDinas'])->name('nota-dinas.create');
    Route::post('/permintaan/{permintaan}/nota-dinas', [KepalaInstalasiController::class, 'storeNotaDinas'])->name('nota-dinas.store');
    Route::post('/permintaan/{permintaan}/approve', [KepalaInstalasiController::class, 'approve'])->name('approve');
    Route::post('/permintaan/{permintaan}/reject', [KepalaInstalasiController::class, 'reject'])->name('reject');
    Route::post('/permintaan/{permintaan}/revisi', [KepalaInstalasiController::class, 'requestRevision'])->name('revisi');
});

// Routes untuk Kepala Bidang
Route::middleware(['auth', 'verified'])->prefix('kepala-bidang')->name('kepala-bidang.')->group(function () {
    Route::get('/dashboard', [KepalaBidangController::class, 'dashboard'])->name('dashboard');
    Route::get('/', [KepalaBidangController::class, 'index'])->name('index');
    Route::get('/permintaan/{permintaan}', [KepalaBidangController::class, 'show'])->name('show');
    Route::get('/permintaan/{permintaan}/disposisi/create', [KepalaBidangController::class, 'createDisposisi'])->name('disposisi.create');
    Route::post('/permintaan/{permintaan}/disposisi', [KepalaBidangController::class, 'storeDisposisi'])->name('disposisi.store');
    Route::post('/permintaan/{permintaan}/approve', [KepalaBidangController::class, 'approve'])->name('approve');
    Route::post('/permintaan/{permintaan}/reject', [KepalaBidangController::class, 'reject'])->name('reject');
    Route::post('/permintaan/{permintaan}/revisi', [KepalaBidangController::class, 'requestRevision'])->name('revisi');
});

require __DIR__.'/auth.php';
