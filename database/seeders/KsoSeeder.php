<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KsoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "\n🔄 Memulai seeder untuk data KSO...\n\n";

        // Cek apakah sudah ada data perencanaan
        $perencanaanData = DB::table('perencanaan')
            ->select('perencanaan_id', 'rencana_kegiatan')
            ->get();

        // Jika belum ada perencanaan, buat dummy perencanaan dulu
        if ($perencanaanData->isEmpty()) {
            echo "📝 Tidak ada data perencanaan. Membuat dummy data perencanaan dan disposisi...\n";
            
            // Buat dummy disposisi dulu
            $dummyDisposisi = [];
            for ($i = 1; $i <= 15; $i++) {
                $dummyDisposisi[] = [
                    'nota_id' => 1, // Asumsi ada nota_id 1
                    'jabatan_tujuan' => 'Staff Perencanaan',
                    'tanggal_disposisi' => Carbon::now()->subDays(rand(1, 30)),
                    'catatan' => 'Untuk keperluan KSO',
                    'status' => 'selesai',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }
            
            DB::table('disposisi')->insert($dummyDisposisi);
            
            // Ambil ID disposisi yang baru dibuat
            $disposisiIds = DB::table('disposisi')
                ->orderBy('disposisi_id', 'desc')
                ->limit(15)
                ->pluck('disposisi_id')
                ->toArray();
            
            $dummyPerencanaan = [];
            foreach ($disposisiIds as $index => $disposisiId) {
                $dummyPerencanaan[] = [
                    'disposisi_id' => $disposisiId,
                    'rencana_kegiatan' => 'Rencana Kegiatan KSO ' . ($index + 1),
                    'tanggal_mulai' => Carbon::now()->addDays($index + 1),
                    'tanggal_selesai' => Carbon::now()->addDays($index + 31),
                    'anggaran' => rand(10, 500) * 1000000,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }
            
            DB::table('perencanaan')->insert($dummyPerencanaan);
            
            // Ambil lagi data perencanaan yang baru dibuat
            $perencanaanData = DB::table('perencanaan')
                ->select('perencanaan_id', 'rencana_kegiatan')
                ->get();
            
            echo "✅ Berhasil membuat " . count($dummyPerencanaan) . " dummy perencanaan\n\n";
        }

        echo "📊 Ditemukan " . $perencanaanData->count() . " data perencanaan\n";
        echo "🎯 Membuat data KSO untuk " . min(15, $perencanaanData->count()) . " permintaan...\n\n";

        $ksoData = [];
        $vendors = [
            'PT. Kimia Farma Tbk',
            'PT. Indofarma Tbk',
            'PT. Phapros Tbk',
            'PT. Tempo Scan Pacific Tbk',
            'PT. Kalbe Farma Tbk',
            'PT. Combiphar',
            'PT. Sanbe Farma',
            'PT. Meprofarm',
            'PT. Novartis Indonesia',
            'PT. Pfizer Indonesia',
            'PT. Sanofi Aventis Indonesia',
            'PT. GSK Indonesia',
            'CV. Mitra Medika',
            'CV. Sumber Sehat',
            'CV. Karya Medis',
            'PT. Siloam Hospitals',
            'PT. Enseval Putera Megatrading',
            'PT. Prodia Widyahusada',
        ];

        $isiKerjasama = [
            'Kerjasama pengadaan obat-obatan dan alat kesehatan untuk kebutuhan operasional rumah sakit. Termasuk di dalamnya adalah obat generik, obat paten, alat kesehatan habis pakai, dan reagensia laboratorium. Kerjasama ini berlaku untuk periode 1 tahun dengan opsi perpanjangan.',
            
            'Kerjasama pengadaan alat kesehatan medis dan non-medis untuk menunjang pelayanan kesehatan di rumah sakit. Meliputi alat diagnostik, alat bedah, alat perawatan pasien, dan peralatan penunjang medis lainnya. Termasuk juga layanan after-sales berupa maintenance dan kalibrasi berkala.',
            
            'Kerjasama pengadaan reagensia dan bahan habis pakai laboratorium untuk pemeriksaan penunjang diagnostik. Mencakup reagensia kimia klinik, hematologi, imunologi, mikrobiologi, dan patologi anatomi. Vendor wajib menyediakan training penggunaan dan SOP untuk setiap produk.',
            
            'Kerjasama pengadaan alat kesehatan dan peralatan medis untuk instalasi gawat darurat. Meliputi monitor pasien, ventilator, defibrillator, suction pump, dan emergency cart. Vendor bertanggung jawab atas instalasi, commissioning, dan pelatihan operator.',
            
            'Kerjasama pengadaan obat-obatan khusus dan high-cost drugs untuk penanganan kasus-kasus tertentu. Termasuk obat kanker, obat imunologi, obat jantung, dan obat-obat khusus lainnya. Pembayaran dilakukan secara konsinyasi dengan sistem 30-45 hari.',
            
            'Kerjasama pengadaan alat radiologi dan imaging diagnostic untuk instalasi radiologi. Meliputi CT Scan, MRI, X-Ray Digital, USG 4D, dan Mammography. Termasuk juga maintenance contract selama 5 tahun dan training untuk radiographer.',
            
            'Kerjasama pengadaan sistem informasi manajemen rumah sakit (SIMRS) terintegrasi. Mencakup modul administrasi, medis, farmasi, laboratorium, radiologi, dan keuangan. Vendor menyediakan customization, training, dan technical support 24/7.',
            
            'Kerjasama pengadaan peralatan bedah dan instrumen surgical untuk instalasi bedah sentral. Meliputi set bedah umum, bedah orthopedi, bedah saraf, bedah jantung, dan bedah minimal invasif. Termasuk layanan sterilisasi dan sharpening berkala.',
            
            'Kerjasama pengadaan bahan habis pakai medis (BHPM) untuk seluruh instalasi rumah sakit. Mencakup sarung tangan, masker, APD, infus set, spuit, kateter, dan dressing wound. Pengiriman dilakukan weekly sesuai kebutuhan dengan sistem just-in-time.',
            
            'Kerjasama pengadaan gas medis untuk kebutuhan operasional rumah sakit. Meliputi oksigen, nitrogen, CO2, dan gas anestesi. Vendor bertanggung jawab atas instalasi pipeline, maintenance reguler, dan emergency supply 24 jam.',
        ];

        $status = ['draft', 'aktif', 'selesai'];
        $createdDates = [];
        
        // Generate tanggal-tanggal yang berbeda
        for ($i = 0; $i < 15; $i++) {
            $createdDates[] = Carbon::now()->subDays(rand(1, 90));
        }

        // Buat 15 data KSO dari perencanaan yang ada
        $count = 0;
        foreach ($perencanaanData->take(15) as $index => $perencanaan) {
            $vendorIndex = $index % count($vendors);
            $isiIndex = $index % count($isiKerjasama);
            $statusIndex = $index % 3;
            
            // Nilai kontrak random antara 10 juta - 500 juta
            $nilaiKontrak = rand(10, 500) * 1000000;
            
            $createdAt = $createdDates[$index];
            $updatedAt = (clone $createdAt)->addDays(rand(1, 7));
            
            $ksoData[] = [
                'perencanaan_id' => $perencanaan->perencanaan_id,
                'no_kso' => 'KSO/' . str_pad($index + 1, 3, '0', STR_PAD_LEFT) . '/RSUD/' . date('Y'),
                'tanggal_kso' => $createdAt->format('Y-m-d'),
                'pihak_pertama' => 'RSUD Kota Sehat',
                'pihak_kedua' => $vendors[$vendorIndex],
                'isi_kerjasama' => $isiKerjasama[$isiIndex],
                'nilai_kontrak' => $nilaiKontrak,
                'status' => $status[$statusIndex],
                'created_at' => $createdAt,
                'updated_at' => $updatedAt,
            ];
            
            $count++;
        }

        // Insert ke database
        DB::table('kso')->insert($ksoData);

        echo "✅ Berhasil membuat {$count} data KSO!\n\n";

        // Tampilkan statistik
        echo "📊 STATISTIK DATA KSO:\n";
        echo "══════════════════════════════════════════════════════════════\n";
        
        $stats = DB::table('kso')
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get();
        
        foreach ($stats as $stat) {
            $emoji = match($stat->status) {
                'draft' => '📝',
                'aktif' => '✅',
                'selesai' => '🏁',
                'batal' => '❌',
                default => '📌'
            };
            echo "  {$emoji} Status {$stat->status}: {$stat->total} dokumen\n";
        }
        
        echo "\n💰 NILAI KONTRAK:\n";
        echo "══════════════════════════════════════════════════════════════\n";
        
        $totalNilai = DB::table('kso')->sum('nilai_kontrak');
        $avgNilai = DB::table('kso')->avg('nilai_kontrak');
        $maxNilai = DB::table('kso')->max('nilai_kontrak');
        $minNilai = DB::table('kso')->min('nilai_kontrak');
        
        echo "  💵 Total Nilai Kontrak: Rp " . number_format($totalNilai, 0, ',', '.') . "\n";
        echo "  📊 Rata-rata: Rp " . number_format($avgNilai, 0, ',', '.') . "\n";
        echo "  📈 Tertinggi: Rp " . number_format($maxNilai, 0, ',', '.') . "\n";
        echo "  📉 Terendah: Rp " . number_format($minNilai, 0, ',', '.') . "\n";
        
        echo "\n🏢 VENDOR TERLIBAT:\n";
        echo "══════════════════════════════════════════════════════════════\n";
        
        $vendors = DB::table('kso')
            ->select('pihak_kedua', DB::raw('count(*) as total'))
            ->groupBy('pihak_kedua')
            ->orderByDesc('total')
            ->limit(10)
            ->get();
        
        foreach ($vendors as $vendor) {
            echo "  🏪 {$vendor->pihak_kedua}: {$vendor->total} KSO\n";
        }
        
        echo "\n📋 CONTOH DATA KSO:\n";
        echo "══════════════════════════════════════════════════════════════\n";
        
        $samples = DB::table('kso')
            ->orderBy('kso_id', 'desc')
            ->limit(3)
            ->get();
        
        foreach ($samples as $i => $sample) {
            echo "\n  " . ($i + 1) . ". {$sample->no_kso}\n";
            echo "     📅 Tanggal: {$sample->tanggal_kso}\n";
            echo "     🏢 Vendor: {$sample->pihak_kedua}\n";
            echo "     💰 Nilai: Rp " . number_format($sample->nilai_kontrak, 0, ',', '.') . "\n";
            echo "     📌 Status: {$sample->status}\n";
            echo "     📝 Isi: " . substr($sample->isi_kerjasama, 0, 80) . "...\n";
        }
        
        echo "\n══════════════════════════════════════════════════════════════\n";
        echo "✅ Seeder KSO selesai!\n";
        echo "🎯 Silakan login ke http://localhost:8000/kso/dashboard\n";
        echo "📧 Email: kso@rsud.id | Password: password\n";
        echo "══════════════════════════════════════════════════════════════\n\n";
    }
}
