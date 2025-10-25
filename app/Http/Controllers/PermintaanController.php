<?php

namespace App\Http\Controllers;

use App\Models\Permintaan;
use App\Models\NotaDinas;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
            'disposisi_tujuan' => 'required|string',
            'catatan_disposisi' => 'nullable|string',
            'wadir_tujuan' => 'nullable|string',
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

        // Auto set status ke 'diajukan' jika tidak ada atau kosong
        if (empty($data['status'])) {
            $data['status'] = 'diajukan';
        }

        // attach current user if available
        if (Auth::check()) {
            $data['user_id'] = Auth::id();
        }

        // Extract nota dinas data
        $notaDinasData = [
            'kepada' => $data['nota_kepada'],
            'dari' => $data['nota_dari'],
            'tanggal_nota' => $data['nota_tanggal_nota'],
            'no_nota' => $data['nota_no_nota'],
            'sifat' => $data['nota_sifat'] ?? null,
            'lampiran' => $data['nota_lampiran'] ?? $data['link_scan'] ?? null,
            'perihal' => $data['nota_perihal'],
            'detail' => $data['nota_detail'] ?? null,
            'mengetahui' => $data['nota_mengetahui'] ?? null,
        ];

        // Remove nota dinas fields from permintaan data
        unset(
            $data['nota_kepada'], 
            $data['nota_dari'], 
            $data['nota_tanggal_nota'], 
            $data['nota_no_nota'],
            $data['nota_sifat'],
            $data['nota_lampiran'],
            $data['nota_perihal'],
            $data['nota_detail'],
            $data['nota_mengetahui']
        );

        // Create permintaan
        $permintaan = Permintaan::create($data);

        // Create nota dinas
        $permintaan->notaDinas()->create($notaDinasData);

        return redirect()->route('permintaan.index')->with('success', 'Permintaan dan Nota Dinas berhasil dibuat.');
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
        $user = Auth::user();
        
        // Admin bisa edit permintaan dengan status 'revisi' atau 'ditolak'
        $allowedStatuses = ['revisi', 'ditolak'];
        if (!in_array(strtolower($permintaan->status), $allowedStatuses)) {
            return redirect()->route('permintaan.show', $permintaan)
                ->with('error', 'Permintaan hanya dapat diedit jika dalam status Revisi atau Ditolak.');
        }

        $permintaan->load('user');
        return Inertia::render('Permintaan/Edit', [
            'permintaan' => $permintaan,
            'userLogin' => $user,
        ]);
    }

    /** Update the specified resource in storage. */
    public function update(Request $request, Permintaan $permintaan)
    {
        // Admin bisa update permintaan dengan status 'revisi' atau 'ditolak'
        $allowedStatuses = ['revisi', 'ditolak'];
        if (!in_array(strtolower($permintaan->status), $allowedStatuses)) {
            return redirect()->route('permintaan.show', $permintaan)
                ->with('error', 'Permintaan hanya dapat diupdate jika dalam status Revisi atau Ditolak.');
        }

        $data = $request->validate([
            'bidang' => 'nullable|string',
            'tanggal_permintaan' => 'nullable|date',
            'deskripsi' => 'required|string',
            'pic_pimpinan' => 'nullable|string',
            'no_nota_dinas' => 'nullable|string',
            'link_scan' => 'nullable|url',
            'disposisi_tujuan' => 'nullable|string',
            'catatan_disposisi' => 'nullable|string',
            'wadir_tujuan' => 'nullable|string',
        ]);

        // Set status ke 'diajukan' untuk resubmit
        $data['status'] = 'diajukan';

        $permintaan->update($data);

        return redirect()->route('permintaan.show', $permintaan)
            ->with('success', 'Permintaan berhasil diperbaiki dan diajukan kembali.');
    }

    /** Remove the specified resource from storage. */
    public function destroy(Permintaan $permintaan)
    {
        $user = Auth::user();
        
        // Hanya admin yang bisa delete
        if ($user->role !== 'admin') {
            return redirect()->route('permintaan.index')
                ->with('error', 'Anda tidak memiliki akses untuk menghapus permintaan.');
        }
        
        // Hanya bisa delete jika status 'ditolak'
        if (strtolower($permintaan->status) !== 'ditolak') {
            return redirect()->route('permintaan.show', $permintaan)
                ->with('error', 'Permintaan hanya dapat dihapus jika dalam status Ditolak.');
        }

        $permintaanId = $permintaan->permintaan_id;
        $permintaan->delete();

        return redirect()->route('permintaan.index')
            ->with('success', "Permintaan #{$permintaanId} berhasil dihapus.");
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

        // Get complete tracking (termasuk tahapan yang belum dilalui)
        $completeTracking = $permintaan->getCompleteTracking();
        $progress = $permintaan->getProgressPercentage();
        $nextStep = $permintaan->getNextStep();

        // Pisahkan completed dan pending steps
        $completedSteps = array_filter($completeTracking, function($item) {
            return $item['completed'];
        });
        
        $pendingSteps = array_filter($completeTracking, function($item) {
            return !$item['completed'];
        });

        return Inertia::render('Permintaan/Tracking', [
            'permintaan' => $permintaan,
            'completeTracking' => array_values($completeTracking),
            'completedSteps' => array_values($completedSteps),
            'pendingSteps' => array_values($pendingSteps),
            'nextStep' => $nextStep,
            'progress' => $progress,
            'userLogin' => Auth::user(),
        ]);
    }

    /**
     * Cetak Nota Dinas
     * Generate HTML untuk cetak nota dinas
     */
    public function cetakNotaDinas(Permintaan $permintaan)
    {
        // Load nota dinas terkait
        $permintaan->load(['notaDinas', 'user']);
        
        // Ambil nota dinas pertama (atau yang terbaru)
        $notaDinas = $permintaan->notaDinas()->latest('created_at')->first();
        
        if (!$notaDinas) {
            return redirect()->route('permintaan.show', $permintaan)
                ->with('error', 'Nota Dinas tidak ditemukan untuk permintaan ini.');
        }

        // Return view untuk cetak
        return view('cetak.nota-dinas', [
            'permintaan' => $permintaan,
            'notaDinas' => $notaDinas,
        ]);
    }

    /**
     * Lihat/Download Lampiran Nota Dinas
     */
    public function lihatLampiran(NotaDinas $notaDinas)
    {
        // Validasi apakah nota dinas punya lampiran
        if (!$notaDinas->lampiran) {
            return redirect()->back()->with('error', 'Lampiran tidak ditemukan untuk nota dinas ini.');
        }

        // Jika lampiran adalah URL (http/https), redirect ke URL tersebut
        if (filter_var($notaDinas->lampiran, FILTER_VALIDATE_URL)) {
            return redirect($notaDinas->lampiran);
        }

        // Jika lampiran adalah file path di storage
        if (Storage::exists($notaDinas->lampiran)) {
            return Storage::download($notaDinas->lampiran);
        }

        // Jika file tidak ditemukan
        return redirect()->back()->with('error', 'File lampiran tidak ditemukan.');
    }
}
