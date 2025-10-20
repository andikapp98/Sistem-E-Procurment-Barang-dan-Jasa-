<?php

namespace App\Http\Controllers;

use App\Models\Permintaan;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class PermintaanController extends Controller
{
    /** Display a listing of the resource. */
    public function index(Request $request)
    {
        $query = Permintaan::with('user');

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

        if ($request->filled('bidang')) {
            $query->where('bidang', $request->bidang);
        }

        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal_permintaan', '>=', $request->tanggal_dari);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal_permintaan', '<=', $request->tanggal_sampai);
        }

        // Pagination dengan 10 items per page (bisa diubah sesuai kebutuhan)
        $perPage = $request->input('per_page', 10);
        $permintaans = $query->orderByDesc('permintaan_id')
            ->paginate($perPage)
            ->through(function($permintaan) {
                // Tambahkan tracking info untuk admin
                $permintaan->tracking_status = $permintaan->trackingStatus;
                $permintaan->progress = $permintaan->getProgressPercentage();
                $permintaan->timeline_count = count($permintaan->getTimelineTracking());
                return $permintaan;
            });

        return Inertia::render('Permintaan/Index', [
            'permintaans' => $permintaans,
            'userLogin' => Auth::user(),
            'filters' => $request->only(['search', 'status', 'bidang', 'tanggal_dari', 'tanggal_sampai', 'per_page']),
        ]);
    }

    /** Show the form for creating a new resource. */
    public function create()
    {
        return Inertia::render('Permintaan/Create');
    }

    /** Store a newly created resource in storage. */
    public function store(Request $request)
    {
        $data = $request->validate([
            // adjust fields to your table structure
            'bidang' => 'nullable|string',
            'tanggal_permintaan' => 'nullable|date',
            'deskripsi' => 'required|string',
            'status' => 'nullable|string',
            'pic_pimpinan' => 'nullable|string',
            'no_nota_dinas' => 'nullable|string',
            'link_scan' => 'nullable|url',
        ]);

        // Auto set status ke 'diajukan' jika tidak ada atau kosong
        if (empty($data['status'])) {
            $data['status'] = 'diajukan';
        }

        // attach current user if available
        if (Auth::check()) {
            $data['user_id'] = Auth::id();
        }

        $permintaan = Permintaan::create($data);

        return redirect()->route('permintaan.index')->with('success', 'Permintaan berhasil dibuat.');
    }

    /** Display the specified resource. */
    public function show(Permintaan $permintaan)
    {
        // Load all relations for tracking
        $permintaan->load([
            'user',
            'notaDinas.disposisi.perencanaan.kso.pengadaan.notaPenerimaan.serahTerima'
        ]);
        
        // Get timeline tracking untuk admin
        $timeline = $permintaan->getTimelineTracking();
        $progress = $permintaan->getProgressPercentage();
        
        return Inertia::render('Permintaan/Show', [
            'permintaan' => $permintaan,
            'trackingStatus' => $permintaan->trackingStatus,
            'timeline' => $timeline,
            'progress' => $progress,
            'userLogin' => Auth::user(),
        ]);
    }

    /** Show the form for editing the specified resource. */
    public function edit(Permintaan $permintaan)
    {
        // Hanya bisa edit jika status ditolak (revisi)
        if (strtolower($permintaan->status) !== 'ditolak') {
            return redirect()->route('permintaan.show', $permintaan)
                ->with('error', 'Permintaan hanya dapat diedit jika dalam status ditolak (revisi).');
        }

        $permintaan->load('user');
        return Inertia::render('Permintaan/Edit', [
            'permintaan' => $permintaan,
        ]);
    }

    /** Update the specified resource in storage. */
    public function update(Request $request, Permintaan $permintaan)
    {
        // Hanya bisa update jika status ditolak (revisi)
        if (strtolower($permintaan->status) !== 'ditolak') {
            return redirect()->route('permintaan.show', $permintaan)
                ->with('error', 'Permintaan hanya dapat diupdate jika dalam status ditolak (revisi).');
        }

        $data = $request->validate([
            'bidang' => 'nullable|string',
            'tanggal_permintaan' => 'nullable|date',
            'deskripsi' => 'required|string',
            'status' => 'nullable|string',
            'pic_pimpinan' => 'nullable|string',
            'no_nota_dinas' => 'nullable|string',
            'link_scan' => 'nullable|url',
        ]);

        $permintaan->update($data);

        return redirect()->route('permintaan.show', $permintaan)->with('success', 'Permintaan diperbarui.');
    }

    /** Remove the specified resource from storage. */
    public function destroy(Permintaan $permintaan)
    {
        // Hanya bisa delete jika status ditolak
        if (strtolower($permintaan->status) !== 'ditolak') {
            return redirect()->route('permintaan.index')
                ->with('error', 'Permintaan hanya dapat dihapus jika dalam status ditolak.');
        }

        $permintaan->delete();
        return redirect()->route('permintaan.index')->with('success', 'Permintaan dihapus.');
    }

    /**
     * Tampilkan timeline tracking untuk permintaan (Admin)
     * Dedicated page untuk melihat tracking detail
     */
    public function tracking(Permintaan $permintaan)
    {
        // Load all relations
        $permintaan->load([
            'user',
            'notaDinas.disposisi.perencanaan.kso.pengadaan.notaPenerimaan.serahTerima'
        ]);

        // Get timeline tracking lengkap
        $timeline = $permintaan->getTimelineTracking();
        $progress = $permintaan->getProgressPercentage();

        // Tahapan yang belum dilalui
        $allSteps = [
            'Permintaan',
            'Nota Dinas',
            'Disposisi',
            'Perencanaan',
            'KSO',
            'Pengadaan',
            'Nota Penerimaan',
            'Serah Terima',
        ];

        $completedSteps = array_column($timeline, 'tahapan');
        $pendingSteps = array_diff($allSteps, $completedSteps);

        return Inertia::render('Permintaan/Tracking', [
            'permintaan' => $permintaan,
            'timeline' => $timeline,
            'progress' => $progress,
            'completedSteps' => $completedSteps,
            'pendingSteps' => array_values($pendingSteps),
            'userLogin' => Auth::user(),
        ]);
    }
}
