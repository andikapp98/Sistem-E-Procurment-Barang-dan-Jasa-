<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    permintaan: Object,
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
                                    <label class="block text-sm font-medium text-gray-700">Pihak Kedua</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ kso.pihak_kedua }}</p>
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700">Isi Kerjasama</label>
                                    <p class="mt-1 text-sm text-gray-900 whitespace-pre-line">{{ kso.isi_kerjasama }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Nilai Kontrak</label>
                                    <p class="mt-1 text-sm text-gray-900">
                                        {{ kso.nilai_kontrak ? new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(kso.nilai_kontrak) : '-' }}
                                    </p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Status KSO</label>
                                    <span :class="getStatusColor(kso.status)" class="inline-block px-3 py-1 text-xs font-semibold rounded-full mt-1">
                                        {{ kso.status }}
                                    </span>
                                </div>
                            </div>

                            <!-- Timeline -->
                            <div class="mt-8">
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
                                <Link :href="route('kso.create', permintaan.permintaan_id)" 
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
