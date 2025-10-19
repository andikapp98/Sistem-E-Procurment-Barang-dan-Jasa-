<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Kso extends Model
{
    protected $table = 'kso';
    protected $primaryKey = 'kso_id';
    public $timestamps = true;

    protected $casts = [
        'perencanaan_id' => 'int',
        'tanggal_kso' => 'date'
    ];

    protected $fillable = [
        'perencanaan_id',
        'no_kso',
        'tanggal_kso',
        'pihak_pertama',
        'pihak_kedua',
        'isi_kerjasama',
        'nilai_kontrak',
        'status'
    ];

    public function perencanaan()
    {
        return $this->belongsTo(Perencanaan::class, 'perencanaan_id', 'perencanaan_id');
    }

    public function pengadaan()
    {
        return $this->hasMany(Pengadaan::class, 'kso_id', 'kso_id');
    }
}
