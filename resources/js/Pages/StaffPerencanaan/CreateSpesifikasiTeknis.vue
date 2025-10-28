<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Buat Spesifikasi Teknis - Permintaan #{{ permintaan.permintaan_id }}
                </h2>
                <Link
                    :href="route('staff-perencanaan.show', permintaan.permintaan_id)"
                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50"
                >
                    ← Kembali
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Informasi Permintaan -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg mb-6">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Informasi Permintaan</h3>
                    </div>
                    <div class="p-6">
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">ID Permintaan</dt>
                                <dd class="mt-1 text-sm text-gray-900">#{{ permintaan.permintaan_id }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Tanggal</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ formatDate(permintaan.tanggal_permintaan) }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Pemohon</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ permintaan.user?.nama }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Unit Kerja</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ permintaan.user?.unit_kerja }}</dd>
                            </div>
                            <div class="md:col-span-2">
                                <dt class="text-sm font-medium text-gray-500">Deskripsi</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ permintaan.deskripsi }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Form Spesifikasi Teknis -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Form Spesifikasi Teknis</h3>
                        <p class="mt-1 text-sm text-gray-600">
                            Lengkapi detail spesifikasi teknis untuk pengadaan barang/jasa
                        </p>
                    </div>

                    <form @submit.prevent="submit" class="p-6 space-y-6">
                        <!-- Info Banner -->
                        <div class="bg-blue-50 border-l-4 border-blue-400 p-4">
                            <p class="text-sm text-blue-700">
                                <strong>Field dengan tanda bintang merah (*) wajib diisi.</strong> Pastikan semua informasi teknis diisi dengan lengkap dan akurat.
                            </p>
                        </div>

                        <!-- Section 1: Latar Belakang & Tujuan -->
                        <div class="border border-gray-200 rounded-lg p-6 bg-gray-50">
                            <h4 class="text-md font-semibold text-gray-900 mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Latar Belakang & Tujuan
                            </h4>
                            
                            <div class="space-y-4">
                                <!-- Latar Belakang -->
                                <div>
                                    <label for="latar_belakang" class="block text-sm font-medium text-gray-700">
                                        Latar Belakang <span class="text-red-500">*</span>
                                    </label>
                                    <textarea
                                        id="latar_belakang"
                                        v-model="form.latar_belakang"
                                        rows="4"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500"
                                        :class="{ 'border-red-500': errors.latar_belakang }"
                                        placeholder="Jelaskan latar belakang kebutuhan pengadaan barang/jasa ini..."
                                    ></textarea>
                                    <p v-if="errors.latar_belakang" class="mt-1 text-sm text-red-600">{{ errors.latar_belakang }}</p>
                                </div>

                                <!-- Maksud & Tujuan -->
                                <div>
                                    <label for="maksud_tujuan" class="block text-sm font-medium text-gray-700">
                                        Maksud & Tujuan <span class="text-red-500">*</span>
                                    </label>
                                    <textarea
                                        id="maksud_tujuan"
                                        v-model="form.maksud_tujuan"
                                        rows="4"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500"
                                        :class="{ 'border-red-500': errors.maksud_tujuan }"
                                        placeholder="Jelaskan maksud dan tujuan pengadaan..."
                                    ></textarea>
                                    <p v-if="errors.maksud_tujuan" class="mt-1 text-sm text-red-600">{{ errors.maksud_tujuan }}</p>
                                </div>

                                <!-- Target/Sasaran yang diharapkan -->
                                <div>
                                    <label for="target_sasaran" class="block text-sm font-medium text-gray-700">
                                        Target/Sasaran yang Diharapkan <span class="text-red-500">*</span>
                                    </label>
                                    <textarea
                                        id="target_sasaran"
                                        v-model="form.target_sasaran"
                                        rows="3"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500"
                                        :class="{ 'border-red-500': errors.target_sasaran }"
                                        placeholder="Target atau sasaran yang ingin dicapai..."
                                    ></textarea>
                                    <p v-if="errors.target_sasaran" class="mt-1 text-sm text-red-600">{{ errors.target_sasaran }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Section 2: Pejabat & Anggaran -->
                        <div class="border border-gray-200 rounded-lg p-6 bg-gray-50">
                            <h4 class="text-md font-semibold text-gray-900 mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                Pejabat & Anggaran
                            </h4>
                            
                            <div class="space-y-4">
                                <!-- Pejabat Pengadaan -->
                                <div>
                                    <label for="pejabat_pengadaan" class="block text-sm font-medium text-gray-700">
                                        Pejabat Pengadaan <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        id="pejabat_pengadaan"
                                        v-model="form.pejabat_pengadaan"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500"
                                        :class="{ 'border-red-500': errors.pejabat_pengadaan }"
                                        placeholder="Nama pejabat yang bertanggung jawab..."
                                    />
                                    <p v-if="errors.pejabat_pengadaan" class="mt-1 text-sm text-red-600">{{ errors.pejabat_pengadaan }}</p>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <!-- Sumber Dana -->
                                    <div>
                                        <label for="sumber_dana" class="block text-sm font-medium text-gray-700">
                                            Sumber Dana <span class="text-red-500">*</span>
                                        </label>
                                        <input
                                            type="text"
                                            id="sumber_dana"
                                            v-model="form.sumber_dana"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500"
                                            :class="{ 'border-red-500': errors.sumber_dana }"
                                            placeholder="APBD, APBN, dll..."
                                        />
                                        <p v-if="errors.sumber_dana" class="mt-1 text-sm text-red-600">{{ errors.sumber_dana }}</p>
                                    </div>

                                    <!-- Perkiraan Biaya -->
                                    <div>
                                        <label for="perkiraan_biaya" class="block text-sm font-medium text-gray-700">
                                            Perkiraan Biaya <span class="text-red-500">*</span>
                                        </label>
                                        <input
                                            type="text"
                                            id="perkiraan_biaya"
                                            v-model="form.perkiraan_biaya"
                                            @input="formatCurrency"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500"
                                            :class="{ 'border-red-500': errors.perkiraan_biaya }"
                                            placeholder="Rp 0"
                                        />
                                        <p v-if="errors.perkiraan_biaya" class="mt-1 text-sm text-red-600">{{ errors.perkiraan_biaya }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section 3: Detail Barang/Jasa -->
                        <div class="border border-gray-200 rounded-lg p-6 bg-gray-50">
                            <h4 class="text-md font-semibold text-gray-900 mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                                Detail Barang/Jasa
                            </h4>
                            
                            <div class="space-y-4">
                                <!-- Macam/Jenis Barang/Jasa -->
                                <div>
                                    <label for="jenis_barang_jasa" class="block text-sm font-medium text-gray-700">
                                        Macam/Jenis Barang/Jasa yang Dibutuhkan <span class="text-red-500">*</span>
                                    </label>
                                    <textarea
                                        id="jenis_barang_jasa"
                                        v-model="form.jenis_barang_jasa"
                                        rows="3"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500"
                                        :class="{ 'border-red-500': errors.jenis_barang_jasa }"
                                        placeholder="Sebutkan jenis dan spesifikasi barang/jasa yang dibutuhkan..."
                                    ></textarea>
                                    <p v-if="errors.jenis_barang_jasa" class="mt-1 text-sm text-red-600">{{ errors.jenis_barang_jasa }}</p>
                                </div>

                                <!-- Fungsi dan Manfaat -->
                                <div>
                                    <label for="fungsi_manfaat" class="block text-sm font-medium text-gray-700">
                                        Fungsi dan Manfaat dari Barang/Jasa <span class="text-red-500">*</span>
                                    </label>
                                    <textarea
                                        id="fungsi_manfaat"
                                        v-model="form.fungsi_manfaat"
                                        rows="3"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500"
                                        :class="{ 'border-red-500': errors.fungsi_manfaat }"
                                        placeholder="Jelaskan fungsi dan manfaat barang/jasa tersebut..."
                                    ></textarea>
                                    <p v-if="errors.fungsi_manfaat" class="mt-1 text-sm text-red-600">{{ errors.fungsi_manfaat }}</p>
                                </div>

                                <!-- Kegiatan Rutin -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Apakah Barang/Jasa Dibutuhkan untuk Menunjang Kegiatan Rutin? <span class="text-red-500">*</span>
                                    </label>
                                    <div class="flex gap-4">
                                        <label class="inline-flex items-center">
                                            <input type="radio" v-model="form.kegiatan_rutin" value="Ya" class="form-radio text-cyan-600" />
                                            <span class="ml-2">Ya</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" v-model="form.kegiatan_rutin" value="Tidak" class="form-radio text-cyan-600" />
                                            <span class="ml-2">Tidak</span>
                                        </label>
                                    </div>
                                    <p v-if="errors.kegiatan_rutin" class="mt-1 text-sm text-red-600">{{ errors.kegiatan_rutin }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Section 4: Waktu & Tenaga -->
                        <div class="border border-gray-200 rounded-lg p-6 bg-gray-50">
                            <h4 class="text-md font-semibold text-gray-900 mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Waktu & Tenaga
                            </h4>
                            
                            <div class="space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <!-- Jangka Waktu Pelaksanaan -->
                                    <div>
                                        <label for="jangka_waktu" class="block text-sm font-medium text-gray-700">
                                            Jangka Waktu Pelaksanaan Pekerjaan <span class="text-red-500">*</span>
                                        </label>
                                        <input
                                            type="text"
                                            id="jangka_waktu"
                                            v-model="form.jangka_waktu"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500"
                                            :class="{ 'border-red-500': errors.jangka_waktu }"
                                            placeholder="Contoh: 30 hari kalender"
                                        />
                                        <p v-if="errors.jangka_waktu" class="mt-1 text-sm text-red-600">{{ errors.jangka_waktu }}</p>
                                    </div>

                                    <!-- Estimasi Waktu Barang/Jasa Datang -->
                                    <div>
                                        <label for="estimasi_waktu_datang" class="block text-sm font-medium text-gray-700">
                                            Estimasi Waktu Barang/Jasa Datang <span class="text-red-500">*</span>
                                        </label>
                                        <input
                                            type="text"
                                            id="estimasi_waktu_datang"
                                            v-model="form.estimasi_waktu_datang"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500"
                                            :class="{ 'border-red-500': errors.estimasi_waktu_datang }"
                                            placeholder="Contoh: 14 hari setelah kontrak"
                                        />
                                        <p v-if="errors.estimasi_waktu_datang" class="mt-1 text-sm text-red-600">{{ errors.estimasi_waktu_datang }}</p>
                                    </div>
                                </div>

                                <!-- Tenaga yang Diperlukan -->
                                <div>
                                    <label for="tenaga_diperlukan" class="block text-sm font-medium text-gray-700">
                                        Berapa Tenaga yang Diperlukan untuk Pengadaan Barang/Jasa Tersebut? <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        id="tenaga_diperlukan"
                                        v-model="form.tenaga_diperlukan"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500"
                                        :class="{ 'border-red-500': errors.tenaga_diperlukan }"
                                        placeholder="Contoh: 2 orang tenaga ahli, 3 orang teknisi"
                                    />
                                    <p v-if="errors.tenaga_diperlukan" class="mt-1 text-sm text-red-600">{{ errors.tenaga_diperlukan }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Section 5: Pelaku Usaha & Konsolidasi -->
                        <div class="border border-gray-200 rounded-lg p-6 bg-gray-50">
                            <h4 class="text-md font-semibold text-gray-900 mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                Pelaku Usaha & Konsolidasi
                            </h4>
                            
                            <div class="space-y-4">
                                <!-- Pelaku Usaha -->
                                <div>
                                    <label for="pelaku_usaha" class="block text-sm font-medium text-gray-700">
                                        Terdapat Pelaku Usaha yang Dinilai Mampu dan Memenuhi Syarat <span class="text-red-500">*</span>
                                    </label>
                                    <textarea
                                        id="pelaku_usaha"
                                        v-model="form.pelaku_usaha"
                                        rows="3"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500"
                                        :class="{ 'border-red-500': errors.pelaku_usaha }"
                                        placeholder="Sebutkan nama pelaku usaha/vendor yang mampu menyediakan..."
                                    ></textarea>
                                    <p v-if="errors.pelaku_usaha" class="mt-1 text-sm text-red-600">{{ errors.pelaku_usaha }}</p>
                                </div>

                                <!-- Pengadaan Sejenis -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Terdapat Pengadaan Barang/Jasa Sejenis pada Kegiatan Lain? <span class="text-red-500">*</span>
                                    </label>
                                    <div class="flex gap-4 mb-2">
                                        <label class="inline-flex items-center">
                                            <input type="radio" v-model="form.pengadaan_sejenis" value="Ya" class="form-radio text-cyan-600" />
                                            <span class="ml-2">Ya</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" v-model="form.pengadaan_sejenis" value="Tidak" class="form-radio text-cyan-600" />
                                            <span class="ml-2">Tidak</span>
                                        </label>
                                    </div>
                                    <input
                                        v-if="form.pengadaan_sejenis === 'Ya'"
                                        type="text"
                                        v-model="form.pengadaan_sejenis_keterangan"
                                        class="mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500"
                                        placeholder="Jelaskan pengadaan sejenis yang dimaksud..."
                                    />
                                    <p v-if="errors.pengadaan_sejenis" class="mt-1 text-sm text-red-600">{{ errors.pengadaan_sejenis }}</p>
                                </div>

                                <!-- Indikasi Konsolidasi -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Indikasi Konsolidasi atas Pengadaan Barang/Jasa <span class="text-red-500">*</span>
                                    </label>
                                    <div class="flex gap-4 mb-2">
                                        <label class="inline-flex items-center">
                                            <input type="radio" v-model="form.indikasi_konsolidasi" value="Ya" class="form-radio text-cyan-600" />
                                            <span class="ml-2">Ya</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" v-model="form.indikasi_konsolidasi" value="Tidak" class="form-radio text-cyan-600" />
                                            <span class="ml-2">Tidak</span>
                                        </label>
                                    </div>
                                    <textarea
                                        v-if="form.indikasi_konsolidasi === 'Ya'"
                                        v-model="form.indikasi_konsolidasi_keterangan"
                                        rows="2"
                                        class="mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500"
                                        placeholder="Jelaskan konsolidasi yang akan dilakukan..."
                                    ></textarea>
                                    <p v-if="errors.indikasi_konsolidasi" class="mt-1 text-sm text-red-600">{{ errors.indikasi_konsolidasi }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="flex items-center justify-end gap-3 pt-4 border-t">
                            <Link
                                :href="route('staff-perencanaan.show', permintaan.permintaan_id)"
                                class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50"
                            >
                                Batal
                            </Link>
                            <button
                                type="submit"
                                :disabled="processing"
                                class="inline-flex items-center px-6 py-3 bg-cyan-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-cyan-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cyan-500 disabled:opacity-50 transition"
                            >
                                <svg v-if="processing" class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                {{ processing ? 'Menyimpan...' : 'Simpan Spesifikasi Teknis' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    permintaan: Object,
});

const form = useForm({
    // Section 1: Latar Belakang & Tujuan
    latar_belakang: '',
    maksud_tujuan: '',
    target_sasaran: '',
    
    // Section 2: Pejabat & Anggaran
    pejabat_pengadaan: '',
    sumber_dana: '',
    perkiraan_biaya: '',
    
    // Section 3: Detail Barang/Jasa
    jenis_barang_jasa: '',
    fungsi_manfaat: '',
    kegiatan_rutin: '',
    
    // Section 4: Waktu & Tenaga
    jangka_waktu: '',
    estimasi_waktu_datang: '',
    tenaga_diperlukan: '',
    
    // Section 5: Pelaku Usaha & Konsolidasi
    pelaku_usaha: '',
    pengadaan_sejenis: '',
    pengadaan_sejenis_keterangan: '',
    indikasi_konsolidasi: '',
    indikasi_konsolidasi_keterangan: '',
});

const processing = ref(false);
const errors = ref({});

const formatDate = (dateString) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
};

const formatCurrency = (event) => {
    let value = event.target.value.replace(/\D/g, '');
    if (value) {
        value = parseInt(value).toLocaleString('id-ID');
        form.perkiraan_biaya = 'Rp ' + value;
    } else {
        form.perkiraan_biaya = '';
    }
};

const submit = () => {
    errors.value = {};

    // Validasi
    const requiredFields = {
        latar_belakang: 'Latar Belakang',
        maksud_tujuan: 'Maksud & Tujuan',
        target_sasaran: 'Target/Sasaran',
        pejabat_pengadaan: 'Pejabat Pengadaan',
        sumber_dana: 'Sumber Dana',
        perkiraan_biaya: 'Perkiraan Biaya',
        jenis_barang_jasa: 'Jenis Barang/Jasa',
        fungsi_manfaat: 'Fungsi dan Manfaat',
        kegiatan_rutin: 'Kegiatan Rutin',
        jangka_waktu: 'Jangka Waktu',
        estimasi_waktu_datang: 'Estimasi Waktu Datang',
        tenaga_diperlukan: 'Tenaga yang Diperlukan',
        pelaku_usaha: 'Pelaku Usaha',
        pengadaan_sejenis: 'Pengadaan Sejenis',
        indikasi_konsolidasi: 'Indikasi Konsolidasi',
    };

    for (const [field, label] of Object.entries(requiredFields)) {
        if (!form[field]) {
            errors.value[field] = `${label} harus diisi`;
            return;
        }
    }

    processing.value = true;

    form.post(route('staff-perencanaan.spesifikasi-teknis.store', props.permintaan.permintaan_id), {
        onSuccess: () => {
            processing.value = false;
        },
        onError: (err) => {
            errors.value = err;
            processing.value = false;
        },
    });
};
</script>
