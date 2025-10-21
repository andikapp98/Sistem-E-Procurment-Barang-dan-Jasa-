<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Detail Permintaan #{{ permintaan.permintaan_id }}
                </h2>
                <Link :href="route('staff-perencanaan.index')" class="text-sm text-gray-600 hover:text-gray-900">
                    ← Kembali ke Daftar
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                
                <!-- Alert Info -->
                <div class="bg-blue-50 border-l-4 border-blue-400 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm text-blue-700">
                                <strong>Permintaan dari {{ permintaan.bidang }}</strong> - 
                                Status: <span class="font-semibold">{{ permintaan.status.toUpperCase() }}</span>
                            </p>
                            <p class="text-xs text-blue-600 mt-1">
                                Tracking Status: {{ trackingStatus }} ({{ progress }}% selesai)
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Progress Timeline -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900">
                                Progress Tracking
                            </h3>
                            <span class="text-lg font-bold text-green-600">
                                {{ progress }}%
                            </span>
                        </div>
                    </div>
                    
                    <div class="p-6">
                        <!-- Progress Bar -->
                        <div class="w-full bg-gray-200 rounded-full h-4 mb-6">
                            <div class="bg-green-600 h-4 rounded-full transition-all duration-500" :style="{ width: progress + '%' }"></div>
                        </div>

                        <!-- Timeline -->
                        <div class="space-y-4">
                            <div v-for="(step, index) in timeline" :key="index" class="flex items-start">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center ring-4 ring-white">
                                        <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4 flex-1 pb-4" :class="{ 'border-l-2 border-gray-200 pl-4 -ml-4': index < timeline.length - 1 }">
                                    <div class="flex items-center justify-between mb-1">
                                        <p class="font-semibold text-gray-900">{{ step.tahapan }}</p>
                                        <span class="text-xs text-gray-500">
                                            {{ formatDate(step.tanggal) }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-600 mb-2">{{ step.keterangan }}</p>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        {{ step.status }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detail Permintaan -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">
                            Informasi Permintaan
                        </h3>
                    </div>
                    
                    <div class="p-6">
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">ID Permintaan</dt>
                                <dd class="mt-1 text-sm font-semibold text-gray-900">#{{ permintaan.permintaan_id }}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Status</dt>
                                <dd class="mt-1">
                                    <span :class="{
                                        'bg-yellow-100 text-yellow-800': permintaan.status === 'disetujui',
                                        'bg-blue-100 text-blue-800': permintaan.status === 'proses',
                                        'bg-green-100 text-green-800': permintaan.status === 'selesai',
                                        'bg-gray-100 text-gray-800': permintaan.status === 'diajukan',
                                        'bg-red-100 text-red-800': permintaan.status === 'ditolak',
                                    }" class="px-3 py-1 inline-flex text-sm font-semibold rounded-full">
                                        {{ permintaan.status.toUpperCase() }}
                                    </span>
                                </dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Bidang / Unit</dt>
                                <dd class="mt-1 text-sm font-semibold text-gray-900">{{ permintaan.bidang }}</dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Tanggal Permintaan</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ formatDate(permintaan.tanggal_permintaan) }}</dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Pemohon</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ permintaan.user?.nama || '-' }}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">PIC Pimpinan Saat Ini</dt>
                                <dd class="mt-1">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ permintaan.pic_pimpinan || '-' }}
                                    </span>
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">No. Nota Dinas</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ permintaan.no_nota_dinas || '-' }}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Tracking Status</dt>
                                <dd class="mt-1 text-sm font-medium text-gray-900">{{ trackingStatus }}</dd>
                            </div>
                            
                            <div class="sm:col-span-2">
                                <dt class="text-sm font-medium text-gray-500 mb-2">Deskripsi Permintaan</dt>
                                <dd class="mt-1 text-sm text-gray-900 bg-gray-50 p-4 rounded-lg whitespace-pre-line border border-gray-200">{{ permintaan.deskripsi }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg" v-if="permintaan.status === 'disetujui' || permintaan.status === 'proses'">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">
                                    Aksi Perencanaan
                                </h3>
                                <p class="text-sm text-gray-600 mt-1">
                                    Pilih aksi yang akan dilakukan untuk permintaan ini
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                            <!-- Buat Nota Dinas Usulan -->
                            <Link :href="route('staff-perencanaan.nota-dinas.create', permintaan.permintaan_id)"
                                class="inline-flex justify-center items-center px-6 py-3 bg-[#028174] text-white rounded-lg hover:bg-[#03a089] transition-colors gap-2 shadow-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <span>Buat Nota Dinas Usulan</span>
                            </Link>
                            
                            <!-- Buat Nota Dinas Pembelian -->
                            <Link :href="route('staff-perencanaan.nota-dinas-pembelian.create', permintaan.permintaan_id)"
                                class="inline-flex justify-center items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors gap-2 shadow-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                <span>Buat Nota Dinas Pembelian</span>
                            </Link>
                            
                            <!-- Buat DPP -->
                            <Link :href="route('staff-perencanaan.dpp.create', permintaan.permintaan_id)"
                                class="inline-flex justify-center items-center px-6 py-3 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors gap-2 shadow-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <span>Buat DPP</span>
                            </Link>
                            
                            <!-- Buat HPS -->
                            <Link :href="route('staff-perencanaan.hps.create', permintaan.permintaan_id)"
                                class="inline-flex justify-center items-center px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors gap-2 shadow-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                </svg>
                                <span>Buat HPS</span>
                            </Link>
                            
                            <!-- Buat Disposisi -->
                            <Link :href="route('staff-perencanaan.disposisi.create', permintaan.permintaan_id)"
                                class="inline-flex justify-center items-center px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors gap-2 shadow-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                </svg>
                                <span>Buat Disposisi</span>
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Riwayat Nota Dinas & Disposisi -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">
                            Riwayat Nota Dinas & Disposisi
                        </h3>
                    </div>

                    <div class="p-6">
                        <div v-if="permintaan.nota_dinas && permintaan.nota_dinas.length > 0" class="space-y-4">
                            <div v-for="nota in permintaan.nota_dinas" :key="nota.nota_id"
                                class="border border-gray-200 rounded-lg p-5 hover:shadow-md transition-shadow">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-1">
                                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            <h4 class="font-semibold text-gray-900">{{ nota.no_nota }}</h4>
                                        </div>
                                        <p class="text-sm text-gray-700 font-medium mt-2">{{ nota.perihal }}</p>
                                    </div>
                                    <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">
                                        {{ formatDate(nota.tanggal_nota) }}
                                    </span>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm bg-gray-50 p-3 rounded">
                                    <div>
                                        <span class="text-gray-500 font-medium">Dari:</span>
                                        <span class="ml-2 text-gray-900">{{ nota.dari }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500 font-medium">Kepada:</span>
                                        <span class="ml-2 text-gray-900">{{ nota.kepada }}</span>
                                    </div>
                                </div>

                                <!-- Disposisi for this Nota -->
                                <div v-if="nota.disposisi && nota.disposisi.length > 0" class="mt-4 pl-4 border-l-4 border-green-300 bg-green-50 p-3 rounded-r">
                                    <p class="text-xs font-semibold text-gray-700 mb-3 flex items-center gap-2">
                                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                        </svg>
                                        Disposisi
                                    </p>
                                    <div v-for="disp in nota.disposisi" :key="disp.disposisi_id" class="text-sm mb-3 bg-white p-3 rounded">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-gray-900 font-medium">→ {{ disp.jabatan_tujuan }}</span>
                                            <span :class="{
                                                'px-2.5 py-0.5 text-xs rounded-full font-medium': true,
                                                'bg-green-100 text-green-800': disp.status === 'disetujui',
                                                'bg-yellow-100 text-yellow-800': disp.status === 'dalam_proses',
                                                'bg-red-100 text-red-800': disp.status === 'ditolak'
                                            }">
                                                {{ disp.status }}
                                            </span>
                                        </div>
                                        <p class="text-xs text-gray-600 mb-1" v-if="disp.catatan">
                                            <span class="font-medium">Catatan:</span> {{ disp.catatan }}
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            <span class="font-medium">Tanggal:</span> {{ formatDate(disp.tanggal_disposisi) }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p class="mt-2 text-sm text-gray-500">Belum ada nota dinas untuk permintaan ini</p>
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
    permintaan: Object,
    trackingStatus: String,
    timeline: Array,
    progress: Number,
    userLogin: Object,
});

const formatDate = (date) => {
    if (!date) return '-';
    return new Date(date).toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
    });
};
</script>

