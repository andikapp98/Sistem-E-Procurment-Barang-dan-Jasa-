<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    ksos: Object,
    userLogin: Object,
    filters: Object,
});

// Search & filters
const search = ref(props.filters?.search || '');
const status = ref(props.filters?.status || '');
const perPage = ref(props.filters?.per_page || 15);

// Apply filters
const applyFilters = () => {
    router.get(route('kso.list-all'), {
        search: search.value,
        status: status.value,
        per_page: perPage.value,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};

// Watch for changes
watch([search, status, perPage], () => {
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

// Status badge color
const getStatusColor = (status) => {
    const colors = {
        'draft': 'bg-gray-100 text-gray-800',
        'aktif': 'bg-blue-100 text-blue-800',
        'selesai': 'bg-green-100 text-green-800',
        'batal': 'bg-red-100 text-red-800',
    };
    return colors[status] || 'bg-gray-100 text-gray-800';
};

// Format currency
const formatCurrency = (value) => {
    if (!value) return '-';
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(value);
};
</script>

<template>
    <Head title="Lihat Semua KSO" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Lihat Semua KSO
                </h2>
                <Link :href="route('kso.index')" class="text-sm text-gray-600 hover:text-gray-900">
                    ← Kembali ke Permintaan
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <!-- Stats Summary -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-500">Total KSO</div>
                                    <div class="text-2xl font-bold text-gray-900">{{ ksos?.total || 0 }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-8 w-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-500">Draft</div>
                                    <div class="text-2xl font-bold text-gray-900">{{ ksos?.data ? ksos.data.filter(k => k.status === 'draft').length : 0 }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-500">Aktif</div>
                                    <div class="text-2xl font-bold text-blue-900">{{ ksos?.data ? ksos.data.filter(k => k.status === 'aktif').length : 0 }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-500">Selesai</div>
                                    <div class="text-2xl font-bold text-green-900">{{ ksos?.data ? ksos.data.filter(k => k.status === 'selesai').length : 0 }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

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
                                    placeholder="Cari No. KSO, Pihak Kedua..."
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                />
                            </div>

                            <!-- Status Filter -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                <select 
                                    v-model="status"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                >
                                    <option value="">Semua Status</option>
                                    <option value="draft">Draft</option>
                                    <option value="aktif">Aktif</option>
                                    <option value="selesai">Selesai</option>
                                    <option value="batal">Batal</option>
                                </select>
                            </div>

                            <!-- Per Page -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tampilkan</label>
                                <select 
                                    v-model="perPage"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                >
                                    <option value="10">10 per halaman</option>
                                    <option value="15">15 per halaman</option>
                                    <option value="25">25 per halaman</option>
                                    <option value="50">50 per halaman</option>
                                    <option value="100">100 per halaman</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- KSO List -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table v-if="ksos?.data && ksos.data.length > 0" class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. KSO</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pihak Kedua (Vendor)</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Permintaan</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nilai Kontrak</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="kso in ksos.data" :key="kso.kso_id" class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ kso.no_kso }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ formatDate(kso.tanggal_kso) }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-900">{{ kso.pihak_kedua }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div v-if="kso.permintaan" class="text-sm text-gray-900">
                                                <div class="font-medium">#{{ kso.permintaan.permintaan_id }}</div>
                                                <div class="text-xs text-gray-500">{{ kso.permintaan.bidang }}</div>
                                            </div>
                                            <div v-else class="text-sm text-gray-400 italic">-</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span :class="getStatusColor(kso.status)" class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full">
                                                {{ kso.status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ formatCurrency(kso.nilai_kontrak) }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <Link v-if="kso.permintaan" 
                                                  :href="route('kso.show', kso.permintaan.permintaan_id)" 
                                                  class="text-blue-600 hover:text-blue-900 mr-3">
                                                Detail
                                            </Link>
                                            <Link v-if="kso.permintaan" 
                                                  :href="route('kso.edit', { permintaan: kso.permintaan.permintaan_id, kso: kso.kso_id })" 
                                                  class="text-green-600 hover:text-green-900">
                                                Edit
                                            </Link>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <!-- Empty State -->
                            <div v-else class="text-center py-12">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada KSO</h3>
                                <p class="mt-1 text-sm text-gray-500">Belum ada dokumen KSO yang dibuat.</p>
                            </div>
                        </div>

                        <!-- Pagination -->
                        <div v-if="ksos?.data && ksos.data.length > 0" class="mt-6 flex items-center justify-between border-t border-gray-200 pt-4">
                            <div class="flex-1 flex justify-between sm:hidden">
                                <Link v-if="ksos.prev_page_url" 
                                      :href="ksos.prev_page_url" 
                                      class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    Previous
                                </Link>
                                <Link v-if="ksos.next_page_url" 
                                      :href="ksos.next_page_url" 
                                      class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    Next
                                </Link>
                            </div>
                            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm text-gray-700">
                                        Menampilkan
                                        <span class="font-medium">{{ ksos.from || 0 }}</span>
                                        sampai
                                        <span class="font-medium">{{ ksos.to || 0 }}</span>
                                        dari
                                        <span class="font-medium">{{ ksos.total || 0 }}</span>
                                        hasil
                                    </p>
                                </div>
                                <div v-if="ksos.links && ksos.links.length > 0">
                                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                        <template v-for="link in ksos.links" :key="link.label">
                                            <Link v-if="link.url" 
                                                  :href="link.url"
                                                  v-html="link.label"
                                                  :class="[
                                                      link.active ? 'z-10 bg-blue-50 border-blue-500 text-blue-600' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
                                                      'relative inline-flex items-center px-4 py-2 border text-sm font-medium'
                                                  ]"
                                            />
                                            <span v-else
                                                  v-html="link.label"
                                                  class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-gray-100 text-gray-400 text-sm font-medium cursor-not-allowed"
                                            />
                                        </template>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
