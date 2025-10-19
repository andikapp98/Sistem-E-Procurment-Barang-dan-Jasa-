<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Daftar Permintaan - Wakil Direktur
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Info Box -->
                <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4 mb-6">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-indigo-600 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <h3 class="font-semibold text-indigo-900 mb-1">Approval Level 3 - Eksekutif</h3>
                            <p class="text-sm text-indigo-800">
                                Anda melihat permintaan yang sudah disetujui oleh Kepala Bidang. 
                                Setelah approve, permintaan akan diteruskan ke Bagian Perencanaan/Pengadaan.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Filter Bar -->
                <FilterBar 
                    route-name="wakil-direktur.index"
                    :initial-filters="filters"
                    :show-bidang-filter="true"
                />

                <!-- Table -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">
                            Semua Permintaan ({{ permintaans.data ? permintaans.data.length : 0 }})
                        </h3>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        ID
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Deskripsi
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Bidang
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Tanggal
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Progress
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-if="!permintaans.data || permintaans.data.length === 0">
                                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                        </svg>
                                        <p>Tidak ada permintaan yang perlu direview</p>
                                    </td>
                                </tr>
                                
                                <tr v-for="permintaan in permintaans.data" :key="permintaan.permintaan_id" class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        #{{ permintaan.permintaan_id }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        <div class="max-w-xs">
                                            {{ permintaan.deskripsi }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ permintaan.bidang }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ new Date(permintaan.tanggal_permintaan).toLocaleDateString('id-ID') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span :class="{
                                            'bg-yellow-100 text-yellow-800': permintaan.status === 'proses',
                                            'bg-green-100 text-green-800': permintaan.status === 'disetujui',
                                            'bg-red-100 text-red-800': permintaan.status === 'ditolak',
                                            'bg-blue-100 text-blue-800': permintaan.status === 'diajukan'
                                        }" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                            {{ permintaan.status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="w-32">
                                            <div class="flex items-center justify-between mb-1">
                                                <span class="text-xs text-gray-600">{{ permintaan.tracking_status }}</span>
                                                <span class="text-xs font-semibold text-indigo-600">{{ permintaan.progress }}%</span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-1.5">
                                                <div class="bg-indigo-600 h-1.5 rounded-full" :style="{ width: permintaan.progress + '%' }"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <Link :href="route('wakil-direktur.show', permintaan.permintaan_id)"
                                            class="text-indigo-600 hover:text-indigo-900">
                                            Detail →
                                        </Link>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div v-if="permintaans.data && permintaans.data.length > 0" class="px-6 py-4 border-t border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-700">
                                Menampilkan 
                                <span class="font-medium">{{ permintaans.from }}</span>
                                sampai
                                <span class="font-medium">{{ permintaans.to }}</span>
                                dari
                                <span class="font-medium">{{ permintaans.total }}</span>
                                hasil
                            </div>
                            
                            <div class="flex space-x-2">
                                <Link 
                                    v-for="link in permintaans.links" 
                                    :key="link.label"
                                    :href="link.url"
                                    :class="{
                                        'bg-indigo-600 text-white': link.active,
                                        'bg-white text-gray-700 hover:bg-gray-50': !link.active,
                                        'opacity-50 cursor-not-allowed': !link.url
                                    }"
                                    class="px-3 py-2 text-sm font-medium rounded-md border border-gray-300"
                                    v-html="link.label"
                                    :disabled="!link.url"
                                />
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

defineProps({
    permintaans: Object,
    userLogin: Object,
    filters: {
        type: Object,
        default: () => ({})
    }
});
</script>
