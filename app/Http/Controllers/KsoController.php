<?php

namespace App\Http\Controllers;

use App\Models\Kso;
use App\Models\Perencanaan;
use App\Models\Permintaan;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

/**
 * Controller untuk Bagian KSO (Kerja Sama Operasional)
 * 
 * Tugas Bagian KSO:
 * 1. Menerima perencanaan yang sudah dibuat Staff Perencanaan
 * 2. Membuat dokumen KSO (Kerja Sama Operasional)
 * 3. Mengelola kerjasama dengan pihak ketiga
 * 4. Forward ke Bagian Pengadaan setelah KSO selesai
 */
class KsoController extends Controller
{
    /**
     * Dashboard Bagian KSO
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        // Ambil perencanaan yang siap untuk dibuatkan KSO
        // Kriteria: permintaan dengan pic_pimpinan = 'Bagian KSO'
        $permintaans = Permintaan::with(['user', 'notaDinas', 'notaDinas.disposisi.perencanaan'])
            ->where(function($q) use ($user) {
                $q->where('pic_pimpinan', 'Bagian KSO')
                  ->orWhere('pic_pimpinan', $user->nama);
            })
            ->whereIn('status', ['proses', 'disetujui'])
            ->get();

        $stats = [
            'total' => $permintaans->count(),
            'belum_kso' => $permintaans->filter(function($p) {
                // Cek apakah sudah ada KSO
                return !$this->hasKso($p);
            })->count(),
            'aktif' => Kso::where('status', 'aktif')->count(),
            'selesai' => Kso::where('status', 'selesai')->count(),
        ];

        // Ambil 5 permintaan terbaru
        $recentPermintaans = $permintaans
            ->sortByDesc('permintaan_id')
            ->take(5)
            ->map(function($permintaan) {
                $permintaan->has_kso = $this->hasKso($permintaan);
                return $permintaan;
            })
            ->values();

        return Inertia::render('KSO/Dashboard', [
            'stats' => $stats,
            'recentPermintaans' => $recentPermintaans,
            'userLogin' => $user,
        ]);
    }

    /**
     * Tampilkan daftar permintaan untuk Bagian KSO
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Query dasar - hanya permintaan yang ditujukan ke Bagian KSO
        $query = Permintaan::with(['user', 'notaDinas', 'notaDinas.disposisi.perencanaan.kso'])
            ->where(function($q) use ($user) {
                $q->where('pic_pimpinan', 'Bagian KSO')
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

        if ($request->filled('bidang')) {
            $query->where('bidang', $request->bidang);
        }

        // Pagination
        $perPage = $request->input('per_page', 10);
        $permintaans = $query->orderByDesc('permintaan_id')
            ->paginate($perPage)
            ->through(function($permintaan) {
                $permintaan->has_kso = $this->hasKso($permintaan);
                $permintaan->kso_data = $this->getKsoData($permintaan);
                return $permintaan;
            });

        return Inertia::render('KSO/Index', [
            'permintaans' => $permintaans,
            'userLogin' => $user,
            'filters' => $request->only(['search', 'status', 'bidang', 'per_page']),
        ]);
    }

    /**
     * Tampilkan detail permintaan
     */
    public function show(Permintaan $permintaan)
    {
        $user = Auth::user();
        
        // Cek otorisasi
        if ($permintaan->pic_pimpinan !== 'Bagian KSO' && $permintaan->pic_pimpinan !== $user->nama) {
            abort(403, 'Anda tidak memiliki akses untuk melihat permintaan ini.');
        }
        
        $permintaan->load(['user', 'notaDinas.disposisi.perencanaan.kso']);
        
        // Get perencanaan data
        $perencanaan = $this->getPerencanaanFromPermintaan($permintaan);
        
        // Get KSO if exists
        $kso = $perencanaan ? $perencanaan->kso()->first() : null;
        
        return Inertia::render('KSO/Show', [
            'permintaan' => $permintaan,
            'perencanaan' => $perencanaan,
            'kso' => $kso,
            'userLogin' => $user,
        ]);
    }

    /**
     * Form untuk membuat KSO
     */
    public function create(Permintaan $permintaan)
    {
        $user = Auth::user();
        
        // Cek otorisasi
        if ($permintaan->pic_pimpinan !== 'Bagian KSO' && $permintaan->pic_pimpinan !== $user->nama) {
            abort(403, 'Anda tidak memiliki akses untuk membuat KSO permintaan ini.');
        }
        
        $permintaan->load(['user', 'notaDinas.disposisi.perencanaan']);
        
        // Get perencanaan
        $perencanaan = $this->getPerencanaanFromPermintaan($permintaan);
        
        if (!$perencanaan) {
            return redirect()->route('kso.index')
                ->withErrors(['error' => 'Permintaan ini belum memiliki perencanaan.']);
        }
        
        // Cek apakah sudah ada KSO
        $existingKso = $perencanaan->kso()->first();
        if ($existingKso) {
            return redirect()->route('kso.edit', ['permintaan' => $permintaan->permintaan_id, 'kso' => $existingKso->kso_id])
                ->with('info', 'KSO sudah ada, Anda dapat mengeditnya.');
        }
        
        return Inertia::render('KSO/Create', [
            'permintaan' => $permintaan,
            'perencanaan' => $perencanaan,
        ]);
    }

    /**
     * Store KSO baru
     */
    public function store(Request $request, Permintaan $permintaan)
    {
        $user = Auth::user();
        
        // Cek otorisasi
        if ($permintaan->pic_pimpinan !== 'Bagian KSO' && $permintaan->pic_pimpinan !== $user->nama) {
            abort(403, 'Anda tidak memiliki akses untuk membuat KSO permintaan ini.');
        }
        
        $data = $request->validate([
            'no_kso' => 'required|string|unique:kso,no_kso',
            'tanggal_kso' => 'required|date',
            'pihak_pertama' => 'required|string',
            'pihak_kedua' => 'required|string',
            'isi_kerjasama' => 'required|string',
            'status' => 'nullable|in:draft,aktif,selesai,batal',
        ]);

        // Get perencanaan
        $perencanaan = $this->getPerencanaanFromPermintaan($permintaan);
        
        if (!$perencanaan) {
            return redirect()->back()->withErrors(['error' => 'Perencanaan tidak ditemukan.']);
        }

        $data['perencanaan_id'] = $perencanaan->perencanaan_id;
        $data['status'] = $data['status'] ?? 'draft';

        $kso = Kso::create($data);

        // Jika status langsung aktif atau selesai, update permintaan
        if (in_array($data['status'], ['aktif', 'selesai'])) {
            $permintaan->update([
                'pic_pimpinan' => 'Bagian Pengadaan',
                'status' => 'proses',
            ]);
        }

        return redirect()
            ->route('kso.show', $permintaan->permintaan_id)
            ->with('success', 'KSO berhasil dibuat.');
    }

    /**
     * Form edit KSO
     */
    public function edit(Permintaan $permintaan, Kso $kso)
    {
        $user = Auth::user();
        
        // Cek otorisasi
        if ($permintaan->pic_pimpinan !== 'Bagian KSO' && $permintaan->pic_pimpinan !== $user->nama) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit KSO permintaan ini.');
        }
        
        $permintaan->load(['user', 'notaDinas.disposisi.perencanaan']);
        
        $perencanaan = $this->getPerencanaanFromPermintaan($permintaan);
        
        return Inertia::render('KSO/Edit', [
            'permintaan' => $permintaan,
            'perencanaan' => $perencanaan,
            'kso' => $kso,
        ]);
    }

    /**
     * Update KSO
     */
    public function update(Request $request, Permintaan $permintaan, Kso $kso)
    {
        $user = Auth::user();
        
        // Cek otorisasi
        if ($permintaan->pic_pimpinan !== 'Bagian KSO' && $permintaan->pic_pimpinan !== $user->nama) {
            abort(403, 'Anda tidak memiliki akses untuk mengupdate KSO permintaan ini.');
        }
        
        $data = $request->validate([
            'no_kso' => 'required|string|unique:kso,no_kso,' . $kso->kso_id . ',kso_id',
            'tanggal_kso' => 'required|date',
            'pihak_pertama' => 'required|string',
            'pihak_kedua' => 'required|string',
            'isi_kerjasama' => 'required|string',
            'status' => 'nullable|in:draft,aktif,selesai,batal',
        ]);

        $kso->update($data);

        // Jika status berubah jadi aktif atau selesai, forward ke Pengadaan
        if (in_array($data['status'], ['aktif', 'selesai']) && $permintaan->pic_pimpinan === 'Bagian KSO') {
            $permintaan->update([
                'pic_pimpinan' => 'Bagian Pengadaan',
                'status' => 'proses',
            ]);
        }

        return redirect()
            ->route('kso.show', $permintaan->permintaan_id)
            ->with('success', 'KSO berhasil diupdate.');
    }

    /**
     * Delete KSO
     */
    public function destroy(Permintaan $permintaan, Kso $kso)
    {
        $user = Auth::user();
        
        // Cek otorisasi
        if ($permintaan->pic_pimpinan !== 'Bagian KSO' && $permintaan->pic_pimpinan !== $user->nama) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus KSO permintaan ini.');
        }
        
        $kso->delete();

        return redirect()
            ->route('kso.index')
            ->with('success', 'KSO berhasil dihapus.');
    }

    /**
     * Helper: Cek apakah permintaan sudah punya KSO
     */
    private function hasKso(Permintaan $permintaan)
    {
        $perencanaan = $this->getPerencanaanFromPermintaan($permintaan);
        
        if (!$perencanaan) {
            return false;
        }
        
        return $perencanaan->kso()->exists();
    }

    /**
     * Helper: Get KSO data dari permintaan
     */
    private function getKsoData(Permintaan $permintaan)
    {
        $perencanaan = $this->getPerencanaanFromPermintaan($permintaan);
        
        if (!$perencanaan) {
            return null;
        }
        
        return $perencanaan->kso()->first();
    }

    /**
     * Helper: Get Perencanaan dari Permintaan
     */
    private function getPerencanaanFromPermintaan(Permintaan $permintaan)
    {
        // Cari perencanaan melalui nota dinas → disposisi → perencanaan
        $notaDinas = $permintaan->notaDinas()->latest('tanggal_nota')->first();
        
        if (!$notaDinas) {
            return null;
        }
        
        $disposisi = $notaDinas->disposisi()->latest('tanggal_disposisi')->first();
        
        if (!$disposisi) {
            return null;
        }
        
        return $disposisi->perencanaan()->first();
    }
}
