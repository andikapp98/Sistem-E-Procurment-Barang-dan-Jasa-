<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        Review Permintaan #{{ permintaan.permintaan_id }}
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">
                        {{ permintaan.bidang }}
                    </p>
                </div>
                <Link
                    :href="route('kepala-instalasi.index')"
                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none"
                >
                    ← Kembali
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
                
                <!-- Success notification -->
                <div v-if="$page.props.flash?.success" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ $page.props.flash.success }}</span>
                </div>

                <!-- Card Informasi Utama -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-medium text-gray-900">Informasi Permintaan</h3>
                            <span
                                :class="getStatusClass(permintaan.status)"
                                class="inline-flex items-center px-3 py-1.5 text-sm font-medium rounded-full"
                            >
                                {{ getStatusLabel(permintaan.status) }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="p-6">
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 mb-1">ID Permintaan</dt>
                                <dd class="text-base text-gray-900 font-semibold">#{{ permintaan.permintaan_id }}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500 mb-1">Tanggal Permintaan</dt>
                                <dd class="text-base text-gray-900">{{ formatDate(permintaan.tanggal_permintaan) }}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500 mb-1">Pemohon</dt>
                                <dd class="text-base text-gray-900">{{ permintaan.user?.nama || '-' }}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500 mb-1">Unit Kerja</dt>
                                <dd class="text-base text-gray-900">{{ permintaan.user?.unit_kerja || '-' }}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500 mb-1">Bidang</dt>
                                <dd class="text-base text-gray-900">{{ permintaan.bidang || '-' }}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500 mb-1">Tracking Status</dt>
                                <dd>
                                    <span
                                        :class="getTrackingClass(trackingStatus)"
                                        class="px-2 py-1 text-sm font-medium rounded"
                                    >
                                        {{ trackingStatus }}
                                    </span>
                                </dd>
                            </div>

                            <div class="md:col-span-2">
                                <dt class="text-sm font-medium text-gray-500 mb-1">Deskripsi</dt>
                                <dd class="text-base text-gray-900 whitespace-pre-line">{{ permintaan.deskripsi }}</dd>
                            </div>

                            <div v-if="permintaan.pic_pimpinan">
                                <dt class="text-sm font-medium text-gray-500 mb-1">PIC Pimpinan</dt>
                                <dd class="text-base text-gray-900">{{ permintaan.pic_pimpinan }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Nota Dinas (jika ada) -->
                <div v-if="permintaan.nota_dinas" class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Nota Dinas</h3>
                    </div>
                    <div class="p-6">
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 mb-1">Dari Unit</dt>
                                <dd class="text-base text-gray-900">{{ permintaan.nota_dinas.dari_unit }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 mb-1">Ke Jabatan</dt>
                                <dd class="text-base text-gray-900">{{ permintaan.nota_dinas.ke_jabatan }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 mb-1">Tanggal Nota</dt>
                                <dd class="text-base text-gray-900">{{ formatDate(permintaan.nota_dinas.tanggal_nota) }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 mb-1">Status Nota</dt>
                                <dd>
                                    <span :class="getStatusClass(permintaan.nota_dinas.status)" class="px-2 py-1 text-sm font-medium rounded">
                                        {{ permintaan.nota_dinas.status }}
                                    </span>
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div v-if="permintaan.status === 'diajukan'" class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Tindakan</h3>
                    </div>
                    <div class="p-6">
                        <div class="flex flex-wrap gap-3">
                            <!-- Approve -->
                            <button
                                @click="showApproveModal = true"
                                class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 font-medium"
                            >
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Setujui
                            </button>

                            <!-- Buat Nota Dinas -->
                            <Link
                                :href="route('kepala-instalasi.nota-dinas.create', permintaan.permintaan_id)"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 font-medium"
                            >
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Buat Nota Dinas
                            </Link>

                            <!-- Request Revisi -->
                            <button
                                @click="showRevisiModal = true"
                                class="inline-flex items-center px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700 font-medium"
                            >
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Minta Revisi
                            </button>

                            <!-- Reject -->
                            <button
                                @click="showRejectModal = true"
                                class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 font-medium"
                            >
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Tolak
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Approve -->
        <div v-if="showApproveModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Setujui Permintaan</h3>
                    <div class="mt-2 px-7 py-3">
                        <p class="text-sm text-gray-500">
                            Apakah Anda yakin ingin menyetujui permintaan ini? Permintaan akan diteruskan ke Bagian Pengadaan.
                        </p>
                    </div>
                    <div class="flex gap-3 px-4 py-3">
                        <button
                            @click="approve"
                            class="flex-1 px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700"
                        >
                            Ya, Setujui
                        </button>
                        <button
                            @click="showApproveModal = false"
                            class="flex-1 px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400"
                        >
                            Batal
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Reject -->
        <div v-if="showRejectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Tolak Permintaan</h3>
                    <div class="mt-2 px-7 py-3">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Alasan Penolakan</label>
                        <textarea
                            v-model="rejectForm.alasan"
                            rows="4"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500"
                            placeholder="Masukkan alasan penolakan..."
                        ></textarea>
                    </div>
                    <div class="flex gap-3 px-4 py-3">
                        <button
                            @click="reject"
                            :disabled="!rejectForm.alasan"
                            class="flex-1 px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 disabled:opacity-50"
                        >
                            Tolak
                        </button>
                        <button
                            @click="showRejectModal = false"
                            class="flex-1 px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400"
                        >
                            Batal
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Revisi -->
        <div v-if="showRevisiModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Minta Revisi</h3>
                    <div class="mt-2 px-7 py-3">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Revisi</label>
                        <textarea
                            v-model="revisiForm.catatan_revisi"
                            rows="4"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500"
                            placeholder="Masukkan catatan revisi..."
                        ></textarea>
                    </div>
                    <div class="flex gap-3 px-4 py-3">
                        <button
                            @click="requestRevision"
                            :disabled="!revisiForm.catatan_revisi"
                            class="flex-1 px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700 disabled:opacity-50"
                        >
                            Kirim Revisi
                        </button>
                        <button
                            @click="showRevisiModal = false"
                            class="flex-1 px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400"
                        >
                            Batal
                        </button>
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
    trackingStatus: String,
});

const showApproveModal = ref(false);
const showRejectModal = ref(false);
const showRevisiModal = ref(false);

const rejectForm = ref({
    alasan: '',
});

const revisiForm = ref({
    catatan_revisi: '',
});

const formatDate = (dateString) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
};

const getStatusClass = (status) => {
    const classes = {
        diajukan: 'bg-yellow-100 text-yellow-800',
        proses: 'bg-blue-100 text-blue-800',
        disetujui: 'bg-green-100 text-green-800',
        ditolak: 'bg-red-100 text-red-800',
        revisi: 'bg-orange-100 text-orange-800',
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
};

const getStatusLabel = (status) => {
    const labels = {
        diajukan: 'Diajukan',
        proses: 'Dalam Proses',
        disetujui: 'Disetujui',
        ditolak: 'Ditolak',
        revisi: 'Perlu Revisi',
    };
    return labels[status] || status;
};

const getTrackingClass = (tracking) => {
    const classes = {
        Permintaan: 'bg-gray-100 text-gray-700',
        'Nota Dinas': 'bg-blue-100 text-blue-700',
        Disposisi: 'bg-purple-100 text-purple-700',
        Perencanaan: 'bg-indigo-100 text-indigo-700',
        KSO: 'bg-pink-100 text-pink-700',
        Pengadaan: 'bg-orange-100 text-orange-700',
        'Nota Penerimaan': 'bg-teal-100 text-teal-700',
        'Serah Terima': 'bg-green-100 text-green-700',
    };
    return classes[tracking] || 'bg-gray-100 text-gray-700';
};

const approve = () => {
    router.post(route('kepala-instalasi.approve', props.permintaan.permintaan_id), {}, {
        onSuccess: () => {
            showApproveModal.value = false;
        }
    });
};

const reject = () => {
    router.post(route('kepala-instalasi.reject', props.permintaan.permintaan_id), rejectForm.value, {
        onSuccess: () => {
            showRejectModal.value = false;
        }
    });
};

const requestRevision = () => {
    router.post(route('kepala-instalasi.revisi', props.permintaan.permintaan_id), revisiForm.value, {
        onSuccess: () => {
            showRevisiModal.value = false;
        }
    });
};
</script>
