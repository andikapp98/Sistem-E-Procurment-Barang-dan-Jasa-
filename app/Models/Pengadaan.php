<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Pengadaan
 * 
 * REVISI WORKFLOW: Pengadaan setelah Perencanaan, sebelum KSO
 * Urutan baru: Perencanaan → Pengadaan → KSO
 */
class Pengadaan extends Model
{
    protected $table = 'pengadaan';
    protected $primaryKey = 'pengadaan_id';
    public $timestamps = true;

    protected $casts = [
        'perencanaan_id' => 'int', // REVISI: perencanaan_id instead of kso_id
        'kso_id' => 'int',         // DEPRECATED: will be removed
        'tanggal_pengadaan' => 'date'
    ];

    protected $fillable = [
        'perencanaan_id', // NEW: Link to Perencanaan
        'kso_id',        // DEPRECATED: Keep for backward compatibility
        'no_pengadaan',
        'tanggal_pengadaan',
        'vendor',
        'total_harga',
        'status',
        'no_tracking',
    ];

    /**
     * Relasi ke Perencanaan (NEW: Parent)
     */
    public function perencanaan()
    {
        return $this->belongsTo(Perencanaan::class, 'perencanaan_id', 'perencanaan_id');
    }

    /**
     * Relasi ke KSO (REVISI: Pengadaan has many KSO)
     */
    public function kso()
    {
        return $this->hasMany(Kso::class, 'pengadaan_id', 'pengadaan_id');
    }

    /**
     * Relasi ke Nota Penerimaan (DEPRECATED: will move to KSO)
     * Nota Penerimaan sekarang via KSO
     */
    public function notaPenerimaan()
    {
        return $this->hasMany(NotaPenerimaan::class, 'pengadaan_id', 'pengadaan_id');
    }
}
