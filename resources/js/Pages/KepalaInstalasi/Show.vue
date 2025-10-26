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

                <!-- Error notification -->
                <div v-if="$page.props.flash?.error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ $page.props.flash.error }}</span>
                </div>

                <!-- Validation errors -->
                <div v-if="Object.keys($page.props.errors || {}).length > 0" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Terdapat kesalahan!</strong>
                    <ul class="list-disc list-inside mt-2">
                        <li v-for="(error, key) in $page.props.errors" :key="key">{{ error }}</li>
                    </ul>
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
                <div v-if="permintaan.nota_dinas && permintaan.nota_dinas.length > 0" class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Nota Dinas</h3>
                        <p class="text-sm text-gray-500 mt-1">Daftar nota dinas yang terkait dengan permintaan ini</p>
                    </div>
                    <div class="p-6 space-y-4">
                        <div v-for="(nota, index) in permintaan.nota_dinas" :key="nota.nota_id" 
                             class="border border-gray-200 rounded-lg p-4"
                             :class="{ 'border-l-4 border-l-blue-500': index === 0 }">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-sm font-medium text-gray-500">Nota #{{ index + 1 }}</span>
                                <span class="text-xs text-gray-500">{{ nota.no_nota || '-' }}</span>
                            </div>
                            <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 mb-1">Dari</dt>
                                    <dd class="text-base text-gray-900">{{ nota.dari || '-' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 mb-1">Kepada</dt>
                                    <dd class="text-base text-gray-900">{{ nota.kepada || '-' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 mb-1">Tanggal Nota</dt>
                                    <dd class="text-base text-gray-900">{{ formatDate(nota.tanggal_nota) }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 mb-1">Sifat</dt>
                                    <dd class="text-base text-gray-900">{{ nota.sifat || '-' }}</dd>
                                </div>
                                <div class="md:col-span-2">
                                    <dt class="text-sm font-medium text-gray-500 mb-1">Perihal</dt>
                                    <dd class="text-base text-gray-900">{{ nota.perihal || '-' }}</dd>
                                </div>
                                <div v-if="nota.lampiran" class="md:col-span-2">
                                    <dt class="text-sm font-medium text-gray-500 mb-2">Lampiran</dt>
                                    <dd>
                                        <a 
                                            :href="route('kepala-instalasi.lampiran', nota.nota_id)" 
                                            target="_blank" 
                                            class="inline-flex items-center px-4 py-2 bg-indigo-50 border border-indigo-200 rounded-lg text-indigo-700 hover:bg-indigo-100 hover:border-indigo-300 transition-colors duration-150"
                                        >
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                            </svg>
                                            <span class="font-medium">Lihat Lampiran</span>
                                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                            </svg>
                                        </a>
                                    </dd>
                                </div>
                                <div v-if="nota.detail" class="md:col-span-2">
                                    <dt class="text-sm font-medium text-gray-500 mb-1">Detail</dt>
                                    <dd class="text-sm text-gray-900 whitespace-pre-line">{{ nota.detail }}</dd>
                                </div>
                            </dl>
                        </div>
                        <div v-if="!permintaan.nota_dinas || permintaan.nota_dinas.length === 0" class="text-center py-8 text-gray-500">
                            Belum ada nota dinas untuk permintaan ini
                        </div>
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
                        <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Revisi <span class="text-red-500">*</span></label>
                        <textarea
                            v-model="revisiForm.catatan_revisi"
                            rows="4"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500"
                            placeholder="Masukkan catatan revisi (minimal 5 karakter)..."
                        ></textarea>
                        <p v-if="revisiForm.catatan_revisi && revisiForm.catatan_revisi.length < 5" class="text-xs text-red-500 mt-1">
                            Minimal 5 karakter ({{ revisiForm.catatan_revisi.length }}/5)
                        </p>
                        <p v-if="$page.props.errors?.catatan_revisi" class="text-xs text-red-500 mt-1">
                            {{ $page.props.errors.catatan_revisi }}
                        </p>
                    </div>
                    <div class="flex gap-3 px-4 py-3">
                        <button
                            @click="requestRevision"
                            :disabled="!revisiForm.catatan_revisi || revisiForm.catatan_revisi.length < 5"
                            class="flex-1 px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700 disabled:opacity-50 disabled:cursor-not-allowed"
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
    // Validasi minimal 5 karakter sebelum submit
    if (!revisiForm.value.catatan_revisi || revisiForm.value.catatan_revisi.trim().length < 5) {
        alert('Catatan revisi minimal 5 karakter');
        return;
    }
    
    router.post(route('kepala-instalasi.revisi', props.permintaan.permintaan_id), revisiForm.value, {
        onSuccess: () => {
            showRevisiModal.value = false;
            revisiForm.value.catatan_revisi = ''; // Reset form
        },
        onError: (errors) => {
            console.error('Error:', errors);
            // Modal tetap terbuka jika ada error
        }
    });
};
</script>
