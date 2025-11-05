<?php

namespace Database\Seeders;

use App\Models\Permintaan;
use App\Models\NotaDinas;
use App\Models\Disposisi;
use App\Models\Perencanaan;
use App\Models\Hps;
use App\Models\HpsItem;
use App\Models\SpesifikasiTeknis;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class CompleteStaffPerencanaanSeeder extends Seeder
{
    /**
     * Seeder lengkap untuk Staff Perencanaan
     * Includes: Permintaan → Nota Dinas → Disposisi → Perencanaan (DPP) → HPS → Spesifikasi Teknis
     */
    public function run(): void
    {
        $this->command->info('🚀 Creating complete workflow data untuk Staff Perencanaan...');
        $this->command->info('');

        // Get users
        $kepalaIGD = User::where('email', 'kepala.igd@rsud.id')->first();
        $staffPerencanaan = User::where('email', 'perencanaan@rsud.id')->first();

        if (!$kepalaIGD || !$staffPerencanaan) {
            $this->command->error('❌ Users tidak ditemukan! Jalankan UserSeeder terlebih dahulu.');
            return;
        }

        // ========================================================
        // PERMINTAAN 1: Complete dengan semua dokumen (DPP, HPS, Spesifikasi Teknis)
        // ========================================================
        $this->command->info('📝 Creating Permintaan #1 - Complete dengan semua dokumen...');
        
        $permintaan1 = Permintaan::create([
            'user_id' => $kepalaIGD->id,
            'bidang' => 'Instalasi Gawat Darurat',
            'klasifikasi_permintaan' => 'Medis',
            'tanggal_permintaan' => Carbon::now()->subDays(30),
            'deskripsi' => 'Pengadaan 2 unit Defibrillator Portable merk Philips HeartStart HS1 untuk IGD. Defibrillator yang ada sudah berusia 10 tahun dan sering error. Spesifikasi: Biphasic, AED mode, Battery life 4 tahun.',
            'status' => 'proses',
            'pic_pimpinan' => 'Bagian Pengadaan',
            'no_nota_dinas' => 'ND/IGD/2025/001',
            'created_at' => Carbon::now()->subDays(30),
        ]);

        // Nota Dinas
        $nota1 = NotaDinas::create([
            'permintaan_id' => $permintaan1->permintaan_id,
            'no_nota' => 'ND/IGD/2025/001',
            'dari' => 'Kepala Instalasi Gawat Darurat',
            'kepada' => 'Staff Perencanaan',
            'tanggal_nota' => Carbon::now()->subDays(29),
            'perihal' => 'Permohonan Pengadaan Defibrillator Portable',
            'sifat' => 'Segera',
            'detail' => 'Mohon diproses perencanaan pengadaan 2 unit defibrillator portable untuk meningkatkan kesiapan penanganan cardiac arrest di IGD.',
        ]);

        // Disposisi
        $disposisi1 = Disposisi::create([
            'nota_id' => $nota1->nota_id,
            'jabatan_tujuan' => 'Staff Perencanaan',
            'tanggal_disposisi' => Carbon::now()->subDays(28),
            'catatan' => 'Disetujui oleh Direktur. Segera buat perencanaan pengadaan.',
            'status' => 'disetujui',
        ]);

        // Perencanaan (DPP)
        $perencanaan1 = Perencanaan::create([
            'disposisi_id' => $disposisi1->disposisi_id,
            'rencana_kegiatan' => 'Pengadaan Defibrillator Portable untuk IGD',
            'tanggal_mulai' => Carbon::now()->subDays(25),
            'tanggal_selesai' => Carbon::now()->addDays(30),
            'anggaran' => 85000000,
            'metode_pengadaan' => 'E-Purchasing',
            
            // DPP Fields
            'ppk_ditunjuk' => 'Dr. Ahmad Yani, Sp.PD',
            'nama_paket' => 'Pengadaan Defibrillator Portable IGD Tahun 2025',
            'lokasi' => 'RSUD Ibnu Sina Kabupaten Gresik - Instalasi Gawat Darurat',
            'uraian_program' => 'Program Peningkatan Mutu Pelayanan Kesehatan',
            'uraian_kegiatan' => 'Kegiatan Pengadaan Alat Kesehatan',
            'sub_kegiatan' => 'Pengadaan Alat Medis Emergency',
            'kode_rekening' => '5.1.02.01.01.0051',
            'sumber_dana' => 'APBD Kabupaten Gresik',
            'pagu_paket' => 85000000,
            'nilai_hps' => 82000000,
            'sumber_data_survei_hps' => 'Survei pasar toko alat kesehatan dan e-catalog',
            'jenis_kontrak' => 'Lumsum',
            'kualifikasi' => 'Non Kualifikasi',
            'jangka_waktu_pelaksanaan' => 30,
            'nama_kegiatan' => 'Pengadaan Defibrillator Portable IGD',
            'jenis_pengadaan' => 'Barang',
        ]);

        // HPS
        $hps1 = Hps::create([
            'permintaan_id' => $permintaan1->permintaan_id,
            'metode_pengadaan' => 'E-Purchasing',
            'total_hps' => 82000000,
            'keterangan' => 'HPS berdasarkan survei pasar dan e-catalog LKPP',
        ]);

        // HPS Items
        HpsItem::create([
            'hps_id' => $hps1->hps_id,
            'nama_item' => 'Defibrillator Portable Philips HeartStart HS1',
            'spesifikasi' => 'Biphasic waveform, AED mode, Voice prompts, Battery life 4 years, Includes carrying case and pads',
            'kuantitas' => 2,
            'satuan' => 'Unit',
            'harga_satuan' => 40000000,
            'total_harga' => 80000000,
        ]);

        HpsItem::create([
            'hps_id' => $hps1->hps_id,
            'nama_item' => 'Electrode Pads (spare)',
            'spesifikasi' => 'Compatible with Philips HS1, Adult pads',
            'kuantitas' => 4,
            'satuan' => 'Set',
            'harga_satuan' => 500000,
            'total_harga' => 2000000,
        ]);

        // Spesifikasi Teknis
        SpesifikasiTeknis::create([
            'permintaan_id' => $permintaan1->permintaan_id,
            'nama_barang' => 'Defibrillator Portable',
            'spesifikasi' => "Spesifikasi Teknis Defibrillator Portable:\n\n1. SPESIFIKASI UMUM:\n   - Jenis: Automated External Defibrillator (AED)\n   - Waveform: Biphasic\n   - Mode: AED dengan panduan suara\n   - Display: LED indicators untuk status dan panduan\n\n2. SPESIFIKASI TEKNIS:\n   - Energy output: 150 Joules\n   - Shock delivery time: < 10 detik dari deteksi VF\n   - Voice prompts: Bahasa Indonesia dan Inggris\n   - Visual prompts: LED status indicators\n   - Battery life: Minimum 4 tahun standby atau 200 shocks\n   - Self-test: Otomatis harian\n\n3. KELENGKAPAN:\n   - Main unit with battery installed\n   - Adult electrode pads (1 set installed)\n   - Spare adult electrode pads (2 sets)\n   - Carrying case with strap\n   - Quick reference guide\n   - User manual (Bahasa Indonesia)\n\n4. SERTIFIKASI:\n   - ISO 13485 (Medical devices quality management)\n   - CE Mark\n   - FDA Approved\n   - Izin edar Kemenkes RI\n\n5. GARANSI & AFTER SALES:\n   - Garansi unit: 3 tahun\n   - Garansi battery: 4 tahun\n   - Training operator: Included\n   - Maintenance: Free 1st year",
            'kuantitas' => 2,
            'satuan' => 'Unit',
        ]);

        $this->command->info('   ✅ Permintaan #' . $permintaan1->permintaan_id . ' - Complete dengan DPP, HPS (2 items), dan Spesifikasi Teknis');

        // ========================================================
        // PERMINTAAN 2: Hanya sampai DPP (belum ada HPS)
        // ========================================================
        $this->command->info('📝 Creating Permintaan #2 - Dengan DPP saja...');

        $permintaan2 = Permintaan::create([
            'user_id' => $kepalaIGD->id,
            'bidang' => 'Instalasi Farmasi',
            'klasifikasi_permintaan' => 'Medis',
            'tanggal_permintaan' => Carbon::now()->subDays(20),
            'deskripsi' => 'Pengadaan Obat Emergency untuk stok 3 bulan: Adrenalin injeksi 100 ampul, Atropin injeksi 100 ampul, Dopamin injeksi 50 vial.',
            'status' => 'proses',
            'pic_pimpinan' => 'Staff Perencanaan',
            'no_nota_dinas' => 'ND/FARM/2025/002',
            'created_at' => Carbon::now()->subDays(20),
        ]);

        $nota2 = NotaDinas::create([
            'permintaan_id' => $permintaan2->permintaan_id,
            'no_nota' => 'ND/FARM/2025/002',
            'dari' => 'Kepala Instalasi Farmasi',
            'kepada' => 'Staff Perencanaan',
            'tanggal_nota' => Carbon::now()->subDays(19),
            'perihal' => 'Permohonan Pengadaan Obat Emergency',
            'sifat' => 'Sangat Segera',
        ]);

        $disposisi2 = Disposisi::create([
            'nota_id' => $nota2->nota_id,
            'jabatan_tujuan' => 'Staff Perencanaan',
            'tanggal_disposisi' => Carbon::now()->subDays(18),
            'catatan' => 'Approved. Prioritas tinggi.',
            'status' => 'disetujui',
        ]);

        Perencanaan::create([
            'disposisi_id' => $disposisi2->disposisi_id,
            'rencana_kegiatan' => 'Pengadaan Obat Emergency Farmasi',
            'tanggal_mulai' => Carbon::now()->subDays(15),
            'tanggal_selesai' => Carbon::now()->addDays(45),
            'anggaran' => 25000000,
            'metode_pengadaan' => 'E-Catalog',
            'ppk_ditunjuk' => 'Apt. Siti Nurhaliza, S.Farm',
            'nama_paket' => 'Pengadaan Obat Emergency Tahun 2025',
            'lokasi' => 'RSUD Ibnu Sina - Instalasi Farmasi',
            'uraian_program' => 'Program Pelayanan Kesehatan',
            'uraian_kegiatan' => 'Pengadaan Obat dan Perbekalan Kesehatan',
            'kode_rekening' => '5.1.02.01.01.0052',
            'sumber_dana' => 'APBD',
            'pagu_paket' => 25000000,
            'nilai_hps' => 24500000,
            'sumber_data_survei_hps' => 'E-catalog LKPP',
            'jenis_kontrak' => 'Lumsum',
            'nama_kegiatan' => 'Pengadaan Obat Emergency',
            'jenis_pengadaan' => 'Barang',
        ]);

        $this->command->info('   ✅ Permintaan #' . $permintaan2->permintaan_id . ' - Dengan DPP (belum ada HPS)');

        // ========================================================
        // PERMINTAAN 3: Baru sampai Staff Perencanaan (belum ada DPP)
        // ========================================================
        $this->command->info('📝 Creating Permintaan #3 - Baru diterima Staff Perencanaan...');

        $permintaan3 = Permintaan::create([
            'user_id' => $kepalaIGD->id,
            'bidang' => 'Instalasi Laboratorium',
            'klasifikasi_permintaan' => 'Penunjang',
            'tanggal_permintaan' => Carbon::now()->subDays(5),
            'deskripsi' => 'Pengadaan Reagen Hematologi untuk pemeriksaan darah lengkap 1000 test: Complete Blood Count reagent, Control solution, Diluent.',
            'status' => 'disetujui',
            'pic_pimpinan' => 'Staff Perencanaan',
            'no_nota_dinas' => 'ND/LAB/2025/003',
            'created_at' => Carbon::now()->subDays(5),
        ]);

        NotaDinas::create([
            'permintaan_id' => $permintaan3->permintaan_id,
            'no_nota' => 'ND/LAB/2025/003',
            'dari' => 'Kepala Instalasi Laboratorium',
            'kepada' => 'Staff Perencanaan',
            'tanggal_nota' => Carbon::now()->subDays(4),
            'perihal' => 'Permohonan Pengadaan Reagen Hematologi',
            'sifat' => 'Biasa',
        ]);

        $this->command->info('   ✅ Permintaan #' . $permintaan3->permintaan_id . ' - Baru diterima (belum ada DPP)');

        // ========================================================
        // PERMINTAAN 4: Siap untuk Staff Perencanaan
        // ========================================================
        $this->command->info('📝 Creating Permintaan #4 - Siap diproses...');

        $permintaan4 = Permintaan::create([
            'user_id' => $kepalaIGD->id,
            'bidang' => 'Instalasi Radiologi',
            'klasifikasi_permintaan' => 'Penunjang',
            'tanggal_permintaan' => Carbon::now()->subDays(8),
            'deskripsi' => 'Pengadaan Film Rontgen ukuran 14x17 inch sebanyak 500 lembar dan Developer + Fixer masing-masing 20 liter.',
            'status' => 'disetujui',
            'pic_pimpinan' => 'Staff Perencanaan',
            'no_nota_dinas' => 'ND/RAD/2025/004',
            'created_at' => Carbon::now()->subDays(8),
        ]);

        NotaDinas::create([
            'permintaan_id' => $permintaan4->permintaan_id,
            'no_nota' => 'ND/RAD/2025/004',
            'dari' => 'Kepala Instalasi Radiologi',
            'kepada' => 'Staff Perencanaan',
            'tanggal_nota' => Carbon::now()->subDays(7),
            'perihal' => 'Permohonan Pengadaan Film Rontgen dan Kimia',
        ]);

        $this->command->info('   ✅ Permintaan #' . $permintaan4->permintaan_id . ' - Siap diproses');

        // Summary
        $this->command->info('');
        $this->command->info('========================================');
        $this->command->info('✅ SEEDING COMPLETE!');
        $this->command->info('========================================');
        $this->command->info('');
        $this->command->info('📊 Data Created:');
        $this->command->info('   • 4 Permintaan untuk Staff Perencanaan');
        $this->command->info('   • 4 Nota Dinas');
        $this->command->info('   • 3 Disposisi');
        $this->command->info('   • 2 Perencanaan (DPP)');
        $this->command->info('   • 1 HPS dengan 2 items');
        $this->command->info('   • 1 Spesifikasi Teknis');
        $this->command->info('');
        $this->command->info('📋 Status Permintaan:');
        $this->command->info('   1. #' . $permintaan1->permintaan_id . ' - ✅ COMPLETE (DPP + HPS + Spesifikasi) - Status: PROSES → Pengadaan');
        $this->command->info('   2. #' . $permintaan2->permintaan_id . ' - ⚙️  DPP ONLY (perlu buat HPS) - Status: PROSES');
        $this->command->info('   3. #' . $permintaan3->permintaan_id . ' - 📝 NEW (perlu buat DPP) - Status: DISETUJUI');
        $this->command->info('   4. #' . $permintaan4->permintaan_id . ' - 📝 NEW (perlu buat DPP) - Status: DISETUJUI');
        $this->command->info('');
        $this->command->info('🔐 Login sebagai Staff Perencanaan:');
        $this->command->info('   📧 Email: perencanaan@rsud.id');
        $this->command->info('   🔑 Password: password');
        $this->command->info('');
        $this->command->info('🎯 Test Scenarios:');
        $this->command->info('   ✓ View complete history on Permintaan #' . $permintaan1->permintaan_id);
        $this->command->info('   ✓ Create HPS for Permintaan #' . $permintaan2->permintaan_id);
        $this->command->info('   ✓ Create DPP for Permintaan #' . $permintaan3->permintaan_id . ' or #' . $permintaan4->permintaan_id);
        $this->command->info('');
    }
}
