<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    History Perencanaan - Permintaan #{{ permintaan.permintaan_id }}
                </h2>
                <Link :href="route('staff-perencanaan.show', permintaan.permintaan_id)" class="text-sm text-gray-600 hover:text-gray-900">
                    ← Kembali ke Detail
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                
                <!-- Informasi Permintaan -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            Informasi Permintaan
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-500">No. Permintaan</p>
                                <p class="font-semibold text-gray-900">{{ permintaan.permintaan_id }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Bidang Pemohon</p>
                                <p class="font-semibold text-gray-900">{{ permintaan.bidang || '-' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Status Saat Ini</p>
                                <span :class="{
                                    'bg-yellow-100 text-yellow-800': permintaan.status === 'proses',
                                    'bg-green-100 text-green-800': permintaan.status === 'disetujui',
                                    'bg-red-100 text-red-800': permintaan.status === 'ditolak',
                                    'bg-blue-100 text-blue-800': permintaan.status === 'pending',
                                }" class="px-3 py-1 inline-flex text-xs font-semibold rounded-full">
                                    {{ permintaan.status.toUpperCase() }}
                                </span>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">PIC Saat Ini</p>
                                <p class="font-semibold text-gray-900">{{ permintaan.pic_pimpinan || 'Staff Perencanaan' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Workflow Info -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                    <div class="flex items-start">
                        <svg class="w-6 h-6 text-blue-600 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        <div class="flex-1">
                            <h4 class="font-semibold text-blue-900 mb-1">Alur Proses Perencanaan</h4>
                            <p class="text-blue-800 text-sm">
                                Staff Perencanaan → Staff Pengadaan
                            </p>
                            <p class="text-sm text-blue-700 mt-2">
                                Setelah Staff Perencanaan menyelesaikan dokumen perencanaan (Nota Dinas, HPS, Spesifikasi Teknis), 
                                permintaan akan diteruskan ke Staff Pengadaan untuk proses pengadaan barang/jasa.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Activity History -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">
                            Riwayat Aktivitas
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">
                            Total aktivitas: {{ activityHistory.length }} aktivitas
                        </p>
                    </div>
                    
                    <div class="p-6">
                        <div v-if="activityHistory.length > 0" class="flow-root">
                            <ul role="list" class="-mb-8">
                                <li v-for="(activity, index) in activityHistory" :key="activity.id">
                                    <div class="relative pb-8">
                                        <!-- Connecting Line -->
                                        <span 
                                            v-if="index !== activityHistory.length - 1" 
                                            class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-300"
                                            aria-hidden="true"
                                        ></span>
                                        
                                        <div class="relative flex space-x-3">
                                            <!-- Icon -->
                                            <div>
                                                <span 
                                                    :class="getActivityColor(activity.action)"
                                                    class="h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white"
                                                >
                                                    <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                        <path v-if="activity.action.includes('create') || activity.action.includes('upload')" 
                                                            fill-rule="evenodd" 
                                                            d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" 
                                                            clip-rule="evenodd" 
                                                        />
                                                        <path v-else-if="activity.action.includes('update')" 
                                                            fill-rule="evenodd" 
                                                            d="M4 2a2 2 0 00-2 2v11a3 3 0 106 0V4a2 2 0 00-2-2H4zm1 14a1 1 0 100-2 1 1 0 000 2zm5-1.757l4.9-4.9a2 2 0 000-2.828L13.485 5.1a2 2 0 00-2.828 0L10 5.757v8.486zM16 18H9.071l6-6H16a2 2 0 012 2v2a2 2 0 01-2 2z" 
                                                            clip-rule="evenodd" 
                                                        />
                                                        <path v-else-if="activity.action.includes('delete')" 
                                                            fill-rule="evenodd" 
                                                            d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" 
                                                            clip-rule="evenodd" 
                                                        />
                                                        <path v-else-if="activity.action.includes('forward') || activity.action.includes('submit')" 
                                                            fill-rule="evenodd" 
                                                            d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" 
                                                            clip-rule="evenodd" 
                                                        />
                                                        <path v-else 
                                                            fill-rule="evenodd" 
                                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" 
                                                            clip-rule="evenodd" 
                                                        />
                                                    </svg>
                                                </span>
                                            </div>
                                            
                                            <!-- Content -->
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center justify-between">
                                                    <div class="flex-1">
                                                        <p class="text-sm font-semibold text-gray-900">
                                                            {{ activity.description || activity.action }}
                                                        </p>
                                                        <p class="text-xs text-gray-500 mt-1">
                                                            oleh: {{ activity.user?.name || 'System' }} 
                                                            <span v-if="activity.role" class="ml-1">({{ activity.role }})</span>
                                                        </p>
                                                    </div>
                                                    <div class="flex-shrink-0 text-right">
                                                        <p class="text-xs text-gray-500">
                                                            {{ formatDate(activity.created_at) }}
                                                        </p>
                                                        <p class="text-xs text-gray-400">
                                                            {{ formatTime(activity.created_at) }}
                                                        </p>
                                                    </div>
                                                </div>
                                                
                                                <!-- Additional Info -->
                                                <div v-if="activity.module" class="mt-2">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                        {{ activity.module }}
                                                    </span>
                                                    <span v-if="activity.related_type" class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                        {{ activity.related_type }} #{{ activity.related_id }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        
                        <!-- Empty State -->
                        <div v-else class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada aktivitas</h3>
                            <p class="mt-1 text-sm text-gray-500">Riwayat aktivitas akan muncul di sini.</p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center">
                            <Link 
                                :href="route('staff-perencanaan.show', permintaan.permintaan_id)" 
                                class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                            >
                                ← Kembali ke Detail
                            </Link>
                            
                            <Link 
                                :href="route('staff-perencanaan.index')" 
                                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150"
                            >
                                Lihat Semua Permintaan
                            </Link>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    permintaan: Object,
    activityHistory: Array,
    userLogin: Object,
});

// Helper function to get activity color based on action type
const getActivityColor = (action) => {
    if (!action) return 'bg-gray-500';
    
    const actionLower = action.toLowerCase();
    
    if (actionLower.includes('create') || actionLower.includes('upload')) {
        return 'bg-green-500';
    } else if (actionLower.includes('update') || actionLower.includes('edit')) {
        return 'bg-blue-500';
    } else if (actionLower.includes('delete') || actionLower.includes('reject')) {
        return 'bg-red-500';
    } else if (actionLower.includes('approve') || actionLower.includes('forward') || actionLower.includes('submit')) {
        return 'bg-purple-500';
    } else if (actionLower.includes('view') || actionLower.includes('show')) {
        return 'bg-yellow-500';
    } else {
        return 'bg-gray-500';
    }
};

// Format date
const formatDate = (date) => {
    if (!date) return '-';
    const d = new Date(date);
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return d.toLocaleDateString('id-ID', options);
};

// Format time
const formatTime = (date) => {
    if (!date) return '-';
    const d = new Date(date);
    const options = { hour: '2-digit', minute: '2-digit', second: '2-digit' };
    return d.toLocaleTimeString('id-ID', options);
};
</script>
