<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class NotaPenerimaan
 * 
 * REVISI WORKFLOW: Nota Penerimaan setelah KSO
 * Urutan baru: Pengadaan → KSO → Nota Penerimaan
 */
class NotaPenerimaan extends Model
{
    protected $table = 'nota_penerimaan';
    protected $primaryKey = 'nota_penerimaan_id';
    public $timestamps = true;

    protected $casts = [
        'kso_id' => 'int',         // REVISI: kso_id instead of pengadaan_id
        'pengadaan_id' => 'int',   // DEPRECATED: will be removed
        'tanggal_penerimaan' => 'date'
    ];

    protected $fillable = [
        'kso_id',         // NEW: Link to KSO
        'pengadaan_id',   // DEPRECATED: Keep for backward compatibility
        'no_penerimaan',
        'tanggal_penerimaan',
        'penerima',
        'keterangan',
        'kondisi',
    ];

    /**
     * Relasi ke KSO (REVISI: Parent is KSO)
     */
    public function kso()
    {
        return $this->belongsTo(Kso::class, 'kso_id', 'kso_id');
    }

    /**
     * Relasi ke Pengadaan (DEPRECATED: untuk backward compatibility)
     */
    public function pengadaan()
    {
        return $this->belongsTo(Pengadaan::class, 'pengadaan_id', 'pengadaan_id');
    }

    /**
     * Relasi ke Serah Terima
     */
    public function serahTerima()
    {
        return $this->hasMany(SerahTerima::class, 'nota_penerimaan_id', 'nota_penerimaan_id');
    }
}
