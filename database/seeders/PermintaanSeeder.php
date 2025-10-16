<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PermintaanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('permintaan')->insert([
            'user_id' => 1, // Sesuaikan dengan ID user yang ada
            'tanggal_permintaan' => Carbon::now(),
            'deskripsi' => 'Permintaan obat penurun panas (paracetamol 500mg) sebanyak 100 strip untuk stok ruang farmasi mengingat meningkatnya kasus demam pada pasien rawat inap dan rawat jalan',
            'status' => 'diajukan',
            'pic_pimpinan' => 'Dr. Ahmad Suryanto, Sp.PD',
            'no_nota_dinas' => 'ND/RS/2025/001/X',
            'link_scan' => 'https://drive.google.com/file/d/example-scan-nota-dinas',
        ]);
    }
}
