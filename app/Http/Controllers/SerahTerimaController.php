<?php

namespace App\Http\Controllers;

use App\Models\SerahTerima;
use App\Models\NotaPenerimaan;
use App\Models\Pengadaan;
use App\Models\Permintaan;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

/**
 * Controller untuk Serah Terima Barang ke Kepala Instalasi
 * 
 * Tugas:
 * 1. Menerima barang dari vendor (buat nota penerimaan)
 * 2. Serah terima barang ke Kepala Instalasi
 * 3. Tutup workflow pengadaan
 */
class SerahTerimaController extends Controller
{
    /**
     * Dashboard Serah Terima
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        // Ambil pengadaan yang sudah diterima (siap untuk diserahterimakan)
        $pengadaans = Pengadaan::with(['kso.perencanaan.disposisi.notaDinas.permintaan', 'notaPenerimaan.serahTerima'])
            ->where('status', 'diterima')
            ->orWhereHas('notaPenerimaan', function($q) {
                $q->whereDoesntHave('serahTerima');
            })
            ->get();

        $stats = [
            'total_penerimaan' => NotaPenerimaan::count(),
            'belum_serah_terima' => NotaPenerimaan::whereDoesntHave('serahTerima')->count(),
            'sudah_serah_terima' => NotaPenerimaan::whereHas('serahTerima')->count(),
            'kondisi_baik' => NotaPenerimaan::where('kondisi', 'baik')->count(),
        ];

        // Ambil 5 nota penerimaan terbaru
        $recentPenerimaan = NotaPenerimaan::with(['pengadaan.kso.perencanaan.disposisi.notaDinas.permintaan', 'serahTerima'])
            ->orderByDesc('penerimaan_id')
            ->take(5)
            ->get()
            ->map(function($nota) {
                $nota->has_serah_terima = $nota->serahTerima()->exists();
                return $nota;
            });

        return Inertia::render('SerahTerima/Dashboard', [
            'stats' => $stats,
            'recentPenerimaan' => $recentPenerimaan,
            'userLogin' => $user,
        ]);
    }

    /**
     * List Nota Penerimaan
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $query = NotaPenerimaan::with(['pengadaan.kso.perencanaan.disposisi.notaDinas.permintaan', 'serahTerima']);

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('no_penerimaan', 'like', "%{$search}%")
                  ->orWhere('penerima', 'like', "%{$search}%");
            });
        }

        if ($request->filled('kondisi')) {
            $query->where('kondisi', $request->kondisi);
        }

        // Pagination
        $perPage = $request->input('per_page', 10);
        $penerimaans = $query->orderByDesc('penerimaan_id')
            ->paginate($perPage)
            ->through(function($nota) {
                $nota->has_serah_terima = $nota->serahTerima()->exists();
                return $nota;
            });

        return Inertia::render('SerahTerima/Index', [
            'penerimaans' => $penerimaans,
            'userLogin' => $user,
            'filters' => $request->only(['search', 'kondisi', 'per_page']),
        ]);
    }

    /**
     * Form buat Nota Penerimaan
     */
    public function createPenerimaan(Pengadaan $pengadaan)
    {
        $user = Auth::user();
        
        $pengadaan->load(['kso.perencanaan.disposisi.notaDinas.permintaan']);
        
        // Cek apakah sudah ada nota penerimaan
        $existingNota = $pengadaan->notaPenerimaan()->first();
        if ($existingNota) {
            return redirect()->route('serah-terima.edit-penerimaan', $existingNota->penerimaan_id)
                ->with('info', 'Nota penerimaan sudah ada, Anda dapat mengeditnya.');
        }
        
        return Inertia::render('SerahTerima/CreatePenerimaan', [
            'pengadaan' => $pengadaan,
        ]);
    }

    /**
     * Store Nota Penerimaan
     */
    public function storePenerimaan(Request $request, Pengadaan $pengadaan)
    {
        $user = Auth::user();
        
        $data = $request->validate([
            'no_penerimaan' => 'required|string|unique:nota_penerimaan,no_penerimaan',
            'tanggal_penerimaan' => 'required|date',
            'penerima' => 'required|string',
            'keterangan' => 'nullable|string',
            'kondisi' => 'required|in:baik,rusak,tidak lengkap',
        ]);

        $data['pengadaan_id'] = $pengadaan->pengadaan_id;

        $notaPenerimaan = NotaPenerimaan::create($data);

        // Update status pengadaan jadi diterima
        $pengadaan->update(['status' => 'diterima']);

        return redirect()
            ->route('serah-terima.index')
            ->with('success', 'Nota penerimaan berhasil dibuat.');
    }

    /**
     * Form edit Nota Penerimaan
     */
    public function editPenerimaan(NotaPenerimaan $penerimaan)
    {
        $penerimaan->load(['pengadaan.kso.perencanaan.disposisi.notaDinas.permintaan']);
        
        return Inertia::render('SerahTerima/EditPenerimaan', [
            'penerimaan' => $penerimaan,
        ]);
    }

    /**
     * Update Nota Penerimaan
     */
    public function updatePenerimaan(Request $request, NotaPenerimaan $penerimaan)
    {
        $data = $request->validate([
            'no_penerimaan' => 'required|string|unique:nota_penerimaan,no_penerimaan,' . $penerimaan->penerimaan_id . ',penerimaan_id',
            'tanggal_penerimaan' => 'required|date',
            'penerima' => 'required|string',
            'keterangan' => 'nullable|string',
            'kondisi' => 'required|in:baik,rusak,tidak lengkap',
        ]);

        $penerimaan->update($data);

        return redirect()
            ->route('serah-terima.index')
            ->with('success', 'Nota penerimaan berhasil diupdate.');
    }

    /**
     * Form buat Serah Terima ke Kepala Instalasi
     */
    public function createSerahTerima(NotaPenerimaan $penerimaan)
    {
        $user = Auth::user();
        
        $penerimaan->load(['pengadaan.kso.perencanaan.disposisi.notaDinas.permintaan', 'serahTerima']);
        
        // Cek apakah sudah ada serah terima
        $existingSerahTerima = $penerimaan->serahTerima()->first();
        if ($existingSerahTerima) {
            return redirect()->route('serah-terima.edit-serah-terima', $existingSerahTerima->serah_terima_id)
                ->with('info', 'Serah terima sudah ada, Anda dapat mengeditnya.');
        }
        
        // Get Kepala Instalasi dari permintaan
        $permintaan = $penerimaan->pengadaan->kso->perencanaan->disposisi->notaDinas->permintaan;
        $kepalaInstalasi = $permintaan->user; // User yang buat permintaan
        
        return Inertia::render('SerahTerima/CreateSerahTerima', [
            'penerimaan' => $penerimaan,
            'kepalaInstalasi' => $kepalaInstalasi,
        ]);
    }

    /**
     * Store Serah Terima
     */
    public function storeSerahTerima(Request $request, NotaPenerimaan $penerimaan)
    {
        $user = Auth::user();
        
        $data = $request->validate([
            'no_serah_terima' => 'required|string|unique:serah_terima,no_serah_terima',
            'tanggal_serah_terima' => 'required|date',
            'penyerah' => 'required|string',
            'penerima' => 'required|string', // Kepala Instalasi
            'keterangan' => 'nullable|string',
        ]);

        $data['penerimaan_id'] = $penerimaan->penerimaan_id;

        $serahTerima = SerahTerima::create($data);

        // Update status permintaan jadi selesai
        $permintaan = $penerimaan->pengadaan->kso->perencanaan->disposisi->notaDinas->permintaan;
        $permintaan->update([
            'status' => 'disetujui', // atau 'selesai' jika ada
            'pic_pimpinan' => $data['penerima'], // Kepala Instalasi yang terima
        ]);

        return redirect()
            ->route('serah-terima.index')
            ->with('success', 'Serah terima berhasil dibuat. Barang telah diserahkan ke ' . $data['penerima']);
    }

    /**
     * Form edit Serah Terima
     */
    public function editSerahTerima(SerahTerima $serahTerima)
    {
        $serahTerima->load(['notaPenerimaan.pengadaan.kso.perencanaan.disposisi.notaDinas.permintaan']);
        
        return Inertia::render('SerahTerima/EditSerahTerima', [
            'serahTerima' => $serahTerima,
        ]);
    }

    /**
     * Update Serah Terima
     */
    public function updateSerahTerima(Request $request, SerahTerima $serahTerima)
    {
        $data = $request->validate([
            'no_serah_terima' => 'required|string|unique:serah_terima,no_serah_terima,' . $serahTerima->serah_terima_id . ',serah_terima_id',
            'tanggal_serah_terima' => 'required|date',
            'penyerah' => 'required|string',
            'penerima' => 'required|string',
            'keterangan' => 'nullable|string',
        ]);

        $serahTerima->update($data);

        return redirect()
            ->route('serah-terima.index')
            ->with('success', 'Serah terima berhasil diupdate.');
    }

    /**
     * Show detail Serah Terima
     */
    public function show(NotaPenerimaan $penerimaan)
    {
        $penerimaan->load(['pengadaan.kso.perencanaan.disposisi.notaDinas.permintaan', 'serahTerima']);
        
        return Inertia::render('SerahTerima/Show', [
            'penerimaan' => $penerimaan,
        ]);
    }
}
