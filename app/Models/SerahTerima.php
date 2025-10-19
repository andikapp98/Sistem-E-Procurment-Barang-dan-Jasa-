<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class SerahTerima extends Model
{
    protected $table = 'serah_terima';
    protected $primaryKey = 'serah_id';
    public $timestamps = true;

    protected $casts = [
        'nota_penerimaan_id' => 'int',
        'tanggal_serah' => 'date'
    ];

    protected $fillable = [
        'penerimaan_id',
        'no_serah_terima',
        'tanggal_serah_terima',
        'penyerah',
        'penerima',
        'keterangan',
    ];

    public function notaPenerimaan()
    {
        return $this->belongsTo(NotaPenerimaan::class, 'nota_penerimaan_id', 'nota_penerimaan_id');
    }
}
