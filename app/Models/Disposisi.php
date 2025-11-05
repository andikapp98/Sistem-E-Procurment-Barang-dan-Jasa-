<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Disposisi
 * 
 * @property int $disposisi_id
 * @property int $nota_id
 * @property string $jabatan_tujuan
 * @property Carbon $tanggal_disposisi
 * @property string|null $catatan
 * @property string $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Disposisi extends Model
{
    protected $table = 'disposisi';
    protected $primaryKey = 'disposisi_id';
    public $timestamps = true;

    protected $casts = [
        'nota_id' => 'int',
        'tanggal_disposisi' => 'date'
    ];

    protected $fillable = [
        'nota_id',
        'jabatan_tujuan',
        'tanggal_disposisi',
        'catatan',
        'status'
    ];

    /**
     * Relasi ke Nota Dinas
     */
    public function notaDinas()
    {
        return $this->belongsTo(NotaDinas::class, 'nota_id', 'nota_id');
    }

    /**
     * Relasi ke Perencanaan
     * One Disposisi has One Perencanaan
     */
    public function perencanaan()
    {
        return $this->hasOne(Perencanaan::class, 'disposisi_id', 'disposisi_id');
    }
}
