<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpesifikasiTeknis extends Model
{
    protected $table = 'spesifikasi_teknis';
    protected $primaryKey = 'spesifikasi_id';
    
    protected $fillable = [
        'permintaan_id',
        // Section 1: Latar Belakang & Tujuan
        'latar_belakang',
        'maksud_tujuan',
        'target_sasaran',
        
        // Section 2: Pejabat & Anggaran
        'pejabat_pengadaan',
        'sumber_dana',
        'perkiraan_biaya',
        
        // Section 3: Detail Barang/Jasa
        'jenis_barang_jasa',
        'fungsi_manfaat',
        'kegiatan_rutin',
        
        // Section 4: Waktu & Tenaga
        'jangka_waktu',
        'estimasi_waktu_datang',
        'tenaga_diperlukan',
        
        // Section 5: Pelaku Usaha & Konsolidasi
        'pelaku_usaha',
        'pengadaan_sejenis',
        'pengadaan_sejenis_keterangan',
        'indikasi_konsolidasi',
        'indikasi_konsolidasi_keterangan',
    ];

    /**
     * Relasi ke Permintaan
     */
    public function permintaan(): BelongsTo
    {
        return $this->belongsTo(Permintaan::class, 'permintaan_id', 'permintaan_id');
    }
}
