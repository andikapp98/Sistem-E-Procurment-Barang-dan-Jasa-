<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

/**
 * Class Permintaan
 * 
 * @property int $permintaan_id
 * @property int|null $user_id
 * @property string|null $bidang
 * @property Carbon|null $tanggal_permintaan
 * @property string|null $deskripsi
 * @property string|null $status
 * @property string|null $pic_pimpinan
 * @property string|null $no_nota_dinas
 * @property string|null $link_scan
 *
 * @package App\Models
 */
class Permintaan extends Model
{
	protected $table = 'permintaan';
	protected $primaryKey = 'permintaan_id';
	public $timestamps = false;

	protected $casts = [
		'user_id' => 'int',
		'tanggal_permintaan' => 'datetime'
	];

	protected $fillable = [
		'user_id',
		'bidang',
		'tanggal_permintaan',
		'deskripsi',
		'status',
		'pic_pimpinan',
		'no_nota_dinas',
		'link_scan'
	];

	/**
	 * The user that owns the permintaan.
	 */
	public function user()
	{
		return $this->belongsTo(User::class, 'user_id', 'user_id');
	}

	/**
	 * Relasi ke Nota Dinas
	 */
	public function notaDinas()
	{
		return $this->hasMany(NotaDinas::class, 'permintaan_id', 'permintaan_id');
	}

	/**
	 * Get tracking status lengkap untuk permintaan ini
	 */
	public function getTrackingStatusAttribute()
	{
		$status = 'Permintaan';
		
		// Cek apakah ada nota dinas
		if ($this->notaDinas && $this->notaDinas->count() > 0) {
			$status = 'Nota Dinas';
			
			// Cek tahapan selanjutnya hanya jika method/relasi ada
			// Untuk sementara, kita hanya cek sampai nota dinas
			// Relasi lain (disposisi, perencanaan, dll) bisa ditambahkan nanti
		}
		
		return $status;
	}
}
