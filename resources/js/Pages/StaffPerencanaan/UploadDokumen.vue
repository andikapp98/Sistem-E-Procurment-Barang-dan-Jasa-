<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    permintaan: Object,
    jenisDokumen: Object,
    dokumenDiupload: Object,
    progress: Object,
});

// Form untuk setiap jenis dokumen
const uploadForms = ref({});
const uploadingFiles = ref({});

// Initialize forms
Object.keys(props.jenisDokumen).forEach(jenis => {
    uploadForms.value[jenis] = useForm({
        jenis_dokumen: jenis,
        file: null,
    });
});

// Progress percentage
const progressPercentage = computed(() => {
    return Math.round((props.progress.uploaded / props.progress.total) * 100);
});

// Check if document is uploaded
const isUploaded = (jenis) => {
    return props.dokumenDiupload.hasOwnProperty(jenis);
};

// Handle file selection
const handleFileSelect = (jenis, event) => {
    const file = event.target.files[0];
    if (file) {
        uploadForms.value[jenis].file = file;
    }
};

// Upload document
const uploadDocument = (jenis) => {
    if (!uploadForms.value[jenis].file) {
        alert('Pilih file terlebih dahulu');
        return;
    }

    uploadingFiles.value[jenis] = true;

    uploadForms.value[jenis].post(route('staff-perencanaan.dokumen.store', props.permintaan.permintaan_id), {
        preserveScroll: true,
        onSuccess: () => {
            uploadForms.value[jenis].reset('file');
            uploadingFiles.value[jenis] = false;
            // Reset file input
            const fileInput = document.getElementById(`file-${jenis}`);
            if (fileInput) fileInput.value = '';
        },
        onError: () => {
            uploadingFiles.value[jenis] = false;
        },
    });
};

// Download document
const downloadDocument = (permintaanId, dokumenId) => {
    window.location.href = route('staff-perencanaan.dokumen.download', [permintaanId, dokumenId]);
};

// Delete document
const deleteDocument = (jenis, dokumenId) => {
    if (!confirm('Yakin ingin menghapus dokumen ini?')) return;

    router.delete(route('staff-perencanaan.dokumen.delete', [props.permintaan.permintaan_id, dokumenId]), {
        preserveScroll: true,
    });
};

// Format file size
const formatFileSize = (bytes) => {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
};

// Format date
const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};
</script>

<template>
    <Head title="Upload Dokumen Pengadaan" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        Upload Dokumen Pengadaan
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">
                        Permintaan #{{ permintaan.permintaan_id }} - {{ permintaan.deskripsi }}
                    </p>
                </div>
                <button
                    @click="router.visit(route('staff-perencanaan.show', permintaan.permintaan_id))"
                    class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition"
                >
                    Kembali
                </button>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Progress Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <div class="mb-4">
                            <div class="flex justify-between items-center mb-2">
                                <h3 class="text-lg font-semibold text-gray-800">Progress Upload Dokumen</h3>
                                <span class="text-sm font-medium text-gray-600">
                                    {{ progress.uploaded }} / {{ progress.total }} Dokumen
                                </span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-4 overflow-hidden">
                                <div 
                                    class="h-4 rounded-full transition-all duration-300"
                                    :class="progressPercentage === 100 ? 'bg-green-500' : 'bg-blue-500'"
                                    :style="{ width: progressPercentage + '%' }"
                                >
                                    <span class="flex items-center justify-center h-full text-xs font-medium text-white">
                                        {{ progressPercentage }}%
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div v-if="progressPercentage === 100" class="mt-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div>
                                    <p class="font-semibold text-green-800">Semua Dokumen Lengkap!</p>
                                    <p class="text-sm text-green-700">Permintaan telah diteruskan ke Bagian Pengadaan.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Document Upload Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div 
                        v-for="(label, jenis) in jenisDokumen" 
                        :key="jenis"
                        class="bg-white overflow-hidden shadow-sm sm:rounded-lg"
                    >
                        <div class="p-6">
                            <!-- Document Header -->
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex-1">
                                    <h4 class="text-lg font-semibold text-gray-800 mb-1">{{ label }}</h4>
                                    <div v-if="isUploaded(jenis)" class="flex items-center text-sm text-green-600">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span class="font-medium">Sudah diupload</span>
                                    </div>
                                    <div v-else class="flex items-center text-sm text-gray-500">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span>Belum diupload</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Uploaded Document Info -->
                            <div v-if="isUploaded(jenis)" class="mb-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-800 mb-1">
                                            {{ dokumenDiupload[jenis].nama_file }}
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            Diupload: {{ formatDate(dokumenDiupload[jenis].tanggal_upload) }}
                                        </p>
                                    </div>
                                    <div class="flex gap-2 ml-4">
                                        <button
                                            @click="downloadDocument(permintaan.permintaan_id, dokumenDiupload[jenis].dokumen_id)"
                                            class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition"
                                            title="Download"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        </button>
                                        <button
                                            @click="deleteDocument(jenis, dokumenDiupload[jenis].dokumen_id)"
                                            class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition"
                                            title="Hapus"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Upload Form -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ isUploaded(jenis) ? 'Ganti Dokumen' : 'Pilih File' }}
                                </label>
                                <div class="flex gap-2">
                                    <input
                                        :id="`file-${jenis}`"
                                        type="file"
                                        @change="handleFileSelect(jenis, $event)"
                                        accept=".pdf,.jpg,.jpeg,.png"
                                        class="flex-1 text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                    />
                                    <button
                                        @click="uploadDocument(jenis)"
                                        :disabled="!uploadForms[jenis].file || uploadingFiles[jenis]"
                                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed transition flex items-center gap-2"
                                    >
                                        <svg v-if="uploadingFiles[jenis]" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <span>{{ uploadingFiles[jenis] ? 'Uploading...' : 'Upload' }}</span>
                                    </button>
                                </div>
                                <p class="mt-2 text-xs text-gray-500">
                                    Format: PDF, JPG, PNG (Maksimal 10MB)
                                </p>
                                <div v-if="uploadForms[jenis].errors.file" class="mt-2 text-sm text-red-600">
                                    {{ uploadForms[jenis].errors.file }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Info Box -->
                <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-6">
                    <div class="flex items-start">
                        <svg class="w-6 h-6 text-blue-500 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <h4 class="font-semibold text-blue-800 mb-2">Informasi Upload Dokumen</h4>
                            <ul class="text-sm text-blue-700 space-y-1 list-disc list-inside">
                                <li>Upload semua 6 dokumen yang diperlukan</li>
                                <li>Anda dapat mengganti dokumen yang sudah diupload</li>
                                <li>Setelah semua dokumen lengkap, permintaan akan otomatis diteruskan ke Bagian Pengadaan</li>
                                <li>Pastikan dokumen yang diupload sudah benar dan lengkap</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
