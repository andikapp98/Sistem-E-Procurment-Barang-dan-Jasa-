<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Riwayat Keputusan Direktur
                </h2>
                <Link :href="route('direktur.dashboard')" class="text-sm text-gray-600 hover:text-gray-900">
                    ← Kembali ke Dashboard
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                
                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="text-sm font-medium text-gray-500">Total Diproses</div>
                            <div class="mt-2 text-3xl font-semibold text-gray-900">{{ stats.total }}</div>
                        </div>
                    </div>
                    <div class="bg-green-50 overflow-hidden shadow-sm sm:rounded-lg border border-green-200">
                        <div class="p-6">
                            <div class="text-sm font-medium text-green-800">Disetujui</div>
                            <div class="mt-2 text-3xl font-semibold text-green-900">{{ stats.approved }}</div>
                        </div>
                    </div>
                    <div class="bg-red-50 overflow-hidden shadow-sm sm:rounded-lg border border-red-200">
                        <div class="p-6">
                            <div class="text-sm font-medium text-red-800">Ditolak</div>
                            <div class="mt-2 text-3xl font-semibold text-red-900">{{ stats.rejected }}</div>
                        </div>
                    </div>
                    <div class="bg-orange-50 overflow-hidden shadow-sm sm:rounded-lg border border-orange-200">
                        <div class="p-6">
                            <div class="text-sm font-medium text-orange-800">Revisi</div>
                            <div class="mt-2 text-3xl font-semibold text-orange-900">{{ stats.revision }}</div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Cari</label>
                                <input v-model="form.search" type="text" 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    placeholder="ID atau deskripsi...">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Bidang</label>
                                <input v-model="form.bidang" type="text" 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    placeholder="Semua bidang">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <select v-model="form.status" 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Semua Status</option>
                                    <option value="proses">Proses</option>
                                    <option value="ditolak">Ditolak</option>
                                    <option value="revisi">Revisi</option>
                                </select>
                            </div>
                            <div class="flex items-end gap-2">
                                <button @click="applyFilters" 
                                    class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                    Filter
                                </button>
                                <button @click="resetFilters" 
                                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                                    Reset
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
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
                                        Keputusan
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Tanggal
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status Saat Ini
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="permintaan in permintaans.data" :key="permintaan.permintaan_id" 
                                    class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        #{{ permintaan.permintaan_id }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ permintaan.bidang }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        <div class="max-w-xs truncate">
                                            {{ permintaan.deskripsi }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span :class="{
                                            'bg-green-100 text-green-800': permintaan.direktur_decision_class === 'approved',
                                            'bg-red-100 text-red-800': permintaan.direktur_decision_class === 'rejected',
                                            'bg-orange-100 text-orange-800': permintaan.direktur_decision_class === 'revision',
                                            'bg-gray-100 text-gray-800': !permintaan.direktur_decision_class || permintaan.direktur_decision_class === 'unknown'
                                        }" class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full">
                                            {{ permintaan.direktur_decision || '-' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ permintaan.direktur_date ? new Date(permintaan.direktur_date).toLocaleDateString('id-ID') : '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span :class="{
                                            'bg-yellow-100 text-yellow-800': permintaan.status === 'proses',
                                            'bg-green-100 text-green-800': permintaan.status === 'disetujui',
                                            'bg-red-100 text-red-800': permintaan.status === 'ditolak',
                                            'bg-blue-100 text-blue-800': permintaan.status === 'revisi'
                                        }" class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full">
                                            {{ permintaan.status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <Link :href="route('direktur.tracking', permintaan.permintaan_id)"
                                            class="text-blue-600 hover:text-blue-900 mr-3">
                                            Tracking
                                        </Link>
                                        <button @click="showDetail(permintaan)" 
                                            class="text-gray-600 hover:text-gray-900">
                                            Detail
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-700">
                                Menampilkan {{ permintaans.from ?? 0 }} - {{ permintaans.to ?? 0 }} dari {{ permintaans.total }} data
                            </div>
                            <div class="flex gap-2">
                                <template v-for="link in permintaans.links" :key="link.label">
                                    <Link v-if="link.url"
                                        :href="link.url"
                                        v-html="link.label"
                                        :class="{
                                            'bg-blue-600 text-white': link.active,
                                            'bg-white text-gray-700 hover:bg-gray-50': !link.active
                                        }"
                                        class="px-3 py-2 text-sm border border-gray-300 rounded-md">
                                    </Link>
                                    <span v-else
                                        v-html="link.label"
                                        class="px-3 py-2 text-sm border border-gray-300 rounded-md bg-gray-100 text-gray-400 cursor-not-allowed">
                                    </span>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail Modal -->
        <Modal :show="showDetailModal" @close="showDetailModal = false">
            <div class="p-6" v-if="selectedPermintaan">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    Detail Keputusan - Permintaan #{{ selectedPermintaan.permintaan_id }}
                </h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Bidang</label>
                        <p class="mt-1 text-sm text-gray-900">{{ selectedPermintaan.bidang }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
                        <p class="mt-1 text-sm text-gray-900 bg-gray-50 p-3 rounded-lg whitespace-pre-line">
                            {{ selectedPermintaan.deskripsi }}
                        </p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Keputusan Direktur</label>
                        <span :class="{
                            'bg-green-100 text-green-800': selectedPermintaan.direktur_decision_class === 'approved',
                            'bg-red-100 text-red-800': selectedPermintaan.direktur_decision_class === 'rejected',
                            'bg-orange-100 text-orange-800': selectedPermintaan.direktur_decision_class === 'revision',
                            'bg-gray-100 text-gray-800': !selectedPermintaan.direktur_decision_class || selectedPermintaan.direktur_decision_class === 'unknown'
                        }" class="mt-1 px-3 py-1 inline-flex text-sm font-semibold rounded-full">
                            {{ selectedPermintaan.direktur_decision || '-' }}
                        </span>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Catatan Direktur</label>
                        <p class="mt-1 text-sm text-gray-900 bg-gray-50 p-3 rounded-lg">
                            {{ selectedPermintaan.direktur_notes || 'Tidak ada catatan' }}
                        </p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tanggal Keputusan</label>
                        <p class="mt-1 text-sm text-gray-900">
                            {{ selectedPermintaan.direktur_date ? new Date(selectedPermintaan.direktur_date).toLocaleString('id-ID') : '-' }}
                        </p>
                    </div>
                </div>
                
                <div class="mt-6 flex justify-end gap-3">
                    <button @click="showDetailModal = false" 
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                        Tutup
                    </button>
                    <Link :href="route('direktur.tracking', selectedPermintaan.permintaan_id)"
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Lihat Tracking Lengkap
                    </Link>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, reactive } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Modal from '@/Components/Modal.vue';
import { Link, router } from '@inertiajs/vue3';

const props = defineProps({
    permintaans: Object,
    stats: Object,
    userLogin: Object,
    filters: Object,
});

const form = reactive({
    search: props.filters.search || '',
    bidang: props.filters.bidang || '',
    status: props.filters.status || '',
});

const showDetailModal = ref(false);
const selectedPermintaan = ref(null);

const applyFilters = () => {
    router.get(route('direktur.approved'), form, {
        preserveState: true,
        preserveScroll: true,
    });
};

const resetFilters = () => {
    form.search = '';
    form.bidang = '';
    form.status = '';
    applyFilters();
};

const showDetail = (permintaan) => {
    selectedPermintaan.value = permintaan;
    showDetailModal.value = true;
};
</script>
