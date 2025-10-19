<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    permintaans: Object,
    userLogin: Object,
    filters: Object,
});

// Search & filters
const search = ref(props.filters?.search || '');
const status = ref(props.filters?.status || '');
const bidang = ref(props.filters?.bidang || '');

// Apply filters
const applyFilters = () => {
    router.get(route('kso.index'), {
        search: search.value,
        status: status.value,
        bidang: bidang.value,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};

// Watch for changes
watch([search, status, bidang], () => {
    applyFilters();
});

// Format date
const formatDate = (date) => {
    if (!date) return '-';
    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    });
};
</script>

<template>
    <Head title="Daftar Permintaan KSO" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Daftar Permintaan KSO
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <!-- Filters -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Search -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Cari</label>
                                <input 
                                    v-model="search"
                                    type="text" 
                                    placeholder="Cari ID atau deskripsi..."
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                />
                            </div>

                            <!-- Status -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                <select 
                                    v-model="status"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                >
                                    <option value="">Semua Status</option>
                                    <option value="proses">Proses</option>
                                    <option value="disetujui">Disetujui</option>
                                </select>
                            </div>

                            <!-- Bidang -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Bidang</label>
                                <select 
                                    v-model="bidang"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                >
                                    <option value="">Semua Bidang</option>
                                    <option value="Farmasi">Farmasi</option>
                                    <option value="IGD">IGD</option>
                                    <option value="Rawat Inap">Rawat Inap</option>
                                    <option value="Laboratorium">Laboratorium</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div v-if="permintaans.data && permintaans.data.length > 0" class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bidang</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status KSO</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="permintaan in permintaans.data" :key="permintaan.permintaan_id" class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            #{{ permintaan.permintaan_id }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            {{ permintaan.deskripsi?.substring(0, 60) }}{{ permintaan.deskripsi?.length > 60 ? '...' : '' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ permintaan.bidang }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ formatDate(permintaan.tanggal_permintaan) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span :class="{
                                                'bg-blue-100 text-blue-800': permintaan.status === 'proses',
                                                'bg-green-100 text-green-800': permintaan.status === 'disetujui',
                                            }" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                                {{ permintaan.status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span v-if="permintaan.has_kso" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Sudah Ada
                                            </span>
                                            <span v-else class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                Belum Ada
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                            <Link :href="route('kso.show', permintaan.permintaan_id)" 
                                                  class="text-blue-600 hover:text-blue-900">
                                                Detail
                                            </Link>
                                            <Link v-if="!permintaan.has_kso" 
                                                  :href="route('kso.create', permintaan.permintaan_id)" 
                                                  class="text-green-600 hover:text-green-900">
                                                Buat KSO
                                            </Link>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <!-- Pagination -->
                            <div class="mt-4 flex justify-between items-center">
                                <div class="text-sm text-gray-700">
                                    Menampilkan {{ permintaans.from }} - {{ permintaans.to }} dari {{ permintaans.total }} data
                                </div>
                                <div class="flex space-x-2">
                                    <Link v-for="link in permintaans.links" 
                                          :key="link.label"
                                          :href="link.url || '#'"
                                          :class="{
                                              'bg-blue-600 text-white': link.active,
                                              'bg-white text-gray-700 hover:bg-gray-50': !link.active,
                                              'opacity-50 cursor-not-allowed': !link.url,
                                          }"
                                          class="px-3 py-2 text-sm font-medium rounded-md border border-gray-300"
                                          v-html="link.label">
                                    </Link>
                                </div>
                            </div>
                        </div>

                        <div v-else class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada data</h3>
                            <p class="mt-1 text-sm text-gray-500">Belum ada permintaan untuk Bagian KSO.</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
