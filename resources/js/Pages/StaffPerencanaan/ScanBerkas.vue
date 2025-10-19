<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { CheckCircleIcon, DocumentArrowUpIcon, TrashIcon, ArrowDownTrayIcon, XMarkIcon } from '@heroicons/vue/24/outline';
import { CheckCircleIcon as CheckCircleSolid } from '@heroicons/vue/24/solid';

const props = defineProps({
    permintaan: Object,
    jenisDokumen: Object,
    dokumenDiupload: Object,
    progress: Object,
});

const selectedDokumen = ref(null);
const uploadForm = ref({
    jenis_dokumen: '',
    file: null,
});
const isUploading = ref(false);
const showModal = ref(false);

// Progress percentage
const progressPercentage = computed(() => {
    return Math.round((props.progress.uploaded / props.progress.total) * 100);
});

// Check if dokumen jenis ini sudah diupload
const isDokumenUploaded = (jenisKey) => {
    return props.dokumenDiupload.hasOwnProperty(jenisKey);
};

// Handle file selection
const handleFileSelect = (event) => {
    const file = event.target.files[0];
    if (file) {
        uploadForm.value.file = file;
    }
};

// Open upload modal
const openUploadModal = (jenisKey, jenisLabel) => {
    selectedDokumen.value = { key: jenisKey, label: jenisLabel };
    uploadForm.value.jenis_dokumen = jenisKey;
    uploadForm.value.file = null;
    showModal.value = true;
};

// Close modal
const closeModal = () => {
    showModal.value = false;
    selectedDokumen.value = null;
    uploadForm.value.jenis_dokumen = '';
    uploadForm.value.file = null;
};

// Submit upload
const submitUpload = () => {
    if (!uploadForm.value.file) {
        alert('Pilih file terlebih dahulu');
        return;
    }

    isUploading.value = true;

    const formData = new FormData();
    formData.append('jenis_dokumen', uploadForm.value.jenis_dokumen);
    formData.append('file', uploadForm.value.file);

    router.post(route('staff-perencanaan.dokumen.store', props.permintaan.permintaan_id), formData, {
        forceFormData: true,
        onSuccess: () => {
            closeModal();
            isUploading.value = false;
        },
        onError: () => {
            isUploading.value = false;
        },
    });
};

// Delete dokumen
const deleteDokumen = (jenisKey, dokumenId) => {
    if (confirm('Yakin ingin menghapus dokumen ini?')) {
        router.delete(route('staff-perencanaan.dokumen.delete', {
            permintaan: props.permintaan.permintaan_id,
            dokumen: dokumenId
        }));
    }
};

// Format date
const formatDate = (date) => {
    if (!date) return '-';
    return new Date(date).toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'long',
        year: 'numeric'
    });
};
</script>

<template>
    <Head title="Scan Berkas Pengadaan" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900">Scan Berkas Pengadaan</h2>
                                <p class="text-sm text-gray-600 mt-1">Permintaan #{{ permintaan.permintaan_id }} - {{ permintaan.bidang }}</p>
                            </div>
                            <Link :href="route('staff-perencanaan.show', permintaan.permintaan_id)" 
                                class="text-blue-600 hover:text-blue-700 font-medium">
                                ← Kembali
                            </Link>
                        </div>

                        <!-- Progress Bar -->
                        <div class="mb-4">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-gray-700">Progress Upload</span>
                                <span class="text-sm font-medium text-gray-700">{{ progress.uploaded }} / {{ progress.total }} dokumen</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3">
                                <div class="bg-blue-600 h-3 rounded-full transition-all duration-300" :style="`width: ${progressPercentage}%`"></div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">{{ progressPercentage }}% Selesai</p>
                        </div>

                        <!-- Info -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <p class="text-sm text-blue-800">
                                <strong>Catatan:</strong> Upload semua 6 dokumen untuk melanjutkan ke tahap pengadaan. 
                                Format file: PDF, JPG, atau PNG (Max 10MB per file).
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Daftar Dokumen -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div v-for="(label, key) in jenisDokumen" :key="key" 
                        class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition">
                        <div class="p-6">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-2">
                                        <CheckCircleSolid v-if="isDokumenUploaded(key)" class="w-6 h-6 text-green-500" />
                                        <DocumentArrowUpIcon v-else class="w-6 h-6 text-gray-400" />
                                        <h3 class="text-lg font-semibold text-gray-900">{{ label }}</h3>
                                    </div>

                                    <!-- Jika sudah diupload -->
                                    <div v-if="isDokumenUploaded(key)" class="mt-3">
                                        <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                                            <p class="text-sm text-green-800 font-medium mb-1">
                                                {{ dokumenDiupload[key].nama_file }}
                                            </p>
                                            <p class="text-xs text-green-600">
                                                Diupload: {{ formatDate(dokumenDiupload[key].tanggal_upload) }}
                                            </p>
                                        </div>
                                        
                                        <div class="flex gap-2 mt-3">
                                            <a :href="route('staff-perencanaan.dokumen.download', {
                                                    permintaan: permintaan.permintaan_id,
                                                    dokumen: dokumenDiupload[key].dokumen_id
                                                })"
                                                class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                                                <ArrowDownTrayIcon class="w-4 h-4" />
                                                Download
                                            </a>
                                            <button @click="openUploadModal(key, label)"
                                                class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 bg-yellow-600 text-white text-sm font-medium rounded-lg hover:bg-yellow-700 transition">
                                                <DocumentArrowUpIcon class="w-4 h-4" />
                                                Ganti
                                            </button>
                                            <button @click="deleteDokumen(key, dokumenDiupload[key].dokumen_id)"
                                                class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition">
                                                <TrashIcon class="w-4 h-4" />
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Jika belum diupload -->
                                    <div v-else class="mt-3">
                                        <button @click="openUploadModal(key, label)"
                                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition">
                                            <DocumentArrowUpIcon class="w-5 h-5" />
                                            Upload {{ label }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Success Message -->
                <div v-if="progress.uploaded === progress.total" class="mt-6 bg-green-50 border border-green-200 rounded-lg p-6">
                    <div class="flex items-center gap-3">
                        <CheckCircleSolid class="w-8 h-8 text-green-500" />
                        <div>
                            <h3 class="text-lg font-semibold text-green-900">Semua Dokumen Lengkap!</h3>
                            <p class="text-sm text-green-700 mt-1">
                                Permintaan ini telah otomatis diteruskan ke Bagian Pengadaan untuk diproses lebih lanjut.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upload Modal -->
        <div v-if="showModal" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black opacity-50" @click="closeModal"></div>
                
                <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">
                            Upload {{ selectedDokumen?.label }}
                        </h3>
                        <button @click="closeModal" class="text-gray-400 hover:text-gray-600">
                            <XMarkIcon class="w-6 h-6" />
                        </button>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Pilih File
                            </label>
                            <input type="file" 
                                @change="handleFileSelect"
                                accept=".pdf,.jpg,.jpeg,.png"
                                class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none">
                            <p class="text-xs text-gray-500 mt-1">PDF, JPG, atau PNG (Max 10MB)</p>
                        </div>

                        <div v-if="uploadForm.file" class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                            <p class="text-sm text-blue-800">
                                <strong>File dipilih:</strong> {{ uploadForm.file.name }}
                            </p>
                            <p class="text-xs text-blue-600 mt-1">
                                Ukuran: {{ (uploadForm.file.size / 1024 / 1024).toFixed(2) }} MB
                            </p>
                        </div>

                        <div class="flex gap-3 pt-4">
                            <button @click="closeModal" 
                                :disabled="isUploading"
                                class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition disabled:opacity-50">
                                Batal
                            </button>
                            <button @click="submitUpload" 
                                :disabled="!uploadForm.file || isUploading"
                                class="flex-1 px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition disabled:opacity-50 disabled:cursor-not-allowed">
                                {{ isUploading ? 'Mengupload...' : 'Upload' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
