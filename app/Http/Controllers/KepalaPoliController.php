<?php

namespace App\Http\Controllers;

use App\Models\Permintaan;
use App\Models\NotaDinas;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/**
 * Controller untuk Kepala Poli (IRJA - Instalasi Rawat Jalan)
 * 
 * Kepala Poli memiliki hak akses yang sama dengan Admin untuk:
 * - Input permintaan baru
 * - Melihat semua permintaan di polinya
 * - Mengedit dan menghapus permintaan yang dibuatnya
 */
class KepalaPoliController extends Controller
{
    /**
     * Dashboard Kepala Poli - Redirect ke index
     * Bidang otomatis diset ke "Instalasi Rawat Jalan"
     */
    public function dashboard()
    {
        // Redirect ke index untuk menghindari loop
        return redirect()->route('kepala-poli.index');
    }

    /**
     * Display a listing of permintaan untuk poli ini
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $query = Permintaan::with('user')
            ->whereHas('user', function($q) use ($user) {
                $q->where('unit_kerja', $user->unit_kerja);
            });

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('permintaan_id', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%")
                  ->orWhere('no_nota_dinas', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal_permintaan', '>=', $request->tanggal_dari);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal_permintaan', '<=', $request->tanggal_sampai);
        }

        $perPage = $request->input('per_page', 10);
        $permintaans = $query->orderByDesc('permintaan_id')
            ->paginate($perPage)
            ->through(function($permintaan) {
                $permintaan->tracking_status = $permintaan->trackingStatus;
                $permintaan->progress = $permintaan->getProgressPercentage();
                $permintaan->timeline_count = count($permintaan->getTimelineTracking());
                return $permintaan;
            });

        return Inertia::render('KepalaPoli/Index', [
            'permintaans' => $permintaans,
            'userLogin' => $user,
            'filters' => $request->only(['search', 'status', 'tanggal_dari', 'tanggal_sampai', 'per_page']),
        ]);
    }

    /**
     * Show the form for creating a new permintaan
     */
    public function create()
    {
        return Inertia::render('KepalaPoli/Create');
    }

    /**
     * Store a newly created permintaan
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'bidang' => 'nullable|string',
            'klasifikasi_permintaan' => 'required|string|in:Medis,Non Medis,Penunjang',
            'tanggal_permintaan' => 'nullable|date',
            'deskripsi' => 'required|string',
            'status' => 'nullable|string',
            'pic_pimpinan' => 'nullable|string',
            'no_nota_dinas' => 'nullable|string',
            'link_scan' => 'nullable|url',
            'disposisi_tujuan' => 'required|string',
            'catatan_disposisi' => 'nullable|string',
            'wadir_tujuan' => 'nullable|string',
            'kabid_tujuan' => 'nullable|string|in:Kabid Umum,Kabid Yanmed,Kabid Penunjang',
            // Nota Dinas fields
            'nota_kepada' => 'required|string',
            'nota_dari' => 'required|string',
            'nota_tanggal_nota' => 'required|date',
            'nota_no_nota' => 'required|string',
            'nota_sifat' => 'nullable|string',
            'nota_lampiran' => 'nullable|string',
            'nota_perihal' => 'required|string',
            'nota_detail' => 'nullable|string',
            'nota_mengetahui' => 'nullable|string',
        ]);

        // Auto set status
        if (empty($data['status'])) {
            $data['status'] = 'diajukan';
        }

        // Set user_id ke user yang login
        $data['user_id'] = Auth::id();

        // Set bidang dari unit_kerja user jika tidak diisi
        if (empty($data['bidang'])) {
            $data['bidang'] = Auth::user()->unit_kerja;
        }

        // Set tanggal permintaan ke hari ini jika tidak diisi
        if (empty($data['tanggal_permintaan'])) {
            $data['tanggal_permintaan'] = now();
        }

        // Extract nota dinas data
        $notaDinasData = [
            'kepada' => $data['nota_kepada'],
            'dari' => $data['nota_dari'],
            'tanggal_nota' => $data['nota_tanggal_nota'],
            'no_nota' => $data['nota_no_nota'],
            'sifat' => $data['nota_sifat'] ?? null,
            'lampiran' => $data['nota_lampiran'] ?? null,
            'perihal' => $data['nota_perihal'],
            'detail' => $data['nota_detail'] ?? null,
            'mengetahui' => $data['nota_mengetahui'] ?? null,
        ];

        // Remove nota dinas fields from permintaan data
        unset($data['nota_kepada'], $data['nota_dari'], $data['nota_tanggal_nota'], 
              $data['nota_no_nota'], $data['nota_sifat'], $data['nota_lampiran'], 
              $data['nota_perihal'], $data['nota_detail'], $data['nota_mengetahui']);

        // Create permintaan
        $permintaan = Permintaan::create($data);

        // Create nota dinas
        $notaDinasData['permintaan_id'] = $permintaan->permintaan_id;
        NotaDinas::create($notaDinasData);

        return redirect()->route('kepala-poli.index')
            ->with('success', 'Permintaan berhasil dibuat');
    }

    /**
     * Display the specified permintaan
     */
    public function show(Permintaan $permintaan)
    {
        $user = Auth::user();
        
        // Pastikan permintaan dari unit kerja yang sama
        if ($permintaan->user->unit_kerja !== $user->unit_kerja) {
            abort(403, 'Anda tidak memiliki akses ke permintaan ini');
        }

        $permintaan->load(['user', 'notaDinas', 'disposisi', 'perencanaan']);
        
        return Inertia::render('KepalaPoli/Show', [
            'permintaan' => $permintaan,
        ]);
    }

    /**
     * Show the form for editing the specified permintaan
     */
    public function edit(Permintaan $permintaan)
    {
        $user = Auth::user();
        
        // Pastikan permintaan dari unit kerja yang sama
        if ($permintaan->user->unit_kerja !== $user->unit_kerja) {
            abort(403, 'Anda tidak memiliki akses ke permintaan ini');
        }

        $permintaan->load('notaDinas');
        
        return Inertia::render('KepalaPoli/Edit', [
            'permintaan' => $permintaan,
        ]);
    }

    /**
     * Update the specified permintaan
     */
    public function update(Request $request, Permintaan $permintaan)
    {
        $user = Auth::user();
        
        // Pastikan permintaan dari unit kerja yang sama
        if ($permintaan->user->unit_kerja !== $user->unit_kerja) {
            abort(403, 'Anda tidak memiliki akses ke permintaan ini');
        }

        $data = $request->validate([
            'bidang' => 'nullable|string',
            'klasifikasi_permintaan' => 'required|string|in:Medis,Non Medis,Penunjang',
            'tanggal_permintaan' => 'nullable|date',
            'deskripsi' => 'required|string',
            'status' => 'nullable|string',
            'pic_pimpinan' => 'nullable|string',
            'no_nota_dinas' => 'nullable|string',
            'link_scan' => 'nullable|url',
            'disposisi_tujuan' => 'nullable|string',
            'catatan_disposisi' => 'nullable|string',
            'wadir_tujuan' => 'nullable|string',
            'kabid_tujuan' => 'nullable|string|in:Kabid Umum,Kabid Yanmed,Kabid Penunjang',
        ]);

        $permintaan->update($data);

        return redirect()->route('kepala-poli.index')
            ->with('success', 'Permintaan berhasil diupdate');
    }

    /**
     * Remove the specified permintaan
     */
    public function destroy(Permintaan $permintaan)
    {
        $user = Auth::user();
        
        // Pastikan permintaan dari unit kerja yang sama
        if ($permintaan->user->unit_kerja !== $user->unit_kerja) {
            abort(403, 'Anda tidak memiliki akses ke permintaan ini');
        }

        $permintaan->delete();

        return redirect()->route('kepala-poli.index')
            ->with('success', 'Permintaan berhasil dihapus');
    }

    /**
     * Display tracking information for a permintaan
     */
    public function tracking(Permintaan $permintaan)
    {
        $user = Auth::user();
        
        // Pastikan permintaan dari unit kerja yang sama
        if ($permintaan->user->unit_kerja !== $user->unit_kerja) {
            abort(403, 'Anda tidak memiliki akses ke permintaan ini');
        }

        $permintaan->load(['user', 'notaDinas', 'disposisi', 'perencanaan']);
        
        $timeline = $permintaan->getTimelineTracking();
        $progress = $permintaan->getProgressPercentage();

        return Inertia::render('KepalaPoli/Tracking', [
            'permintaan' => $permintaan,
            'timeline' => $timeline,
            'progress' => $progress,
        ]);
    }

    /**
     * Cetak Nota Dinas
     */
    public function cetakNotaDinas(Permintaan $permintaan)
    {
        $user = Auth::user();
        
        // Pastikan permintaan dari unit kerja yang sama
        if ($permintaan->user->unit_kerja !== $user->unit_kerja) {
            abort(403, 'Anda tidak memiliki akses ke permintaan ini');
        }

        $permintaan->load(['user', 'notaDinas']);
        
        return Inertia::render('KepalaPoli/CetakNotaDinas', [
            'permintaan' => $permintaan,
        ]);
    }

    /**
     * Lihat Lampiran Nota Dinas
     */
    public function lihatLampiran(NotaDinas $notaDinas)
    {
        $user = Auth::user();
        
        // Pastikan nota dinas dari unit kerja yang sama
        if ($notaDinas->permintaan->user->unit_kerja !== $user->unit_kerja) {
            abort(403, 'Anda tidak memiliki akses ke nota dinas ini');
        }

        return Inertia::render('KepalaPoli/LampiranNotaDinas', [
            'notaDinas' => $notaDinas,
        ]);
    }
}
