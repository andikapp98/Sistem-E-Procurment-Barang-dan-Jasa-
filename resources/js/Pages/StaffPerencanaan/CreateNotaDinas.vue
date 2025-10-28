<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Buat Nota Dinas Usulan - Permintaan #{{ permintaan.permintaan_id }}
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
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
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

                <!-- Form Nota Dinas Usulan -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Form Nota Dinas Usulan</h3>
                        <p class="mt-1 text-sm text-gray-600">
                            Isi informasi nota dinas usulan termasuk pagu anggaran yang telah ditetapkan.
                        </p>
                    </div>

                    <form @submit.prevent="submit" class="p-6 space-y-6">
                        <!-- Header Section -->
                        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
                            <p class="text-sm text-blue-700">
                                <strong>Field dengan tanda bintang merah (*) wajib diisi.</strong> Nomor nota akan di-generate otomatis jika dikosongkan.
                            </p>
                        </div>

                        <!-- Grid 2 Kolom untuk Header -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Nomor Nota Dinas -->
                            <div>
                                <label for="nomor" class="block text-sm font-medium text-gray-700">
                                    Nomor Nota Dinas <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="nomor"
                                    v-model="form.nomor"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    :class="{ 'border-red-500': errors.nomor }"
                                    placeholder="Contoh: 001/ND/SP/2025"
                                />
                                <p class="mt-1 text-xs text-gray-500">Kosongkan untuk auto-generate</p>
                                <p v-if="errors.nomor" class="mt-1 text-sm text-red-600">{{ errors.nomor }}</p>
                            </div>

                            <!-- Tanggal -->
                            <div>
                                <label for="tanggal_nota" class="block text-sm font-medium text-gray-700">
                                    Tanggal <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="date"
                                    id="tanggal_nota"
                                    v-model="form.tanggal_nota"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    :class="{ 'border-red-500': errors.tanggal_nota }"
                                />
                                <p v-if="errors.tanggal_nota" class="mt-1 text-sm text-red-600">{{ errors.tanggal_nota }}</p>
                            </div>

                            <!-- Usulan Ruangan -->
                            <div>
                                <label for="usulan_ruangan" class="block text-sm font-medium text-gray-700">
                                    Usulan Ruangan <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="usulan_ruangan"
                                    v-model="form.usulan_ruangan"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    :class="{ 'border-red-500': errors.usulan_ruangan }"
                                    placeholder="Contoh: IGD, Poliklinik, Farmasi"
                                />
                                <p v-if="errors.usulan_ruangan" class="mt-1 text-sm text-red-600">{{ errors.usulan_ruangan }}</p>
                            </div>

                            <!-- Sifat -->
                            <div>
                                <label for="sifat" class="block text-sm font-medium text-gray-700">
                                    Sifat <span class="text-red-500">*</span>
                                </label>
                                <select
                                    id="sifat"
                                    v-model="form.sifat"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    :class="{ 'border-red-500': errors.sifat }"
                                >
                                    <option value="">Pilih Sifat</option>
                                    <option value="Sangat Segera">Sangat Segera</option>
                                    <option value="Segera">Segera</option>
                                    <option value="Biasa">Biasa</option>
                                    <option value="Rahasia">Rahasia</option>
                                </select>
                                <p v-if="errors.sifat" class="mt-1 text-sm text-red-600">{{ errors.sifat }}</p>
                            </div>
                        </div>

                        <!-- Perihal -->
                        <div>
                            <label for="perihal" class="block text-sm font-medium text-gray-700">
                                Perihal <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                id="perihal"
                                v-model="form.perihal"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                :class="{ 'border-red-500': errors.perihal }"
                                placeholder="Contoh: Usulan Pengadaan Alat Medis"
                            />
                            <p v-if="errors.perihal" class="mt-1 text-sm text-red-600">{{ errors.perihal }}</p>
                        </div>

                        <!-- Dari dan Kepada -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Dari -->
                            <div>
                                <label for="dari" class="block text-sm font-medium text-gray-700">
                                    Dari <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="dari"
                                    v-model="form.dari"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    :class="{ 'border-red-500': errors.dari }"
                                    placeholder="Staff Perencanaan"
                                />
                                <p v-if="errors.dari" class="mt-1 text-sm text-red-600">{{ errors.dari }}</p>
                            </div>

                            <!-- Kepada -->
                            <div>
                                <label for="kepada" class="block text-sm font-medium text-gray-700">
                                    Kepada <span class="text-red-500">*</span>
                                </label>
                                <select
                                    id="kepada"
                                    v-model="form.kepada"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    :class="{ 'border-red-500': errors.kepada }"
                                >
                                    <option value="">Pilih Tujuan</option>
                                    <option value="Direktur">Direktur</option>
                                    <option value="Wakil Direktur">Wakil Direktur</option>
                                    <option value="Kepala Bagian Keuangan">Kepala Bagian Keuangan</option>
                                    <option value="Bagian KSO">Bagian KSO</option>
                                    <option value="Bagian Pengadaan">Bagian Pengadaan</option>
                                </select>
                                <p v-if="errors.kepada" class="mt-1 text-sm text-red-600">{{ errors.kepada }}</p>
                            </div>
                        </div>

                        <!-- Kode Program, Kegiatan, Rekening (Grid 3 kolom) -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="kode_program" class="block text-sm font-medium text-gray-700">
                                    Kode Program
                                </label>
                                <input
                                    type="text"
                                    id="kode_program"
                                    v-model="form.kode_program"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="Contoh: 01"
                                />
                            </div>
                            <div>
                                <label for="kode_kegiatan" class="block text-sm font-medium text-gray-700">
                                    Kode Kegiatan
                                </label>
                                <input
                                    type="text"
                                    id="kode_kegiatan"
                                    v-model="form.kode_kegiatan"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="Contoh: 001"
                                />
                            </div>
                            <div>
                                <label for="kode_rekening" class="block text-sm font-medium text-gray-700">
                                    Kode Rekening
                                </label>
                                <input
                                    type="text"
                                    id="kode_rekening"
                                    v-model="form.kode_rekening"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="Contoh: 5.1.1"
                                />
                            </div>
                        </div>

                        <!-- Uraian -->
                        <div>
                            <label for="uraian" class="block text-sm font-medium text-gray-700">
                                Uraian
                            </label>
                            <textarea
                                id="uraian"
                                v-model="form.uraian"
                                rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="Uraian detail pengadaan"
                            ></textarea>
                        </div>

                        <!-- Pagu Anggaran -->
                        <div>
                            <label for="pagu_anggaran" class="block text-sm font-medium text-gray-700">
                                Pagu Anggaran <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">Rp</span>
                                </div>
                                <input
                                    type="text"
                                    id="pagu_anggaran"
                                    v-model="paguAnggaranFormatted"
                                    @input="handlePaguAnggaranInput"
                                    class="pl-12 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    :class="{ 'border-red-500': errors.pagu_anggaran }"
                                    placeholder="0"
                                />
                            </div>
                            <p v-if="form.pagu_anggaran" class="mt-1 text-xs text-gray-500">
                                {{ formatRupiahTerbilang(form.pagu_anggaran) }}
                            </p>
                            <p v-if="errors.pagu_anggaran" class="mt-1 text-sm text-red-600">{{ errors.pagu_anggaran }}</p>
                        </div>

                        <!-- Pajak (Grid 2 kolom untuk PPh dan PPN) -->
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                            <h4 class="text-sm font-medium text-gray-700 mb-4">Pajak</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="pph" class="block text-sm font-medium text-gray-700">
                                        PPh
                                    </label>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">Rp</span>
                                        </div>
                                        <input
                                            type="number"
                                            id="pph"
                                            v-model="form.pph"
                                            class="pl-12 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            placeholder="0"
                                            step="0.01"
                                        />
                                    </div>
                                </div>
                                <div>
                                    <label for="ppn" class="block text-sm font-medium text-gray-700">
                                        PPN
                                    </label>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">Rp</span>
                                        </div>
                                        <input
                                            type="number"
                                            id="ppn"
                                            v-model="form.ppn"
                                            class="pl-12 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            placeholder="0"
                                            step="0.01"
                                        />
                                    </div>
                                </div>
                                <div>
                                    <label for="pph_21" class="block text-sm font-medium text-gray-700">
                                        PPh 21
                                    </label>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">Rp</span>
                                        </div>
                                        <input
                                            type="number"
                                            id="pph_21"
                                            v-model="form.pph_21"
                                            class="pl-12 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            placeholder="0"
                                            step="0.01"
                                        />
                                    </div>
                                </div>
                                <div>
                                    <label for="pph_4_2" class="block text-sm font-medium text-gray-700">
                                        PPh 4(2)
                                    </label>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">Rp</span>
                                        </div>
                                        <input
                                            type="number"
                                            id="pph_4_2"
                                            v-model="form.pph_4_2"
                                            class="pl-12 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            placeholder="0"
                                            step="0.01"
                                        />
                                    </div>
                                </div>
                                <div>
                                    <label for="pph_22" class="block text-sm font-medium text-gray-700">
                                        PPh 22
                                    </label>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">Rp</span>
                                        </div>
                                        <input
                                            type="number"
                                            id="pph_22"
                                            v-model="form.pph_22"
                                            class="pl-12 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            placeholder="0"
                                            step="0.01"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Unit Instalasi -->
                        <div>
                            <label for="unit_instalasi" class="block text-sm font-medium text-gray-700">
                                Unit Instalasi
                            </label>
                            <input
                                type="text"
                                id="unit_instalasi"
                                v-model="form.unit_instalasi"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="Unit/Instalasi"
                            />
                        </div>

                        <!-- Faktur & Kwitansi -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="no_faktur_pajak" class="block text-sm font-medium text-gray-700">
                                    No Faktur Pajak
                                </label>
                                <input
                                    type="text"
                                    id="no_faktur_pajak"
                                    v-model="form.no_faktur_pajak"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="No Faktur Pajak"
                                />
                            </div>
                            <div>
                                <label for="tanggal_faktur_pajak" class="block text-sm font-medium text-gray-700">
                                    Tanggal Faktur Pajak
                                </label>
                                <input
                                    type="date"
                                    id="tanggal_faktur_pajak"
                                    v-model="form.tanggal_faktur_pajak"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                />
                            </div>
                        </div>

                        <div>
                            <label for="no_kwitansi" class="block text-sm font-medium text-gray-700">
                                No Kwitansi
                            </label>
                            <input
                                type="text"
                                id="no_kwitansi"
                                v-model="form.no_kwitansi"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="No Kwitansi"
                            />
                        </div>

                        <!-- Ringkasan Permintaan (Read-only untuk referensi) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Ringkasan Permintaan
                            </label>
                            <div class="bg-gray-50 p-4 rounded-md border border-gray-200">
                                <p class="text-sm text-gray-900">{{ permintaan.deskripsi }}</p>
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
                                    <h3 class="text-sm font-medium text-blue-800">Informasi</h3>
                                    <div class="mt-2 text-sm text-blue-700">
                                        <p>Nota dinas usulan akan dibuat dengan pagu anggaran yang ditetapkan. Pastikan semua data anggaran sudah benar sebelum submit.</p>
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
                                {{ processing ? 'Menyimpan...' : 'Buat Nota Dinas Usulan' }}
                            </button>
                        </div>
                    </form>
                </div>
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
    nomor: '',
    tanggal_nota: new Date().toISOString().split('T')[0],
    usulan_ruangan: props.permintaan.user?.unit_kerja || '',
    sifat: '',
    perihal: '',
    dari: 'Staff Perencanaan',
    kepada: '',
    penerima: '',
    kode_program: '',
    kode_kegiatan: '',
    kode_rekening: '',
    uraian: '',
    pagu_anggaran: '',
    pph: '',
    ppn: '',
    pph_21: '',
    pph_4_2: '',
    pph_22: '',
    unit_instalasi: props.permintaan.user?.unit_kerja || '',
    no_faktur_pajak: '',
    no_kwitansi: '',
    tanggal_faktur_pajak: '',
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

// Pagu Anggaran formatted display
const paguAnggaranFormatted = computed({
    get() {
        return formatRupiah(form.pagu_anggaran);
    },
    set(value) {
        // Not used, handled by handlePaguAnggaranInput
    }
});

const handlePaguAnggaranInput = (event) => {
    const value = event.target.value.replace(/\./g, '').replace(/,/g, '.');
    form.pagu_anggaran = value;
};

const formatDate = (dateString) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
};

const submit = () => {
    // Reset errors
    errors.value = {};

    // Validasi semua field wajib
    if (!form.nomor) {
        errors.value.nomor = 'Nomor Nota Dinas harus diisi';
        return;
    }
    if (!form.tanggal_nota) {
        errors.value.tanggal_nota = 'Tanggal harus diisi';
        return;
    }
    if (!form.usulan_ruangan) {
        errors.value.usulan_ruangan = 'Usulan Ruangan harus diisi';
        return;
    }
    if (!form.sifat) {
        errors.value.sifat = 'Sifat harus dipilih';
        return;
    }
    if (!form.perihal) {
        errors.value.perihal = 'Perihal harus diisi';
        return;
    }
    if (!form.dari) {
        errors.value.dari = 'Dari harus diisi';
        return;
    }
    if (!form.kepada) {
        errors.value.kepada = 'Kepada harus diisi';
        return;
    }
    if (!form.pagu_anggaran || form.pagu_anggaran <= 0) {
        errors.value.pagu_anggaran = 'Pagu anggaran harus diisi dan lebih dari 0';
        return;
    }

    processing.value = true;

    form.post(route('staff-perencanaan.nota-dinas.store', props.permintaan.permintaan_id), {
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
