<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class KepalaPoliSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Poli Bedah
        User::updateOrCreate(
            ['email' => 'kepala.poli.bedah@rsud.id'],
            [
                'name' => 'Dr. Budi Hartono, Sp.B',
                'password' => Hash::make('password'),
                'role' => 'kepala_poli',
                'jabatan' => 'Kepala Poli Bedah',
                'unit_kerja' => 'Poli Bedah',
                'email_verified_at' => now(),
            ]
        );

        // 2. Poli Gigi
        User::updateOrCreate(
            ['email' => 'kepala.poli.gigi@rsud.id'],
            [
                'name' => 'drg. Joko Santoso, Sp.KG',
                'password' => Hash::make('password'),
                'role' => 'kepala_poli',
                'jabatan' => 'Kepala Poli Gigi',
                'unit_kerja' => 'Poli Gigi',
                'email_verified_at' => now(),
            ]
        );

        // 3. Poli Kulit Kelamin
        User::updateOrCreate(
            ['email' => 'kepala.poli.kulit@rsud.id'],
            [
                'name' => 'Dr. Indah Sari, Sp.KK',
                'password' => Hash::make('password'),
                'role' => 'kepala_poli',
                'jabatan' => 'Kepala Poli Kulit Kelamin',
                'unit_kerja' => 'Poli Kulit Kelamin',
                'email_verified_at' => now(),
            ]
        );

        // 4. Poli Penyakit Dalam
        User::updateOrCreate(
            ['email' => 'kepala.poli.dalam@rsud.id'],
            [
                'name' => 'Dr. Ahmad Budiman, Sp.PD',
                'password' => Hash::make('password'),
                'role' => 'kepala_poli',
                'jabatan' => 'Kepala Poli Penyakit Dalam',
                'unit_kerja' => 'Poli Penyakit Dalam',
                'email_verified_at' => now(),
            ]
        );

        // 5. Poli Jiwa
        User::updateOrCreate(
            ['email' => 'kepala.poli.jiwa@rsud.id'],
            [
                'name' => 'Dr. Nurul Hidayah, Sp.KJ',
                'password' => Hash::make('password'),
                'role' => 'kepala_poli',
                'jabatan' => 'Kepala Poli Jiwa',
                'unit_kerja' => 'Poli Jiwa',
                'email_verified_at' => now(),
            ]
        );

        // 6. Poli Psikologi
        User::updateOrCreate(
            ['email' => 'kepala.poli.psikologi@rsud.id'],
            [
                'name' => 'Maya Kusuma, M.Psi, Psikolog',
                'password' => Hash::make('password'),
                'role' => 'kepala_poli',
                'jabatan' => 'Kepala Poli Psikologi',
                'unit_kerja' => 'Poli Psikologi',
                'email_verified_at' => now(),
            ]
        );

        // 7. Poli Mata
        User::updateOrCreate(
            ['email' => 'kepala.poli.mata@rsud.id'],
            [
                'name' => 'Dr. Dewi Lestari, Sp.M',
                'password' => Hash::make('password'),
                'role' => 'kepala_poli',
                'jabatan' => 'Kepala Poli Mata',
                'unit_kerja' => 'Poli Mata',
                'email_verified_at' => now(),
            ]
        );

        // 8. Klinik Gizi
        User::updateOrCreate(
            ['email' => 'kepala.klinik.gizi@rsud.id'],
            [
                'name' => 'Lestari Wulandari, S.Gz, M.Gizi',
                'password' => Hash::make('password'),
                'role' => 'kepala_poli',
                'jabatan' => 'Kepala Klinik Gizi',
                'unit_kerja' => 'Klinik Gizi',
                'email_verified_at' => now(),
            ]
        );

        // 9. Laboratorium
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

        // 10. Apotek
        User::updateOrCreate(
            ['email' => 'kepala.apotek@rsud.id'],
            [
                'name' => 'Apt. Siti Nurhaliza, S.Farm',
                'password' => Hash::make('password'),
                'role' => 'kepala_poli',
                'jabatan' => 'Kepala Apotek',
                'unit_kerja' => 'Apotek',
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('✅ 10 Kepala Poli/Unit berhasil dibuat!');
        $this->command->info('');
        $this->command->info('📋 Daftar Akun Kepala Poli/Unit:');
        $this->command->info('═══════════════════════════════════════════════════════════════════════════════════════');
        $this->command->info('Unit/Poli               | Email                           | Password      ');
        $this->command->info('───────────────────────────────────────────────────────────────────────────────────────');
        $this->command->info('Poli Bedah              | kepala.poli.bedah@rsud.id       | password      ');
        $this->command->info('Poli Gigi               | kepala.poli.gigi@rsud.id        | password      ');
        $this->command->info('Poli Kulit Kelamin      | kepala.poli.kulit@rsud.id       | password      ');
        $this->command->info('Poli Penyakit Dalam     | kepala.poli.dalam@rsud.id       | password      ');
        $this->command->info('Poli Jiwa               | kepala.poli.jiwa@rsud.id        | password      ');
        $this->command->info('Poli Psikologi          | kepala.poli.psikologi@rsud.id   | password      ');
        $this->command->info('Poli Mata               | kepala.poli.mata@rsud.id        | password      ');
        $this->command->info('Klinik Gizi             | kepala.klinik.gizi@rsud.id      | password      ');
        $this->command->info('Laboratorium            | kepala.laboratorium@rsud.id     | password      ');
        $this->command->info('Apotek                  | kepala.apotek@rsud.id           | password      ');
        $this->command->info('═══════════════════════════════════════════════════════════════════════════════════════');
    }
}
