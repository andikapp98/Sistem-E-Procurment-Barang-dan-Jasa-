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
    // Admin dashboard - other roles are redirected by redirect.role middleware
    $user = Auth::user();
    
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
})->middleware(['auth', 'verified', 'redirect.role'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

use App\Http\Controllers\PermintaanController;
use App\Http\Controllers\KepalaInstalasiController;
use App\Http\Controllers\KepalaBidangController;
use App\Http\Controllers\WakilDirekturController;
use App\Http\Controllers\DirekturController;
use App\Http\Controllers\StaffPerencanaanController;

Route::middleware(['auth', 'verified', 'redirect.role'])->group(function () {
    Route::resource('permintaan', PermintaanController::class)
        ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);
});

// Middleware untuk mencegah KSO dan Pengadaan akses /permintaan
Route::middleware(['auth', 'verified'])->prefix('permintaan')->group(function () {
    // Additional protection will be handled in the controller
});

// Route tracking untuk admin di permintaan
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/permintaan/{permintaan}/tracking', [PermintaanController::class, 'tracking'])->name('permintaan.tracking');
});

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
    
    // Routes untuk review dan resubmit permintaan yang ditolak
    Route::get('/permintaan/{permintaan}/review-rejected', [KepalaInstalasiController::class, 'reviewRejected'])->name('review-rejected');
    Route::post('/permintaan/{permintaan}/resubmit', [KepalaInstalasiController::class, 'resubmit'])->name('resubmit');
});

// Routes untuk Kepala Bidang
Route::middleware(['auth', 'verified'])->prefix('kepala-bidang')->name('kepala-bidang.')->group(function () {
    Route::get('/dashboard', [KepalaBidangController::class, 'dashboard'])->name('dashboard');
    Route::get('/', [KepalaBidangController::class, 'index'])->name('index');
    Route::get('/permintaan/{permintaan}', [KepalaBidangController::class, 'show'])->name('show');
    Route::get('/permintaan/{permintaan}/tracking', [KepalaBidangController::class, 'tracking'])->name('tracking');
    Route::get('/approved', [KepalaBidangController::class, 'approved'])->name('approved');
    Route::get('/permintaan/{permintaan}/disposisi/create', [KepalaBidangController::class, 'createDisposisi'])->name('disposisi.create');
    Route::post('/permintaan/{permintaan}/disposisi', [KepalaBidangController::class, 'storeDisposisi'])->name('disposisi.store');
    Route::post('/permintaan/{permintaan}/approve', [KepalaBidangController::class, 'approve'])->name('approve');
    Route::post('/permintaan/{permintaan}/reject', [KepalaBidangController::class, 'reject'])->name('reject');
    Route::post('/permintaan/{permintaan}/revisi', [KepalaBidangController::class, 'requestRevision'])->name('revisi');
});

// Routes untuk Wakil Direktur
Route::middleware(['auth', 'verified'])->prefix('wakil-direktur')->name('wakil-direktur.')->group(function () {
    Route::get('/dashboard', [WakilDirekturController::class, 'dashboard'])->name('dashboard');
    Route::get('/', [WakilDirekturController::class, 'index'])->name('index');
    Route::get('/permintaan/{permintaan}', [WakilDirekturController::class, 'show'])->name('show');
    Route::get('/permintaan/{permintaan}/tracking', [WakilDirekturController::class, 'tracking'])->name('tracking');
    Route::get('/approved', [WakilDirekturController::class, 'approved'])->name('approved');
    Route::get('/permintaan/{permintaan}/disposisi/create', [WakilDirekturController::class, 'createDisposisi'])->name('disposisi.create');
    Route::post('/permintaan/{permintaan}/disposisi', [WakilDirekturController::class, 'storeDisposisi'])->name('disposisi.store');
    Route::post('/permintaan/{permintaan}/approve', [WakilDirekturController::class, 'approve'])->name('approve');
    Route::post('/permintaan/{permintaan}/reject', [WakilDirekturController::class, 'reject'])->name('reject');
    Route::post('/permintaan/{permintaan}/revisi', [WakilDirekturController::class, 'requestRevision'])->name('revisi');
});

// Routes untuk Direktur
Route::middleware(['auth', 'verified'])->prefix('direktur')->name('direktur.')->group(function () {
    Route::get('/dashboard', [DirekturController::class, 'dashboard'])->name('dashboard');
    Route::get('/', [DirekturController::class, 'index'])->name('index');
    Route::get('/permintaan/{permintaan}', [DirekturController::class, 'show'])->name('show');
    Route::get('/permintaan/{permintaan}/tracking', [DirekturController::class, 'tracking'])->name('tracking');
    Route::get('/approved', [DirekturController::class, 'approved'])->name('approved');
    Route::get('/permintaan/{permintaan}/disposisi/create', [DirekturController::class, 'createDisposisi'])->name('disposisi.create');
    Route::post('/permintaan/{permintaan}/disposisi', [DirekturController::class, 'storeDisposisi'])->name('disposisi.store');
    Route::post('/permintaan/{permintaan}/approve', [DirekturController::class, 'approve'])->name('approve');
    Route::post('/permintaan/{permintaan}/reject', [DirekturController::class, 'reject'])->name('reject');
    Route::post('/permintaan/{permintaan}/revisi', [DirekturController::class, 'requestRevision'])->name('revisi');
});

// Routes untuk Staff Perencanaan
Route::middleware(['auth', 'verified'])->prefix('staff-perencanaan')->name('staff-perencanaan.')->group(function () {
    Route::get('/dashboard', [StaffPerencanaanController::class, 'dashboard'])->name('dashboard');
    Route::get('/', [StaffPerencanaanController::class, 'index'])->name('index');
    Route::get('/permintaan/{permintaan}', [StaffPerencanaanController::class, 'show'])->name('show');
    Route::get('/permintaan/{permintaan}/tracking', [StaffPerencanaanController::class, 'tracking'])->name('tracking');
    Route::get('/approved', [StaffPerencanaanController::class, 'approved'])->name('approved');
    Route::get('/permintaan/{permintaan}/perencanaan/create', [StaffPerencanaanController::class, 'createPerencanaan'])->name('perencanaan.create');
    Route::post('/permintaan/{permintaan}/perencanaan', [StaffPerencanaanController::class, 'storePerencanaan'])->name('perencanaan.store');
    Route::get('/permintaan/{permintaan}/disposisi/create', [StaffPerencanaanController::class, 'createDisposisi'])->name('disposisi.create');
    Route::post('/permintaan/{permintaan}/disposisi', [StaffPerencanaanController::class, 'storeDisposisi'])->name('disposisi.store');
    
    // Routes untuk Scan Berkas
    Route::get('/permintaan/{permintaan}/scan-berkas', [StaffPerencanaanController::class, 'uploadDokumen'])->name('scan-berkas');
    Route::post('/permintaan/{permintaan}/dokumen', [StaffPerencanaanController::class, 'storeDokumen'])->name('dokumen.store');
    Route::get('/permintaan/{permintaan}/dokumen/{dokumen}/download', [StaffPerencanaanController::class, 'downloadDokumen'])->name('dokumen.download');
    Route::delete('/permintaan/{permintaan}/dokumen/{dokumen}', [StaffPerencanaanController::class, 'deleteDokumen'])->name('dokumen.delete');
});

// Routes untuk Bagian KSO
Route::middleware(['auth', 'verified'])->prefix('kso')->name('kso.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\KsoController::class, 'dashboard'])->name('dashboard');
    Route::get('/', [App\Http\Controllers\KsoController::class, 'index'])->name('index');
    Route::get('/permintaan/{permintaan}', [App\Http\Controllers\KsoController::class, 'show'])->name('show');
    Route::get('/permintaan/{permintaan}/create', [App\Http\Controllers\KsoController::class, 'create'])->name('create');
    Route::post('/permintaan/{permintaan}', [App\Http\Controllers\KsoController::class, 'store'])->name('store');
    Route::get('/permintaan/{permintaan}/kso/{kso}/edit', [App\Http\Controllers\KsoController::class, 'edit'])->name('edit');
    Route::put('/permintaan/{permintaan}/kso/{kso}', [App\Http\Controllers\KsoController::class, 'update'])->name('update');
    Route::delete('/permintaan/{permintaan}/kso/{kso}', [App\Http\Controllers\KsoController::class, 'destroy'])->name('destroy');
});

// Routes untuk Bagian Pengadaan
Route::middleware(['auth', 'verified'])->prefix('pengadaan')->name('pengadaan.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\PengadaanController::class, 'dashboard'])->name('dashboard');
    Route::get('/', [App\Http\Controllers\PengadaanController::class, 'index'])->name('index');
    Route::get('/permintaan/{permintaan}', [App\Http\Controllers\PengadaanController::class, 'show'])->name('show');
    Route::get('/permintaan/{permintaan}/create', [App\Http\Controllers\PengadaanController::class, 'create'])->name('create');
    Route::post('/permintaan/{permintaan}', [App\Http\Controllers\PengadaanController::class, 'store'])->name('store');
    Route::get('/permintaan/{permintaan}/pengadaan/{pengadaan}/edit', [App\Http\Controllers\PengadaanController::class, 'edit'])->name('edit');
    Route::put('/permintaan/{permintaan}/pengadaan/{pengadaan}', [App\Http\Controllers\PengadaanController::class, 'update'])->name('update');
    Route::delete('/permintaan/{permintaan}/pengadaan/{pengadaan}', [App\Http\Controllers\PengadaanController::class, 'destroy'])->name('destroy');
});

// Routes untuk Serah Terima
Route::middleware(['auth', 'verified'])->prefix('serah-terima')->name('serah-terima.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\SerahTerimaController::class, 'dashboard'])->name('dashboard');
    Route::get('/', [App\Http\Controllers\SerahTerimaController::class, 'index'])->name('index');
    Route::get('/penerimaan/{penerimaan}', [App\Http\Controllers\SerahTerimaController::class, 'show'])->name('show');
    
    // Nota Penerimaan
    Route::get('/pengadaan/{pengadaan}/penerimaan/create', [App\Http\Controllers\SerahTerimaController::class, 'createPenerimaan'])->name('create-penerimaan');
    Route::post('/pengadaan/{pengadaan}/penerimaan', [App\Http\Controllers\SerahTerimaController::class, 'storePenerimaan'])->name('store-penerimaan');
    Route::get('/penerimaan/{penerimaan}/edit', [App\Http\Controllers\SerahTerimaController::class, 'editPenerimaan'])->name('edit-penerimaan');
    Route::put('/penerimaan/{penerimaan}', [App\Http\Controllers\SerahTerimaController::class, 'updatePenerimaan'])->name('update-penerimaan');
    
    // Serah Terima ke Kepala Instalasi
    Route::get('/penerimaan/{penerimaan}/serah-terima/create', [App\Http\Controllers\SerahTerimaController::class, 'createSerahTerima'])->name('create-serah-terima');
    Route::post('/penerimaan/{penerimaan}/serah-terima', [App\Http\Controllers\SerahTerimaController::class, 'storeSerahTerima'])->name('store-serah-terima');
    Route::get('/serah-terima/{serahTerima}/edit', [App\Http\Controllers\SerahTerimaController::class, 'editSerahTerima'])->name('edit-serah-terima');
    Route::put('/serah-terima/{serahTerima}', [App\Http\Controllers\SerahTerimaController::class, 'updateSerahTerima'])->name('update-serah-terima');
});

require __DIR__.'/auth.php';
