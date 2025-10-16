<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div
        class="flex min-h-screen flex-col items-center bg-gray-100 pt-6 sm:justify-center sm:pt-0"
    >
        <div class="flex flex-col items-center justify-center mb-4">
            <!-- Logo -->
            <img
                src="/images/logorsis.png"
                alt="RSUD Ibnu Sina Kabupaten Gresik"
                class="h-20 w-auto object-contain drop-shadow-md"
            />

            <!-- Tulisan di bawah logo -->
            <h2
                class="text-lg font-semibold text-[#028174] mt-2 tracking-wide text-center"
            >
                Sistem e-Procurement
            </h2>
        </div>

        <div
            class="mt-3 w-full overflow-hidden bg-white px-5 py-4 shadow-md sm:max-w-md sm:rounded-lg"
        >
            <slot />
        </div>
    </div>
</template>
