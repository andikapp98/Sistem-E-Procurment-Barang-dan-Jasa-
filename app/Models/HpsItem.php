<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HpsItem extends Model
{
    protected $table = 'hps_items';
    protected $primaryKey = 'hps_item_id';
    public $timestamps = true;

    protected $fillable = [
        'hps_id',
        'nama_item',
        'volume',
        'satuan',
        'harga_satuan',
        'type',
        'merk',
        'total',
    ];

    protected $casts = [
        'volume' => 'integer',
        'harga_satuan' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function hps()
    {
        return $this->belongsTo(Hps::class, 'hps_id', 'hps_id');
    }
}
