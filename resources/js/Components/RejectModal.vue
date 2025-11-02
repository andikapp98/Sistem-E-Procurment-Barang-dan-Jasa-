<template>
    <!-- Modal Reject -->
    <div v-if="show" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" @click.self="$emit('close')">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Tolak Permintaan</h3>
                    <button @click="$emit('close')" class="text-gray-400 hover:text-gray-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <div class="px-7 py-3 space-y-4">
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <div class="flex">
                            <svg class="w-5 h-5 text-red-600 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <div>
                                <h4 class="font-semibold text-red-900 text-sm">Perhatian!</h4>
                                <p class="text-xs text-red-700 mt-1">
                                    Penolakan bersifat permanen. Pastikan Anda memberikan alasan yang jelas.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Alasan Penolakan <span class="text-red-500">*</span>
                        </label>
                        <textarea
                            v-model="alasan"
                            rows="4"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500"
                            :class="{ 'border-red-500': errors.alasan }"
                            placeholder="Masukkan alasan penolakan (minimal 5 karakter)..."
                        ></textarea>
                        <p v-if="alasan && alasan.length < 5" class="text-xs text-red-500 mt-1">
                            Minimal 5 karakter ({{ alasan.length }}/5)
                        </p>
                        <p v-if="errors.alasan" class="text-xs text-red-500 mt-1">
                            {{ errors.alasan }}
                        </p>
                    </div>
                </div>
                
                <div class="flex gap-3 px-4 py-3 mt-4">
                    <button
                        @click="handleReject"
                        :disabled="!alasan || alasan.length < 5"
                        class="flex-1 px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 font-medium transition-colors duration-150 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        Tolak Permintaan
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

const emit = defineEmits(['close', 'reject']);

const alasan = ref('');

const handleReject = () => {
    if (alasan.value && alasan.value.length >= 5) {
        emit('reject', alasan.value);
        alasan.value = '';
    }
};
</script>
