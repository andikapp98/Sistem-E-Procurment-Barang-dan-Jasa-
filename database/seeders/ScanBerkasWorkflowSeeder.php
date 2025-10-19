<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Permintaan;
use App\Models\NotaDinas;
use App\Models\Disposisi;
use Carbon\Carbon;

/**
 * Seeder untuk workflow: Kabid -> Wadir -> Direktur -> Staff Perencanaan
 * Untuk testing fitur Scan Berkas
 */
class ScanBerkasWorkflowSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Cari atau buat users
        $kepalaInstalasi = User::where('role', 'kepala_instalasi')->first();
        $kepalaBidang = User::where('role', 'kepala_bidang')->first();
        $wakilDirektur = User::where('role', 'wakil_direktur')->first();
        $direktur = User::where('role', 'direktur')->first();
        $staffPerencanaan = User::where('role', 'staff_perencanaan')->first();

        if (!$kepalaInstalasi || !$kepalaBidang || !$wakilDirektur || !$direktur || !$staffPerencanaan) {
            $this->command->error('User dengan role yang diperlukan belum ada. Jalankan seeder user terlebih dahulu.');
            return;
        }

        // 2. Buat 5 permintaan untuk testing scan berkas
        $bidangList = ['Instalasi Farmasi', 'Instalasi Gizi', 'Instalasi Laboratorium', 'Instalasi Radiologi', 'Instalasi Bedah Sentral'];
        $jenisBarang = [
            'Pengadaan Alat Kesehatan',
            'Pengadaan Obat-obatan',
            'Pengadaan Peralatan Medis',
            'Pengadaan Bahan Habis Pakai',
            'Pengadaan Sistem Informasi'
        ];

        for ($i = 1; $i <= 5; $i++) {
            $bidang = $bidangList[$i - 1];
            $tanggalPermintaan = Carbon::now()->subDays(30 - ($i * 2));

            // Buat Permintaan
            $permintaan = Permintaan::create([
                'user_id' => $kepalaInstalasi->id,
                'bidang' => $bidang,
                'tanggal_permintaan' => $tanggalPermintaan,
                'deskripsi' => "{$jenisBarang[$i - 1]} untuk {$bidang}. Permintaan ini telah melalui approval lengkap dan siap untuk scan berkas.",
                'status' => 'disetujui',
                'pic_pimpinan' => 'Staff Perencanaan',
                'no_nota_dinas' => 'ND/RSHD/' . Carbon::now()->format('Y') . '/' . str_pad($i, 4, '0', STR_PAD_LEFT),
            ]);

            // Buat Nota Dinas dari Kepala Instalasi ke Kepala Bidang
            $notaDinas1 = NotaDinas::create([
                'permintaan_id' => $permintaan->permintaan_id,
                'no_nota' => 'ND/KI/' . Carbon::now()->format('Y') . '/' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'tanggal_nota' => $tanggalPermintaan->copy()->addDays(1),
                'dari' => 'Kepala Instalasi',
                'kepada' => 'Kepala Bidang',
                'perihal' => $jenisBarang[$i - 1],
            ]);

            // Disposisi dari Kabid ke Wadir
            $disposisi1 = Disposisi::create([
                'nota_id' => $notaDinas1->nota_id,
                'jabatan_tujuan' => 'Wakil Direktur',
                'tanggal_disposisi' => $tanggalPermintaan->copy()->addDays(2),
                'catatan' => 'Disetujui oleh Kepala Bidang. Mohon persetujuan Wakil Direktur.',
                'status' => 'disetujui',
            ]);

            // Nota Dinas dari Kabid ke Wadir
            $notaDinas2 = NotaDinas::create([
                'permintaan_id' => $permintaan->permintaan_id,
                'no_nota' => 'ND/KB/' . Carbon::now()->format('Y') . '/' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'tanggal_nota' => $tanggalPermintaan->copy()->addDays(3),
                'dari' => 'Kepala Bidang',
                'kepada' => 'Wakil Direktur',
                'perihal' => $jenisBarang[$i - 1],
            ]);

            // Disposisi dari Wadir ke Direktur
            $disposisi2 = Disposisi::create([
                'nota_id' => $notaDinas2->nota_id,
                'jabatan_tujuan' => 'Direktur',
                'tanggal_disposisi' => $tanggalPermintaan->copy()->addDays(4),
                'catatan' => 'Disetujui oleh Wakil Direktur. Mohon persetujuan Direktur.',
                'status' => 'disetujui',
            ]);

            // Nota Dinas dari Wadir ke Direktur
            $notaDinas3 = NotaDinas::create([
                'permintaan_id' => $permintaan->permintaan_id,
                'no_nota' => 'ND/WD/' . Carbon::now()->format('Y') . '/' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'tanggal_nota' => $tanggalPermintaan->copy()->addDays(5),
                'dari' => 'Wakil Direktur',
                'kepada' => 'Direktur',
                'perihal' => $jenisBarang[$i - 1],
            ]);

            // Disposisi dari Direktur ke Staff Perencanaan
            $disposisi3 = Disposisi::create([
                'nota_id' => $notaDinas3->nota_id,
                'jabatan_tujuan' => 'Staff Perencanaan',
                'tanggal_disposisi' => $tanggalPermintaan->copy()->addDays(6),
                'catatan' => 'Disetujui oleh Direktur. Silakan lakukan scan berkas dan lanjutkan proses pengadaan.',
                'status' => 'disetujui',
            ]);

            $this->command->info("✓ Permintaan #{$permintaan->permintaan_id} - {$bidang} - Siap untuk Scan Berkas");
        }

        $this->command->info("\n=== Seeder Scan Berkas Workflow Selesai ===");
        $this->command->info("Total: 5 permintaan siap untuk scan berkas oleh Staff Perencanaan");
        $this->command->info("\nLogin sebagai Staff Perencanaan untuk mengakses fitur Scan Berkas:");
        $this->command->info("Email: {$staffPerencanaan->email}");
        $this->command->info("Password: password (default)");
    }
}
