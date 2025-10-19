<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DokumenPengadaan extends Model
{
    protected $table = 'dokumen_pengadaan';
    protected $primaryKey = 'dokumen_id';
    
    protected $fillable = [
        'permintaan_id',
        'jenis_dokumen',
        'nama_file',
        'link_file',
        'tanggal_upload',
        'uploaded_by',
        'keterangan'
    ];
    
    protected $casts = [
        'tanggal_upload' => 'date'
    ];
    
    public function permintaan(): BelongsTo
    {
        return $this->belongsTo(Permintaan::class, 'permintaan_id', 'permintaan_id');
    }
}
