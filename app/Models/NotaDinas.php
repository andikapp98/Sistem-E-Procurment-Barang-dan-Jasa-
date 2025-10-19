<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class NotaDinas
 * 
 * @property int $nota_id
 * @property int $permintaan_id
 * @property string|null $no_nota
 * @property Carbon $tanggal_nota
 * @property string|null $dari
 * @property string|null $kepada
 * @property string|null $perihal
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class NotaDinas extends Model
{
    protected $table = 'nota_dinas';
    protected $primaryKey = 'nota_id';
    public $timestamps = true; // Enable timestamps

    protected $casts = [
        'permintaan_id' => 'int',
        'tanggal_nota' => 'date'
    ];

    protected $fillable = [
        'permintaan_id',
        'no_nota',
        'tanggal_nota',
        'dari',
        'kepada',
        'perihal'
    ];

    /**
     * Relasi ke Permintaan
     */
    public function permintaan()
    {
        return $this->belongsTo(Permintaan::class, 'permintaan_id', 'permintaan_id');
    }

    /**
     * Relasi ke Disposisi
     */
    public function disposisi()
    {
        return $this->hasMany(Disposisi::class, 'nota_id', 'nota_id');
    }
}
