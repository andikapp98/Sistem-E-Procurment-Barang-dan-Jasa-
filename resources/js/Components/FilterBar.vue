<template>
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-semibold text-gray-700">Filter & Pencarian</h3>
            <button 
                v-if="hasActiveFilters"
                @click="clearFilters"
                class="text-xs text-gray-600 hover:text-gray-900">
                Reset Filter
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Search -->
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">
                    Cari
                </label>
                <input 
                    type="text"
                    v-model="filters.search"
                    @input="applyFilters"
                    placeholder="ID atau Deskripsi..."
                    class="w-full text-sm rounded-md border-gray-300 focus:border-purple-500 focus:ring-purple-500">
            </div>

            <!-- Status -->
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">
                    Status
                </label>
                <select 
                    v-model="filters.status"
                    @change="applyFilters"
                    class="w-full text-sm rounded-md border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                    <option value="">Semua Status</option>
                    <option value="diajukan">Diajukan</option>
                    <option value="proses">Proses</option>
                    <option value="disetujui">Disetujui</option>
                    <option value="ditolak">Ditolak</option>
                    <option value="revisi">Revisi</option>
                </select>
            </div>

            <!-- Bidang (if applicable) -->
            <div v-if="showBidangFilter">
                <label class="block text-xs font-medium text-gray-700 mb-1">
                    Bidang
                </label>
                <select 
                    v-model="filters.bidang"
                    @change="applyFilters"
                    class="w-full text-sm rounded-md border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                    <option value="">Semua Bidang</option>
                    <option value="Instalasi Farmasi">Instalasi Farmasi</option>
                    <option value="Instalasi IGD">Instalasi IGD</option>
                    <option value="Instalasi Radiologi">Instalasi Radiologi</option>
                    <option value="Instalasi Laboratorium">Instalasi Laboratorium</option>
                    <option value="Instalasi Gizi">Instalasi Gizi</option>
                </select>
            </div>

            <!-- Tanggal -->
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">
                    Dari Tanggal
                </label>
                <input 
                    type="date"
                    v-model="filters.tanggal_dari"
                    @change="applyFilters"
                    class="w-full text-sm rounded-md border-gray-300 focus:border-purple-500 focus:ring-purple-500">
            </div>

            <div v-if="showDateRange">
                <label class="block text-xs font-medium text-gray-700 mb-1">
                    Sampai Tanggal
                </label>
                <input 
                    type="date"
                    v-model="filters.tanggal_sampai"
                    @change="applyFilters"
                    class="w-full text-sm rounded-md border-gray-300 focus:border-purple-500 focus:ring-purple-500">
            </div>
        </div>

        <!-- Active Filters Display -->
        <div v-if="hasActiveFilters" class="mt-4 flex flex-wrap gap-2">
            <span v-if="filters.search" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                Cari: "{{ filters.search }}"
                <button @click="filters.search = ''; applyFilters()" class="ml-2">×</button>
            </span>
            <span v-if="filters.status" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                Status: {{ filters.status }}
                <button @click="filters.status = ''; applyFilters()" class="ml-2">×</button>
            </span>
            <span v-if="filters.bidang" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                Bidang: {{ filters.bidang }}
                <button @click="filters.bidang = ''; applyFilters()" class="ml-2">×</button>
            </span>
            <span v-if="filters.tanggal_dari" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                Dari: {{ filters.tanggal_dari }}
                <button @click="filters.tanggal_dari = ''; applyFilters()" class="ml-2">×</button>
            </span>
            <span v-if="filters.tanggal_sampai" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                Sampai: {{ filters.tanggal_sampai }}
                <button @click="filters.tanggal_sampai = ''; applyFilters()" class="ml-2">×</button>
            </span>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    routeName: {
        type: String,
        required: true,
    },
    initialFilters: {
        type: Object,
        default: () => ({})
    },
    showBidangFilter: {
        type: Boolean,
        default: false,
    },
    showDateRange: {
        type: Boolean,
        default: true,
    }
});

const filters = ref({
    search: props.initialFilters.search || '',
    status: props.initialFilters.status || '',
    bidang: props.initialFilters.bidang || '',
    tanggal_dari: props.initialFilters.tanggal_dari || '',
    tanggal_sampai: props.initialFilters.tanggal_sampai || '',
});

const hasActiveFilters = computed(() => {
    return filters.value.search || 
           filters.value.status || 
           filters.value.bidang || 
           filters.value.tanggal_dari || 
           filters.value.tanggal_sampai;
});

let debounceTimer = null;

const applyFilters = () => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        const params = {};
        
        if (filters.value.search) params.search = filters.value.search;
        if (filters.value.status) params.status = filters.value.status;
        if (filters.value.bidang) params.bidang = filters.value.bidang;
        if (filters.value.tanggal_dari) params.tanggal_dari = filters.value.tanggal_dari;
        if (filters.value.tanggal_sampai) params.tanggal_sampai = filters.value.tanggal_sampai;

        console.log('Applying filters:', params);
        console.log('Route name:', props.routeName);

        router.get(route(props.routeName), params, {
            preserveState: true,
            preserveScroll: true,
        });
    }, 300);
};

const clearFilters = () => {
    filters.value = {
        search: '',
        status: '',
        bidang: '',
        tanggal_dari: '',
        tanggal_sampai: '',
    };
    applyFilters();
};
</script>
