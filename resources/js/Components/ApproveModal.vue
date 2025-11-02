<template>
    <!-- Modal Approve -->
    <div v-if="show" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" @click.self="$emit('close')">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Setujui Permintaan</h3>
                    <button @click="$emit('close')" class="text-gray-400 hover:text-gray-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <div class="px-7 py-3 space-y-4">
                    <p class="text-sm text-gray-700">
                        Apakah Anda yakin ingin menyetujui permintaan ini?
                    </p>
                    
                    <!-- Info Klasifikasi (jika ada) -->
                    <div v-if="klasifikasi && kabidTujuan" class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <h4 class="font-semibold text-blue-900 text-sm mb-2">Informasi Routing:</h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex items-start">
                                <svg class="w-4 h-4 text-blue-600 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                                <div>
                                    <span class="text-gray-600">Klasifikasi:</span>
                                    <span class="font-medium text-blue-900 ml-1">
                                        {{ formatKlasifikasi(klasifikasi) }}
                                    </span>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <svg class="w-4 h-4 text-blue-600 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                                <div>
                                    <span class="text-gray-600">Akan diteruskan ke:</span>
                                    <span class="font-medium text-blue-900 ml-1">
                                        {{ kabidTujuan }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Catatan Optional -->
                    <div v-if="showCatatanField">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Catatan (Opsional)
                        </label>
                        <textarea
                            v-model="catatan"
                            rows="3"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="Tambahkan catatan jika diperlukan..."
                        ></textarea>
                    </div>
                    
                    <p v-if="kabidTujuan" class="text-xs text-gray-500 italic">
                        Permintaan akan otomatis dikirim ke {{ kabidTujuan }} untuk review dan persetujuan selanjutnya.
                    </p>
                </div>
                
                <div class="flex gap-3 px-4 py-3 mt-4">
                    <button
                        @click="handleApprove"
                        class="flex-1 px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 font-medium transition-colors duration-150"
                    >
                        Ya, Setujui
                    </button>
                    <button
                        @click="$emit('close')"
                        class="flex-1 px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 font-medium transition-colors duration-150"
                    >
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    klasifikasi: {
        type: String,
        default: null,
    },
    kabidTujuan: {
        type: String,
        default: null,
    },
    showCatatanField: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['close', 'approve']);

const catatan = ref('');

// Helper untuk format klasifikasi
const formatKlasifikasi = (klasifikasi) => {
    const mapping = {
        'medis': 'Medis',
        'penunjang_medis': 'Penunjang Medis',
        'non_medis': 'Non Medis'
    };
    return mapping[klasifikasi] || klasifikasi || '-';
};

const handleApprove = () => {
    emit('approve', catatan.value);
    catatan.value = '';
};
</script>
