<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Admin / Super User (admin@rsud.id)
        User::updateOrCreate(
            ['email' => 'admin@rsud.id'],
            [
                'name' => 'Administrator', // Changed from 'nama' to 'name'
                'password' => Hash::make('password'),
                'role' => 'admin',
                'jabatan' => 'Administrator Sistem',
                'unit_kerja' => 'IT',
                'email_verified_at' => now(),
            ]
        );

        // 1b. Admin alternatif (admin@rs.com)
        User::updateOrCreate(
            ['email' => 'admin@rs.com'],
            [
                'name' => 'Administrator RS', // Changed from 'nama' to 'name'
                'password' => Hash::make('password'),
                'role' => 'admin',
                'jabatan' => 'Administrator Sistem',
                'unit_kerja' => 'IT',
                'email_verified_at' => now(),
            ]
        );

        // 2. Kepala Instalasi Gawat Darurat (IGD)
        User::updateOrCreate(
            ['email' => 'kepala.igd@rsud.id'],
            [
                'name' => 'Dr. Ahmad Yani, Sp.PD',
                'password' => Hash::make('password'),
                'role' => 'kepala_instalasi',
                'jabatan' => 'Kepala Instalasi',
                'unit_kerja' => 'Gawat Darurat',
                'email_verified_at' => now(),
            ]
        );

        // 3. Kepala Instalasi Farmasi
        User::updateOrCreate(
            ['email' => 'kepala.farmasi@rsud.id'],
            [
                'name' => 'Apt. Siti Nurhaliza, S.Farm',
                'password' => Hash::make('password'),
                'role' => 'kepala_instalasi',
                'jabatan' => 'Kepala Instalasi',
                'unit_kerja' => 'Farmasi',
                'email_verified_at' => now(),
            ]
        );

        // 4. Kepala Instalasi Laboratorium
        User::updateOrCreate(
            ['email' => 'kepala.lab@rsud.id'],
            [
                'name' => 'Dr. Budi Santoso, Sp.PK',
                'password' => Hash::make('password'),
                'role' => 'kepala_instalasi',
                'jabatan' => 'Kepala Instalasi',
                'unit_kerja' => 'Laboratorium',
                'email_verified_at' => now(),
            ]
        );

        // 5. Kepala Instalasi Radiologi
        User::updateOrCreate(
            ['email' => 'kepala.radiologi@rsud.id'],
            [
                'name' => 'Dr. Dewi Kusuma, Sp.Rad',
                'password' => Hash::make('password'),
                'role' => 'kepala_instalasi',
                'jabatan' => 'Kepala Instalasi',
                'unit_kerja' => 'Radiologi',
                'email_verified_at' => now(),
            ]
        );

        // 6. Kepala Instalasi Bedah Sentral
        User::updateOrCreate(
            ['email' => 'kepala.bedah@rsud.id'],
            [
                'name' => 'Dr. Eko Prasetyo, Sp.B',
                'password' => Hash::make('password'),
                'role' => 'kepala_instalasi',
                'jabatan' => 'Kepala Instalasi',
                'unit_kerja' => 'Bedah Sentral',
                'email_verified_at' => now(),
            ]
        );

        // 6b. Kepala Instalasi Rawat Inap (IRNA)
        User::updateOrCreate(
            ['email' => 'kepala.ranap@rsud.id'],
            [
                'name' => 'Ns. Siti Aminah, S.Kep, M.Kep',
                'password' => Hash::make('password'),
                'role' => 'kepala_instalasi',
                'jabatan' => 'Kepala Instalasi Rawat Inap',
                'unit_kerja' => 'Rawat Inap',
                'email_verified_at' => now(),
            ]
        );

        // 6b-1. Kepala Ruang Anggrek (IRNA)
        User::updateOrCreate(
            ['email' => 'ruang.anggrek@rsud.id'],
            [
                'name' => 'Ns. Anita Sari, S.Kep',
                'password' => Hash::make('password'),
                'role' => 'kepala_ruang',
                'jabatan' => 'Kepala Ruang Anggrek',
                'unit_kerja' => 'Anggrek',
                'email_verified_at' => now(),
            ]
        );

        // 6b-2. Kepala Ruang Bougenville (IRNA)
        User::updateOrCreate(
            ['email' => 'ruang.bougenville@rsud.id'],
            [
                'name' => 'Ns. Bunga Lestari, S.Kep',
                'password' => Hash::make('password'),
                'role' => 'kepala_ruang',
                'jabatan' => 'Kepala Ruang Bougenville',
                'unit_kerja' => 'Bougenville',
                'email_verified_at' => now(),
            ]
        );

        // 6b-3. Kepala Ruang Cempaka (IRNA)
        User::updateOrCreate(
            ['email' => 'ruang.cempaka@rsud.id'],
            [
                'name' => 'Ns. Citra Dewi, S.Kep',
                'password' => Hash::make('password'),
                'role' => 'kepala_ruang',
                'jabatan' => 'Kepala Ruang Cempaka',
                'unit_kerja' => 'Cempaka',
                'email_verified_at' => now(),
            ]
        );

        // 6b-4. Kepala Ruang Dahlia (IRNA)
        User::updateOrCreate(
            ['email' => 'ruang.dahlia@rsud.id'],
            [
                'name' => 'Ns. Dewi Anggraini, S.Kep',
                'password' => Hash::make('password'),
                'role' => 'kepala_ruang',
                'jabatan' => 'Kepala Ruang Dahlia',
                'unit_kerja' => 'Dahlia',
                'email_verified_at' => now(),
            ]
        );

        // 6b-5. Kepala Ruang Edelweiss (IRNA)
        User::updateOrCreate(
            ['email' => 'ruang.edelweiss@rsud.id'],
            [
                'name' => 'Ns. Eka Putri, S.Kep',
                'password' => Hash::make('password'),
                'role' => 'kepala_ruang',
                'jabatan' => 'Kepala Ruang Edelweiss',
                'unit_kerja' => 'Edelweiss',
                'email_verified_at' => now(),
            ]
        );

        // 6b-6. Kepala Ruang Flamboyan (IRNA)
        User::updateOrCreate(
            ['email' => 'ruang.flamboyan@rsud.id'],
            [
                'name' => 'Ns. Fitri Handayani, S.Kep',
                'password' => Hash::make('password'),
                'role' => 'kepala_ruang',
                'jabatan' => 'Kepala Ruang Flamboyan',
                'unit_kerja' => 'Flamboyan',
                'email_verified_at' => now(),
            ]
        );

        // 6b-7. Kepala Ruang Gardena (IRNA)
        User::updateOrCreate(
            ['email' => 'ruang.gardena@rsud.id'],
            [
                'name' => 'Ns. Gita Puspita, S.Kep',
                'password' => Hash::make('password'),
                'role' => 'kepala_ruang',
                'jabatan' => 'Kepala Ruang Gardena',
                'unit_kerja' => 'Gardena',
                'email_verified_at' => now(),
            ]
        );

        // 6b-8. Kepala Ruang Heliconia (IRNA)
        User::updateOrCreate(
            ['email' => 'ruang.heliconia@rsud.id'],
            [
                'name' => 'Ns. Hesti Wulandari, S.Kep',
                'password' => Hash::make('password'),
                'role' => 'kepala_ruang',
                'jabatan' => 'Kepala Ruang Heliconia',
                'unit_kerja' => 'Heliconia',
                'email_verified_at' => now(),
            ]
        );

        // 6b-9. Kepala Ruang Ixia (IRNA)
        User::updateOrCreate(
            ['email' => 'ruang.ixia@rsud.id'],
            [
                'name' => 'Ns. Indah Permata, S.Kep',
                'password' => Hash::make('password'),
                'role' => 'kepala_ruang',
                'jabatan' => 'Kepala Ruang Ixia',
                'unit_kerja' => 'Ixia',
                'email_verified_at' => now(),
            ]
        );

        // 6c. Kepala Instalasi Rawat Jalan
        User::updateOrCreate(
            ['email' => 'kepala.rajal@rsud.id'],
            [
                'name' => 'Dr. Putri Handayani, Sp.PD',
                'password' => Hash::make('password'),
                'role' => 'kepala_instalasi',
                'jabatan' => 'Kepala Instalasi',
                'unit_kerja' => 'Rawat Jalan',
                'email_verified_at' => now(),
            ]
        );

        // 6c-1. Kepala Poli Bedah
        User::updateOrCreate(
            ['email' => 'kepala.poli.bedah@rsud.id'],
            [
                'name' => 'Dr. Budi Gunawan, Sp.B',
                'password' => Hash::make('password'),
                'role' => 'kepala_poli',
                'jabatan' => 'Kepala Poli Bedah',
                'unit_kerja' => 'Poli Bedah',
                'email_verified_at' => now(),
            ]
        );

        // 6c-2. Kepala Poli Gigi
        User::updateOrCreate(
            ['email' => 'kepala.poli.gigi@rsud.id'],
            [
                'name' => 'drg. Gita Puspita, Sp.KG',
                'password' => Hash::make('password'),
                'role' => 'kepala_poli',
                'jabatan' => 'Kepala Poli Gigi',
                'unit_kerja' => 'Poli Gigi',
                'email_verified_at' => now(),
            ]
        );

        // 6c-3. Kepala Poli Kulit Kelamin
        User::updateOrCreate(
            ['email' => 'kepala.poli.kulit@rsud.id'],
            [
                'name' => 'Dr. Joko Santoso, Sp.KK',
                'password' => Hash::make('password'),
                'role' => 'kepala_poli',
                'jabatan' => 'Kepala Poli Kulit Kelamin',
                'unit_kerja' => 'Poli Kulit Kelamin',
                'email_verified_at' => now(),
            ]
        );

        // 6c-4. Kepala Poli Penyakit Dalam
        User::updateOrCreate(
            ['email' => 'kepala.poli.dalam@rsud.id'],
            [
                'name' => 'Dr. Ahmad Santoso, Sp.PD',
                'password' => Hash::make('password'),
                'role' => 'kepala_poli',
                'jabatan' => 'Kepala Poli Penyakit Dalam',
                'unit_kerja' => 'Poli Penyakit Dalam',
                'email_verified_at' => now(),
            ]
        );

        // 6c-5. Kepala Poli Jiwa
        User::updateOrCreate(
            ['email' => 'kepala.poli.jiwa@rsud.id'],
            [
                'name' => 'Dr. Dewi Sartika, Sp.KJ',
                'password' => Hash::make('password'),
                'role' => 'kepala_poli',
                'jabatan' => 'Kepala Poli Jiwa',
                'unit_kerja' => 'Poli Jiwa',
                'email_verified_at' => now(),
            ]
        );

        // 6c-6. Kepala Poli Psikologi
        User::updateOrCreate(
            ['email' => 'kepala.poli.psikologi@rsud.id'],
            [
                'name' => 'Psi. Eka Putri, M.Psi',
                'password' => Hash::make('password'),
                'role' => 'kepala_poli',
                'jabatan' => 'Kepala Poli Psikologi',
                'unit_kerja' => 'Poli Psikologi',
                'email_verified_at' => now(),
            ]
        );

        // 6c-7. Kepala Poli Mata
        User::updateOrCreate(
            ['email' => 'kepala.poli.mata@rsud.id'],
            [
                'name' => 'Dr. Fajar Hidayat, Sp.M',
                'password' => Hash::make('password'),
                'role' => 'kepala_poli',
                'jabatan' => 'Kepala Poli Mata',
                'unit_kerja' => 'Poli Mata',
                'email_verified_at' => now(),
            ]
        );

        // 6c-8. Kepala Klinik Gizi
        User::updateOrCreate(
            ['email' => 'kepala.klinik.gizi@rsud.id'],
            [
                'name' => 'Nurhayati, S.Gz, M.Gizi',
                'password' => Hash::make('password'),
                'role' => 'kepala_poli',
                'jabatan' => 'Kepala Klinik Gizi',
                'unit_kerja' => 'Klinik Gizi',
                'email_verified_at' => now(),
            ]
        );

        // 6c-9. Kepala Laboratorium
        User::updateOrCreate(
            ['email' => 'kepala.laboratorium@rsud.id'],
            [
                'name' => 'Dr. Hendra Wijaya, Sp.PK',
                'password' => Hash::make('password'),
                'role' => 'kepala_poli',
                'jabatan' => 'Kepala Laboratorium',
                'unit_kerja' => 'Laboratorium',
                'email_verified_at' => now(),
            ]
        );

        // 6c-10. Kepala Apotek
        User::updateOrCreate(
            ['email' => 'kepala.apotek@rsud.id'],
            [
                'name' => 'Apt. Indah Permata, S.Farm',
                'password' => Hash::make('password'),
                'role' => 'kepala_poli',
                'jabatan' => 'Kepala Apotek',
                'unit_kerja' => 'Apotek',
                'email_verified_at' => now(),
            ]
        );

        // 6d. Kepala Instalasi ICU/ICCU
        User::updateOrCreate(
            ['email' => 'kepala.icu@rsud.id'],
            [
                'name' => 'Dr. Muhammad Fajar, Sp.An',
                'password' => Hash::make('password'),
                'role' => 'kepala_instalasi',
                'jabatan' => 'Kepala Instalasi',
                'unit_kerja' => 'ICU/ICCU',
                'email_verified_at' => now(),
            ]
        );

        // 6e. Kepala Instalasi Rekam Medis
        User::updateOrCreate(
            ['email' => 'kepala.rm@rsud.id'],
            [
                'name' => 'Ns. Retno Wulan, S.KM, M.Kes',
                'password' => Hash::make('password'),
                'role' => 'kepala_instalasi',
                'jabatan' => 'Kepala Instalasi',
                'unit_kerja' => 'Rekam Medis',
                'email_verified_at' => now(),
            ]
        );

        // 6f. Kepala Instalasi Gizi
        User::updateOrCreate(
            ['email' => 'kepala.gizi@rsud.id'],
            [
                'name' => 'Nurhayati, S.Gz, M.Gizi',
                'password' => Hash::make('password'),
                'role' => 'kepala_instalasi',
                'jabatan' => 'Kepala Instalasi',
                'unit_kerja' => 'Gizi',
                'email_verified_at' => now(),
            ]
        );

        // 6g. Kepala Instalasi Sanitasi & Pemeliharaan
        User::updateOrCreate(
            ['email' => 'kepala.sanitasi@rsud.id'],
            [
                'name' => 'Ir. Bambang Susilo, M.T',
                'password' => Hash::make('password'),
                'role' => 'kepala_instalasi',
                'jabatan' => 'Kepala Instalasi',
                'unit_kerja' => 'Sanitasi & Pemeliharaan',
                'email_verified_at' => now(),
            ]
        );

        // 6h. Kepala Instalasi Laundry & Linen
        User::updateOrCreate(
            ['email' => 'kepala.laundry@rsud.id'],
            [
                'name' => 'Sri Wahyuni, S.T',
                'password' => Hash::make('password'),
                'role' => 'kepala_instalasi',
                'jabatan' => 'Kepala Instalasi',
                'unit_kerja' => 'Laundry & Linen',
                'email_verified_at' => now(),
            ]
        );

        // 6i. Kepala Instalasi (Generic Account)
        User::updateOrCreate(
            ['email' => 'kepala_instalasi@rsud.id'],
            [
                'name' => 'Kepala Instalasi',
                'password' => Hash::make('password123'),
                'role' => 'kepala_instalasi',
                'jabatan' => 'Kepala Instalasi',
                'unit_kerja' => 'Instalasi',
                'email_verified_at' => now(),
            ]
        );

        // 7. Staff Pengadaan
        User::updateOrCreate(
            ['email' => 'pengadaan@rsud.id'],
            [
                'name' => 'Rina Wulandari, SE',
                'password' => Hash::make('password'),
                'role' => 'staff_pengadaan',
                'jabatan' => 'Staff Pengadaan',
                'unit_kerja' => 'Bagian Pengadaan',
                'email_verified_at' => now(),
            ]
        );

        // 8. Kepala Bagian Keuangan
        User::updateOrCreate(
            ['email' => 'keuangan@rsud.id'],
            [
                'name' => 'Bambang Wijaya, S.E., M.M',
                'password' => Hash::make('password'),
                'role' => 'kepala_bagian',
                'jabatan' => 'Kepala Bagian Keuangan',
                'unit_kerja' => 'Keuangan',
                'email_verified_at' => now(),
            ]
        );

        // 9. Kepala Bidang Pelayanan Medis
        User::updateOrCreate(
            ['email' => 'kabid.yanmed@rsud.id'],
            [
                'name' => 'Dr. Lestari Wijaya, M.Kes',
                'password' => Hash::make('password'),
                'role' => 'kepala_bidang',
                'jabatan' => 'Kepala Bidang Pelayanan Medis',
                'unit_kerja' => 'Bidang Pelayanan Medis',
                'email_verified_at' => now(),
            ]
        );

        // 10. Kepala Bidang Penunjang Medis
        User::updateOrCreate(
            ['email' => 'kabid.penunjang@rsud.id'],
            [
                'name' => 'Dr. Rina Kusumawati, Sp.PK, M.Kes',
                'password' => Hash::make('password'),
                'role' => 'kepala_bidang',
                'jabatan' => 'Kepala Bidang Penunjang Medis',
                'unit_kerja' => 'Bidang Penunjang Medis',
                'email_verified_at' => now(),
            ]
        );

        // 11. Kepala Bidang Keperawatan
        User::updateOrCreate(
            ['email' => 'kabid.keperawatan@rsud.id'],
            [
                'name' => 'Ns. Maria Ulfa, S.Kep, M.Kep',
                'password' => Hash::make('password'),
                'role' => 'kepala_bidang',
                'jabatan' => 'Kepala Bidang Keperawatan',
                'unit_kerja' => 'Bidang Keperawatan',
                'email_verified_at' => now(),
            ]
        );

        // 12. Direktur
        User::updateOrCreate(
            ['email' => 'direktur@rsud.id'],
            [
                'name' => 'Dr. Soekarno Hatta, Sp.OG, M.Kes',
                'password' => Hash::make('password'),
                'role' => 'direktur',
                'jabatan' => 'Direktur RSUD',
                'unit_kerja' => 'Direksi',
                'email_verified_at' => now(),
            ]
        );

        // 13. Wakil Direktur Umum & Keuangan
        User::updateOrCreate(
            ['email' => 'wadir.umum@rsud.id'],
            [
                'name' => 'Ir. Sulistyo Wardoyo, M.M',
                'password' => Hash::make('password'),
                'role' => 'wakil_direktur',
                'jabatan' => 'Wakil Direktur Umum & Keuangan',
                'unit_kerja' => 'Direksi',
                'email_verified_at' => now(),
            ]
        );

        // 14. Wakil Direktur Pelayanan Medis
        User::updateOrCreate(
            ['email' => 'wadir.yanmed@rsud.id'],
            [
                'name' => 'Dr. Anita Kusuma, Sp.A, M.Kes',
                'password' => Hash::make('password'),
                'role' => 'wakil_direktur',
                'jabatan' => 'Wakil Direktur Pelayanan Medis',
                'unit_kerja' => 'Direksi',
                'email_verified_at' => now(),
            ]
        );

        // 15. Staff IT
        User::updateOrCreate(
            ['email' => 'it@rsud.id'],
            [
                'name' => 'Agus Setiawan, S.Kom',
                'password' => Hash::make('password'),
                'role' => 'staff_it',
                'jabatan' => 'Staff IT',
                'unit_kerja' => 'IT',
                'email_verified_at' => now(),
            ]
        );

        // 16. Staff Perencanaan
        User::updateOrCreate(
            ['email' => 'perencanaan@rsud.id'],
            [
                'name' => 'Dian Pramudita, S.E., M.M',
                'password' => Hash::make('password'),
                'role' => 'staff_perencanaan',
                'jabatan' => 'Staff Perencanaan',
                'unit_kerja' => 'Bagian Perencanaan',
                'email_verified_at' => now(),
            ]
        );

        // 17. Staff Perencanaan Alternatif
        User::updateOrCreate(
            ['email' => 'staff.perencanaan@rsud.id'],
            [
                'name' => 'Rudi Hartono, S.T., M.T',
                'password' => Hash::make('password'),
                'role' => 'staff_perencanaan',
                'jabatan' => 'Staff Perencanaan',
                'unit_kerja' => 'Bagian Perencanaan',
                'email_verified_at' => now(),
            ]
        );

        // 18. Bagian KSO (Kerja Sama Operasional)
        User::updateOrCreate(
            ['email' => 'kso@rsud.id'],
            [
                'name' => 'Siti Rahmawati, S.H., M.M',
                'password' => Hash::make('password'),
                'role' => 'kso',
                'jabatan' => 'Staff KSO',
                'unit_kerja' => 'Bagian KSO',
                'email_verified_at' => now(),
            ]
        );

        // 19. Bagian KSO Alternatif
        User::updateOrCreate(
            ['email' => 'staff.kso@rsud.id'],
            [
                'name' => 'Andi Firmansyah, S.Sos, M.Si',
                'password' => Hash::make('password'),
                'role' => 'kso',
                'jabatan' => 'Koordinator KSO',
                'unit_kerja' => 'Bagian KSO',
                'email_verified_at' => now(),
            ]
        );

        // 20. Bagian Pengadaan (sudah ada di line 121-131 tapi update role)
        // Update existing user pengadaan
        User::where('email', 'pengadaan@rsud.id')->update([
            'role' => 'pengadaan',
        ]);

        // 21. Bagian Pengadaan Alternatif
        User::updateOrCreate(
            ['email' => 'staff.pengadaan@rsud.id'],
            [
                'name' => 'Indra Gunawan, S.T',
                'password' => Hash::make('password'),
                'role' => 'pengadaan',
                'jabatan' => 'Staff Pengadaan',
                'unit_kerja' => 'Bagian Pengadaan',
                'email_verified_at' => now(),
            ]
        );

        // 22. Bagian Serah Terima / Penerimaan Barang
        User::updateOrCreate(
            ['email' => 'penerimaan@rsud.id'],
            [
                'name' => 'Hendra Saputra, S.E',
                'password' => Hash::make('password'),
                'role' => 'bagian_penerimaan',
                'jabatan' => 'Staff Penerimaan Barang',
                'unit_kerja' => 'Bagian Penerimaan',
                'email_verified_at' => now(),
            ]
        );

        // 23. Bagian Serah Terima Alternatif
        User::updateOrCreate(
            ['email' => 'serah.terima@rsud.id'],
            [
                'name' => 'Maya Kusuma, S.T',
                'password' => Hash::make('password'),
                'role' => 'bagian_penerimaan',
                'jabatan' => 'Koordinator Penerimaan Barang',
                'unit_kerja' => 'Bagian Penerimaan',
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('✅ 49 users berhasil dibuat!');
        $this->command->info('');
        $this->command->info('📋 Daftar Akun:');
        $this->command->info('═══════════════════════════════════════════════════════════════════════════════════════');
        $this->command->info('Role                | Email                              | Password      | Unit Kerja');
        $this->command->info('───────────────────────────────────────────────────────────────────────────────────────');
        $this->command->info('Admin               | admin@rsud.id                      | password      | IT');
        $this->command->info('Admin               | admin@rs.com                       | password      | IT');
        $this->command->info('Kepala Instalasi    | kepala.igd@rsud.id                 | password      | Gawat Darurat');
        $this->command->info('Kepala Instalasi    | kepala.farmasi@rsud.id             | password      | Farmasi');
        $this->command->info('Kepala Instalasi    | kepala.lab@rsud.id                 | password      | Laboratorium');
        $this->command->info('Kepala Instalasi    | kepala.radiologi@rsud.id           | password      | Radiologi');
        $this->command->info('Kepala Instalasi    | kepala.bedah@rsud.id               | password      | Bedah Sentral');
        $this->command->info('Kepala Instalasi    | kepala.ranap@rsud.id               | password      | Rawat Inap');
        $this->command->info('  Kepala Ruang      | ruang.anggrek@rsud.id              | password      | Anggrek');
        $this->command->info('  Kepala Ruang      | ruang.bougenville@rsud.id          | password      | Bougenville');
        $this->command->info('  Kepala Ruang      | ruang.cempaka@rsud.id              | password      | Cempaka');
        $this->command->info('  Kepala Ruang      | ruang.dahlia@rsud.id               | password      | Dahlia');
        $this->command->info('  Kepala Ruang      | ruang.edelweiss@rsud.id            | password      | Edelweiss');
        $this->command->info('  Kepala Ruang      | ruang.flamboyan@rsud.id            | password      | Flamboyan');
        $this->command->info('  Kepala Ruang      | ruang.gardena@rsud.id              | password      | Gardena');
        $this->command->info('  Kepala Ruang      | ruang.heliconia@rsud.id            | password      | Heliconia');
        $this->command->info('  Kepala Ruang      | ruang.ixia@rsud.id                 | password      | Ixia');
        $this->command->info('Kepala Instalasi    | kepala.rajal@rsud.id               | password      | Rawat Jalan');
        $this->command->info('  Kepala Poli       | kepala.poli.bedah@rsud.id          | password      | Poli Bedah');
        $this->command->info('  Kepala Poli       | kepala.poli.gigi@rsud.id           | password      | Poli Gigi');
        $this->command->info('  Kepala Poli       | kepala.poli.kulit@rsud.id          | password      | Poli Kulit Kelamin');
        $this->command->info('  Kepala Poli       | kepala.poli.dalam@rsud.id          | password      | Poli Penyakit Dalam');
        $this->command->info('  Kepala Poli       | kepala.poli.jiwa@rsud.id           | password      | Poli Jiwa');
        $this->command->info('  Kepala Poli       | kepala.poli.psikologi@rsud.id      | password      | Poli Psikologi');
        $this->command->info('  Kepala Poli       | kepala.poli.mata@rsud.id           | password      | Poli Mata');
        $this->command->info('  Kepala Poli       | kepala.klinik.gizi@rsud.id         | password      | Klinik Gizi');
        $this->command->info('  Kepala Poli       | kepala.laboratorium@rsud.id        | password      | Laboratorium');
        $this->command->info('  Kepala Poli       | kepala.apotek@rsud.id              | password      | Apotek');
        $this->command->info('Kepala Instalasi    | kepala.icu@rsud.id                 | password      | ICU/ICCU');
        $this->command->info('Kepala Instalasi    | kepala.rm@rsud.id                  | password      | Rekam Medis');
        $this->command->info('Kepala Instalasi    | kepala.gizi@rsud.id                | password      | Gizi');
        $this->command->info('Kepala Instalasi    | kepala.sanitasi@rsud.id            | password      | Sanitasi');
        $this->command->info('Kepala Instalasi    | kepala.laundry@rsud.id             | password      | Laundry & Linen');
        $this->command->info('Kepala Instalasi    | kepala_instalasi@rsud.id           | password123   | Instalasi');
        $this->command->info('Staff Pengadaan     | pengadaan@rsud.id                  | password      | Bagian Pengadaan');
        $this->command->info('Kepala Bagian       | keuangan@rsud.id                   | password      | Keuangan');
        $this->command->info('Kepala Bidang       | kabid.yanmed@rsud.id               | password      | Bid. Pelayanan Medis');
        $this->command->info('Kepala Bidang       | kabid.penunjang@rsud.id            | password      | Bid. Penunjang Medis');
        $this->command->info('Kepala Bidang       | kabid.keperawatan@rsud.id          | password      | Bid. Keperawatan');
        $this->command->info('Direktur            | direktur@rsud.id                   | password      | Direksi');
        $this->command->info('Wakil Direktur      | wadir.umum@rsud.id                 | password      | Direksi');
        $this->command->info('Wakil Direktur      | wadir.yanmed@rsud.id               | password      | Direksi');
        $this->command->info('Staff IT            | it@rsud.id                         | password      | IT');
        $this->command->info('Staff Perencanaan   | perencanaan@rsud.id                | password      | Bagian Perencanaan');
        $this->command->info('Staff Perencanaan   | staff.perencanaan@rsud.id          | password      | Bagian Perencanaan');
        $this->command->info('Bagian KSO          | kso@rsud.id                        | password      | Bagian KSO');
        $this->command->info('Bagian KSO          | staff.kso@rsud.id                  | password      | Bagian KSO');
        $this->command->info('Bagian Pengadaan    | staff.pengadaan@rsud.id            | password      | Bagian Pengadaan');
        $this->command->info('Bagian Penerimaan   | penerimaan@rsud.id                 | password      | Bagian Penerimaan');
        $this->command->info('Bagian Penerimaan   | serah.terima@rsud.id               | password      | Bagian Penerimaan');
        $this->command->info('═══════════════════════════════════════════════════════════════════════════════════════');
        $this->command->info('');
        $this->command->info('📋 Daftar Akun:');
        $this->command->info('═══════════════════════════════════════════════════════════════════════════════════════');
        $this->command->info('Role                | Email                        | Password      | Unit Kerja');
        $this->command->info('───────────────────────────────────────────────────────────────────────────────────────');
        $this->command->info('Admin               | admin@rsud.id                | password      | IT');
        $this->command->info('Admin               | admin@rs.com                 | password      | IT');
        $this->command->info('Kepala Instalasi    | kepala.igd@rsud.id           | password      | Gawat Darurat');
        $this->command->info('Kepala Instalasi    | kepala.farmasi@rsud.id       | password      | Farmasi');
        $this->command->info('Kepala Instalasi    | kepala.lab@rsud.id           | password      | Laboratorium');
        $this->command->info('Kepala Instalasi    | kepala.radiologi@rsud.id     | password      | Radiologi');
        $this->command->info('Kepala Instalasi    | kepala.bedah@rsud.id         | password      | Bedah Sentral');
        $this->command->info('Kepala Instalasi    | kepala.ranap@rsud.id         | password      | Rawat Inap');
        $this->command->info('  Kepala Ruang      | ruang.anggrek@rsud.id        | password      | Anggrek');
        $this->command->info('  Kepala Ruang      | ruang.bougenville@rsud.id    | password      | Bougenville');
        $this->command->info('  Kepala Ruang      | ruang.cempaka@rsud.id        | password      | Cempaka');
        $this->command->info('  Kepala Ruang      | ruang.dahlia@rsud.id         | password      | Dahlia');
        $this->command->info('  Kepala Ruang      | ruang.edelweiss@rsud.id      | password      | Edelweiss');
        $this->command->info('  Kepala Ruang      | ruang.flamboyan@rsud.id      | password      | Flamboyan');
        $this->command->info('  Kepala Ruang      | ruang.gardena@rsud.id        | password      | Gardena');
        $this->command->info('  Kepala Ruang      | ruang.heliconia@rsud.id      | password      | Heliconia');
        $this->command->info('  Kepala Ruang      | ruang.ixia@rsud.id           | password      | Ixia');
        $this->command->info('Kepala Instalasi    | kepala.rajal@rsud.id         | password      | Rawat Jalan');
        $this->command->info('Kepala Instalasi    | kepala.icu@rsud.id           | password      | ICU/ICCU');
        $this->command->info('Kepala Instalasi    | kepala.rm@rsud.id            | password      | Rekam Medis');
        $this->command->info('Kepala Instalasi    | kepala.gizi@rsud.id          | password      | Gizi');
        $this->command->info('Kepala Instalasi    | kepala.sanitasi@rsud.id      | password      | Sanitasi');
        $this->command->info('Kepala Instalasi    | kepala.laundry@rsud.id       | password      | Laundry & Linen');
        $this->command->info('Kepala Instalasi    | kepala_instalasi@rsud.id     | password123   | Instalasi');
        $this->command->info('Staff Pengadaan     | pengadaan@rsud.id            | password      | Bagian Pengadaan');
        $this->command->info('Kepala Bagian       | keuangan@rsud.id             | password      | Keuangan');
        $this->command->info('Kepala Bidang       | kabid.yanmed@rsud.id         | password      | Bid. Pelayanan Medis');
        $this->command->info('Kepala Bidang       | kabid.penunjang@rsud.id      | password      | Bid. Penunjang Medis');
        $this->command->info('Kepala Bidang       | kabid.keperawatan@rsud.id    | password      | Bid. Keperawatan');
        $this->command->info('Direktur            | direktur@rsud.id             | password      | Direksi');
        $this->command->info('Wakil Direktur      | wadir.umum@rsud.id           | password      | Direksi');
        $this->command->info('Wakil Direktur      | wadir.yanmed@rsud.id         | password      | Direksi');
        $this->command->info('Staff IT            | it@rsud.id                   | password      | IT');
        $this->command->info('Staff Perencanaan   | perencanaan@rsud.id          | password      | Bagian Perencanaan');
        $this->command->info('Staff Perencanaan   | staff.perencanaan@rsud.id    | password      | Bagian Perencanaan');
        $this->command->info('Bagian KSO          | kso@rsud.id                  | password      | Bagian KSO');
        $this->command->info('Bagian KSO          | staff.kso@rsud.id            | password      | Bagian KSO');
        $this->command->info('Bagian Pengadaan    | staff.pengadaan@rsud.id      | password      | Bagian Pengadaan');
        $this->command->info('Bagian Penerimaan   | penerimaan@rsud.id           | password      | Bagian Penerimaan');
        $this->command->info('Bagian Penerimaan   | serah.terima@rsud.id         | password      | Bagian Penerimaan');
        $this->command->info('═══════════════════════════════════════════════════════════════════════════════════════');
    }
}
