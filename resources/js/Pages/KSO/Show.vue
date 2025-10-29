<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    permintaan: Object,
    perencanaan: Object,
    kso: Object,
    userLogin: Object,
});

const formatDate = (date) => {
    if (!date) return '-';
    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    });
};

const getStatusColor = (status) => {
    const colors = {
        'draft': 'bg-gray-100 text-gray-800',
        'diajukan': 'bg-yellow-100 text-yellow-800',
        'proses': 'bg-blue-100 text-blue-800',
        'disetujui': 'bg-green-100 text-green-800',
        'ditolak': 'bg-red-100 text-red-800',
        'revisi': 'bg-orange-100 text-orange-800',
        'aktif': 'bg-blue-100 text-blue-800',
        'selesai': 'bg-green-100 text-green-800',
        'batal': 'bg-red-100 text-red-800',
    };
    return colors[status] || 'bg-gray-100 text-gray-800';
};

const deleteKso = () => {
    if (confirm('Apakah Anda yakin ingin menghapus data KSO ini?')) {
        router.delete(route('kso.destroy', {
            permintaan: props.permintaan.permintaan_id,
            kso: props.kso.kso_id
        }), {
            onSuccess: () => {
                alert('Data KSO berhasil dihapus');
            }
        });
    }
};

const downloadFile = (filePath, fileName) => {
    const link = document.createElement('a');
    link.href = `/storage/${filePath}`;
    link.download = fileName;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
};
</script>

<template>
    <Head title="Detail Permintaan KSO" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Detail Permintaan #{{ permintaan.permintaan_id }}
                </h2>
                <Link :href="route('kso.index')" class="text-sm text-gray-600 hover:text-gray-900">
                    ← Kembali
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                
                <!-- Info Permintaan -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Informasi Permintaan</h3>
                            <span :class="getStatusColor(permintaan.status)" class="px-3 py-1 text-sm font-semibold rounded-full">
                                {{ permintaan.status }}
                            </span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">ID Permintaan</label>
                                <p class="mt-1 text-sm text-gray-900">#{{ permintaan.permintaan_id }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tanggal Permintaan</label>
                                <p class="mt-1 text-sm text-gray-900">{{ formatDate(permintaan.tanggal_permintaan) }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Bidang</label>
                                <p class="mt-1 text-sm text-gray-900">{{ permintaan.bidang || '-' }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Unit Kerja</label>
                                <p class="mt-1 text-sm text-gray-900">{{ permintaan.unit_kerja || '-' }}</p>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
                                <p class="mt-1 text-sm text-gray-900">{{ permintaan.deskripsi || '-' }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Jumlah</label>
                                <p class="mt-1 text-sm text-gray-900">{{ permintaan.jumlah || '-' }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Satuan</label>
                                <p class="mt-1 text-sm text-gray-900">{{ permintaan.satuan || '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Data KSO -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Data KSO</h3>
                            <div v-if="kso" class="flex space-x-2">
                                <Link :href="route('kso.edit', { permintaan: permintaan.permintaan_id, kso: kso.kso_id })" 
                                      class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">
                                    Edit
                                </Link>
                                <button @click="deleteKso" 
                                        class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm">
                                    Hapus
                                </button>
                            </div>
                        </div>

                        <div v-if="kso">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">No. KSO</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ kso.no_kso }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Tanggal KSO</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ formatDate(kso.tanggal_kso) }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Pihak Pertama</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ kso.pihak_pertama }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Pihak Kedua (Vendor/Partner)</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ kso.pihak_kedua }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Status KSO</label>
                                    <span :class="getStatusColor(kso.status)" class="inline-block px-3 py-1 text-xs font-semibold rounded-full mt-1">
                                        {{ kso.status }}
                                    </span>
                                </div>

                                <div v-if="kso.nilai_kontrak">
                                    <label class="block text-sm font-medium text-gray-700">Nilai Kontrak</label>
                                    <p class="mt-1 text-sm text-gray-900">
                                        {{ new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(kso.nilai_kontrak) }}
                                    </p>
                                </div>

                                <div v-if="kso.isi_kerjasama" class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700">Isi Kerjasama</label>
                                    <p class="mt-1 text-sm text-gray-900 whitespace-pre-line">{{ kso.isi_kerjasama }}</p>
                                </div>
                            </div>

                            <!-- Dokumen KSO (PKS & MoU) -->
                            <div class="mt-8 border-t pt-6">
                                <h4 class="text-sm font-semibold text-gray-900 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                    Dokumen KSO
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <!-- File PKS -->
                                    <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-500 transition-colors">
                                        <div class="flex items-start justify-between">
                                            <div class="flex items-start space-x-3 flex-1">
                                                <div class="flex-shrink-0">
                                                    <svg class="w-10 h-10 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd" />
                                                    </svg>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-medium text-gray-900 mb-1">
                                                        PKS (Perjanjian Kerja Sama)
                                                    </p>
                                                    <p v-if="kso.file_pks" class="text-xs text-gray-500 truncate">
                                                        {{ kso.file_pks.split('/').pop() }}
                                                    </p>
                                                    <p v-else class="text-xs text-gray-400 italic">
                                                        Belum diupload
                                                    </p>
                                                </div>
                                            </div>
                                            <a v-if="kso.file_pks" 
                                               :href="`/storage/${kso.file_pks}`" 
                                               target="_blank"
                                               download
                                               class="ml-3 inline-flex items-center px-3 py-1.5 border border-blue-600 text-xs font-medium rounded text-blue-600 bg-white hover:bg-blue-50 focus:outline-none">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                Download
                                            </a>
                                            <span v-else class="ml-3 inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded text-gray-400 bg-gray-50 cursor-not-allowed">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                                N/A
                                            </span>
                                        </div>
                                    </div>

                                    <!-- File MoU -->
                                    <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-500 transition-colors">
                                        <div class="flex items-start justify-between">
                                            <div class="flex items-start space-x-3 flex-1">
                                                <div class="flex-shrink-0">
                                                    <svg class="w-10 h-10 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd" />
                                                    </svg>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-medium text-gray-900 mb-1">
                                                        MoU (Memorandum of Understanding)
                                                    </p>
                                                    <p v-if="kso.file_mou" class="text-xs text-gray-500 truncate">
                                                        {{ kso.file_mou.split('/').pop() }}
                                                    </p>
                                                    <p v-else class="text-xs text-gray-400 italic">
                                                        Belum diupload
                                                    </p>
                                                </div>
                                            </div>
                                            <a v-if="kso.file_mou" 
                                               :href="`/storage/${kso.file_mou}`" 
                                               target="_blank"
                                               download
                                               class="ml-3 inline-flex items-center px-3 py-1.5 border border-blue-600 text-xs font-medium rounded text-blue-600 bg-white hover:bg-blue-50 focus:outline-none">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                Download
                                            </a>
                                            <span v-else class="ml-3 inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded text-gray-400 bg-gray-50 cursor-not-allowed">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                                N/A
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Keterangan -->
                            <div v-if="kso.keterangan" class="mt-6 border-t pt-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <p class="text-sm text-gray-900 whitespace-pre-line">{{ kso.keterangan }}</p>
                                </div>
                            </div>

                            <!-- Timeline -->
                            <div class="mt-8 border-t pt-6">
                                <h4 class="text-sm font-semibold text-gray-900 mb-4">Timeline</h4>
                                <div class="border-l-2 border-gray-200 pl-4 space-y-4">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Dibuat</p>
                                        <p class="text-xs text-gray-500">{{ formatDate(kso.created_at) }}</p>
                                    </div>
                                    <div v-if="kso.updated_at !== kso.created_at">
                                        <p class="text-sm font-medium text-gray-900">Terakhir Diupdate</p>
                                        <p class="text-xs text-gray-500">{{ formatDate(kso.updated_at) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Jika belum ada KSO -->
                        <div v-else class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Belum Ada Data KSO</h3>
                            <p class="mt-1 text-sm text-gray-500">Silakan buat dokumen KSO untuk permintaan ini.</p>
                            <div class="mt-6">
                                <Link :href="route('kso.create', { permintaan: permintaan.permintaan_id })" 
                                      class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    Buat Dokumen KSO
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
