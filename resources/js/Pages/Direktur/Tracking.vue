<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Tracking Permintaan #{{ permintaan.permintaan_id }}
                </h2>
                <Link :href="route('direktur.approved')" class="text-sm text-gray-600 hover:text-gray-900">
                    ← Kembali ke Daftar
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                
                <!-- Progress Summary -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">
                                Progress Pengadaan
                            </h3>
                            <span class="text-2xl font-bold text-red-600">
                                {{ progress }}%
                            </span>
                        </div>
                        
                        <!-- Progress Bar -->
                        <div class="w-full bg-gray-200 rounded-full h-4 mb-4">
                            <div 
                                class="bg-red-600 h-4 rounded-full transition-all duration-500" 
                                :style="{ width: progress + '%' }"
                            ></div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                            <div class="text-center">
                                <p class="text-gray-500">Tahap Selesai</p>
                                <p class="text-xl font-bold text-green-600">{{ completedSteps.length }}/8</p>
                            </div>
                            <div class="text-center">
                                <p class="text-gray-500">Tahap Pending</p>
                                <p class="text-xl font-bold text-gray-600">{{ pendingSteps.length }}</p>
                            </div>
                            <div class="text-center">
                                <p class="text-gray-500">Status</p>
                                <span :class="{
                                    'bg-yellow-100 text-yellow-800': permintaan.status === 'proses',
                                    'bg-green-100 text-green-800': permintaan.status === 'disetujui',
                                    'bg-red-100 text-red-800': permintaan.status === 'ditolak',
                                }" class="px-3 py-1 inline-flex text-sm font-semibold rounded-full">
                                    {{ permintaan.status.toUpperCase() }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Next Step Info -->
                <div v-if="!nextStep.completed" class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                    <div class="flex items-start">
                        <svg class="w-6 h-6 text-blue-600 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        <div class="flex-1">
                            <h4 class="font-semibold text-blue-900 mb-1">Tahap Berikutnya</h4>
                            <p class="text-blue-800 font-medium">{{ nextStep.tahapan }}</p>
                            <p class="text-sm text-blue-700 mt-1">{{ nextStep.description }}</p>
                            <p class="text-sm text-blue-600 mt-1">Penanggung jawab: <strong>{{ nextStep.responsible }}</strong></p>
                        </div>
                    </div>
                </div>

                <!-- Complete Timeline -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">
                            Timeline Lengkap
                        </h3>
                    </div>
                    
                    <div class="p-6">
                        <div class="flow-root">
                            <ul role="list" class="-mb-8">
                                <li v-for="(item, index) in completeTracking" :key="index">
                                    <div class="relative pb-8">
                                        <!-- Connecting Line -->
                                        <span 
                                            v-if="index !== completeTracking.length - 1" 
                                            class="absolute top-4 left-4 -ml-px h-full w-0.5"
                                            :class="item.completed ? 'bg-green-400' : 'bg-gray-300'"
                                            aria-hidden="true"
                                        ></span>
                                        
                                        <div class="relative flex space-x-3">
                                            <!-- Icon -->
                                            <div>
                                                <span 
                                                    :class="[
                                                        item.completed ? 'bg-green-500' : 'bg-gray-300',
                                                        'h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white'
                                                    ]"
                                                >
                                                    <svg 
                                                        v-if="item.completed"
                                                        class="h-5 w-5 text-white" 
                                                        fill="currentColor" 
                                                        viewBox="0 0 20 20"
                                                    >
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    <svg 
                                                        v-else
                                                        class="h-5 w-5 text-gray-500" 
                                                        fill="none" 
                                                        stroke="currentColor" 
                                                        viewBox="0 0 24 24"
                                                    >
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                </span>
                                            </div>
                                            
                                            <!-- Content -->
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center justify-between">
                                                    <div class="flex-1">
                                                        <p :class="item.completed ? 'text-gray-900 font-medium' : 'text-gray-500'">
                                                            <span class="text-sm font-semibold text-gray-400 mr-2">{{ item.step }}.</span>
                                                            {{ item.tahapan }}
                                                        </p>
                                                        <p class="text-sm text-gray-500 mt-0.5">{{ item.description }}</p>
                                                        <p class="text-sm text-gray-500 mt-0.5">
                                                            <span class="text-gray-400">Penanggung jawab:</span> {{ item.responsible }}
                                                        </p>
                                                    </div>
                                                    
                                                    <div class="text-right flex-shrink-0 ml-4">
                                                        <span 
                                                            :class="{
                                                                'bg-green-100 text-green-800': item.completed,
                                                                'bg-gray-100 text-gray-600': !item.completed,
                                                            }"
                                                            class="px-2.5 py-0.5 rounded-full text-xs font-medium"
                                                        >
                                                            {{ item.status }}
                                                        </span>
                                                        <p v-if="item.tanggal" class="text-xs text-gray-500 mt-1">
                                                            {{ formatDate(item.tanggal) }}
                                                        </p>
                                                    </div>
                                                </div>
                                                
                                                <div v-if="item.keterangan && item.completed" class="mt-2">
                                                    <p class="text-sm text-gray-600 bg-gray-50 rounded px-3 py-2">
                                                        {{ item.keterangan }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
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
                                <dd class="mt-1 text-sm text-gray-900">#{{ permintaan.permintaan_id }}</dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Bidang</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ permintaan.bidang }}</dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Tanggal Permintaan</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ formatDate(permintaan.tanggal_permintaan) }}</dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Pemohon</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ permintaan.user?.nama || '-' }}</dd>
                            </div>
                            
                            <div class="sm:col-span-2">
                                <dt class="text-sm font-medium text-gray-500 mb-2">Deskripsi</dt>
                                <dd class="mt-1 text-sm text-gray-900 bg-gray-50 p-4 rounded-lg whitespace-pre-line">
                                    {{ permintaan.deskripsi }}
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

const props = defineProps({
    permintaan: Object,
    completeTracking: Array,
    completedSteps: Array,
    pendingSteps: Array,
    nextStep: Object,
    progress: Number,
    userLogin: Object,
});

const formatDate = (date) => {
    if (!date) return '-';
    return new Date(date).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
};
</script>
