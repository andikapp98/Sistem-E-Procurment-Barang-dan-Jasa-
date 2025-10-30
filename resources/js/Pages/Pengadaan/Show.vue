<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Detail Permintaan #{{ permintaan.permintaan_id }}
                </h2>
                <Link :href="route('pengadaan.index')" class="text-sm text-gray-600 hover:text-gray-900">
                    ← Kembali
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <!-- Informasi Permintaan -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            Informasi Permintaan
                        </h3>
                        <dl class="grid grid-cols-2 gap-4">
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
                                <dd class="mt-1">
                                    <span :class="getStatusClass(permintaan.status)" class="px-2 py-1 text-xs rounded-full">
                                        {{ permintaan.status }}
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">PIC Saat Ini</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ permintaan.pic_pimpinan || '-' }}</dd>
                            </div>
                            <div class="col-span-2">
                                <dt class="text-sm font-medium text-gray-500">Deskripsi</dt>
                                <dd class="mt-1 text-sm text-gray-900 whitespace-pre-line">{{ permintaan.deskripsi }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Data KSO (jika ada) -->
                <div v-if="kso" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            Data KSO
                        </h3>
                        <dl class="grid grid-cols-2 gap-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">No KSO</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ kso.no_kso }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Tanggal</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ formatDate(kso.tanggal_kso) }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Data Pengadaan (jika ada) -->
                <div v-if="pengadaan" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            Data Pengadaan
                        </h3>
                        <dl class="grid grid-cols-2 gap-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">No Pengadaan</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ pengadaan.no_pengadaan }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Tanggal</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ formatDate(pengadaan.tanggal_pengadaan) }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Vendor</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ pengadaan.vendor || '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Status</dt>
                                <dd class="mt-1">
                                    <span :class="getStatusClass(pengadaan.status)" class="px-2 py-1 text-xs rounded-full">
                                        {{ pengadaan.status }}
                                    </span>
                                </dd>
                            </div>
                            <div v-if="pengadaan.total_harga">
                                <dt class="text-sm font-medium text-gray-500">Total Harga</dt>
                                <dd class="mt-1 text-sm text-gray-900">Rp {{ formatRupiah(pengadaan.total_harga) }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Actions -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            Aksi
                        </h3>
                        
                        <div class="flex gap-3">
                            <!-- Forward ke KSO -->
                            <button
                                v-if="permintaan.pic_pimpinan === 'Bagian Pengadaan'"
                                @click="showForwardModal = true"
                                class="inline-flex items-center px-4 py-2 bg-[#028174] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#03a089] focus:outline-none focus:ring-2 focus:ring-offset-2"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                </svg>
                                Forward ke Bagian KSO
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Forward ke KSO -->
        <Modal :show="showForwardModal" @close="showForwardModal = false">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    Forward ke Bagian KSO
                </h3>
                
                <form @submit.prevent="submitForward">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Catatan
                        </label>
                        <textarea
                            v-model="forwardForm.catatan"
                            rows="4"
                            class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500"
                            placeholder="Tambahkan catatan untuk Bagian KSO (opsional)"
                        ></textarea>
                    </div>

                    <div class="flex justify-end gap-3">
                        <button
                            type="button"
                            @click="showForwardModal = false"
                            class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            :disabled="forwardForm.processing"
                            class="px-4 py-2 bg-[#028174] border border-transparent rounded-md text-sm font-medium text-white hover:bg-[#03a089] disabled:opacity-50"
                        >
                            {{ forwardForm.processing ? 'Memproses...' : 'Forward ke KSO' }}
                        </button>
                    </div>
                </form>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Modal from '@/Components/Modal.vue';
import { Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    permintaan: Object,
    kso: Object,
    pengadaan: Object,
});

const showForwardModal = ref(false);

const forwardForm = useForm({
    catatan: '',
});

const submitForward = () => {
    forwardForm.post(route('pengadaan.forward-to-kso', props.permintaan.permintaan_id), {
        preserveScroll: true,
        onSuccess: () => {
            showForwardModal.value = false;
            forwardForm.reset();
        },
    });
};

const getStatusClass = (status) => {
    const classes = {
        'draft': 'bg-gray-100 text-gray-800',
        'menunggu': 'bg-yellow-100 text-yellow-800',
        'proses': 'bg-blue-100 text-blue-800',
        'disetujui': 'bg-green-100 text-green-800',
        'ditolak': 'bg-red-100 text-red-800',
        'selesai': 'bg-purple-100 text-purple-800',
        'persiapan': 'bg-yellow-100 text-yellow-800',
        'pembelian': 'bg-blue-100 text-blue-800',
        'pengiriman': 'bg-indigo-100 text-indigo-800',
        'diterima': 'bg-green-100 text-green-800',
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
};

const formatDate = (date) => {
    if (!date) return '-';
    return new Date(date).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

const formatRupiah = (value) => {
    if (!value) return '0';
    return new Intl.NumberFormat('id-ID').format(value);
};
</script>
