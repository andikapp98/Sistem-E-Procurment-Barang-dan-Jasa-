<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Review Permintaan - Kepala Instalasi
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Success notification -->
                <div v-if="$page.props.flash?.success" class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ $page.props.flash.success }}</span>
                </div>

                <!-- Filter Bar -->
                <FilterBar 
                    route-name="kepala-instalasi.index"
                    :initial-filters="filters"
                    :show-bidang-filter="false"
                />

                <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
                    <!-- Header card -->
                    <div class="flex items-center justify-between px-6 pt-6 pb-4 border-b border-gray-200">
                        <div>
                            <h3 class="font-semibold text-gray-800 text-lg">
                                Daftar Permintaan untuk Review
                            </h3>
                            <p class="text-sm text-gray-600 mt-1">
                                Unit Kerja: {{ userLogin.unit_kerja ?? 'Tidak ada unit' }}
                            </p>
                        </div>
                        
                        <Link
                            :href="route('kepala-instalasi.dashboard')"
                            class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 text-sm font-medium"
                        >
                            ← Dashboard
                        </Link>
                    </div>

                    <!-- Body -->
                    <div class="p-6 text-gray-900">
                        <div v-if="permintaans.length" class="overflow-x-auto">
                            <table class="w-full table-auto text-sm border-collapse">
                                <thead class="bg-gray-50">
                                    <tr class="text-xs uppercase tracking-wide text-gray-600 border-b border-gray-200">
                                        <th class="py-3 px-3 text-left font-medium w-16">ID</th>
                                        <th class="py-3 px-3 text-left font-medium">Deskripsi</th>
                                        <th class="py-3 px-3 text-left font-medium">Pemohon</th>
                                        <th class="py-3 px-3 text-left font-medium">Bidang</th>
                                        <th class="py-3 px-3 text-left font-medium">Tanggal</th>
                                        <th class="py-3 px-3 text-left font-medium">Status</th>
                                        <th class="py-3 px-3 text-left font-medium">Tracking</th>
                                        <th class="py-3 px-3 text-center font-medium w-32">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <tr
                                        v-for="permintaan in permintaans"
                                        :key="permintaan.permintaan_id"
                                        class="hover:bg-gray-50 transition-colors"
                                    >
                                        <td class="py-3 px-3 text-gray-900 font-medium">
                                            #{{ permintaan.permintaan_id }}
                                        </td>
                                        <td class="py-3 px-3 text-gray-900">
                                            <div class="max-w-xs truncate">
                                                {{ permintaan.deskripsi }}
                                            </div>
                                        </td>
                                        <td class="py-3 px-3 text-gray-600">
                                            {{ permintaan.user?.nama || '-' }}
                                        </td>
                                        <td class="py-3 px-3 text-gray-600">
                                            {{ permintaan.bidang || '-' }}
                                        </td>
                                        <td class="py-3 px-3 text-gray-600">
                                            {{ formatDate(permintaan.tanggal_permintaan) }}
                                        </td>
                                        <td class="py-3 px-3">
                                            <span
                                                :class="getStatusClass(permintaan.status)"
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                            >
                                                {{ getStatusLabel(permintaan.status) }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-3">
                                            <span
                                                :class="getTrackingClass(permintaan.tracking_status)"
                                                class="px-2 py-1 text-xs font-medium rounded"
                                            >
                                                {{ permintaan.tracking_status }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-3 text-center">
                                            <Link
                                                :href="route('kepala-instalasi.show', permintaan.permintaan_id)"
                                                class="inline-flex items-center px-3 py-1.5 bg-[#028174] text-white rounded-md hover:bg-[#03a089] text-xs font-medium"
                                            >
                                                Review
                                            </Link>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Empty State -->
                        <div v-else class="text-center py-16 px-4">
                            <div class="max-w-md mx-auto">
                                <!-- Icon -->
                                <div class="mb-6">
                                    <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center">
                                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                </div>

                                <!-- Title -->
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                    Tidak Ada Permintaan
                                </h3>

                                <!-- Description -->
                                <p class="text-gray-600 mb-6">
                                    Tidak ada permintaan yang perlu direview saat ini untuk unit kerja <span class="font-semibold">{{ userLogin.unit_kerja }}</span>.
                                </p>

                                <!-- Info Cards -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6 text-left">
                                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                        <div class="flex items-start">
                                            <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <div>
                                                <p class="text-sm font-medium text-blue-900">Filter Aktif</p>
                                                <p class="text-xs text-blue-700 mt-1">Coba hapus filter untuk melihat semua data</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                        <div class="flex items-start">
                                            <svg class="w-5 h-5 text-green-600 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <div>
                                                <p class="text-sm font-medium text-green-900">Semua Selesai</p>
                                                <p class="text-xs text-green-700 mt-1">Semua permintaan sudah direview</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                                    <Link
                                        :href="route('kepala-instalasi.dashboard')"
                                        class="inline-flex items-center justify-center px-5 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm font-medium transition-colors"
                                    >
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                        </svg>
                                        Kembali ke Dashboard
                                    </Link>

                                    <Link
                                        :href="route('kepala-instalasi.index')"
                                        class="inline-flex items-center justify-center px-5 py-2.5 bg-[#028174] text-white rounded-lg hover:bg-[#03a089] text-sm font-medium transition-colors"
                                    >
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                        </svg>
                                        Refresh Halaman
                                    </Link>
                                </div>

                                <!-- Additional Help Text -->
                                <div class="mt-8 pt-6 border-t border-gray-200">
                                    <p class="text-xs text-gray-500">
                                        💡 <span class="font-medium">Tips:</span> Permintaan baru akan muncul di sini ketika unit Anda mengajukan permintaan pengadaan.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import FilterBar from '@/Components/FilterBar.vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    permintaans: Array,
    userLogin: Object,
    filters: {
        type: Object,
        default: () => ({})
    }
});

const formatDate = (dateString) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'short',
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
</script>
