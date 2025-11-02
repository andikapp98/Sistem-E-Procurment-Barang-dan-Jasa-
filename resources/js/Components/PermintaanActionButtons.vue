<template>
    <!-- Action Buttons Section -->
    <div v-if="canTakeAction" class="bg-white overflow-hidden shadow-sm rounded-lg">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Tindakan</h3>
            <p class="text-sm text-gray-600 mt-1">Pilih tindakan yang akan dilakukan untuk permintaan ini</p>
        </div>
        <div class="p-6">
            <div class="flex flex-wrap gap-3">
                <!-- Approve Button -->
                <button
                    @click="$emit('show-approve')"
                    class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 font-medium transition-colors duration-150"
                >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Setujui
                </button>

                <!-- Request Revisi Button -->
                <button
                    @click="$emit('show-revisi')"
                    class="inline-flex items-center px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700 font-medium transition-colors duration-150"
                >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Minta Revisi
                </button>

                <!-- Reject Button -->
                <button
                    @click="$emit('show-reject')"
                    class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 font-medium transition-colors duration-150"
                >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Tolak
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    canTakeAction: {
        type: Boolean,
        default: false,
    },
});

defineEmits(['show-approve', 'show-revisi', 'show-reject']);
</script>
