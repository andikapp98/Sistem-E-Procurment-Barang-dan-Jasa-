<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class NotaDinas
 * 
 * @property int $nota_id
 * @property int $permintaan_id
 * @property string|null $dari_unit
 * @property string|null $ke_jabatan
 * @property Carbon $tanggal_nota
 * @property string $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class NotaDinas extends Model
{
    protected $table = 'nota_dinas';
    protected $primaryKey = 'nota_id';
    public $timestamps = false; // Nonaktifkan timestamps karena tabel tidak punya kolom updated_at

    protected $casts = [
        'permintaan_id' => 'int',
        'tanggal_nota' => 'date'
    ];

    protected $fillable = [
        'permintaan_id',
        'dari_unit',
        'ke_jabatan',
        'tanggal_nota',
        'status'
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
