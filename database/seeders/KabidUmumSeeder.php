<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class KabidUmumSeeder extends Seeder
{
    /**
     * Seed user Kabid Umum (Kepala Bidang Umum)
     * Role: kepala_bidang
     * Bidang: Umum (menangani Non Medis)
     */
    public function run(): void
    {
        $this->command->info('🌱 Seeding Kabid Umum...');
        $this->command->info('');

        // Check if user already exists
        $existingUser = DB::table('users')->where('email', 'kabid.umum@rsud.id')->first();

        if ($existingUser) {
            $this->command->warn('⚠️  User Kabid Umum sudah ada, skip...');
            $userId = $existingUser->id;
        } else {
            // Insert Kabid Umum
            $userId = DB::table('users')->insertGetId([
                'nama' => 'Dr. Budi Santoso, M.Kes',
                'name' => 'Dr. Budi Santoso, M.Kes',
                'email' => 'kabid.umum@rsud.id',
                'password' => Hash::make('password'),
                'role' => 'kepala_bidang',
                'jabatan' => 'Kepala Bidang Umum',
                'unit_kerja' => 'Bidang Umum',
                'nip' => '199001012015031002',
                'no_telp' => '081234567892',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            $this->command->info('✅ User Kabid Umum berhasil dibuat!');
        }

        $this->command->info('');
        $this->command->info('📊 Detail User:');
        $this->command->info('   • ID: ' . $userId);
        $this->command->info('   • Nama: Dr. Budi Santoso, M.Kes');
        $this->command->info('   • Email: kabid.umum@rsud.id');
        $this->command->info('   • Password: password');
        $this->command->info('   • Role: kepala_bidang');
        $this->command->info('   • Jabatan: Kepala Bidang Umum');
        $this->command->info('   • Unit Kerja: Bidang Umum');
        $this->command->info('');
        $this->command->info('🎯 Tugas Kabid Umum:');
        $this->command->info('   • Menerima permintaan dengan klasifikasi "Non Medis"');
        $this->command->info('   • Review dan approve/reject permintaan non medis');
        $this->command->info('   • Meneruskan ke Staff Perencanaan jika disetujui');
        $this->command->info('');
        $this->command->info('📋 Jenis Permintaan Non Medis:');
        $this->command->info('   • Linen (sprei, selimut, handuk)');
        $this->command->info('   • Perlengkapan IT (komputer, printer)');
        $this->command->info('   • Alat Tulis Kantor (ATK)');
        $this->command->info('   • Makanan dan konsumsi');
        $this->command->info('   • Peralatan rumah tangga');
        $this->command->info('   • Furnitur');
        $this->command->info('');
    }
}
