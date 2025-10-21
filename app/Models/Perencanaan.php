<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Perencanaan
 */
class Perencanaan extends Model
{
    protected $table = 'perencanaan';
    protected $primaryKey = 'perencanaan_id';
    public $timestamps = true;

    protected $casts = [
        'disposisi_id' => 'int',
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'anggaran' => 'decimal:2',
        'pagu_paket' => 'decimal:2',
        'nilai_hps' => 'decimal:2',
    ];

    protected $fillable = [
        'disposisi_id',
        'rencana_kegiatan',
        'tanggal_mulai',
        'tanggal_selesai',
        'anggaran',
        'link_scan_perencanaan',
        'metode_pengadaan',
        // DPP Fields
        'ppk_ditunjuk',
        'nama_paket',
        'lokasi',
        'uraian_program',
        'uraian_kegiatan',
        'sub_kegiatan',
        'sub_sub_kegiatan',
        'kode_rekening',
        'sumber_dana',
        'pagu_paket',
        'nilai_hps',
        'sumber_data_survei_hps',
        'jenis_kontrak',
        'kualifikasi',
        'jangka_waktu_pelaksanaan',
        'nama_kegiatan',
        'jenis_pengadaan',
    ];

    public function disposisi()
    {
        return $this->belongsTo(Disposisi::class, 'disposisi_id', 'disposisi_id');
    }

    public function kso()
    {
        return $this->hasMany(Kso::class, 'perencanaan_id', 'perencanaan_id');
    }
}
