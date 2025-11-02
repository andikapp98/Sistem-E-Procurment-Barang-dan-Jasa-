<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Dashboard Kepala Instalasi
                </h2>
                <div class="text-sm text-gray-600">
                    {{ new Date().toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) }}
                </div>
            </div>
        </template>

        <div class="py-6 md:py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Welcome Section with Enhanced Design -->
                <div class="bg-white overflow-hidden shadow-lg sm:rounded-xl mb-6 transform hover:scale-[1.01] transition-transform duration-200">
                    <div class="p-8 bg-gradient-to-br from-[#028174] via-[#02967e] to-[#03a089] text-white relative overflow-hidden">
                        <!-- Decorative elements -->
                        <div class="absolute top-0 right-0 w-64 h-64 bg-white opacity-5 rounded-full -mr-32 -mt-32"></div>
                        <div class="absolute bottom-0 left-0 w-48 h-48 bg-white opacity-5 rounded-full -ml-24 -mb-24"></div>
                        
                        <div class="relative z-10">
                            <div class="flex items-center gap-4 mb-3">
                                <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center backdrop-blur-sm">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-3xl font-bold mb-1">
                                        Selamat Datang, {{ userLogin.nama }}
                                    </h3>
                                    <div class="flex items-center gap-2 text-sm opacity-95">
                                        <span class="px-3 py-1 bg-white bg-opacity-20 rounded-full backdrop-blur-sm">
                                            {{ userLogin.jabatan }}
                                        </span>
                                        <span>•</span>
                                        <span class="px-3 py-1 bg-white bg-opacity-20 rounded-full backdrop-blur-sm">
                                            {{ userLogin.unit_kerja }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistics Cards with Enhanced Design -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-6">
                    <!-- Total Permintaan -->
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 overflow-hidden shadow-md sm:rounded-xl hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border border-blue-200">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs text-blue-600 font-semibold uppercase tracking-wide">Total</p>
                                </div>
                            </div>
                            <h4 class="text-blue-700 text-sm font-semibold mb-2">
                                Total Permintaan
                            </h4>
                            <p class="text-4xl font-bold text-blue-900 mb-1">
                                {{ stats.total }}
                            </p>
                            <p class="text-xs text-blue-600">Semua status</p>
                        </div>
                    </div>

                    <!-- Menunggu Review -->
                    <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 overflow-hidden shadow-md sm:rounded-xl hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border border-yellow-200">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-14 h-14 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl flex items-center justify-center shadow-lg">
                                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs text-yellow-600 font-semibold uppercase tracking-wide">Pending</p>
                                </div>
                            </div>
                            <h4 class="text-yellow-700 text-sm font-semibold mb-2">
                                Menunggu Review
                            </h4>
                            <p class="text-4xl font-bold text-yellow-900 mb-1">
                                {{ stats.diajukan }}
                            </p>
                            <p class="text-xs text-yellow-600">Perlu persetujuan</p>
                        </div>
                    </div>

                    <!-- Dalam Proses -->
                    <div class="bg-gradient-to-br from-purple-50 to-purple-100 overflow-hidden shadow-md sm:rounded-xl hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border border-purple-200">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-14 h-14 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs text-purple-600 font-semibold uppercase tracking-wide">Progress</p>
                                </div>
                            </div>
                            <h4 class="text-purple-700 text-sm font-semibold mb-2">
                                Dalam Proses
                            </h4>
                            <p class="text-4xl font-bold text-purple-900 mb-1">
                                {{ stats.proses }}
                            </p>
                            <p class="text-xs text-purple-600">Sedang diproses</p>
                        </div>
                    </div>

                    <!-- Disetujui -->
                    <div class="bg-gradient-to-br from-green-50 to-green-100 overflow-hidden shadow-md sm:rounded-xl hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border border-green-200">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-14 h-14 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center shadow-lg">
                                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs text-green-600 font-semibold uppercase tracking-wide">Approved</p>
                                </div>
                            </div>
                            <h4 class="text-green-700 text-sm font-semibold mb-2">
                                Disetujui
                            </h4>
                            <p class="text-4xl font-bold text-green-900 mb-1">
                                {{ stats.disetujui }}
                            </p>
                            <p class="text-xs text-green-600">Telah disetujui</p>
                        </div>
                    </div>
                </div>

                <!-- Recent Permintaan Table with Enhanced Design -->
                <div class="bg-white overflow-hidden shadow-lg sm:rounded-xl mb-6">
                    <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                            <div>
                                <h3 class="text-xl font-bold text-gray-900 mb-1">
                                    Permintaan Terbaru
                                </h3>
                                <p class="text-sm text-gray-600">Daftar permintaan yang perlu direview</p>
                            </div>
                            <Link
                                :href="route('kepala-instalasi.index')"
                                class="inline-flex items-center px-4 py-2 bg-[#028174] hover:bg-[#03a089] text-white font-semibold text-sm rounded-lg transition-colors duration-200 shadow-sm hover:shadow-md"
                            >
                                <span>Lihat Semua</span>
                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </Link>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                        ID
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                        Deskripsi
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                        Bidang
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                        Tanggal
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                        Tracking
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr
                                    v-for="permintaan in recentPermintaans"
                                    :key="permintaan.permintaan_id"
                                    class="hover:bg-gray-50 transition-colors duration-150"
                                >
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm font-bold text-gray-900">
                                            #{{ permintaan.permintaan_id }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="max-w-xs">
                                            <p class="text-sm text-gray-900 font-medium truncate">
                                                {{ permintaan.deskripsi }}
                                            </p>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm text-gray-700">
                                            {{ permintaan.bidang || "-" }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm text-gray-600">
                                            {{ formatDate(permintaan.tanggal_permintaan) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            :class="getStatusClass(permintaan.status)"
                                            class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full shadow-sm"
                                        >
                                            {{ getStatusLabel(permintaan.status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            :class="getTrackingClass(permintaan.tracking_status)"
                                            class="px-3 py-1 text-xs font-bold rounded-lg shadow-sm"
                                        >
                                            {{ permintaan.tracking_status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <Link
                                            :href="route('kepala-instalasi.show', permintaan.permintaan_id)"
                                            class="inline-flex items-center px-3 py-1.5 bg-[#028174] hover:bg-[#03a089] text-white font-semibold rounded-lg transition-colors duration-200 shadow-sm hover:shadow-md"
                                        >
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                            Detail
                                        </Link>
                                    </td>
                                </tr>
                                <tr v-if="recentPermintaans.length === 0">
                                    <td colspan="7" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            <p class="text-gray-500 font-medium">Tidak ada permintaan saat ini</p>
                                            <p class="text-gray-400 text-sm mt-1">Permintaan baru akan muncul di sini</p>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Quick Actions with Enhanced Design -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6">
                    <div class="bg-white overflow-hidden shadow-md sm:rounded-xl hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border-l-4 border-[#028174]">
                        <Link :href="route('kepala-instalasi.index')" class="block p-6">
                            <div class="flex items-center">
                                <div class="w-14 h-14 bg-gradient-to-br from-[#028174] to-[#03a089] rounded-xl flex items-center justify-center mr-4 shadow-lg">
                                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-900 text-base mb-1">
                                        Review Permintaan
                                    </h4>
                                    <p class="text-sm text-gray-600">
                                        Lihat dan proses permintaan
                                    </p>
                                </div>
                            </div>
                        </Link>
                    </div>

                    <div class="bg-white overflow-hidden shadow-md sm:rounded-xl hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border-l-4 border-blue-500">
                        <Link :href="route('permintaan.index')" class="block p-6">
                            <div class="flex items-center">
                                <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center mr-4 shadow-lg">
                                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-900 text-base mb-1">
                                        Semua Permintaan
                                    </h4>
                                    <p class="text-sm text-gray-600">
                                        Lihat riwayat lengkap
                                    </p>
                                </div>
                            </div>
                        </Link>
                    </div>

                    <div class="bg-white overflow-hidden shadow-md sm:rounded-xl hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border-l-4 border-gray-500">
                        <Link :href="route('profile.edit')" class="block p-6">
                            <div class="flex items-center">
                                <div class="w-14 h-14 bg-gradient-to-br from-gray-500 to-gray-600 rounded-xl flex items-center justify-center mr-4 shadow-lg">
                                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-900 text-base mb-1">
                                        Profil Saya
                                    </h4>
                                    <p class="text-sm text-gray-600">
                                        Kelola akun Anda
                                    </p>
                                </div>
                            </div>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Link } from "@inertiajs/vue3";

const props = defineProps({
    stats: Object,
    recentPermintaans: Array,
    userLogin: Object,
});

const formatDate = (dateString) => {
    if (!dateString) return "-";
    const date = new Date(dateString);
    return date.toLocaleDateString("id-ID", {
        year: "numeric",
        month: "short",
        day: "numeric",
    });
};

const getStatusClass = (status) => {
    const classes = {
        diajukan: "bg-yellow-100 text-yellow-800",
        proses: "bg-blue-100 text-blue-800",
        disetujui: "bg-green-100 text-green-800",
        ditolak: "bg-red-100 text-red-800",
        revisi: "bg-orange-100 text-orange-800",
    };
    return classes[status] || "bg-gray-100 text-gray-800";
};

const getStatusLabel = (status) => {
    const labels = {
        diajukan: "Diajukan",
        proses: "Dalam Proses",
        disetujui: "Disetujui",
        ditolak: "Ditolak",
        revisi: "Perlu Revisi",
    };
    return labels[status] || status;
};

const getTrackingClass = (tracking) => {
    const classes = {
        Permintaan: "bg-gray-100 text-gray-700",
        "Nota Dinas": "bg-blue-100 text-blue-700",
        Disposisi: "bg-purple-100 text-purple-700",
        Perencanaan: "bg-indigo-100 text-indigo-700",
        KSO: "bg-pink-100 text-pink-700",
        Pengadaan: "bg-orange-100 text-orange-700",
        "Nota Penerimaan": "bg-teal-100 text-teal-700",
        "Serah Terima": "bg-green-100 text-green-700",
    };
    return classes[tracking] || "bg-gray-100 text-gray-700";
};
</script>
