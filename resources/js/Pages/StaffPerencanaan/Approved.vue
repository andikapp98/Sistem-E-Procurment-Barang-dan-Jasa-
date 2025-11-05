<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Riwayat Permintaan yang Sudah Diproses
                </h2>
                <Link :href="route('staff-perencanaan.dashboard')" class="text-sm text-gray-600 hover:text-gray-900">
                    ← Kembali ke Dashboard
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                
                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="text-sm font-medium text-gray-500">Total Diproses</div>
                            <div class="mt-2 text-3xl font-semibold text-gray-900">{{ stats.total }}</div>
                        </div>
                    </div>
                    <div class="bg-green-50 overflow-hidden shadow-sm sm:rounded-lg border border-green-200">
                        <div class="p-6">
                            <div class="text-sm font-medium text-green-800">Sudah Diteruskan</div>
                            <div class="mt-2 text-3xl font-semibold text-green-900">{{ stats.forwarded }}</div>
                        </div>
                    </div>
                    <div class="bg-yellow-50 overflow-hidden shadow-sm sm:rounded-lg border border-yellow-200">
                        <div class="p-6">
                            <div class="text-sm font-medium text-yellow-800">Dalam Proses</div>
                            <div class="mt-2 text-3xl font-semibold text-yellow-900">{{ stats.processing }}</div>
                        </div>
                    </div>
                    <div class="bg-blue-50 overflow-hidden shadow-sm sm:rounded-lg border border-blue-200">
                        <div class="p-6">
                            <div class="text-sm font-medium text-blue-800">Selesai</div>
                            <div class="mt-2 text-3xl font-semibold text-blue-900">{{ stats.completed }}</div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Cari</label>
                                <input v-model="form.search" type="text" 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    placeholder="ID, deskripsi, atau No. Nota Dinas">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Bidang</label>
                                <input v-model="form.bidang" type="text" 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    placeholder="Semua bidang">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <select v-model="form.status" 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Semua Status</option>
                                    <option value="proses">Proses</option>
                                    <option value="disetujui">Disetujui</option>
                                    <option value="ditolak">Ditolak</option>
                                    <option value="revisi">Revisi</option>
                                    <option value="selesai">Selesai</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Dari</label>
                                <input v-model="form.tanggal_dari" type="date" 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Sampai</label>
                                <input v-model="form.tanggal_sampai" type="date" 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>
                        <div class="mt-4 flex gap-2">
                            <button @click="applyFilters" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                Terapkan Filter
                            </button>
                            <button @click="resetFilters" 
                                class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                                Reset
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        ID
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Bidang
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Deskripsi
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Tahap Terakhir
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Tanggal
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Progress
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="permintaan in permintaans.data" :key="permintaan.permintaan_id" 
                                    class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        #{{ permintaan.permintaan_id }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ permintaan.bidang }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        <div class="max-w-xs truncate">
                                            {{ permintaan.deskripsi }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ permintaan.last_stage || '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ permintaan.tanggal_permintaan ? new Date(permintaan.tanggal_permintaan).toLocaleDateString('id-ID') : '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span :class="{
                                            'bg-yellow-100 text-yellow-800': permintaan.status === 'proses',
                                            'bg-green-100 text-green-800': permintaan.status === 'disetujui',
                                            'bg-red-100 text-red-800': permintaan.status === 'ditolak',
                                            'bg-blue-100 text-blue-800': permintaan.status === 'revisi',
                                            'bg-purple-100 text-purple-800': permintaan.status === 'selesai'
                                        }" class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full">
                                            {{ permintaan.status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-full bg-gray-200 rounded-full h-2 mr-2">
                                                <div class="bg-green-600 h-2 rounded-full" :style="{ width: permintaan.progress + '%' }"></div>
                                            </div>
                                            <span class="text-xs text-gray-600 font-medium">{{ permintaan.progress }}%</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <Link :href="route('staff-perencanaan.show', permintaan.permintaan_id)"
                                            class="text-blue-600 hover:text-blue-900 mr-3">
                                            Detail
                                        </Link>
                                        <Link :href="route('staff-perencanaan.history', permintaan.permintaan_id)"
                                            class="text-indigo-600 hover:text-indigo-900">
                                            History
                                        </Link>
                                    </td>
                                </tr>
                                <tr v-if="permintaans.data.length === 0">
                                    <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada data</h3>
                                        <p class="mt-1 text-sm text-gray-500">Belum ada permintaan yang diproses.</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-700">
                                Menampilkan {{ permintaans.from ?? 0 }} - {{ permintaans.to ?? 0 }} dari {{ permintaans.total }} data
                            </div>
                            <div class="flex gap-2">
                                <template v-for="link in permintaans.links" :key="link.label">
                                    <Link v-if="link.url"
                                        :href="link.url"
                                        v-html="link.label"
                                        :class="{
                                            'bg-blue-600 text-white': link.active,
                                            'bg-white text-gray-700 hover:bg-gray-50': !link.active
                                        }"
                                        class="px-3 py-2 text-sm border border-gray-300 rounded-md">
                                    </Link>
                                    <span v-else
                                        v-html="link.label"
                                        class="px-3 py-2 text-sm border border-gray-300 rounded-md bg-gray-100 text-gray-400 cursor-not-allowed">
                                    </span>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { reactive } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, router } from '@inertiajs/vue3';

const props = defineProps({
    permintaans: Object,
    stats: Object,
    userLogin: Object,
    filters: Object,
});

const form = reactive({
    search: props.filters?.search || '',
    bidang: props.filters?.bidang || '',
    status: props.filters?.status || '',
    tanggal_dari: props.filters?.tanggal_dari || '',
    tanggal_sampai: props.filters?.tanggal_sampai || '',
});

const applyFilters = () => {
    router.get(route('staff-perencanaan.approved'), form, {
        preserveState: true,
        preserveScroll: true,
    });
};

const resetFilters = () => {
    form.search = '';
    form.bidang = '';
    form.status = '';
    form.tanggal_dari = '';
    form.tanggal_sampai = '';
    applyFilters();
};
</script>
