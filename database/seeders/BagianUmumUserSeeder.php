<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class BagianUmumUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Creates Bagian Umum user for handling non_medis classification
     * This user handles permintaan from: Gizi, Rekam Medis, Sanitasi, Laundry, IT
     */
    public function run(): void
    {
        // Check if user already exists
        $exists = DB::table('users')->where('email', 'bagian.umum@rsud.id')->exists();

        if ($exists) {
            $this->command->info('User Bagian Umum sudah ada, skip...');
            return;
        }

        // Create Bagian Umum user
        DB::table('users')->insert([
            'name' => 'Drs. Ahmad Fauzi, M.M',
            'email' => 'bagian.umum@rsud.id',
            'password' => Hash::make('password'),
            'role' => 'kepala_bidang',
            'jabatan' => 'Kepala Bagian Umum',
            'unit_kerja' => 'Bagian Umum',
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info('✅ User Bagian Umum berhasil dibuat!');
        $this->command->info('   Email: bagian.umum@rsud.id');
        $this->command->info('   Password: password');
        $this->command->info('   Unit: Bagian Umum');
        $this->command->info('   Handles: Non Medis classification (Gizi, Rekam Medis, Sanitasi, Laundry, IT)');
    }
}
