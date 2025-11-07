<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Kso
 * 
 * REVISI WORKFLOW: KSO setelah Pengadaan
 * Urutan baru: Perencanaan → Pengadaan → KSO
 */
class Kso extends Model
{
    protected $table = 'kso';
    protected $primaryKey = 'kso_id';
    public $timestamps = true;

    protected $casts = [
        'pengadaan_id' => 'int',   // REVISI: pengadaan_id instead of perencanaan_id
        'perencanaan_id' => 'int', // DEPRECATED: will be removed
        'tanggal_kso' => 'date'
    ];

    protected $fillable = [
        'pengadaan_id',   // NEW: Link to Pengadaan
        'perencanaan_id', // DEPRECATED: Keep for backward compatibility
        'no_kso',
        'tanggal_kso',
        'pihak_pertama',
        'pihak_kedua',
        'isi_kerjasama',
        'nilai_kontrak',
        'file_pks',
        'file_mou',
        'keterangan',
        'status'
    ];

    /**
     * Relasi ke Pengadaan (REVISI: Parent is Pengadaan)
     */
    public function pengadaan()
    {
        return $this->belongsTo(Pengadaan::class, 'pengadaan_id', 'pengadaan_id');
    }

    /**
     * Relasi ke Perencanaan (DEPRECATED: untuk backward compatibility)
     */
    public function perencanaan()
    {
        return $this->belongsTo(Perencanaan::class, 'perencanaan_id', 'perencanaan_id');
    }

    /**
     * Relasi ke Nota Penerimaan (REVISI: KSO has many Nota Penerimaan)
     */
    public function notaPenerimaan()
    {
        return $this->hasMany(NotaPenerimaan::class, 'kso_id', 'kso_id');
    }
}
