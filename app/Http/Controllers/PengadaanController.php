<?php

namespace App\Http\Controllers;

use App\Models\Pengadaan;
use App\Models\Kso;
use App\Models\Permintaan;
use App\Models\NotaPenerimaan;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

/**
 * Controller untuk Bagian Pengadaan
 * 
 * Tugas Bagian Pengadaan:
 * 1. Menerima KSO yang sudah selesai
 * 2. Melakukan proses pengadaan/pembelian
 * 3. Tracking pengiriman barang
 * 4. Koordinasi dengan vendor
 * 5. Forward ke Nota Penerimaan setelah barang diterima
 */
class PengadaanController extends Controller
{
    /**
     * Dashboard Bagian Pengadaan
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        // Ambil permintaan yang ditujukan ke Bagian Pengadaan
        $permintaans = Permintaan::with(['user', 'notaDinas.disposisi.perencanaan.kso.pengadaan'])
            ->where(function($q) use ($user) {
                $q->where('pic_pimpinan', 'Bagian Pengadaan')
                  ->orWhere('pic_pimpinan', $user->nama);
            })
            ->whereIn('status', ['proses', 'disetujui'])
            ->get();

        $stats = [
            'total' => $permintaans->count(),
            'belum_pengadaan' => $permintaans->filter(function($p) {
                return !$this->hasPengadaan($p);
            })->count(),
            'persiapan' => Pengadaan::where('status', 'persiapan')->count(),
            'pembelian' => Pengadaan::where('status', 'pembelian')->count(),
            'pengiriman' => Pengadaan::where('status', 'pengiriman')->count(),
            'diterima' => Pengadaan::where('status', 'diterima')->count(),
        ];

        // Ambil 5 permintaan terbaru
        $recentPermintaans = $permintaans
            ->sortByDesc('permintaan_id')
            ->take(5)
            ->map(function($permintaan) {
                $permintaan->has_pengadaan = $this->hasPengadaan($permintaan);
                $permintaan->pengadaan_data = $this->getPengadaanData($permintaan);
                return $permintaan;
            })
            ->values();

        return Inertia::render('Pengadaan/Dashboard', [
            'stats' => $stats,
            'recentPermintaans' => $recentPermintaans,
            'userLogin' => $user,
        ]);
    }

    /**
     * Tampilkan daftar permintaan untuk Bagian Pengadaan
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Query dasar
        $query = Permintaan::with(['user', 'notaDinas.disposisi.perencanaan.kso.pengadaan'])
            ->where(function($q) use ($user) {
                $q->where('pic_pimpinan', 'Bagian Pengadaan')
                  ->orWhere('pic_pimpinan', $user->nama);
            })
            ->whereIn('status', ['proses', 'disetujui']);

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('permintaan_id', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Pagination
        $perPage = $request->input('per_page', 10);
        $permintaans = $query->orderByDesc('permintaan_id')
            ->paginate($perPage)
            ->through(function($permintaan) {
                $permintaan->has_pengadaan = $this->hasPengadaan($permintaan);
                $permintaan->pengadaan_data = $this->getPengadaanData($permintaan);
                $permintaan->kso_data = $this->getKsoFromPermintaan($permintaan);
                return $permintaan;
            });

        return Inertia::render('Pengadaan/Index', [
            'permintaans' => $permintaans,
            'userLogin' => $user,
            'filters' => $request->only(['search', 'status', 'per_page']),
        ]);
    }

    /**
     * Tampilkan detail permintaan
     */
    public function show(Permintaan $permintaan)
    {
        $user = Auth::user();
        
        // Cek otorisasi
        if ($permintaan->pic_pimpinan !== 'Bagian Pengadaan' && $permintaan->pic_pimpinan !== $user->nama) {
            abort(403, 'Anda tidak memiliki akses untuk melihat permintaan ini.');
        }
        
        $permintaan->load(['user', 'notaDinas.disposisi.perencanaan.kso.pengadaan.notaPenerimaan']);
        
        // Get KSO & Pengadaan
        $kso = $this->getKsoFromPermintaan($permintaan);
        $pengadaan = $kso ? $kso->pengadaan()->first() : null;
        
        return Inertia::render('Pengadaan/Show', [
            'permintaan' => $permintaan,
            'kso' => $kso,
            'pengadaan' => $pengadaan,
        ]);
    }

    /**
     * Form untuk membuat Pengadaan
     */
    public function create(Permintaan $permintaan)
    {
        $user = Auth::user();
        
        // Cek otorisasi
        if ($permintaan->pic_pimpinan !== 'Bagian Pengadaan' && $permintaan->pic_pimpinan !== $user->nama) {
            abort(403, 'Anda tidak memiliki akses untuk membuat pengadaan permintaan ini.');
        }
        
        $permintaan->load(['user', 'notaDinas.disposisi.perencanaan.kso']);
        
        // Get KSO
        $kso = $this->getKsoFromPermintaan($permintaan);
        
        if (!$kso) {
            return redirect()->route('pengadaan.index')
                ->withErrors(['error' => 'Permintaan ini belum memiliki KSO.']);
        }
        
        // Cek apakah sudah ada pengadaan
        $existingPengadaan = $kso->pengadaan()->first();
        if ($existingPengadaan) {
            return redirect()->route('pengadaan.edit', ['permintaan' => $permintaan->permintaan_id, 'pengadaan' => $existingPengadaan->pengadaan_id])
                ->with('info', 'Pengadaan sudah ada, Anda dapat mengeditnya.');
        }
        
        return Inertia::render('Pengadaan/Create', [
            'permintaan' => $permintaan,
            'kso' => $kso,
        ]);
    }

    /**
     * Store Pengadaan baru
     */
    public function store(Request $request, Permintaan $permintaan)
    {
        $user = Auth::user();
        
        // Cek otorisasi
        if ($permintaan->pic_pimpinan !== 'Bagian Pengadaan' && $permintaan->pic_pimpinan !== $user->nama) {
            abort(403, 'Anda tidak memiliki akses untuk membuat pengadaan permintaan ini.');
        }
        
        $data = $request->validate([
            'no_pengadaan' => 'required|string|unique:pengadaan,no_pengadaan',
            'tanggal_pengadaan' => 'required|date',
            'vendor' => 'nullable|string',
            'total_harga' => 'nullable|numeric',
            'status' => 'nullable|in:persiapan,pembelian,pengiriman,diterima',
            'no_tracking' => 'nullable|string',
        ]);

        // Get KSO
        $kso = $this->getKsoFromPermintaan($permintaan);
        
        if (!$kso) {
            return redirect()->back()->withErrors(['error' => 'KSO tidak ditemukan.']);
        }

        $data['kso_id'] = $kso->kso_id;
        $data['status'] = $data['status'] ?? 'persiapan';

        $pengadaan = Pengadaan::create($data);

        return redirect()
            ->route('pengadaan.show', $permintaan->permintaan_id)
            ->with('success', 'Pengadaan berhasil dibuat.');
    }

    /**
     * Form edit Pengadaan
     */
    public function edit(Permintaan $permintaan, Pengadaan $pengadaan)
    {
        $user = Auth::user();
        
        // Cek otorisasi
        if ($permintaan->pic_pimpinan !== 'Bagian Pengadaan' && $permintaan->pic_pimpinan !== $user->nama) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit pengadaan permintaan ini.');
        }
        
        $permintaan->load(['user', 'notaDinas.disposisi.perencanaan.kso']);
        $kso = $this->getKsoFromPermintaan($permintaan);
        
        return Inertia::render('Pengadaan/Edit', [
            'permintaan' => $permintaan,
            'kso' => $kso,
            'pengadaan' => $pengadaan,
        ]);
    }

    /**
     * Update Pengadaan
     */
    public function update(Request $request, Permintaan $permintaan, Pengadaan $pengadaan)
    {
        $user = Auth::user();
        
        // Cek otorisasi
        if ($permintaan->pic_pimpinan !== 'Bagian Pengadaan' && $permintaan->pic_pimpinan !== $user->nama) {
            abort(403, 'Anda tidak memiliki akses untuk mengupdate pengadaan permintaan ini.');
        }
        
        $data = $request->validate([
            'no_pengadaan' => 'required|string|unique:pengadaan,no_pengadaan,' . $pengadaan->pengadaan_id . ',pengadaan_id',
            'tanggal_pengadaan' => 'required|date',
            'vendor' => 'nullable|string',
            'total_harga' => 'nullable|numeric',
            'status' => 'nullable|in:persiapan,pembelian,pengiriman,diterima',
            'no_tracking' => 'nullable|string',
        ]);

        $pengadaan->update($data);

        return redirect()
            ->route('pengadaan.show', $permintaan->permintaan_id)
            ->with('success', 'Pengadaan berhasil diupdate.');
    }

    /**
     * Delete Pengadaan
     */
    public function destroy(Permintaan $permintaan, Pengadaan $pengadaan)
    {
        $user = Auth::user();
        
        // Cek otorisasi
        if ($permintaan->pic_pimpinan !== 'Bagian Pengadaan' && $permintaan->pic_pimpinan !== $user->nama) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus pengadaan permintaan ini.');
        }
        
        $pengadaan->delete();

        return redirect()
            ->route('pengadaan.index')
            ->with('success', 'Pengadaan berhasil dihapus.');
    }

    /**
     * Helper: Cek apakah permintaan sudah punya pengadaan
     */
    private function hasPengadaan(Permintaan $permintaan)
    {
        $kso = $this->getKsoFromPermintaan($permintaan);
        
        if (!$kso) {
            return false;
        }
        
        return $kso->pengadaan()->exists();
    }

    /**
     * Helper: Get pengadaan data dari permintaan
     */
    private function getPengadaanData(Permintaan $permintaan)
    {
        $kso = $this->getKsoFromPermintaan($permintaan);
        
        if (!$kso) {
            return null;
        }
        
        return $kso->pengadaan()->first();
    }

    /**
     * Helper: Get KSO dari Permintaan
     */
    private function getKsoFromPermintaan(Permintaan $permintaan)
    {
        // Cari KSO melalui nota dinas → disposisi → perencanaan → kso
        $notaDinas = $permintaan->notaDinas()->latest('tanggal_nota')->first();
        
        if (!$notaDinas) {
            return null;
        }
        
        $disposisi = $notaDinas->disposisi()->latest('tanggal_disposisi')->first();
        
        if (!$disposisi) {
            return null;
        }
        
        $perencanaan = $disposisi->perencanaan()->first();
        
        if (!$perencanaan) {
            return null;
        }
        
        return $perencanaan->kso()->first();
    }
}
