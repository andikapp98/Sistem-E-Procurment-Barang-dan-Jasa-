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

        // 6b. Kepala Instalasi (Generic Account)
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

        $this->command->info('✅ 23 users berhasil dibuat!');
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
