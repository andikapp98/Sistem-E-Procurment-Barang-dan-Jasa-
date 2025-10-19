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
                
                <!-- Progress Timeline -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">
                                Progress Tracking
                            </h3>
                            <span class="text-sm font-medium text-green-600">
                                {{ progress }}% Complete
                            </span>
                        </div>
                        
                        <!-- Progress Bar -->
                        <div class="w-full bg-gray-200 rounded-full h-3 mb-4">
                            <div class="bg-green-600 h-3 rounded-full transition-all" :style="{ width: progress + '%' }"></div>
                        </div>

                        <!-- Timeline -->
                        <div class="mt-6 space-y-3">
                            <div v-for="(step, index) in timeline" :key="index" class="flex items-start">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center">
                                        <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4 flex-1">
                                    <div class="flex items-center justify-between">
                                        <p class="font-medium text-gray-900">{{ step.tahapan }}</p>
                                        <span class="text-xs text-gray-500">{{ new Date(step.tanggal).toLocaleDateString('id-ID') }}</span>
                                    </div>
                                    <p class="text-sm text-gray-600 mt-1">{{ step.keterangan }}</p>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 mt-1">
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
                                <dt class="text-sm font-medium text-gray-500">Bidang</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ permintaan.bidang }}</dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Status</dt>
                                <dd class="mt-1">
                                    <span :class="{
                                        'bg-yellow-100 text-yellow-800': permintaan.status === 'disetujui',
                                        'bg-blue-100 text-blue-800': permintaan.status === 'proses',
                                        'bg-green-100 text-green-800': permintaan.status === 'selesai'
                                    }" class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full">
                                        {{ permintaan.status.toUpperCase() }}
                                    </span>
                                </dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Tanggal Permintaan</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ new Date(permintaan.tanggal_permintaan).toLocaleDateString('id-ID') }}</dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Pemohon</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ permintaan.user?.nama || '-' }}</dd>
                            </div>
                            
                            <div class="sm:col-span-2">
                                <dt class="text-sm font-medium text-gray-500 mb-2">Deskripsi</dt>
                                <dd class="mt-1 text-sm text-gray-900 bg-gray-50 p-4 rounded-lg whitespace-pre-line">{{ permintaan.deskripsi }}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">PIC Saat Ini</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ permintaan.pic_pimpinan || '-' }}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Tracking Status</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ trackingStatus }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg" v-if="permintaan.status === 'disetujui'">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">
                                    Actions
                                </h3>
                                <p class="text-sm text-gray-600 mt-1">
                                    Upload dokumen atau buat perencanaan pengadaan
                                </p>
                            </div>
                        </div>
                        
                        <div class="flex gap-3">
                            <button @click="showComingSoonModal = true"
                                class="px-6 py-3 bg-gray-400 text-white rounded-lg hover:bg-gray-500 transition-colors flex items-center gap-2 cursor-not-allowed opacity-75">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                                Scan Berkas
                                <span class="text-xs bg-yellow-500 px-2 py-0.5 rounded-full">Coming Soon</span>
                            </button>
                            
                            <button @click="showComingSoonModal = true"
                                class="px-6 py-3 bg-gray-400 text-white rounded-lg hover:bg-gray-500 transition-colors flex items-center gap-2 cursor-not-allowed opacity-75">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                </svg>
                                Buat Disposisi
                                <span class="text-xs bg-yellow-500 px-2 py-0.5 rounded-full">Coming Soon</span>
                            </button>
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
                                class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-start justify-between mb-3">
                                    <div>
                                        <h4 class="font-semibold text-gray-900">{{ nota.no_nota }}</h4>
                                        <p class="text-sm text-gray-600 mt-1">{{ nota.perihal }}</p>
                                    </div>
                                    <span class="text-xs text-gray-500">
                                        {{ new Date(nota.tanggal_nota).toLocaleDateString('id-ID') }}
                                    </span>
                                </div>

                                <div class="grid grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <span class="text-gray-500">Dari:</span>
                                        <span class="ml-2 text-gray-900">{{ nota.dari }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500">Kepada:</span>
                                        <span class="ml-2 text-gray-900">{{ nota.kepada }}</span>
                                    </div>
                                </div>

                                <!-- Disposisi for this Nota -->
                                <div v-if="nota.disposisi && nota.disposisi.length > 0" class="mt-4 pl-4 border-l-2 border-green-200">
                                    <p class="text-xs font-medium text-gray-700 mb-2">Disposisi:</p>
                                    <div v-for="disp in nota.disposisi" :key="disp.disposisi_id" class="text-sm mb-2">
                                        <div class="flex items-center justify-between">
                                            <span class="text-gray-900">→ {{ disp.jabatan_tujuan }}</span>
                                            <span :class="{
                                                'px-2 py-0.5 text-xs rounded-full': true,
                                                'bg-green-100 text-green-800': disp.status === 'disetujui',
                                                'bg-yellow-100 text-yellow-800': disp.status === 'dalam_proses',
                                                'bg-red-100 text-red-800': disp.status === 'ditolak'
                                            }">
                                                {{ disp.status }}
                                            </span>
                                        </div>
                                        <p class="text-xs text-gray-600 mt-1">{{ disp.catatan }}</p>
                                        <p class="text-xs text-gray-500">{{ new Date(disp.tanggal_disposisi).toLocaleDateString('id-ID') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center py-8 text-gray-500">
                            Belum ada nota dinas
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Coming Soon Modal -->
        <div v-if="showComingSoonModal" class="fixed inset-0 z-50 overflow-y-auto" @click.self="showComingSoonModal = false">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black opacity-50"></div>
                
                <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                    <div class="text-center">
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100 mb-4">
                            <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">
                            Fitur Coming Soon
                        </h3>
                        <p class="text-sm text-gray-600 mb-6">
                            Fitur "Scan Berkas" dan "Buat Disposisi" sedang dalam pengembangan dan akan segera tersedia. 
                            Untuk saat ini, permintaan akan otomatis dilanjutkan ke bagian terkait.
                        </p>
                        <button @click="showComingSoonModal = false"
                            class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            Mengerti
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    permintaan: Object,
    trackingStatus: String,
    timeline: Array,
    progress: Number,
});

const showComingSoonModal = ref(false);
</script>
