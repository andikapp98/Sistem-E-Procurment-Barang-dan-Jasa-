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
	public $timestamps = true; // Enable timestamps

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
		return $this->belongsTo(User::class, 'user_id', 'id'); // Fixed: reference 'id' not 'user_id'
	}

	/**
	 * Relasi ke Nota Dinas
	 */
	public function notaDinas()
	{
		return $this->hasMany(NotaDinas::class, 'permintaan_id', 'permintaan_id');
	}

	/**
	 * Relasi ke Dokumen Pengadaan
	 */
	public function dokumenPengadaan()
	{
		return $this->hasMany(DokumenPengadaan::class, 'permintaan_id', 'permintaan_id');
	}

	/**
	 * Get tracking status lengkap untuk permintaan ini
	 */
	public function getTrackingStatusAttribute()
	{
		$timeline = $this->getTimelineTracking();
		
		if (empty($timeline)) {
			return 'Permintaan';
		}
		
		// Return tahap terakhir
		return $timeline[count($timeline) - 1]['tahapan'];
	}

	/**
	 * Get timeline tracking lengkap untuk permintaan ini
	 * Return array tahapan yang sudah dilalui
	 */
	public function getTimelineTracking()
	{
		$timeline = [];

		// TAHAP 1: Permintaan
		$timeline[] = [
			'tahapan' => 'Permintaan',
			'tanggal' => $this->tanggal_permintaan,
			'status' => $this->status,
			'keterangan' => 'Permintaan diajukan',
			'icon' => 'document',
			'completed' => true,
		];

		// TAHAP 2: Nota Dinas
		$notaDinas = $this->notaDinas()->latest('tanggal_nota')->first();
		if ($notaDinas) {
			$timeline[] = [
				'tahapan' => 'Nota Dinas',
				'tanggal' => $notaDinas->tanggal_nota,
				'status' => $notaDinas->status,
				'keterangan' => "Nota dinas ke: {$notaDinas->ke_jabatan}",
				'icon' => 'mail',
				'completed' => true,
			];

			// TAHAP 3: Disposisi
			$disposisi = $notaDinas->disposisi()->latest('tanggal_disposisi')->first();
			if ($disposisi) {
				$timeline[] = [
					'tahapan' => 'Disposisi',
					'tanggal' => $disposisi->tanggal_disposisi,
					'status' => $disposisi->status,
					'keterangan' => "Disposisi ke: {$disposisi->jabatan_tujuan}",
					'icon' => 'clipboard',
					'completed' => true,
				];

				// TAHAP 4: Perencanaan
				$perencanaan = $disposisi->perencanaan()->orderBy('perencanaan_id', 'desc')->first();
				if ($perencanaan) {
					$timeline[] = [
						'tahapan' => 'Perencanaan',
						'tanggal' => $perencanaan->tanggal_mulai ?? $perencanaan->created_at,
						'status' => 'selesai',
						'keterangan' => 'Tahap perencanaan pengadaan',
						'icon' => 'chart',
						'completed' => true,
					];

					// TAHAP 5: KSO
					$kso = $perencanaan->kso()->latest('tanggal_kso')->first();
					if ($kso) {
						$timeline[] = [
							'tahapan' => 'KSO',
							'tanggal' => $kso->tanggal_kso,
							'status' => $kso->status,
							'keterangan' => 'Kerja Sama Operasional',
							'icon' => 'handshake',
							'completed' => true,
						];

						// TAHAP 6: Pengadaan
						$pengadaan = $kso->pengadaan()->latest('tanggal_pengadaan')->first();
						if ($pengadaan) {
							$timeline[] = [
								'tahapan' => 'Pengadaan',
								'tanggal' => $pengadaan->tanggal_pengadaan,
								'status' => $pengadaan->status,
								'keterangan' => "Vendor: {$pengadaan->vendor}" . ($pengadaan->tracking ? " | Tracking: {$pengadaan->tracking}" : ''),
								'icon' => 'shopping-cart',
								'completed' => true,
							];

							// TAHAP 7: Nota Penerimaan
							$notaPenerimaan = $pengadaan->notaPenerimaan()->latest('tanggal_penerimaan')->first();
							if ($notaPenerimaan) {
								$timeline[] = [
									'tahapan' => 'Nota Penerimaan',
									'tanggal' => $notaPenerimaan->tanggal_penerimaan,
									'status' => $notaPenerimaan->status,
									'keterangan' => 'Penerimaan barang/jasa',
									'icon' => 'inbox',
									'completed' => true,
								];

								// TAHAP 8: Serah Terima
								$serahTerima = $notaPenerimaan->serahTerima()->latest('tanggal_serah')->first();
								if ($serahTerima) {
									$timeline[] = [
										'tahapan' => 'Serah Terima',
										'tanggal' => $serahTerima->tanggal_serah,
										'status' => $serahTerima->status,
										'keterangan' => "Penerima: {$serahTerima->penerima}",
										'icon' => 'check-circle',
										'completed' => true,
									];
								}
							}
						}
					}
				}
			}
		}

		return $timeline;
	}

	/**
	 * Get progress percentage
	 */
	public function getProgressPercentage()
	{
		$timeline = $this->getTimelineTracking();
		$totalSteps = 8; // Total tahapan: Permintaan, Nota Dinas, Disposisi, Perencanaan, KSO, Pengadaan, Nota Penerimaan, Serah Terima
		$completedSteps = count($timeline);
		
		return round(($completedSteps / $totalSteps) * 100);
	}

	/**
	 * Get next step information
	 * Return informasi tahapan berikutnya yang perlu dilakukan
	 */
	public function getNextStep()
	{
		$allSteps = [
			[
				'step' => 1,
				'tahapan' => 'Permintaan',
				'description' => 'Permintaan dibuat oleh unit',
				'responsible' => 'Unit/Admin',
			],
			[
				'step' => 2,
				'tahapan' => 'Nota Dinas',
				'description' => 'Kepala Instalasi membuat nota dinas',
				'responsible' => 'Kepala Instalasi',
			],
			[
				'step' => 3,
				'tahapan' => 'Disposisi',
				'description' => 'Disposisi oleh pimpinan (Kepala Bidang → Direktur)',
				'responsible' => 'Kepala Bidang / Direktur',
			],
			[
				'step' => 4,
				'tahapan' => 'Perencanaan',
				'description' => 'Staff Perencanaan membuat rencana pengadaan',
				'responsible' => 'Staff Perencanaan',
			],
			[
				'step' => 5,
				'tahapan' => 'KSO',
				'description' => 'Kerja Sama Operasional dengan vendor',
				'responsible' => 'Bagian KSO',
			],
			[
				'step' => 6,
				'tahapan' => 'Pengadaan',
				'description' => 'Proses pengadaan dan pembelian',
				'responsible' => 'Bagian Pengadaan',
			],
			[
				'step' => 7,
				'tahapan' => 'Nota Penerimaan',
				'description' => 'Penerimaan barang/jasa dari vendor',
				'responsible' => 'Bagian Serah Terima',
			],
			[
				'step' => 8,
				'tahapan' => 'Serah Terima',
				'description' => 'Serah terima kepada unit pemohon',
				'responsible' => 'Bagian Serah Terima',
			],
		];

		$completedTimeline = $this->getTimelineTracking();
		$currentStep = count($completedTimeline);

		// Jika semua tahap selesai
		if ($currentStep >= 8) {
			return [
				'step' => 8,
				'tahapan' => 'Selesai',
				'description' => 'Semua tahapan telah selesai',
				'responsible' => '-',
				'completed' => true,
			];
		}

		// Return tahap berikutnya
		$nextStep = $allSteps[$currentStep];
		$nextStep['completed'] = false;
		return $nextStep;
	}

	/**
	 * Get all remaining steps
	 * Return array semua tahapan yang belum dilalui
	 */
	public function getRemainingSteps()
	{
		$allSteps = [
			'Permintaan',
			'Nota Dinas',
			'Disposisi',
			'Perencanaan',
			'KSO',
			'Pengadaan',
			'Nota Penerimaan',
			'Serah Terima',
		];

		$completedTimeline = $this->getTimelineTracking();
		$completedSteps = array_column($completedTimeline, 'tahapan');

		return array_values(array_diff($allSteps, $completedSteps));
	}

	/**
	 * Get complete tracking with next steps
	 * Return timeline lengkap termasuk tahapan yang belum dilalui
	 */
	public function getCompleteTracking()
	{
		$allSteps = [
			[
				'step' => 1,
				'tahapan' => 'Permintaan',
				'description' => 'Permintaan dibuat oleh unit',
				'responsible' => 'Unit/Admin',
				'icon' => 'document',
			],
			[
				'step' => 2,
				'tahapan' => 'Nota Dinas',
				'description' => 'Kepala Instalasi membuat nota dinas',
				'responsible' => 'Kepala Instalasi',
				'icon' => 'mail',
			],
			[
				'step' => 3,
				'tahapan' => 'Disposisi',
				'description' => 'Disposisi oleh pimpinan',
				'responsible' => 'Kepala Bidang / Direktur',
				'icon' => 'clipboard',
			],
			[
				'step' => 4,
				'tahapan' => 'Perencanaan',
				'description' => 'Staff Perencanaan membuat rencana pengadaan',
				'responsible' => 'Staff Perencanaan',
				'icon' => 'chart',
			],
			[
				'step' => 5,
				'tahapan' => 'KSO',
				'description' => 'Kerja Sama Operasional dengan vendor',
				'responsible' => 'Bagian KSO',
				'icon' => 'handshake',
			],
			[
				'step' => 6,
				'tahapan' => 'Pengadaan',
				'description' => 'Proses pengadaan dan pembelian',
				'responsible' => 'Bagian Pengadaan',
				'icon' => 'shopping-cart',
			],
			[
				'step' => 7,
				'tahapan' => 'Nota Penerimaan',
				'description' => 'Penerimaan barang/jasa dari vendor',
				'responsible' => 'Bagian Serah Terima',
				'icon' => 'inbox',
			],
			[
				'step' => 8,
				'tahapan' => 'Serah Terima',
				'description' => 'Serah terima kepada unit pemohon',
				'responsible' => 'Bagian Serah Terima',
				'icon' => 'check-circle',
			],
		];

		$completedTimeline = $this->getTimelineTracking();
		$result = [];

		foreach ($allSteps as $stepInfo) {
			// Cari apakah tahap ini sudah ada di completed timeline
			$completed = null;
			foreach ($completedTimeline as $item) {
				if ($item['tahapan'] === $stepInfo['tahapan']) {
					$completed = $item;
					break;
				}
			}

			if ($completed) {
				// Tahap sudah selesai
				$result[] = [
					'step' => $stepInfo['step'],
					'tahapan' => $stepInfo['tahapan'],
					'description' => $stepInfo['description'],
					'responsible' => $stepInfo['responsible'],
					'icon' => $stepInfo['icon'],
					'tanggal' => $completed['tanggal'],
					'status' => $completed['status'],
					'keterangan' => $completed['keterangan'],
					'completed' => true,
				];
			} else {
				// Tahap belum dilalui
				$result[] = [
					'step' => $stepInfo['step'],
					'tahapan' => $stepInfo['tahapan'],
					'description' => $stepInfo['description'],
					'responsible' => $stepInfo['responsible'],
					'icon' => $stepInfo['icon'],
					'tanggal' => null,
					'status' => 'pending',
					'keterangan' => 'Belum dilaksanakan',
					'completed' => false,
				];
			}
		}

		return $result;
	}
}
