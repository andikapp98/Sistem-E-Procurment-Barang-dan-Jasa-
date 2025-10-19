<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class NotaPenerimaan extends Model
{
    protected $table = 'nota_penerimaan';
    protected $primaryKey = 'nota_penerimaan_id';
    public $timestamps = true;

    protected $casts = [
        'pengadaan_id' => 'int',
        'tanggal_penerimaan' => 'date'
    ];

    protected $fillable = [
        'pengadaan_id',
        'no_penerimaan',
        'tanggal_penerimaan',
        'penerima',
        'keterangan',
        'kondisi',
    ];

    public function pengadaan()
    {
        return $this->belongsTo(Pengadaan::class, 'pengadaan_id', 'pengadaan_id');
    }

    public function serahTerima()
    {
        return $this->hasMany(SerahTerima::class, 'nota_penerimaan_id', 'nota_penerimaan_id');
    }
}
