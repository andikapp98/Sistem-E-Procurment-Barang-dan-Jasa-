<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Dokumen Persiapan Pengadaan (DPP) - Permintaan #{{ permintaan.permintaan_id }}
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
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Informasi Permintaan -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg mb-6">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Informasi Permintaan</h3>
                    </div>
                    <div class="p-6">
                        <dl class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">ID Permintaan</dt>
                                <dd class="mt-1 text-sm text-gray-900">#{{ permintaan.permintaan_id }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Bidang</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ permintaan.bidang }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Status</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ permintaan.status }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Form DPP -->
                <form @submit.prevent="submit" class="space-y-6">
                    <!-- PPK dan Identifikasi -->
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">PPK dan Identifikasi Paket</h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <!-- PPK yang ditunjuk -->
                            <div>
                                <label for="ppk_ditunjuk" class="block text-sm font-medium text-gray-700">
                                    PPK yang Ditunjuk <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="ppk_ditunjuk"
                                    v-model="form.ppk_ditunjuk"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    :class="{ 'border-red-500': errors.ppk_ditunjuk }"
                                    placeholder="Nama PPK yang ditunjuk"
                                />
                                <p v-if="errors.ppk_ditunjuk" class="mt-1 text-sm text-red-600">{{ errors.ppk_ditunjuk }}</p>
                            </div>

                            <!-- Nama Paket -->
                            <div>
                                <label for="nama_paket" class="block text-sm font-medium text-gray-700">
                                    Nama Paket <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="nama_paket"
                                    v-model="form.nama_paket"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    :class="{ 'border-red-500': errors.nama_paket }"
                                    placeholder="Contoh: Pengadaan Alat Kesehatan Ruang IGD"
                                />
                                <p v-if="errors.nama_paket" class="mt-1 text-sm text-red-600">{{ errors.nama_paket }}</p>
                            </div>

                            <!-- Lokasi -->
                            <div>
                                <label for="lokasi" class="block text-sm font-medium text-gray-700">
                                    Lokasi <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="lokasi"
                                    v-model="form.lokasi"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    :class="{ 'border-red-500': errors.lokasi }"
                                    placeholder="Lokasi pelaksanaan"
                                />
                                <p v-if="errors.lokasi" class="mt-1 text-sm text-red-600">{{ errors.lokasi }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Program dan Kegiatan -->
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Program dan Kegiatan</h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <!-- Uraian Program -->
                            <div>
                                <label for="uraian_program" class="block text-sm font-medium text-gray-700">
                                    Uraian Program <span class="text-red-500">*</span>
                                </label>
                                <textarea
                                    id="uraian_program"
                                    v-model="form.uraian_program"
                                    rows="2"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    :class="{ 'border-red-500': errors.uraian_program }"
                                    placeholder="Uraian program pengadaan"
                                ></textarea>
                                <p v-if="errors.uraian_program" class="mt-1 text-sm text-red-600">{{ errors.uraian_program }}</p>
                            </div>

                            <!-- Uraian Kegiatan -->
                            <div>
                                <label for="uraian_kegiatan" class="block text-sm font-medium text-gray-700">
                                    Uraian Kegiatan <span class="text-red-500">*</span>
                                </label>
                                <textarea
                                    id="uraian_kegiatan"
                                    v-model="form.uraian_kegiatan"
                                    rows="2"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    :class="{ 'border-red-500': errors.uraian_kegiatan }"
                                    placeholder="Uraian kegiatan pengadaan"
                                ></textarea>
                                <p v-if="errors.uraian_kegiatan" class="mt-1 text-sm text-red-600">{{ errors.uraian_kegiatan }}</p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Sub Kegiatan -->
                                <div>
                                    <label for="sub_kegiatan" class="block text-sm font-medium text-gray-700">
                                        Sub Kegiatan
                                    </label>
                                    <input
                                        type="text"
                                        id="sub_kegiatan"
                                        v-model="form.sub_kegiatan"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        placeholder="Sub kegiatan"
                                    />
                                </div>

                                <!-- Sub-sub Kegiatan -->
                                <div>
                                    <label for="sub_sub_kegiatan" class="block text-sm font-medium text-gray-700">
                                        Sub-sub Kegiatan
                                    </label>
                                    <input
                                        type="text"
                                        id="sub_sub_kegiatan"
                                        v-model="form.sub_sub_kegiatan"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        placeholder="Sub-sub kegiatan"
                                    />
                                </div>
                            </div>

                            <!-- Kode Rekening -->
                            <div>
                                <label for="kode_rekening" class="block text-sm font-medium text-gray-700">
                                    Kode Rekening <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="kode_rekening"
                                    v-model="form.kode_rekening"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    :class="{ 'border-red-500': errors.kode_rekening }"
                                    placeholder="Contoh: 5.2.02.01.01.0001"
                                />
                                <p v-if="errors.kode_rekening" class="mt-1 text-sm text-red-600">{{ errors.kode_rekening }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Anggaran dan HPS -->
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Anggaran dan HPS</h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Sumber Dana -->
                                <div>
                                    <label for="sumber_dana" class="block text-sm font-medium text-gray-700">
                                        Sumber Dana <span class="text-red-500">*</span>
                                    </label>
                                    <select
                                        id="sumber_dana"
                                        v-model="form.sumber_dana"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        :class="{ 'border-red-500': errors.sumber_dana }"
                                    >
                                        <option value="">Pilih Sumber Dana</option>
                                        <option value="APBD">APBD</option>
                                        <option value="APBN">APBN</option>
                                        <option value="Hibah">Hibah</option>
                                        <option value="BLUD">BLUD</option>
                                        <option value="Lainnya">Lainnya</option>
                                    </select>
                                    <p v-if="errors.sumber_dana" class="mt-1 text-sm text-red-600">{{ errors.sumber_dana }}</p>
                                </div>

                                <!-- Pagu Paket -->
                                <div>
                                    <label for="pagu_paket" class="block text-sm font-medium text-gray-700">
                                        Pagu Paket <span class="text-red-500">*</span>
                                    </label>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">Rp</span>
                                        </div>
                                        <input
                                            type="text"
                                            id="pagu_paket"
                                            v-model="paguPaketFormatted"
                                            @input="handlePaguPaketInput"
                                            class="pl-12 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            :class="{ 'border-red-500': errors.pagu_paket }"
                                            placeholder="0"
                                        />
                                    </div>
                                    <p v-if="form.pagu_paket" class="mt-1 text-xs text-gray-500">
                                        {{ formatRupiahTerbilang(form.pagu_paket) }}
                                    </p>
                                    <p v-if="errors.pagu_paket" class="mt-1 text-sm text-red-600">{{ errors.pagu_paket }}</p>
                                </div>
                            </div>

                            <!-- HPS / Nilai HPS -->
                            <div>
                                <label for="nilai_hps" class="block text-sm font-medium text-gray-700">
                                    Nilai HPS (Harga Perkiraan Sendiri) <span class="text-red-500">*</span>
                                </label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">Rp</span>
                                    </div>
                                    <input
                                        type="text"
                                        id="nilai_hps"
                                        v-model="nilaiHpsFormatted"
                                        @input="handleNilaiHpsInput"
                                        class="pl-12 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        :class="{ 'border-red-500': errors.nilai_hps }"
                                        placeholder="0"
                                    />
                                </div>
                                <p v-if="form.nilai_hps" class="mt-1 text-xs text-gray-500">
                                    {{ formatRupiahTerbilang(form.nilai_hps) }}
                                </p>
                                <p v-if="errors.nilai_hps" class="mt-1 text-sm text-red-600">{{ errors.nilai_hps }}</p>
                            </div>

                            <!-- Sumber Data Survei HPS -->
                            <div>
                                <label for="sumber_data_survei_hps" class="block text-sm font-medium text-gray-700">
                                    Sumber Data Survei HPS <span class="text-red-500">*</span>
                                </label>
                                <textarea
                                    id="sumber_data_survei_hps"
                                    v-model="form.sumber_data_survei_hps"
                                    rows="3"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    :class="{ 'border-red-500': errors.sumber_data_survei_hps }"
                                    placeholder="Contoh: Survei pasar di Toko A, Toko B, website resmi, dll"
                                ></textarea>
                                <p v-if="errors.sumber_data_survei_hps" class="mt-1 text-sm text-red-600">{{ errors.sumber_data_survei_hps }}</p>
                                <p class="mt-1 text-xs text-gray-500">Sebutkan sumber data yang digunakan untuk menyusun HPS</p>
                            </div>
                        </div>
                    </div>

                    <!-- Jenis Kontrak dan Kualifikasi -->
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Kontrak dan Pelaksanaan</h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Jenis Kontrak -->
                                <div>
                                    <label for="jenis_kontrak" class="block text-sm font-medium text-gray-700">
                                        Jenis Kontrak <span class="text-red-500">*</span>
                                    </label>
                                    <select
                                        id="jenis_kontrak"
                                        v-model="form.jenis_kontrak"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        :class="{ 'border-red-500': errors.jenis_kontrak }"
                                    >
                                        <option value="">Pilih Jenis Kontrak</option>
                                        <option value="Lump Sum">Lump Sum</option>
                                        <option value="Harga Satuan">Harga Satuan</option>
                                        <option value="Gabungan Lump Sum dan Harga Satuan">Gabungan Lump Sum dan Harga Satuan</option>
                                        <option value="Kontrak Payung">Kontrak Payung</option>
                                        <option value="Terima Jadi">Terima Jadi</option>
                                    </select>
                                    <p v-if="errors.jenis_kontrak" class="mt-1 text-sm text-red-600">{{ errors.jenis_kontrak }}</p>
                                </div>

                                <!-- Kualifikasi -->
                                <div>
                                    <label for="kualifikasi" class="block text-sm font-medium text-gray-700">
                                        Kualifikasi <span class="text-red-500">*</span>
                                    </label>
                                    <select
                                        id="kualifikasi"
                                        v-model="form.kualifikasi"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        :class="{ 'border-red-500': errors.kualifikasi }"
                                    >
                                        <option value="">Pilih Kualifikasi</option>
                                        <option value="Kecil">Kecil</option>
                                        <option value="Non Kecil">Non Kecil</option>
                                        <option value="Tidak Dikualifikasi">Tidak Dikualifikasi</option>
                                    </select>
                                    <p v-if="errors.kualifikasi" class="mt-1 text-sm text-red-600">{{ errors.kualifikasi }}</p>
                                </div>
                            </div>

                            <!-- Jangka Waktu Pelaksanaan -->
                            <div>
                                <label for="jangka_waktu_pelaksanaan" class="block text-sm font-medium text-gray-700">
                                    Jangka Waktu Pelaksanaan <span class="text-red-500">*</span>
                                </label>
                                <div class="mt-1 flex items-center gap-2">
                                    <input
                                        type="number"
                                        id="jangka_waktu_pelaksanaan"
                                        v-model="form.jangka_waktu_pelaksanaan"
                                        class="block w-32 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        :class="{ 'border-red-500': errors.jangka_waktu_pelaksanaan }"
                                        placeholder="0"
                                        min="1"
                                    />
                                    <span class="text-sm text-gray-700">hari kalender</span>
                                </div>
                                <p v-if="errors.jangka_waktu_pelaksanaan" class="mt-1 text-sm text-red-600">{{ errors.jangka_waktu_pelaksanaan }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Detail Pengadaan -->
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Detail Pengadaan</h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <!-- Nama Kegiatan -->
                            <div>
                                <label for="nama_kegiatan" class="block text-sm font-medium text-gray-700">
                                    Nama Kegiatan <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="nama_kegiatan"
                                    v-model="form.nama_kegiatan"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    :class="{ 'border-red-500': errors.nama_kegiatan }"
                                    placeholder="Nama kegiatan pengadaan"
                                />
                                <p v-if="errors.nama_kegiatan" class="mt-1 text-sm text-red-600">{{ errors.nama_kegiatan }}</p>
                            </div>

                            <!-- Pengadaan (Jenis) -->
                            <div>
                                <label for="jenis_pengadaan" class="block text-sm font-medium text-gray-700">
                                    Jenis Pengadaan <span class="text-red-500">*</span>
                                </label>
                                <select
                                    id="jenis_pengadaan"
                                    v-model="form.jenis_pengadaan"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    :class="{ 'border-red-500': errors.jenis_pengadaan }"
                                >
                                    <option value="">Pilih Jenis Pengadaan</option>
                                    <option value="Barang">Barang</option>
                                    <option value="Pekerjaan Konstruksi">Pekerjaan Konstruksi</option>
                                    <option value="Jasa Konsultansi">Jasa Konsultansi</option>
                                    <option value="Jasa Lainnya">Jasa Lainnya</option>
                                </select>
                                <p v-if="errors.jenis_pengadaan" class="mt-1 text-sm text-red-600">{{ errors.jenis_pengadaan }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Info Box -->
                    <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800">Informasi DPP</h3>
                                <div class="mt-2 text-sm text-blue-700">
                                    <p>Dokumen Persiapan Pengadaan (DPP) berisi rencana detail pengadaan termasuk PPK, program, anggaran, HPS, dan jenis kontrak. Pastikan semua data sudah akurat sebelum submit.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="flex items-center justify-end gap-3">
                        <Link
                            :href="route('staff-perencanaan.show', permintaan.permintaan_id)"
                            class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50"
                        >
                            Batal
                        </Link>
                        <button
                            type="submit"
                            :disabled="processing"
                            class="inline-flex items-center px-4 py-2 bg-[#028174] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#03a089] focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50"
                        >
                            <svg v-if="processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            {{ processing ? 'Menyimpan...' : 'Simpan DPP' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    permintaan: Object,
});

const form = useForm({
    // PPK dan Identifikasi
    ppk_ditunjuk: '',
    nama_paket: '',
    lokasi: '',
    
    // Program dan Kegiatan
    uraian_program: '',
    uraian_kegiatan: '',
    sub_kegiatan: '',
    sub_sub_kegiatan: '',
    kode_rekening: '',
    
    // Anggaran dan HPS
    sumber_dana: '',
    pagu_paket: '',
    nilai_hps: '',
    sumber_data_survei_hps: '',
    
    // Kontrak dan Pelaksanaan
    jenis_kontrak: '',
    kualifikasi: '',
    jangka_waktu_pelaksanaan: '',
    
    // Detail Pengadaan
    nama_kegiatan: '',
    jenis_pengadaan: '',
});

const processing = ref(false);
const errors = ref({});

// Format rupiah with thousand separators
const formatRupiah = (value) => {
    if (!value) return '';
    const number = value.toString().replace(/[^,\d]/g, '');
    const split = number.split(',');
    const sisa = split[0].length % 3;
    let rupiah = split[0].substr(0, sisa);
    const ribuan = split[0].substr(sisa).match(/\d{3}/gi);

    if (ribuan) {
        const separator = sisa ? '.' : '';
        rupiah += separator + ribuan.join('.');
    }

    rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
    return rupiah;
};

// Format terbilang
const formatRupiahTerbilang = (value) => {
    if (!value || value == 0) return '';
    const angka = parseInt(value);
    if (angka < 1000) return `${angka} rupiah`;
    if (angka < 1000000) return `${(angka/1000).toFixed(1)} ribu rupiah`;
    if (angka < 1000000000) return `${(angka/1000000).toFixed(2)} juta rupiah`;
    return `${(angka/1000000000).toFixed(2)} miliar rupiah`;
};

// Pagu Paket formatted display
const paguPaketFormatted = computed({
    get() {
        return formatRupiah(form.pagu_paket);
    },
    set(value) {
        // Not used, handled by handlePaguPaketInput
    }
});

const handlePaguPaketInput = (event) => {
    const value = event.target.value.replace(/\./g, '').replace(/,/g, '.');
    form.pagu_paket = value;
};

// Nilai HPS formatted display
const nilaiHpsFormatted = computed({
    get() {
        return formatRupiah(form.nilai_hps);
    },
    set(value) {
        // Not used, handled by handleNilaiHpsInput
    }
});

const handleNilaiHpsInput = (event) => {
    const value = event.target.value.replace(/\./g, '').replace(/,/g, '.');
    form.nilai_hps = value;
};

const submit = () => {
    // Reset errors
    errors.value = {};

    // Validasi semua field wajib
    const requiredFields = {
        ppk_ditunjuk: 'PPK yang ditunjuk harus diisi',
        nama_paket: 'Nama paket harus diisi',
        lokasi: 'Lokasi harus diisi',
        uraian_program: 'Uraian program harus diisi',
        uraian_kegiatan: 'Uraian kegiatan harus diisi',
        kode_rekening: 'Kode rekening harus diisi',
        sumber_dana: 'Sumber dana harus dipilih',
        pagu_paket: 'Pagu paket harus diisi',
        nilai_hps: 'Nilai HPS harus diisi',
        sumber_data_survei_hps: 'Sumber data survei HPS harus diisi',
        jenis_kontrak: 'Jenis kontrak harus dipilih',
        kualifikasi: 'Kualifikasi harus dipilih',
        jangka_waktu_pelaksanaan: 'Jangka waktu pelaksanaan harus diisi',
        nama_kegiatan: 'Nama kegiatan harus diisi',
        jenis_pengadaan: 'Jenis pengadaan harus dipilih',
    };

    let hasError = false;
    for (const [field, message] of Object.entries(requiredFields)) {
        if (!form[field] || form[field] === '') {
            errors.value[field] = message;
            hasError = true;
        }
    }

    // Validasi angka
    if (form.pagu_paket && form.pagu_paket <= 0) {
        errors.value.pagu_paket = 'Pagu paket harus lebih dari 0';
        hasError = true;
    }

    if (form.nilai_hps && form.nilai_hps <= 0) {
        errors.value.nilai_hps = 'Nilai HPS harus lebih dari 0';
        hasError = true;
    }

    if (form.jangka_waktu_pelaksanaan && form.jangka_waktu_pelaksanaan < 1) {
        errors.value.jangka_waktu_pelaksanaan = 'Jangka waktu minimal 1 hari';
        hasError = true;
    }

    if (hasError) return;

    processing.value = true;

    form.post(route('staff-perencanaan.dpp.store', props.permintaan.permintaan_id), {
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
