<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Buat Disposisi - Permintaan #{{ permintaan.permintaan_id }}
                </h2>
                <Link :href="route('wakil-direktur.show', permintaan.permintaan_id)" class="text-sm text-gray-600 hover:text-gray-900">
                    ← Kembali
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <!-- Info Permintaan -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            Informasi Permintaan
                        </h3>
                        <dl class="grid grid-cols-2 gap-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Bidang</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ permintaan.bidang }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Tanggal</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ new Date(permintaan.tanggal_permintaan).toLocaleDateString('id-ID') }}</dd>
                            </div>
                            <div class="col-span-2">
                                <dt class="text-sm font-medium text-gray-500">Deskripsi</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ permintaan.deskripsi }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Form Disposisi -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-6">
                            Form Disposisi
                        </h3>

                        <form @submit.prevent="submit">
                            <!-- Jabatan Tujuan -->
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Disposisi ke Jabatan <span class="text-red-500">*</span>
                                </label>
                                <select v-model="form.jabatan_tujuan" required
                                    class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">-- Pilih Jabatan --</option>
                                    <option value="Bagian Perencanaan">Bagian Perencanaan</option>
                                    <option value="Bagian Pengadaan">Bagian Pengadaan</option>
                                    <option value="Bagian KSO">Bagian KSO</option>
                                    <option value="Direktur">Direktur</option>
                                    <option value="Wadir Umum & Keuangan">Wadir Umum & Keuangan</option>
                                </select>
                                <p class="mt-1 text-xs text-gray-500">Pilih bagian yang akan menindaklanjuti permintaan ini</p>
                            </div>

                            <!-- Tanggal Disposisi -->
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Tanggal Disposisi <span class="text-red-500">*</span>
                                </label>
                                <input type="date" v-model="form.tanggal_disposisi" required
                                    class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            </div>

                            <!-- Catatan -->
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Catatan/Instruksi
                                </label>
                                <textarea v-model="form.catatan" rows="4"
                                    class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="Tambahkan catatan atau instruksi untuk bagian tujuan"></textarea>
                                <p class="mt-1 text-xs text-gray-500">Opsional: Berikan instruksi khusus jika diperlukan</p>
                            </div>

                            <!-- Status -->
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Status Disposisi
                                </label>
                                <select v-model="form.status"
                                    class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="dalam_proses">Dalam Proses</option>
                                    <option value="menunggu">Menunggu</option>
                                    <option value="disetujui">Disetujui</option>
                                </select>
                            </div>

                            <!-- Buttons -->
                            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                                <Link :href="route('wakil-direktur.show', permintaan.permintaan_id)"
                                    class="px-6 py-2 text-gray-700 hover:text-gray-900">
                                    Batal
                                </Link>
                                <button type="submit"
                                    class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                                    Buat Disposisi
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, router } from '@inertiajs/vue3';

const props = defineProps({
    permintaan: Object,
});

const form = ref({
    jabatan_tujuan: '',
    tanggal_disposisi: new Date().toISOString().split('T')[0],
    catatan: '',
    status: 'dalam_proses',
});

const submit = () => {
    router.post(route('wakil-direktur.disposisi.store', props.permintaan.permintaan_id), form.value);
};
</script>
