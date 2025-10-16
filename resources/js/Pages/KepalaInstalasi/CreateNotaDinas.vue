<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Buat Nota Dinas - Permintaan #{{ permintaan.permintaan_id }}
                </h2>
                <Link
                    :href="route('kepala-instalasi.show', permintaan.permintaan_id)"
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

                <!-- Form Nota Dinas -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Form Nota Dinas</h3>
                        <p class="mt-1 text-sm text-gray-600">
                            Isi informasi nota dinas untuk meneruskan permintaan ini ke pihak yang berwenang.
                        </p>
                    </div>

                    <form @submit.prevent="submit" class="p-6 space-y-6">
                        <!-- Dari Unit -->
                        <div>
                            <label for="dari_unit" class="block text-sm font-medium text-gray-700">
                                Dari Unit <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                id="dari_unit"
                                v-model="form.dari_unit"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                :class="{ 'border-red-500': errors.dari_unit }"
                                placeholder="Contoh: Instalasi Farmasi"
                            />
                            <p v-if="errors.dari_unit" class="mt-1 text-sm text-red-600">{{ errors.dari_unit }}</p>
                        </div>

                        <!-- Ke Jabatan -->
                        <div>
                            <label for="ke_jabatan" class="block text-sm font-medium text-gray-700">
                                Ditujukan Kepada <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="ke_jabatan"
                                v-model="form.ke_jabatan"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                :class="{ 'border-red-500': errors.ke_jabatan }"
                            >
                                <option value="">Pilih Tujuan</option>
                                <option value="Direktur">Direktur</option>
                                <option value="Wakil Direktur">Wakil Direktur</option>
                                <option value="Kepala Bagian Keuangan">Kepala Bagian Keuangan</option>
                                <option value="Kepala Bagian Umum">Kepala Bagian Umum</option>
                                <option value="Kepala Bagian Perencanaan">Kepala Bagian Perencanaan</option>
                                <option value="Bagian Pengadaan">Bagian Pengadaan</option>
                            </select>
                            <p v-if="errors.ke_jabatan" class="mt-1 text-sm text-red-600">{{ errors.ke_jabatan }}</p>
                        </div>

                        <!-- Tanggal Nota -->
                        <div>
                            <label for="tanggal_nota" class="block text-sm font-medium text-gray-700">
                                Tanggal Nota <span class="text-red-500">*</span>
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
                                        <p>Nota dinas akan dibuat dan dikirim ke pihak yang dituju. Status permintaan akan berubah menjadi "Dalam Proses".</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="flex items-center justify-end gap-3">
                            <Link
                                :href="route('kepala-instalasi.show', permintaan.permintaan_id)"
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
                                {{ processing ? 'Menyimpan...' : 'Buat Nota Dinas' }}
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
    dari_unit: props.permintaan.user?.unit_kerja || '',
    ke_jabatan: '',
    tanggal_nota: new Date().toISOString().split('T')[0],
    status: 'proses',
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
    if (!form.dari_unit) {
        errors.value.dari_unit = 'Dari unit harus diisi';
        return;
    }
    if (!form.ke_jabatan) {
        errors.value.ke_jabatan = 'Tujuan harus dipilih';
        return;
    }
    if (!form.tanggal_nota) {
        errors.value.tanggal_nota = 'Tanggal nota harus diisi';
        return;
    }

    processing.value = true;

    form.post(route('kepala-instalasi.nota-dinas.store', props.permintaan.permintaan_id), {
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
