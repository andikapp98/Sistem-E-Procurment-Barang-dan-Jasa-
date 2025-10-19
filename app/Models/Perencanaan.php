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
        'anggaran' => 'decimal:2'
    ];

    protected $fillable = [
        'disposisi_id',
        'rencana_kegiatan',
        'tanggal_mulai',
        'tanggal_selesai',
        'anggaran',
        'link_scan_perencanaan',
        'metode_pengadaan'
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
