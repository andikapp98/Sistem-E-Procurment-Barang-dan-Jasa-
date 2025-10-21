<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Buat Nota Dinas Pembelian - Permintaan #{{ permintaan.permintaan_id }}
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

                <!-- Form Nota Dinas Pembelian -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Form Nota Dinas Pembelian</h3>
                        <p class="mt-1 text-sm text-gray-600">
                            Nota dinas untuk proses pembelian barang/jasa yang sudah disetujui.
                        </p>
                    </div>

                    <form @submit.prevent="submit" class="p-6 space-y-6">
                        <!-- Header Nota Dinas -->
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                            <h4 class="text-sm font-medium text-gray-700 mb-4">Identitas Nota Dinas</h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Nomor Nota Dinas -->
                                <div>
                                    <label for="nomor_nota_dinas" class="block text-sm font-medium text-gray-700">
                                        Nomor Nota Dinas <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        id="nomor_nota_dinas"
                                        v-model="form.nomor_nota_dinas"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        :class="{ 'border-red-500': errors.nomor_nota_dinas }"
                                        placeholder="Contoh: 001/ND-PEM/SP/2025"
                                    />
                                    <p v-if="errors.nomor_nota_dinas" class="mt-1 text-sm text-red-600">{{ errors.nomor_nota_dinas }}</p>
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
                            </div>
                        </div>

                        <!-- Detail Pembelian -->
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                            <h4 class="text-sm font-medium text-gray-700 mb-4">Detail Pembelian</h4>
                            
                            <!-- Usulan Ruangan -->
                            <div class="mb-4">
                                <label for="usulan_ruangan" class="block text-sm font-medium text-gray-700">
                                    Usulan Ruangan <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="usulan_ruangan"
                                    v-model="form.usulan_ruangan"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    :class="{ 'border-red-500': errors.usulan_ruangan }"
                                    placeholder="Contoh: Ruang IGD, Farmasi, Laboratorium"
                                />
                                <p v-if="errors.usulan_ruangan" class="mt-1 text-sm text-red-600">{{ errors.usulan_ruangan }}</p>
                                <p class="mt-1 text-xs text-gray-500">Ruangan/unit yang mengajukan permintaan pembelian</p>
                            </div>

                            <!-- Sifat -->
                            <div class="mb-4">
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

                            <!-- Perihal -->
                            <div>
                                <label for="perihal" class="block text-sm font-medium text-gray-700">
                                    Perihal <span class="text-red-500">*</span>
                                </label>
                                <textarea
                                    id="perihal"
                                    v-model="form.perihal"
                                    rows="3"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    :class="{ 'border-red-500': errors.perihal }"
                                    placeholder="Contoh: Permintaan Pembelian Alat Kesehatan untuk Ruang IGD"
                                ></textarea>
                                <p v-if="errors.perihal" class="mt-1 text-sm text-red-600">{{ errors.perihal }}</p>
                                <p class="mt-1 text-xs text-gray-500">Jelaskan tujuan dan hal yang diminta dalam nota dinas ini</p>
                            </div>
                        </div>

                        <!-- Penerima & Tujuan -->
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                            <h4 class="text-sm font-medium text-gray-700 mb-4">Tujuan Nota Dinas</h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
                                        <option value="Bagian Pengadaan">Bagian Pengadaan</option>
                                        <option value="Bagian KSO">Bagian KSO</option>
                                        <option value="Kepala Bagian Keuangan">Kepala Bagian Keuangan</option>
                                        <option value="Direktur">Direktur</option>
                                    </select>
                                    <p v-if="errors.kepada" class="mt-1 text-sm text-red-600">{{ errors.kepada }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Isi Nota Dinas -->
                        <div>
                            <label for="isi_nota" class="block text-sm font-medium text-gray-700">
                                Isi Nota Dinas
                            </label>
                            <textarea
                                id="isi_nota"
                                v-model="form.isi_nota"
                                rows="6"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="Detail lengkap mengenai pembelian yang akan dilakukan, termasuk spesifikasi barang/jasa, jumlah, dan alasan pembelian."
                            ></textarea>
                            <p class="mt-1 text-xs text-gray-500">Opsional - Detail tambahan mengenai nota dinas pembelian</p>
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
                                        <p>Nota dinas pembelian akan dibuat untuk proses pengadaan barang/jasa. Pastikan semua informasi sudah lengkap dan akurat.</p>
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
                                {{ processing ? 'Menyimpan...' : 'Buat Nota Dinas Pembelian' }}
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
    nomor_nota_dinas: '',
    tanggal_nota: new Date().toISOString().split('T')[0],
    usulan_ruangan: props.permintaan.user?.unit_kerja || '',
    sifat: '',
    perihal: '',
    dari: 'Staff Perencanaan',
    kepada: '',
    isi_nota: '',
    tipe_nota: 'pembelian', // Identifier untuk tipe nota dinas
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

const submit = () => {
    // Reset errors
    errors.value = {};

    // Validasi
    if (!form.nomor_nota_dinas) {
        errors.value.nomor_nota_dinas = 'Nomor nota dinas harus diisi';
        return;
    }
    if (!form.tanggal_nota) {
        errors.value.tanggal_nota = 'Tanggal harus diisi';
        return;
    }
    if (!form.usulan_ruangan) {
        errors.value.usulan_ruangan = 'Usulan ruangan harus diisi';
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
        errors.value.kepada = 'Kepada harus dipilih';
        return;
    }

    processing.value = true;

    form.post(route('staff-perencanaan.nota-dinas-pembelian.store', props.permintaan.permintaan_id), {
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
