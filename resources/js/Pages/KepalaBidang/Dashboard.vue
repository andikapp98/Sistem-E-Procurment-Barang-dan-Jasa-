<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard Kepala Bidang
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Welcome Section -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 bg-gradient-to-r from-purple-600 to-purple-700 text-white">
                        <h3 class="text-2xl font-bold mb-2">
                            Selamat Datang, {{ userLogin.nama }}
                        </h3>
                        <p class="text-sm opacity-90">
                            {{ userLogin.jabatan }}
                        </p>
                        <p class="text-xs opacity-75 mt-1">
                            Approval Level 2 - Supervisi Semua Unit
                        </p>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                    <!-- Total Permintaan -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition-shadow">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

                    <!-- Menunggu Review -->
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
                                Menunggu Review
                            </h4>
                            <p class="text-3xl font-bold text-yellow-600">
                                {{ stats.menunggu }}
                            </p>
                        </div>
                    </div>

                    <!-- Disetujui -->
                    <Link :href="route('kepala-bidang.approved')" class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition-shadow cursor-pointer">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </div>
                            <h4 class="text-gray-600 text-sm font-medium mb-1">
                                Disetujui (Tracking)
                            </h4>
                            <p class="text-3xl font-bold text-green-600">
                                {{ stats.disetujui }}
                            </p>
                        </div>
                    </Link>

                    <!-- Ditolak -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition-shadow">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <h4 class="text-gray-600 text-sm font-medium mb-1">
                                Ditolak
                            </h4>
                            <p class="text-3xl font-bold text-red-600">
                                {{ stats.ditolak }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Recent Permintaan -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900">
                                Permintaan Terbaru
                            </h3>
                            <Link :href="route('kepala-bidang.index')" class="text-sm text-purple-600 hover:text-purple-800 font-medium">
                                Lihat Semua →
                            </Link>
                        </div>
                    </div>
                    
                    <div class="p-6">
                        <div v-if="recentPermintaans.length === 0" class="text-center py-8 text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                            </svg>
                            <p>Tidak ada permintaan yang menunggu review</p>
                        </div>

                        <div v-else class="space-y-4">
                            <div v-for="permintaan in recentPermintaans" :key="permintaan.permintaan_id"
                                class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-2">
                                            <span class="text-sm font-medium text-gray-500">
                                                #{{ permintaan.permintaan_id }}
                                            </span>
                                            <span :class="{
                                                'bg-yellow-100 text-yellow-800': permintaan.status === 'proses',
                                                'bg-green-100 text-green-800': permintaan.status === 'disetujui',
                                                'bg-red-100 text-red-800': permintaan.status === 'ditolak',
                                                'bg-blue-100 text-blue-800': permintaan.status === 'diajukan'
                                            }" class="px-2 py-1 rounded-full text-xs font-semibold">
                                                {{ permintaan.status.toUpperCase() }}
                                            </span>
                                        </div>
                                        
                                        <h4 class="font-semibold text-gray-900 mb-1">
                                            {{ permintaan.deskripsi }}
                                        </h4>
                                        
                                        <p class="text-sm text-gray-600 mb-2">
                                            Dari: {{ permintaan.bidang }} | Tanggal: {{ new Date(permintaan.tanggal_permintaan).toLocaleDateString('id-ID') }}
                                        </p>

                                        <!-- Progress Bar -->
                                        <div class="mt-3">
                                            <div class="flex items-center justify-between mb-1">
                                                <span class="text-xs text-gray-600">Progress: {{ permintaan.tracking_status }}</span>
                                                <span class="text-xs font-semibold text-purple-600">{{ permintaan.progress }}%</span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-2">
                                                <div class="bg-purple-600 h-2 rounded-full transition-all" :style="{ width: permintaan.progress + '%' }"></div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <Link :href="route('kepala-bidang.show', permintaan.permintaan_id)"
                                        class="ml-4 px-4 py-2 bg-purple-600 text-white text-sm rounded-lg hover:bg-purple-700 transition-colors">
                                        Review
                                    </Link>
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
import { Link } from '@inertiajs/vue3';

defineProps({
    stats: Object,
    recentPermintaans: Array,
    userLogin: Object,
});
</script>
