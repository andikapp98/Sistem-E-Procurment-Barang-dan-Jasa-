<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DirekturSeeder extends Seeder
{
    /**
     * Seed user Direktur untuk testing
     * 
     * User Direktur:
     * - Email: direktur@rsud.id
     * - Password: password
     * - Role: direktur
     * - Jabatan: Direktur RSUD
     * 
     * Use case:
     * 1. Login sebagai Direktur
     * 2. Review permintaan dari Kepala Bidang
     * 3. Approve (Final Approval) → ke Staff Perencanaan
     * 4. Reject → kembali ke Unit Pemohon
     * 5. Minta Revisi → kembali ke Kepala Bidang
     */
    public function run(): void
    {
        $this->command->info('🚀 Membuat user Direktur...');
        $this->command->info('');

        // Direktur RSUD Ibnu Sina Gresik
        $direktur = User::updateOrCreate(
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

        $this->command->info('✅ User Direktur berhasil dibuat/diupdate!');
        $this->command->info('');
        $this->command->info('═══════════════════════════════════════════════════════════════');
        $this->command->info('📋 AKUN DIREKTUR');
        $this->command->info('═══════════════════════════════════════════════════════════════');
        $this->command->info('');
        $this->command->info('  Email         : ' . $direktur->email);
        $this->command->info('  Password      : password');
        $this->command->info('  Nama          : ' . $direktur->name);
        $this->command->info('  Role          : ' . $direktur->role);
        $this->command->info('  Jabatan       : ' . $direktur->jabatan);
        $this->command->info('  Unit Kerja    : ' . $direktur->unit_kerja);
        $this->command->info('');
        $this->command->info('═══════════════════════════════════════════════════════════════');
        $this->command->info('');
        $this->command->info('🔐 CARA LOGIN:');
        $this->command->info('   1. Buka: http://localhost:8000/login');
        $this->command->info('   2. Email: direktur@rsud.id');
        $this->command->info('   3. Password: password');
        $this->command->info('');
        $this->command->info('📌 WORKFLOW DIREKTUR:');
        $this->command->info('   1. Menerima permintaan dari Kepala Bidang');
        $this->command->info('   2. Review dan validasi final tingkat eksekutif tertinggi');
        $this->command->info('   3. Approve → Otomatis routing ke Kabid sesuai klasifikasi');
        $this->command->info('   4. Reject → Proses dihentikan, kembali ke Unit Pemohon');
        $this->command->info('   5. Revisi → Kembali ke Kepala Bidang untuk perbaikan');
        $this->command->info('');
        $this->command->info('🎯 ROUTING OTOMATIS SETELAH APPROVE:');
        $this->command->info('   - Permintaan MEDIS → Kabid Yanmed (Bidang Pelayanan Medis)');
        $this->command->info('   - Permintaan PENUNJANG → Kabid Penunjang Medis');
        $this->command->info('   - Permintaan NON MEDIS → Kabid Umum & Keuangan');
        $this->command->info('');
        $this->command->info('📊 FITUR DIREKTUR:');
        $this->command->info('   ✓ Dashboard dengan statistik dan recent permintaan');
        $this->command->info('   ✓ Index/List permintaan yang menunggu review');
        $this->command->info('   ✓ Detail permintaan dengan tracking timeline');
        $this->command->info('   ✓ Approve (Final Approval) dengan routing otomatis');
        $this->command->info('   ✓ Reject dengan alasan (min 10 karakter)');
        $this->command->info('   ✓ Minta Revisi dengan catatan (min 10 karakter)');
        $this->command->info('   ✓ Create disposisi manual (jika diperlukan)');
        $this->command->info('');
        $this->command->info('💡 TIPS:');
        $this->command->info('   - Gunakan DirekturWorkflowSeeder untuk data testing lengkap');
        $this->command->info('   - Cek VERIFY_DIREKTUR_WORKFLOW.sql untuk query verifikasi');
        $this->command->info('   - Lihat FIX_KABID_DISPOSISI_DAN_LOGOUT.md untuk workflow fix');
        $this->command->info('');
        $this->command->info('═══════════════════════════════════════════════════════════════');
    }
}
