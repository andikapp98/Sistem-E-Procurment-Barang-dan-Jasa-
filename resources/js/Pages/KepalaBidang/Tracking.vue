<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Tracking Permintaan #{{ permintaan.permintaan_id }}
                </h2>
                <Link :href="route('kepala-bidang.approved')" class="text-sm text-gray-600 hover:text-gray-900">
                    ← Kembali ke Daftar
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                
                <!-- Overall Progress Card -->
                <div class="bg-gradient-to-r from-purple-500 to-indigo-600 overflow-hidden shadow-lg sm:rounded-lg">
                    <div class="p-6 text-white">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-2xl font-bold">
                                    {{ permintaan.bidang || 'Permintaan Pengadaan' }}
                                </h3>
                                <p class="text-purple-100 mt-1">
                                    {{ permintaan.user?.unit_kerja || '-' }}
                                </p>
                            </div>
                            <div class="text-right">
                                <div class="text-4xl font-bold">{{ progress }}%</div>
                                <div class="text-purple-100 text-sm">Progress</div>
                            </div>
                        </div>
                        
                        <!-- Progress Bar -->
                        <div class="w-full bg-purple-300 bg-opacity-30 rounded-full h-4 mt-4">
                            <div class="bg-white h-4 rounded-full transition-all duration-500" :style="{ width: progress + '%' }"></div>
                        </div>
                        
                        <div class="mt-4 flex items-center justify-between text-sm">
                            <span>Tahap {{ completedSteps.length }} dari 8</span>
                            <span>{{ completedSteps.length === 8 ? 'Selesai' : 'Dalam Proses' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Timeline Detail -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">
                            Timeline Tracking Lengkap
                        </h3>
                        <p class="text-sm text-gray-600 mt-1">
                            Riwayat lengkap proses permintaan pengadaan
                        </p>
                    </div>
                    
                    <div class="p-6">
                        <!-- Completed Steps -->
                        <div class="space-y-6">
                            <div v-for="(step, index) in timeline" :key="index" class="relative">
                                <!-- Connector Line (except last item) -->
                                <div v-if="index < timeline.length - 1" class="absolute left-4 top-10 w-0.5 h-full bg-green-200"></div>
                                
                                <div class="flex items-start">
                                    <!-- Icon -->
                                    <div class="flex-shrink-0 relative z-10">
                                        <div class="w-10 h-10 rounded-full bg-green-500 flex items-center justify-center shadow-lg">
                                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    
                                    <!-- Content -->
                                    <div class="ml-6 flex-1 bg-green-50 rounded-lg p-4 border-l-4 border-green-500">
                                        <div class="flex items-center justify-between mb-2">
                                            <h4 class="font-semibold text-gray-900 text-lg">
                                                {{ step.tahapan }}
                                            </h4>
                                            <span class="text-xs text-gray-500 bg-white px-3 py-1 rounded-full">
                                                {{ new Date(step.tanggal).toLocaleDateString('id-ID', { 
                                                    day: 'numeric',
                                                    month: 'long',
                                                    year: 'numeric'
                                                }) }}
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-700 mb-2">
                                            {{ step.keterangan }}
                                        </p>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            ✓ {{ step.status }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pending Steps -->
                        <div v-if="pendingSteps.length > 0" class="mt-8 pt-8 border-t border-gray-200">
                            <h4 class="font-semibold text-gray-700 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Tahapan Berikutnya
                            </h4>
                            <div class="space-y-3">
                                <div v-for="(step, index) in pendingSteps" :key="index" class="flex items-center p-3 bg-gray-50 rounded-lg">
                                    <div class="w-8 h-8 rounded-full bg-gray-300 flex items-center justify-center mr-4">
                                        <svg class="w-4 h-4 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <span class="text-sm text-gray-600">{{ step }}</span>
                                    <span class="ml-auto text-xs text-gray-400 bg-gray-200 px-2 py-1 rounded">Menunggu</span>
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
                        <dl class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">No. Permintaan</dt>
                                <dd class="mt-1 text-sm font-semibold text-gray-900">#{{ permintaan.permintaan_id }}</dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Bidang/Instalasi</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ permintaan.bidang }}</dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Status Saat Ini</dt>
                                <dd class="mt-1">
                                    <span :class="{
                                        'bg-yellow-100 text-yellow-800': permintaan.status === 'proses',
                                        'bg-green-100 text-green-800': permintaan.status === 'disetujui',
                                        'bg-red-100 text-red-800': permintaan.status === 'ditolak',
                                        'bg-blue-100 text-blue-800': permintaan.status === 'diajukan'
                                    }" class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full uppercase">
                                        {{ permintaan.status }}
                                    </span>
                                </dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Tanggal Permintaan</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ new Date(permintaan.tanggal_permintaan).toLocaleDateString('id-ID', {
                                        day: 'numeric',
                                        month: 'long',
                                        year: 'numeric'
                                    }) }}
                                </dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Pemohon</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ permintaan.pic_pimpinan || permintaan.user?.name || '-' }}</dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Unit Kerja</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ permintaan.user?.unit_kerja || '-' }}</dd>
                            </div>
                            
                            <div class="sm:col-span-2">
                                <dt class="text-sm font-medium text-gray-500 mb-2">Deskripsi Permintaan</dt>
                                <dd class="mt-1 text-sm text-gray-900 bg-gray-50 p-4 rounded-lg whitespace-pre-line leading-relaxed">
                                    {{ permintaan.deskripsi }}
                                </dd>
                            </div>

                            <div v-if="permintaan.no_nota_dinas" class="sm:col-span-2">
                                <dt class="text-sm font-medium text-gray-500">No. Nota Dinas</dt>
                                <dd class="mt-1 text-sm font-mono text-gray-900 bg-blue-50 px-3 py-2 rounded inline-block">
                                    {{ permintaan.no_nota_dinas }}
                                </dd>
                            </div>
                        </dl>
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
    permintaan: Object,
    timeline: Array,
    progress: Number,
    completedSteps: Array,
    pendingSteps: Array,
    userLogin: Object,
});
</script>
