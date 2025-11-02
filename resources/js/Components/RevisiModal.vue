<template>
    <!-- Modal Revisi -->
    <div v-if="show" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" @click.self="$emit('close')">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Minta Revisi</h3>
                    <button @click="$emit('close')" class="text-gray-400 hover:text-gray-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <div class="px-7 py-3 space-y-4">
                    <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                        <div class="flex">
                            <svg class="w-5 h-5 text-orange-600 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div>
                                <h4 class="font-semibold text-orange-900 text-sm">Informasi</h4>
                                <p class="text-xs text-orange-700 mt-1">
                                    Permintaan akan dikembalikan ke pemohon untuk diperbaiki sesuai catatan Anda.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Catatan Revisi <span class="text-red-500">*</span>
                        </label>
                        <textarea
                            v-model="catatanRevisi"
                            rows="4"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500"
                            :class="{ 'border-orange-500': errors.catatan_revisi }"
                            placeholder="Jelaskan apa yang perlu diperbaiki (minimal 5 karakter)..."
                        ></textarea>
                        <p v-if="catatanRevisi && catatanRevisi.length < 5" class="text-xs text-orange-500 mt-1">
                            Minimal 5 karakter ({{ catatanRevisi.length }}/5)
                        </p>
                        <p v-if="errors.catatan_revisi" class="text-xs text-red-500 mt-1">
                            {{ errors.catatan_revisi }}
                        </p>
                    </div>
                    
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                        <p class="text-xs text-blue-700">
                            <strong>Tips:</strong> Berikan catatan yang spesifik dan jelas agar pemohon dapat memperbaiki dengan tepat.
                        </p>
                    </div>
                </div>
                
                <div class="flex gap-3 px-4 py-3 mt-4">
                    <button
                        @click="handleRevisi"
                        :disabled="!catatanRevisi || catatanRevisi.length < 5"
                        class="flex-1 px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700 font-medium transition-colors duration-150 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        Kirim Revisi
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

defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    errors: {
        type: Object,
        default: () => ({}),
    },
});

const emit = defineEmits(['close', 'revisi']);

const catatanRevisi = ref('');

const handleRevisi = () => {
    if (catatanRevisi.value && catatanRevisi.value.length >= 5) {
        emit('revisi', catatanRevisi.value);
        catatanRevisi.value = '';
    }
};
</script>
