<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Permintaan;
use App\Models\NotaDinas;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class KepalaInstalasiDataSeeder extends Seeder
{
    public function run()
    {
        // 1. Pastikan user kepala instalasi ada
        $kepalaInstalasi = User::firstOrCreate(
            ['email' => 'kepala_instalasi@rsud.id'],
            [
                'nama' => 'Dr. Siti Rahayu, S.Farm., Apt',
                'password' => Hash::make('password123'),
                'role' => 'kepala_instalasi',
                'jabatan' => 'Kepala Instalasi Farmasi',
                'unit_kerja' => 'Instalasi Farmasi',
                'email_verified_at' => now(),
            ]
        );

        echo "✓ Kepala Instalasi: {$kepalaInstalasi->nama}\n";

        // 2. Buat user unit dari Instalasi Farmasi
        $staffFarmasi = User::firstOrCreate(
            ['email' => 'staff.farmasi@rsud.id'],
            [
                'nama' => 'Apt. Budi Santoso',
                'password' => Hash::make('password123'),
                'role' => 'unit',
                'jabatan' => 'Staf Farmasi',
                'unit_kerja' => 'Instalasi Farmasi',
                'email_verified_at' => now(),
            ]
        );

        echo "✓ Staff Farmasi: {$staffFarmasi->nama}\n";

        // 3. Buat beberapa permintaan dari staff farmasi
        $permintaan1 = Permintaan::create([
            'user_id' => $staffFarmasi->user_id,
            'bidang' => 'Farmasi',
            'tanggal_permintaan' => Carbon::now()->subDays(5),
            'deskripsi' => "Permintaan Pengadaan Obat-obatan:\n\n1. Paracetamol 500mg - 10.000 tablet\n2. Amoxicillin 500mg - 5.000 kapsul\n3. OBH Sirup 100ml - 500 botol\n4. Betadine Solution 100ml - 200 botol\n\nUntuk kebutuhan stok bulan Desember 2025. Stok saat ini menipis dan diperkirakan habis dalam 2 minggu.",
            'status' => 'diajukan',
            'pic_pimpinan' => null,
            'no_nota_dinas' => null,
            'link_scan' => null,
        ]);

        echo "✓ Permintaan 1: Pengadaan Obat-obatan (Status: diajukan)\n";

        $permintaan2 = Permintaan::create([
            'user_id' => $staffFarmasi->user_id,
            'bidang' => 'Farmasi',
            'tanggal_permintaan' => Carbon::now()->subDays(3),
            'deskripsi' => "Permintaan Pengadaan Alat Kesehatan:\n\n1. Spuit 3cc disposable - 2.000 pcs\n2. Spuit 5cc disposable - 1.500 pcs\n3. Masker medis 3 ply - 5.000 pcs\n4. Handscoon latex size M - 1.000 box\n5. Alkohol 70% 1 liter - 100 botol\n\nUntuk kebutuhan operasional ruang rawat inap dan IGD.",
            'status' => 'diajukan',
            'pic_pimpinan' => null,
            'no_nota_dinas' => null,
            'link_scan' => null,
        ]);

        echo "✓ Permintaan 2: Pengadaan Alat Kesehatan (Status: diajukan)\n";

        $permintaan3 = Permintaan::create([
            'user_id' => $staffFarmasi->user_id,
            'bidang' => 'Farmasi',
            'tanggal_permintaan' => Carbon::now()->subDays(10),
            'deskripsi' => "Permintaan Pengadaan Vitamin dan Suplemen:\n\n1. Vitamin C 1000mg - 3.000 tablet\n2. Vitamin B Complex - 2.000 tablet\n3. Zinc 50mg - 1.500 tablet\n4. Multivitamin Syrup - 500 botol\n\nUntuk pasien rawat jalan dan program imunisasi.",
            'status' => 'proses',
            'pic_pimpinan' => $kepalaInstalasi->nama,
            'no_nota_dinas' => 'ND/IF/2025/001',
            'link_scan' => null,
        ]);

        echo "✓ Permintaan 3: Pengadaan Vitamin (Status: proses)\n";

        // 4. Buat nota dinas untuk permintaan yang sudah diproses
        $notaDinas1 = NotaDinas::create([
            'permintaan_id' => $permintaan3->permintaan_id,
            'dari_unit' => 'Instalasi Farmasi',
            'ke_jabatan' => 'Direktur',
            'tanggal_nota' => Carbon::now()->subDays(8),
            'status' => 'proses',
        ]);

        echo "✓ Nota Dinas untuk Permintaan 3 (Tujuan: Direktur)\n";

        // 5. Buat permintaan yang sudah disetujui
        $permintaan4 = Permintaan::create([
            'user_id' => $staffFarmasi->user_id,
            'bidang' => 'Farmasi',
            'tanggal_permintaan' => Carbon::now()->subDays(15),
            'deskripsi' => "Permintaan Pengadaan Obat Antibiotik:\n\n1. Cefadroxil 500mg - 3.000 kapsul\n2. Ciprofloxacin 500mg - 2.000 tablet\n3. Metronidazole 500mg - 2.500 tablet\n4. Gentamicin injection - 500 ampul\n\nUntuk kebutuhan pasien rawat inap dengan infeksi bakteri.",
            'status' => 'disetujui',
            'pic_pimpinan' => $kepalaInstalasi->nama,
            'no_nota_dinas' => 'ND/IF/2024/012',
            'link_scan' => null,
        ]);

        echo "✓ Permintaan 4: Pengadaan Antibiotik (Status: disetujui)\n";

        $notaDinas2 = NotaDinas::create([
            'permintaan_id' => $permintaan4->permintaan_id,
            'dari_unit' => 'Instalasi Farmasi',
            'ke_jabatan' => 'Bagian Pengadaan',
            'tanggal_nota' => Carbon::now()->subDays(14),
            'status' => 'disetujui',
        ]);

        echo "✓ Nota Dinas untuk Permintaan 4 (Status: disetujui, diteruskan ke Bagian Pengadaan)\n";

        // 6. Buat permintaan yang ditolak
        $permintaan5 = Permintaan::create([
            'user_id' => $staffFarmasi->user_id,
            'bidang' => 'Farmasi',
            'tanggal_permintaan' => Carbon::now()->subDays(12),
            'deskripsi' => "Permintaan Pengadaan Obat Generik:\n\n1. Ibuprofen 400mg - 5.000 tablet\n2. Asam Mefenamat 500mg - 4.000 tablet\n\n[DITOLAK] Stok obat generik masih mencukupi untuk 3 bulan ke depan. Pengadaan dapat ditunda hingga bulan Januari 2026.",
            'status' => 'ditolak',
            'pic_pimpinan' => $kepalaInstalasi->nama,
            'no_nota_dinas' => null,
            'link_scan' => null,
        ]);

        echo "✓ Permintaan 5: Pengadaan Obat Generik (Status: ditolak)\n";

        $notaDinas3 = NotaDinas::create([
            'permintaan_id' => $permintaan5->permintaan_id,
            'dari_unit' => 'Instalasi Farmasi',
            'ke_jabatan' => 'Unit Pemohon',
            'tanggal_nota' => Carbon::now()->subDays(11),
            'status' => 'ditolak',
        ]);

        echo "✓ Nota Dinas untuk Permintaan 5 (Status: ditolak)\n";

        echo "\n========================================\n";
        echo "DATA SEEDING BERHASIL!\n";
        echo "========================================\n";
        echo "Total Permintaan: 5\n";
        echo "  - Diajukan: 2\n";
        echo "  - Proses: 1\n";
        echo "  - Disetujui: 1\n";
        echo "  - Ditolak: 1\n";
        echo "\nLogin sebagai Kepala Instalasi:\n";
        echo "Email: kepala_instalasi@rsud.id\n";
        echo "Password: password123\n";
        echo "========================================\n";
    }
}
