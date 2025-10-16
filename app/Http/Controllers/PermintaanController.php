<?php

namespace App\Http\Controllers;

use App\Models\Permintaan;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class PermintaanController extends Controller
{
    /** Display a listing of the resource. */
    public function index()
    {
        $permintaans = Permintaan::with('user')
            ->orderByDesc('permintaan_id')
            ->get();

        return Inertia::render('Permintaan/Index', [
            'permintaans' => $permintaans,
            'userLogin' => Auth::user(),
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
        // Load relasi user
        $permintaan->load('user');
        
        // Load tracking tahapan (jika sudah ada data)
        // Nanti akan digunakan saat tabel tracking sudah ada data
        // $permintaan->load([
        //     'notaDinas',
        //     'notaDinas.disposisi',
        //     'notaDinas.disposisi.perencanaan',
        //     'notaDinas.disposisi.perencanaan.kso',
        //     'notaDinas.disposisi.perencanaan.kso.pengadaan',
        //     'notaDinas.disposisi.perencanaan.kso.pengadaan.notaPenerimaan',
        //     'notaDinas.disposisi.perencanaan.kso.pengadaan.notaPenerimaan.serahTerima',
        // ]);
        
        return Inertia::render('Permintaan/Show', [
            'permintaan' => $permintaan,
        ]);
    }

    /** Show the form for editing the specified resource. */
    public function edit(Permintaan $permintaan)
    {
        $permintaan->load('user');
        return Inertia::render('Permintaan/Edit', [
            'permintaan' => $permintaan,
        ]);
    }

    /** Update the specified resource in storage. */
    public function update(Request $request, Permintaan $permintaan)
    {
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
        $permintaan->delete();
        return redirect()->route('permintaan.index')->with('success', 'Permintaan dihapus.');
    }
}
