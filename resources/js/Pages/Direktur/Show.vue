<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Detail Permintaan #{{ permintaan.permintaan_id }}
                </h2>
                <Link :href="route('direktur.index')" class="text-sm text-gray-600 hover:text-gray-900">
                    ← Kembali ke Daftar
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                
                <!-- Progress Timeline -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">
                                Progress Tracking
                            </h3>
                            <span class="text-sm font-medium text-blue-600">
                                {{ progress }}% Complete
                            </span>
                        </div>
                        
                        <!-- Progress Bar -->
                        <div class="w-full bg-gray-200 rounded-full h-3 mb-4">
                            <div class="bg-blue-600 h-3 rounded-full transition-all" :style="{ width: progress + '%' }"></div>
                        </div>

                        <!-- Timeline -->
                        <div class="mt-6 space-y-3">
                            <div v-for="(step, index) in timeline" :key="index" class="flex items-start">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center">
                                        <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4 flex-1">
                                    <div class="flex items-center justify-between">
                                        <p class="font-medium text-gray-900">{{ step.tahapan }}</p>
                                        <span class="text-xs text-gray-500">{{ new Date(step.tanggal).toLocaleDateString('id-ID') }}</span>
                                    </div>
                                    <p class="text-sm text-gray-600 mt-1">{{ step.keterangan }}</p>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 mt-1">
                                        {{ step.status }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detail Permintaan -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">
                            Informasi Permintaan
                        </h3>
                    </div>
                    
                    <div class="p-6">
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Bidang</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ permintaan.bidang }}</dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Status</dt>
                                <dd class="mt-1">
                                    <span :class="{
                                        'bg-yellow-100 text-yellow-800': permintaan.status === 'proses',
                                        'bg-green-100 text-green-800': permintaan.status === 'disetujui',
                                        'bg-red-100 text-red-800': permintaan.status === 'ditolak',
                                        'bg-blue-100 text-blue-800': permintaan.status === 'diajukan'
                                    }" class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full">
                                        {{ permintaan.status.toUpperCase() }}
                                    </span>
                                </dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Tanggal Permintaan</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ new Date(permintaan.tanggal_permintaan).toLocaleDateString('id-ID') }}</dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Pemohon</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ permintaan.user?.nama || '-' }}</dd>
                            </div>
                            
                            <div class="sm:col-span-2">
                                <dt class="text-sm font-medium text-gray-500 mb-2">Deskripsi</dt>
                                <dd class="mt-1 text-sm text-gray-900 bg-gray-50 p-4 rounded-lg whitespace-pre-line">{{ permintaan.deskripsi }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Action Buttons - Hanya untuk status 'proses' -->
                <div v-if="permintaan.status === 'proses'" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            Aksi - Final Approval
                        </h3>
                        
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                            <p class="text-sm text-blue-800">
                                <svg class="w-5 h-5 inline-block mr-2 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                                Sebagai Direktur, ini adalah <strong>approval level terakhir</strong>. Keputusan Anda akan menentukan kelanjutan permintaan ini.
                            </p>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Approve Button -->
                            <button
                                @click="showApproveModal = true"
                                class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Setujui (Final)
                            </button>
                            
                            <!-- Reject Button -->
                            <button
                                @click="showRejectModal = true"
                                class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Tolak
                            </button>
                            
                            <!-- Revisi Button -->
                            <button
                                @click="showRevisiModal = true"
                                class="px-6 py-3 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors font-medium flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Minta Revisi
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Nota Dinas -->
                <div v-if="permintaan.nota_dinas && permintaan.nota_dinas.length > 0" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">
                            Riwayat Nota Dinas
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div v-for="nota in permintaan.nota_dinas" :key="nota.nota_id"
                                class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ nota.dari }} → {{ nota.kepada }}</p>
                                        <p class="text-sm text-gray-600 mt-1">{{ nota.perihal }}</p>
                                        <p class="text-xs text-gray-500 mt-1">{{ new Date(nota.tanggal_nota).toLocaleDateString('id-ID') }}</p>
                                    </div>
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                        Nota Dinas
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Approve Modal -->
        <Modal :show="showApproveModal" @close="showApproveModal = false">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    Setujui Permintaan (Final Approval)
                </h3>
                
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                    <p class="text-sm text-green-800">
                        <svg class="w-5 h-5 inline-block mr-2 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        Permintaan akan diteruskan ke <strong>Staff Perencanaan</strong> untuk diproses lebih lanjut.
                    </p>
                </div>
                
                <form @submit.prevent="submitApprove">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Catatan (Opsional)
                        </label>
                        <textarea v-model="approveForm.catatan" rows="3"
                            class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500"
                            placeholder="Tambahkan catatan jika diperlukan"></textarea>
                    </div>
                    
                    <div class="flex items-center justify-end gap-3">
                        <button type="button" @click="showApproveModal = false"
                            class="px-4 py-2 text-gray-700 hover:text-gray-900">
                            Batal
                        </button>
                        <button type="submit" :disabled="processing"
                            class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:opacity-50">
                            {{ processing ? 'Memproses...' : 'Setujui & Teruskan' }}
                        </button>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Reject Modal -->
        <Modal :show="showRejectModal" @close="showRejectModal = false">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    Tolak Permintaan
                </h3>
                
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                    <p class="text-sm text-red-800">
                        <svg class="w-5 h-5 inline-block mr-2 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                        Penolakan Anda akan <strong>menghentikan</strong> proses permintaan ini.
                    </p>
                </div>
                
                <form @submit.prevent="submitReject">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Alasan Penolakan <span class="text-red-500">*</span>
                        </label>
                        <textarea v-model="rejectForm.alasan" rows="4" required
                            class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500"
                            placeholder="Jelaskan alasan penolakan dengan jelas"></textarea>
                        <p class="text-xs text-gray-500 mt-1">Minimal 10 karakter</p>
                    </div>
                    
                    <div class="flex items-center justify-end gap-3">
                        <button type="button" @click="showRejectModal = false"
                            class="px-4 py-2 text-gray-700 hover:text-gray-900">
                            Batal
                        </button>
                        <button type="submit" :disabled="processing || rejectForm.alasan.length < 10"
                            class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 disabled:opacity-50">
                            {{ processing ? 'Memproses...' : 'Tolak Permintaan' }}
                        </button>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Revisi Modal -->
        <Modal :show="showRevisiModal" @close="showRevisiModal = false">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    Minta Revisi
                </h3>
                
                <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 mb-4">
                    <p class="text-sm text-orange-800">
                        <svg class="w-5 h-5 inline-block mr-2 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        Permintaan akan dikembalikan ke <strong>Kepala Bidang</strong> untuk diperbaiki.
                    </p>
                </div>
                
                <form @submit.prevent="submitRevisi">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Catatan Revisi <span class="text-red-500">*</span>
                        </label>
                        <textarea v-model="revisiForm.catatan_revisi" rows="4" required
                            class="w-full rounded-lg border-gray-300 focus:border-orange-500 focus:ring-orange-500"
                            placeholder="Jelaskan secara detail apa yang perlu direvisi"></textarea>
                        <p class="text-xs text-gray-500 mt-1">Minimal 10 karakter</p>
                    </div>
                    
                    <div class="flex items-center justify-end gap-3">
                        <button type="button" @click="showRevisiModal = false"
                            class="px-4 py-2 text-gray-700 hover:text-gray-900">
                            Batal
                        </button>
                        <button type="submit" :disabled="processing || revisiForm.catatan_revisi.length < 10"
                            class="px-6 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 disabled:opacity-50">
                            {{ processing ? 'Memproses...' : 'Kirim Permintaan Revisi' }}
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
import { Link, router } from '@inertiajs/vue3';

const props = defineProps({
    permintaan: Object,
    trackingStatus: String,
    timeline: Array,
    progress: Number,
});

const showApproveModal = ref(false);
const showRejectModal = ref(false);
const showRevisiModal = ref(false);
const processing = ref(false);

const approveForm = ref({
    catatan: '',
});

const rejectForm = ref({
    alasan: '',
});

const revisiForm = ref({
    catatan_revisi: '',
});

const submitApprove = () => {
    processing.value = true;
    router.post(route('direktur.approve', props.permintaan.permintaan_id), approveForm.value, {
        onSuccess: () => {
            showApproveModal.value = false;
            processing.value = false;
        },
        onError: () => {
            processing.value = false;
        },
    });
};

const submitReject = () => {
    if (rejectForm.value.alasan.length < 10) {
        return;
    }
    processing.value = true;
    router.post(route('direktur.reject', props.permintaan.permintaan_id), rejectForm.value, {
        onSuccess: () => {
            showRejectModal.value = false;
            processing.value = false;
        },
        onError: () => {
            processing.value = false;
        },
    });
};

const submitRevisi = () => {
    if (revisiForm.value.catatan_revisi.length < 10) {
        return;
    }
    processing.value = true;
    router.post(route('direktur.revisi', props.permintaan.permintaan_id), revisiForm.value, {
        onSuccess: () => {
            showRevisiModal.value = false;
            processing.value = false;
        },
        onError: () => {
            processing.value = false;
        },
    });
};
</script>
