<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Daftar Permintaan - Staff Perencanaan
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Info Box -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-600">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-green-800">
                                    Staff Perencanaan
                                </h3>
                                <p class="mt-1 text-sm text-green-700">
                                    Buat perencanaan pengadaan untuk permintaan yang sudah disetujui Direktur, kemudian disposisi ke bagian pelaksana (Pengadaan/KSO).
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filter Bar -->
                <FilterBar :filters="filters" />

                <!-- Permintaans Table -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div v-if="permintaans.data.length > 0">
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
                                            class="hover:bg-gray-50 transition-colors">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="text-sm font-medium text-gray-900">
                                                    #{{ permintaan.permintaan_id }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-3 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">
                                                    {{ permintaan.bidang }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm text-gray-900 max-w-md">
                                                    <p class="line-clamp-2">{{ permintaan.deskripsi }}</p>
                                                    <p class="text-xs text-gray-500 mt-1">
                                                        oleh {{ permintaan.user?.nama || 'Unknown' }}
                                                    </p>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="text-sm text-gray-500">
                                                    {{ new Date(permintaan.tanggal_permintaan).toLocaleDateString('id-ID') }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span :class="{
                                                    'px-3 py-1 text-xs font-medium rounded-full': true,
                                                    'bg-yellow-100 text-yellow-800': permintaan.status === 'disetujui',
                                                    'bg-blue-100 text-blue-800': permintaan.status === 'proses',
                                                    'bg-green-100 text-green-800': permintaan.status === 'selesai'
                                                }">
                                                    {{ permintaan.status.toUpperCase() }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="w-32">
                                                    <div class="flex items-center justify-between text-xs text-gray-600 mb-1">
                                                        <span>{{ permintaan.progress }}%</span>
                                                    </div>
                                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                                        <div class="bg-green-600 h-2 rounded-full" 
                                                            :style="{ width: permintaan.progress + '%' }"></div>
                                                    </div>
                                                    <p class="text-xs text-gray-500 mt-1">{{ permintaan.tracking_status }}</p>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <div class="flex gap-2 justify-end">
                                                    <Link :href="route('staff-perencanaan.show', permintaan.permintaan_id)"
                                                        class="text-green-600 hover:text-green-900">
                                                        Detail
                                                    </Link>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="mt-6 flex items-center justify-between border-t border-gray-200 pt-4">
                                <div class="text-sm text-gray-700">
                                    Menampilkan <span class="font-medium">{{ permintaans.from }}</span> 
                                    sampai <span class="font-medium">{{ permintaans.to }}</span> 
                                    dari <span class="font-medium">{{ permintaans.total }}</span> permintaan
                                </div>
                                <div class="flex gap-2">
                                    <Link v-for="link in permintaans.links" :key="link.label"
                                        :href="link.url || '#'"
                                        :class="{
                                            'px-4 py-2 text-sm rounded-lg transition-colors': true,
                                            'bg-green-600 text-white': link.active,
                                            'bg-white text-gray-700 hover:bg-gray-50 border border-gray-300': !link.active && link.url,
                                            'bg-gray-100 text-gray-400 cursor-not-allowed': !link.url
                                        }"
                                        v-html="link.label"
                                        :disabled="!link.url">
                                    </Link>
                                </div>
                            </div>
                        </div>

                        <!-- Empty State -->
                        <div v-else class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada permintaan</h3>
                            <p class="mt-1 text-sm text-gray-500">Belum ada permintaan yang perlu diproses.</p>
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
    permintaans: Object,
    userLogin: Object,
    filters: Object,
});
</script>
