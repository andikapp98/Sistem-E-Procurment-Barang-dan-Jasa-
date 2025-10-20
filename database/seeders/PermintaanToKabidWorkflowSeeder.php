<?php

namespace Database\Seeders;

use App\Models\Permintaan;
use App\Models\NotaDinas;
use App\Models\Disposisi;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class PermintaanToKabidWorkflowSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Seeder untuk melengkapi workflow dari Permintaan ke Kepala Bidang:
     * 1. Nota Dinas dari Kepala Instalasi
     * 2. Disposisi ke Kepala Bidang yang sesuai
     */
    public function run(): void
    {
        // Get Kepala Bidang users
        $kabidYanmed = User::where('email', 'kabid.yanmed@rsud.id')->first();
        $kabidPenunjang = User::where('email', 'kabid.penunjang@rsud.id')->first();
        $kabidKeperawatan = User::where('email', 'kabid.keperawatan@rsud.id')->first();

        if (!$kabidYanmed || !$kabidPenunjang || !$kabidKeperawatan) {
            $this->command->error('❌ Kepala Bidang users tidak ditemukan. Jalankan KepalaBidangSeeder terlebih dahulu!');
            return;
        }

        // Get all permintaan that need nota dinas
        $permintaans = Permintaan::whereIn('bidang', ['Gawat Darurat', 'Farmasi', 'Laboratorium', 'Radiologi', 'Bedah Sentral'])
            ->whereDoesntHave('notaDinas')
            ->with('user')
            ->get();

        if ($permintaans->isEmpty()) {
            $this->command->warn('⚠️  Tidak ada permintaan yang perlu dibuatkan Nota Dinas.');
            return;
        }

        $notaDinasCount = 0;
        $disposisiCount = 0;

        foreach ($permintaans as $permintaan) {
            // Tentukan Kepala Bidang berdasarkan jenis instalasi
            $kepalaBidang = $this->getKepalaBidang($permintaan->bidang, $kabidYanmed, $kabidPenunjang, $kabidKeperawatan);
            
            if (!$kepalaBidang) {
                continue;
            }

            // Buat Nota Dinas
            $notaDinas = NotaDinas::create([
                'permintaan_id' => $permintaan->permintaan_id,
                'no_nota' => $this->generateNoNota($permintaan->bidang, $permintaan->permintaan_id),
                'tanggal_nota' => Carbon::parse($permintaan->tanggal_permintaan)->addDays(1),
                'dari' => $permintaan->pic_pimpinan,
                'kepada' => $kepalaBidang['nama'],
                'perihal' => $this->generatePerihal($permintaan->deskripsi),
            ]);

            $notaDinasCount++;

            // Buat Disposisi ke Kepala Bidang
            $disposisi = Disposisi::create([
                'nota_id' => $notaDinas->nota_id,
                'jabatan_tujuan' => $kepalaBidang['jabatan'],
                'tanggal_disposisi' => Carbon::parse($notaDinas->tanggal_nota)->addDays(1),
                'catatan' => $this->generateCatatan($permintaan, $kepalaBidang['jabatan']),
                'status' => $this->getDisposisiStatus($permintaan->status),
            ]);

            $disposisiCount++;

            // Update status permintaan jika masih 'diajukan'
            if ($permintaan->status === 'diajukan') {
                $permintaan->update(['status' => 'proses']);
            }
        }

        $this->command->info('✅ Workflow Permintaan ke Kepala Bidang berhasil dibuat!');
        $this->command->info('');
        $this->command->info('📋 Ringkasan:');
        $this->command->info('═══════════════════════════════════════════════════════════════════════════════════════');
        $this->command->info("- Nota Dinas dibuat: {$notaDinasCount}");
        $this->command->info("- Disposisi ke Kepala Bidang: {$disposisiCount}");
        $this->command->info('═══════════════════════════════════════════════════════════════════════════════════════');
        $this->command->info('');
        $this->command->info('📊 Detail per Kepala Bidang:');
        
        // Summary per Kepala Bidang
        $this->showKabidSummary('Kepala Bidang Pelayanan Medis', ['Gawat Darurat', 'Bedah Sentral']);
        $this->showKabidSummary('Kepala Bidang Penunjang Medis', ['Laboratorium', 'Radiologi']);
        $this->showKabidSummary('Kepala Bidang Keperawatan', ['Farmasi']);
    }

    /**
     * Tentukan Kepala Bidang berdasarkan jenis instalasi
     */
    private function getKepalaBidang($bidang, $kabidYanmed, $kabidPenunjang, $kabidKeperawatan)
    {
        $mapping = [
            'Gawat Darurat' => [
                'user' => $kabidYanmed,
                'nama' => 'Dr. Lestari Wijaya, M.Kes',
                'jabatan' => 'Kepala Bidang Pelayanan Medis'
            ],
            'Bedah Sentral' => [
                'user' => $kabidYanmed,
                'nama' => 'Dr. Lestari Wijaya, M.Kes',
                'jabatan' => 'Kepala Bidang Pelayanan Medis'
            ],
            'Laboratorium' => [
                'user' => $kabidPenunjang,
                'nama' => 'Dr. Rina Kusumawati, Sp.PK, M.Kes',
                'jabatan' => 'Kepala Bidang Penunjang Medis'
            ],
            'Radiologi' => [
                'user' => $kabidPenunjang,
                'nama' => 'Dr. Rina Kusumawati, Sp.PK, M.Kes',
                'jabatan' => 'Kepala Bidang Penunjang Medis'
            ],
            'Farmasi' => [
                'user' => $kabidKeperawatan,
                'nama' => 'Ns. Maria Ulfa, S.Kep, M.Kep',
                'jabatan' => 'Kepala Bidang Keperawatan'
            ],
        ];

        return $mapping[$bidang] ?? null;
    }

    /**
     * Generate nomor nota dinas
     */
    private function generateNoNota($bidang, $permintaanId)
    {
        $prefix = $this->getBidangPrefix($bidang);
        return "ND/{$prefix}/" . date('Y') . "/" . str_pad($permintaanId, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Generate perihal nota dinas
     */
    private function generatePerihal($deskripsi)
    {
        // Ambil kata-kata kunci dari deskripsi (max 100 karakter)
        $perihal = substr($deskripsi, 0, 100);
        if (strlen($deskripsi) > 100) {
            $perihal .= '...';
        }
        return "Permintaan Pengadaan: " . $perihal;
    }

    /**
     * Generate catatan disposisi
     */
    private function generateCatatan($permintaan, $jabatanKabid)
    {
        return "Mohon untuk ditindaklanjuti permintaan pengadaan dari {$permintaan->user->unit_kerja}. "
            . "Kepada Yth. {$jabatanKabid}, agar dapat dilakukan review dan persetujuan "
            . "sesuai dengan prosedur yang berlaku. "
            . "Deskripsi: " . substr($permintaan->deskripsi, 0, 150);
    }

    /**
     * Get disposisi status based on permintaan status
     */
    private function getDisposisiStatus($permintaanStatus)
    {
        $statusMapping = [
            'diajukan' => 'pending',
            'proses' => 'diproses',
            'disetujui' => 'selesai',
            'ditolak' => 'ditolak',
            'revisi' => 'pending',
        ];

        return $statusMapping[$permintaanStatus] ?? 'pending';
    }

    /**
     * Get bidang prefix for nota dinas number
     */
    private function getBidangPrefix($bidang)
    {
        $prefixMap = [
            'Gawat Darurat' => 'IGD',
            'Farmasi' => 'FAR',
            'Laboratorium' => 'LAB',
            'Radiologi' => 'RAD',
            'Bedah Sentral' => 'BEDAH',
        ];

        return $prefixMap[$bidang] ?? 'UMUM';
    }

    /**
     * Show summary per Kepala Bidang
     */
    private function showKabidSummary($jabatan, $bidangList)
    {
        $total = 0;
        foreach ($bidangList as $bidang) {
            $count = Permintaan::where('bidang', $bidang)
                ->whereHas('notaDinas.disposisi')
                ->count();
            $total += $count;
        }

        if ($total > 0) {
            $this->command->info("  • {$jabatan}: {$total} disposisi (" . implode(', ', $bidangList) . ")");
        }
    }
}
