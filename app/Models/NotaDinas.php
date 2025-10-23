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
        'tanggal_nota' => 'date',
        'tanggal_faktur_pajak' => 'date',
        'pagu_anggaran' => 'decimal:2',
        'pph' => 'decimal:2',
        'ppn' => 'decimal:2',
        'pph_21' => 'decimal:2',
        'pph_4_2' => 'decimal:2',
        'pph_22' => 'decimal:2'
    ];

    protected $fillable = [
        'permintaan_id',
        'no_nota',
        'tanggal_nota',
        'dari',
        'kepada',
        'perihal',
        'sifat',
        'lampiran',
        'detail',
        'mengetahui',
        'nomor',
        'penerima',
        'kode_program',
        'kode_kegiatan',
        'kode_rekening',
        'uraian',
        'pagu_anggaran',
        'pph',
        'ppn',
        'pph_21',
        'pph_4_2',
        'pph_22',
        'unit_instalasi',
        'no_faktur_pajak',
        'no_kwitansi',
        'tanggal_faktur_pajak'
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
