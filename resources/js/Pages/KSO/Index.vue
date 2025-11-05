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
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Daftar Permintaan KSO
                </h2>
                <Link :href="route('kso.list-all')" 
                      class="inline-flex items-center px-3 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Lihat Semua KSO
                </Link>
            </div>
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
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dokumen KSO</th>
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
                                            <div v-if="permintaan.has_kso" class="flex items-center space-x-2">
                                                <!-- PKS Indicator -->
                                                <div class="group relative">
                                                    <div v-if="permintaan.kso_file_pks" 
                                                         class="flex items-center px-2 py-1 bg-green-100 text-green-700 rounded-md text-xs font-medium">
                                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                        </svg>
                                                        PKS
                                                    </div>
                                                    <div v-else 
                                                         class="flex items-center px-2 py-1 bg-red-100 text-red-700 rounded-md text-xs font-medium">
                                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                                        </svg>
                                                        PKS
                                                    </div>
                                                    <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">
                                                        {{ permintaan.kso_file_pks ? 'PKS Uploaded' : 'PKS Belum Upload' }}
                                                    </div>
                                                </div>

                                                <!-- MoU Indicator -->
                                                <div class="group relative">
                                                    <div v-if="permintaan.kso_file_mou" 
                                                         class="flex items-center px-2 py-1 bg-blue-100 text-blue-700 rounded-md text-xs font-medium">
                                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                        </svg>
                                                        MoU
                                                    </div>
                                                    <div v-else 
                                                         class="flex items-center px-2 py-1 bg-red-100 text-red-700 rounded-md text-xs font-medium">
                                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                                        </svg>
                                                        MoU
                                                    </div>
                                                    <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">
                                                        {{ permintaan.kso_file_mou ? 'MoU Uploaded' : 'MoU Belum Upload' }}
                                                    </div>
                                                </div>
                                            </div>
                                            <span v-else class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                Belum Ada KSO
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                            <Link :href="route('kso.show', { permintaan: permintaan.permintaan_id })" 
                                                  class="inline-flex items-center text-blue-600 hover:text-blue-900">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                Detail
                                            </Link>
                                            
                                            <Link v-if="!permintaan.has_kso" 
                                                  :href="route('kso.create', { permintaan: permintaan.permintaan_id })" 
                                                  class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white rounded-md hover:bg-green-700 text-xs font-medium">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                                </svg>
                                                Upload Dokumen KSO
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
