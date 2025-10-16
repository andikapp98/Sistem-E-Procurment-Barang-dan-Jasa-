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
        // 1. Admin / Super User
        User::updateOrCreate(
            ['email' => 'admin@rsud.id'],
            [
                'nama' => 'Administrator',
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
                'nama' => 'Dr. Ahmad Yani, Sp.PD',
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
                'nama' => 'Apt. Siti Nurhaliza, S.Farm',
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
                'nama' => 'Dr. Budi Santoso, Sp.PK',
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
                'nama' => 'Dr. Dewi Kusuma, Sp.Rad',
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
                'nama' => 'Dr. Eko Prasetyo, Sp.B',
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
                'nama' => 'Kepala Instalasi',
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
                'nama' => 'Rina Wulandari, SE',
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
                'nama' => 'Bambang Wijaya, S.E., M.M',
                'password' => Hash::make('password'),
                'role' => 'kepala_bagian',
                'jabatan' => 'Kepala Bagian Keuangan',
                'unit_kerja' => 'Keuangan',
                'email_verified_at' => now(),
            ]
        );

        // 9. Direktur RSUD
        User::updateOrCreate(
            ['email' => 'direktur@rsud.id'],
            [
                'nama' => 'Prof. Dr. Hendra Gunawan, Sp.OG',
                'password' => Hash::make('password'),
                'role' => 'direktur',
                'jabatan' => 'Direktur RSUD',
                'unit_kerja' => 'Direksi',
                'email_verified_at' => now(),
            ]
        );

        // 10. Staff IT
        User::updateOrCreate(
            ['email' => 'it@rsud.id'],
            [
                'nama' => 'Agus Setiawan, S.Kom',
                'password' => Hash::make('password'),
                'role' => 'staff_it',
                'jabatan' => 'Staff IT',
                'unit_kerja' => 'IT',
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('✅ 11 users berhasil dibuat!');
        $this->command->info('');
        $this->command->info('📋 Daftar Akun:');
        $this->command->info('═══════════════════════════════════════════════════════════════════════════════');
        $this->command->info('Role                | Email                        | Password      | Unit Kerja');
        $this->command->info('──────────────────────────────────────────────────────────────────────────────');
        $this->command->info('Admin               | admin@rsud.id                | password      | IT');
        $this->command->info('Kepala Instalasi    | kepala.igd@rsud.id           | password      | Gawat Darurat');
        $this->command->info('Kepala Instalasi    | kepala.farmasi@rsud.id       | password      | Farmasi');
        $this->command->info('Kepala Instalasi    | kepala.lab@rsud.id           | password      | Laboratorium');
        $this->command->info('Kepala Instalasi    | kepala.radiologi@rsud.id     | password      | Radiologi');
        $this->command->info('Kepala Instalasi    | kepala.bedah@rsud.id         | password      | Bedah Sentral');
        $this->command->info('Kepala Instalasi    | kepala_instalasi@rsud.id     | password123   | Instalasi');
        $this->command->info('Staff Pengadaan     | pengadaan@rsud.id            | password      | Bagian Pengadaan');
        $this->command->info('Kepala Bagian       | keuangan@rsud.id             | password      | Keuangan');
        $this->command->info('Direktur            | direktur@rsud.id             | password      | Direksi');
        $this->command->info('Staff IT            | it@rsud.id                   | password      | IT');
        $this->command->info('═══════════════════════════════════════════════════════════════════════════════');
    }
}
