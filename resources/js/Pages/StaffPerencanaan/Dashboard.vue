<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard Staff Perencanaan
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Welcome Section -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 bg-gradient-to-r from-green-600 to-green-700 text-white">
                        <h3 class="text-2xl font-bold mb-2">
                            Selamat Datang, {{ userLogin.nama }}
                        </h3>
                        <p class="text-sm opacity-90">
                            {{ userLogin.jabatan }}
                        </p>
                        <p class="text-xs opacity-75 mt-1">
                            Perencanaan & Koordinasi Pengadaan
                        </p>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                    <!-- Total Permintaan -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition-shadow">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                            </div>
                            <h4 class="text-gray-600 text-sm font-medium mb-1">
                                Total Permintaan
                            </h4>
                            <p class="text-3xl font-bold text-gray-900">
                                {{ stats.total }}
                            </p>
                        </div>
                    </div>

                    <!-- Belum Diproses -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition-shadow">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <h4 class="text-gray-600 text-sm font-medium mb-1">
                                Belum Diproses
                            </h4>
                            <p class="text-3xl font-bold text-gray-900">
                                {{ stats.belum_diproses }}
                            </p>
                            <p class="text-xs text-gray-500 mt-1">Butuh perencanaan</p>
                        </div>
                    </div>

                    <!-- Sedang Diproses -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition-shadow">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                </div>
                            </div>
                            <h4 class="text-gray-600 text-sm font-medium mb-1">
                                Sedang Diproses
                            </h4>
                            <p class="text-3xl font-bold text-gray-900">
                                {{ stats.sedang_diproses }}
                            </p>
                            <p class="text-xs text-gray-500 mt-1">Sudah disposisi</p>
                        </div>
                    </div>

                    <!-- Selesai -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition-shadow">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <h4 class="text-gray-600 text-sm font-medium mb-1">
                                Selesai
                            </h4>
                            <p class="text-3xl font-bold text-gray-900">
                                {{ stats.selesai }}
                            </p>
                            <p class="text-xs text-gray-500 mt-1">Completed</p>
                        </div>
                    </div>
                </div>

                <!-- Quick Action -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                    Quick Actions
                                </h3>
                                <p class="text-sm text-gray-600">
                                    Akses cepat ke fungsi utama
                                </p>
                            </div>
                            <Link :href="route('staff-perencanaan.index')" 
                                class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                Lihat Semua Permintaan
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Recent Permintaans -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-semibold text-gray-900">
                                5 Permintaan Terbaru
                            </h3>
                            <Link :href="route('staff-perencanaan.index')" class="text-sm text-green-600 hover:text-green-700 font-medium">
                                Lihat Semua →
                            </Link>
                        </div>

                        <div v-if="recentPermintaans.length > 0" class="space-y-4">
                            <div v-for="permintaan in recentPermintaans" :key="permintaan.permintaan_id"
                                class="border border-gray-200 rounded-lg p-4 hover:border-green-300 hover:shadow-md transition-all">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 mb-2">
                                            <h4 class="font-semibold text-gray-900">
                                                #{{ permintaan.permintaan_id }}
                                            </h4>
                                            <span :class="{
                                                'px-3 py-1 text-xs font-medium rounded-full': true,
                                                'bg-yellow-100 text-yellow-800': permintaan.status === 'disetujui',
                                                'bg-blue-100 text-blue-800': permintaan.status === 'proses',
                                                'bg-green-100 text-green-800': permintaan.status === 'selesai'
                                            }">
                                                {{ permintaan.status.toUpperCase() }}
                                            </span>
                                            <span class="px-3 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">
                                                {{ permintaan.bidang }}
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-700 mb-2 line-clamp-2">
                                            {{ permintaan.deskripsi }}
                                        </p>
                                        <div class="flex items-center gap-4 text-xs text-gray-500">
                                            <span>{{ permintaan.user?.nama || 'Unknown' }}</span>
                                            <span>•</span>
                                            <span>{{ new Date(permintaan.tanggal_permintaan).toLocaleDateString('id-ID') }}</span>
                                        </div>
                                    </div>
                                    <div class="flex gap-2 ml-4">
                                        <Link :href="route('staff-perencanaan.show', permintaan.permintaan_id)"
                                            class="px-4 py-2 text-sm bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors whitespace-nowrap">
                                            Detail
                                        </Link>
                                        <Link v-if="permintaan.status === 'disetujui'"
                                            :href="route('staff-perencanaan.scan-berkas', permintaan.permintaan_id)"
                                            class="px-4 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors whitespace-nowrap flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                            </svg>
                                            Scan Berkas
                                        </Link>
                                    </div>
                                </div>

                                <!-- Progress Bar -->
                                <div class="mt-3">
                                    <div class="flex items-center justify-between text-xs text-gray-600 mb-1">
                                        <span>Progress: {{ permintaan.tracking_status }}</span>
                                        <span>{{ permintaan.progress }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-green-600 h-2 rounded-full transition-all" 
                                            :style="{ width: permintaan.progress + '%' }"></div>
                                    </div>
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
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    stats: Object,
    recentPermintaans: Array,
    userLogin: Object,
});
</script>
