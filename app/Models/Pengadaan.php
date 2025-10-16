<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Pengadaan extends Model
{
    protected $table = 'pengadaan';
    protected $primaryKey = 'pengadaan_id';
    public $timestamps = true;

    protected $casts = [
        'kso_id' => 'int',
        'tanggal_pengadaan' => 'date'
    ];

    protected $fillable = [
        'kso_id',
        'tanggal_pengadaan',
        'vendor',
        'tracking',
        'status'
    ];

    public function kso()
    {
        return $this->belongsTo(Kso::class, 'kso_id', 'kso_id');
    }

    public function notaPenerimaan()
    {
        return $this->hasMany(NotaPenerimaan::class, 'pengadaan_id', 'pengadaan_id');
    }
}
