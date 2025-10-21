<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hps extends Model
{
    protected $table = 'hps';
    protected $primaryKey = 'hps_id';
    public $timestamps = true;

    protected $fillable = [
        'permintaan_id',
        'ppk',
        'surat_penawaran_harga',
        'grand_total',
    ];

    protected $casts = [
        'grand_total' => 'decimal:2',
    ];

    public function permintaan()
    {
        return $this->belongsTo(Permintaan::class, 'permintaan_id', 'permintaan_id');
    }

    public function items()
    {
        return $this->hasMany(HpsItem::class, 'hps_id', 'hps_id');
    }
}
