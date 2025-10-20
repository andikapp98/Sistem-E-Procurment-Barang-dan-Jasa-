<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class KepalaBidangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Seeder untuk 10 data Kepala Bidang dengan berbagai unit kerja
     */
    public function run(): void
    {
        $kepalaBidangData = [
            [
                'email' => 'kabid.yanmed@rsud.id',
                'name' => 'Dr. Lestari Wijaya, M.Kes',
                'jabatan' => 'Kepala Bidang Pelayanan Medis',
                'unit_kerja' => 'Bidang Pelayanan Medis',
            ],
            [
                'email' => 'kabid.penunjang@rsud.id',
                'name' => 'Dr. Rina Kusumawati, Sp.PK, M.Kes',
                'jabatan' => 'Kepala Bidang Penunjang Medis',
                'unit_kerja' => 'Bidang Penunjang Medis',
            ],
            [
                'email' => 'kabid.keperawatan@rsud.id',
                'name' => 'Ns. Maria Ulfa, S.Kep, M.Kep',
                'jabatan' => 'Kepala Bidang Keperawatan',
                'unit_kerja' => 'Bidang Keperawatan',
            ],
            [
                'email' => 'kabid.umum@rsud.id',
                'name' => 'Ir. Bambang Sutrisno, M.M',
                'jabatan' => 'Kepala Bidang Umum & Keuangan',
                'unit_kerja' => 'Bidang Umum & Keuangan',
            ],
            [
                'email' => 'kabid.sdm@rsud.id',
                'name' => 'Dr. Hendra Gunawan, S.Psi, M.M',
                'jabatan' => 'Kepala Bidang SDM & Pendidikan',
                'unit_kerja' => 'Bidang SDM & Pendidikan',
            ],
            [
                'email' => 'kabid.mutu@rsud.id',
                'name' => 'Dr. Siti Rahmawati, Sp.KJ, M.Kes',
                'jabatan' => 'Kepala Bidang Mutu & Keselamatan Pasien',
                'unit_kerja' => 'Bidang Mutu & Keselamatan Pasien',
            ],
            [
                'email' => 'kabid.sarana@rsud.id',
                'name' => 'Ir. Agus Prasetyo, M.T',
                'jabatan' => 'Kepala Bidang Sarana & Prasarana',
                'unit_kerja' => 'Bidang Sarana & Prasarana',
            ],
            [
                'email' => 'kabid.rawatjalan@rsud.id',
                'name' => 'Dr. Diana Puspitasari, Sp.PD, M.Kes',
                'jabatan' => 'Kepala Bidang Rawat Jalan',
                'unit_kerja' => 'Bidang Rawat Jalan',
            ],
            [
                'email' => 'kabid.rawatinap@rsud.id',
                'name' => 'Dr. Budi Santoso, Sp.B, M.Kes',
                'jabatan' => 'Kepala Bidang Rawat Inap',
                'unit_kerja' => 'Bidang Rawat Inap',
            ],
            [
                'email' => 'kabid.rekammedis@rsud.id',
                'name' => 'Dra. Ani Sulistyowati, M.Kes',
                'jabatan' => 'Kepala Bidang Rekam Medis & Informasi',
                'unit_kerja' => 'Bidang Rekam Medis & Informasi',
            ],
        ];

        foreach ($kepalaBidangData as $data) {
            User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password'),
                    'role' => 'kepala_bidang',
                    'jabatan' => $data['jabatan'],
                    'unit_kerja' => $data['unit_kerja'],
                    'email_verified_at' => now(),
                ]
            );
        }

        $this->command->info('✅ 10 Kepala Bidang berhasil dibuat!');
        $this->command->info('');
        $this->command->info('📋 Daftar Kepala Bidang:');
        $this->command->info('═══════════════════════════════════════════════════════════════════════════════════════');
        $this->command->info('Email                          | Nama                                    | Unit Kerja');
        $this->command->info('───────────────────────────────────────────────────────────────────────────────────────');
        
        foreach ($kepalaBidangData as $data) {
            $this->command->info(
                str_pad($data['email'], 30) . ' | ' . 
                str_pad($data['name'], 39) . ' | ' . 
                $data['unit_kerja']
            );
        }
        
        $this->command->info('═══════════════════════════════════════════════════════════════════════════════════════');
        $this->command->info('');
        $this->command->info('🔑 Password untuk semua akun: password');
    }
}
