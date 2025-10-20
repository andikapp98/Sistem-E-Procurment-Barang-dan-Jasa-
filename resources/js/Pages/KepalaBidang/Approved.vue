<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        Permintaan yang Disetujui
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">
                        Tracking progress permintaan yang sudah disetujui Kepala Bidang
                    </p>
                </div>
                <Link :href="route('kepala-bidang.dashboard')" class="text-sm text-gray-600 hover:text-gray-900">
                    ← Dashboard
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <!-- Filter Section -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <form @submit.prevent="submitFilter" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Pencarian</label>
                                <input 
                                    v-model="form.search" 
                                    type="text" 
                                    placeholder="Cari no. permintaan, deskripsi..."
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                >
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Bidang</label>
                                <select 
                                    v-model="form.bidang"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                >
                                    <option value="">Semua Bidang</option>
                                    <option value="Gawat Darurat">Gawat Darurat</option>
                                    <option value="Farmasi">Farmasi</option>
                                    <option value="Laboratorium">Laboratorium</option>
                                    <option value="Radiologi">Radiologi</option>
                                    <option value="Bedah Sentral">Bedah Sentral</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
                                <input 
                                    v-model="form.tanggal_dari"
                                    type="date"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                >
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                                <input 
                                    v-model="form.tanggal_sampai"
                                    type="date"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                >
                            </div>
                            
                            <div class="md:col-span-4 flex gap-2">
                                <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 transition">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                    Filter
                                </button>
                                <button type="button" @click="resetFilter" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition">
                                    Reset
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- List Permintaan -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900">
                                Daftar Permintaan ({{ permintaans.total }} Total)
                            </h3>
                        </div>
                    </div>
                    
                    <div class="divide-y divide-gray-200">
                        <div v-if="permintaans.data.length === 0" class="p-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                            </svg>
                            <p class="mt-2 text-sm text-gray-500">Tidak ada permintaan yang disetujui</p>
                        </div>

                        <div 
                            v-for="permintaan in permintaans.data" 
                            :key="permintaan.permintaan_id"
                            class="p-6 hover:bg-gray-50 transition"
                        >
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        <h4 class="text-lg font-semibold text-gray-900">
                                            #{{ permintaan.permintaan_id }} - {{ permintaan.bidang }}
                                        </h4>
                                        <span :class="{
                                            'bg-yellow-100 text-yellow-800': permintaan.status === 'proses',
                                            'bg-green-100 text-green-800': permintaan.status === 'disetujui'
                                        }" class="px-2 py-1 text-xs font-medium rounded-full">
                                            {{ permintaan.status.toUpperCase() }}
                                        </span>
                                    </div>
                                    
                                    <p class="text-sm text-gray-600 mb-3 line-clamp-2">
                                        {{ permintaan.deskripsi }}
                                    </p>
                                    
                                    <div class="flex items-center gap-6 text-sm text-gray-500">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            {{ new Date(permintaan.tanggal_permintaan).toLocaleDateString('id-ID') }}
                                        </div>
                                        
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                            {{ permintaan.user?.name || '-' }}
                                        </div>
                                        
                                        <div class="flex items-center font-medium text-purple-600">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                            </svg>
                                            {{ permintaan.current_stage || 'Permintaan' }}
                                        </div>
                                    </div>

                                    <!-- Progress Bar -->
                                    <div class="mt-4">
                                        <div class="flex items-center justify-between text-xs text-gray-600 mb-1">
                                            <span>Progress</span>
                                            <span class="font-semibold">{{ permintaan.progress }}%</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div 
                                                class="bg-gradient-to-r from-purple-500 to-indigo-600 h-2 rounded-full transition-all duration-500"
                                                :style="{ width: permintaan.progress + '%' }"
                                            ></div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="ml-6 flex flex-col gap-2">
                                    <Link 
                                        :href="route('kepala-bidang.tracking', permintaan.permintaan_id)"
                                        class="inline-flex items-center px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-md hover:bg-purple-700 transition"
                                    >
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                        </svg>
                                        Detail Tracking
                                    </Link>
                                    
                                    <Link 
                                        :href="route('kepala-bidang.show', permintaan.permintaan_id)"
                                        class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-200 transition"
                                    >
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        Lihat Detail
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div v-if="permintaans.data.length > 0" class="p-6 border-t border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-700">
                                Menampilkan {{ permintaans.from }} - {{ permintaans.to }} dari {{ permintaans.total }} permintaan
                            </div>
                            <div class="flex gap-2">
                                <Link
                                    v-for="link in permintaans.links"
                                    :key="link.label"
                                    :href="link.url"
                                    :class="{
                                        'bg-purple-600 text-white': link.active,
                                        'bg-gray-100 text-gray-700 hover:bg-gray-200': !link.active,
                                        'opacity-50 cursor-not-allowed': !link.url
                                    }"
                                    class="px-3 py-2 text-sm rounded-md transition"
                                    v-html="link.label"
                                ></Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, reactive } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, router } from '@inertiajs/vue3';

const props = defineProps({
    permintaans: Object,
    userLogin: Object,
    filters: Object,
});

const form = reactive({
    search: props.filters.search || '',
    bidang: props.filters.bidang || '',
    tanggal_dari: props.filters.tanggal_dari || '',
    tanggal_sampai: props.filters.tanggal_sampai || '',
});

const submitFilter = () => {
    router.get(route('kepala-bidang.approved'), form, {
        preserveState: true,
        preserveScroll: true,
    });
};

const resetFilter = () => {
    form.search = '';
    form.bidang = '';
    form.tanggal_dari = '';
    form.tanggal_sampai = '';
    submitFilter();
};
</script>
