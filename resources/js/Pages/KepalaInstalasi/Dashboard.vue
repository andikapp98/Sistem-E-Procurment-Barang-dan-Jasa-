<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard Kepala Instalasi
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Welcome Section -->
                <div
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6"
                >
                    <div
                        class="p-6 bg-gradient-to-r from-[#028174] to-[#03a089] text-white"
                    >
                        <h3 class="text-2xl font-bold mb-2">
                            Selamat Datang, {{ userLogin.nama }}
                        </h3>
                        <p class="text-sm opacity-90">
                            {{ userLogin.jabatan }} - {{ userLogin.unit_kerja }}
                        </p>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6"
                >
                    <!-- Total Permintaan -->
                    <div
                        class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition-shadow"
                    >
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div
                                    class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center"
                                >
                                    <svg
                                        class="w-6 h-6 text-blue-600"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                        ></path>
                                    </svg>
                                </div>
                            </div>
                            <h4 class="text-gray-600 text-sm font-medium mb-1">
                                Total Permintaan
                            </h4>
                            <p class="text-3xl font-bold text-gray-900">
                                {{ stats.total }}
                            </p>
                        </div>
                    </div>

                    <!-- Menunggu Review -->
                    <div
                        class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition-shadow"
                    >
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div
                                    class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center"
                                >
                                    <svg
                                        class="w-6 h-6 text-yellow-600"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                        ></path>
                                    </svg>
                                </div>
                            </div>
                            <h4 class="text-gray-600 text-sm font-medium mb-1">
                                Menunggu Review
                            </h4>
                            <p class="text-3xl font-bold text-yellow-600">
                                {{ stats.diajukan }}
                            </p>
                        </div>
                    </div>

                    <!-- Dalam Proses -->
                    <div
                        class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition-shadow"
                    >
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div
                                    class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center"
                                >
                                    <svg
                                        class="w-6 h-6 text-purple-600"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
                                        ></path>
                                    </svg>
                                </div>
                            </div>
                            <h4 class="text-gray-600 text-sm font-medium mb-1">
                                Dalam Proses
                            </h4>
                            <p class="text-3xl font-bold text-purple-600">
                                {{ stats.proses }}
                            </p>
                        </div>
                    </div>

                    <!-- Disetujui -->
                    <div
                        class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition-shadow"
                    >
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div
                                    class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center"
                                >
                                    <svg
                                        class="w-6 h-6 text-green-600"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                        ></path>
                                    </svg>
                                </div>
                            </div>
                            <h4 class="text-gray-600 text-sm font-medium mb-1">
                                Disetujui
                            </h4>
                            <p class="text-3xl font-bold text-green-600">
                                {{ stats.disetujui }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Recent Permintaan Table -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-lg font-semibold text-gray-900">
                                Permintaan Terbaru
                            </h3>
                            <Link
                                :href="route('kepala-instalasi.index')"
                                class="text-[#028174] hover:text-[#03a089] font-medium text-sm"
                            >
                                Lihat Semua →
                            </Link>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                        >
                                            ID
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                        >
                                            Deskripsi
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                        >
                                            Bidang
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                        >
                                            Tanggal
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                        >
                                            Status
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                        >
                                            Tracking
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                        >
                                            Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody
                                    class="bg-white divide-y divide-gray-200"
                                >
                                    <tr
                                        v-for="permintaan in recentPermintaans"
                                        :key="permintaan.permintaan_id"
                                        class="hover:bg-gray-50"
                                    >
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"
                                        >
                                            #{{ permintaan.permintaan_id }}
                                        </td>
                                        <td
                                            class="px-6 py-4 text-sm text-gray-900"
                                        >
                                            <div class="max-w-xs truncate">
                                                {{ permintaan.deskripsi }}
                                            </div>
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                        >
                                            {{ permintaan.bidang || "-" }}
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                        >
                                            {{
                                                formatDate(
                                                    permintaan.tanggal_permintaan
                                                )
                                            }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                :class="
                                                    getStatusClass(
                                                        permintaan.status
                                                    )
                                                "
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                            >
                                                {{
                                                    getStatusLabel(
                                                        permintaan.status
                                                    )
                                                }}
                                            </span>
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm"
                                        >
                                            <span
                                                :class="
                                                    getTrackingClass(
                                                        permintaan.tracking_status
                                                    )
                                                "
                                                class="px-2 py-1 text-xs font-medium rounded"
                                            >
                                                {{ permintaan.tracking_status }}
                                            </span>
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm font-medium"
                                        >
                                            <Link
                                                :href="
                                                    route(
                                                        'kepala-instalasi.show',
                                                        permintaan.permintaan_id
                                                    )
                                                "
                                                class="text-[#028174] hover:text-[#03a089]"
                                            >
                                                Detail
                                            </Link>
                                        </td>
                                    </tr>
                                    <tr v-if="recentPermintaans.length === 0">
                                        <td
                                            colspan="7"
                                            class="px-6 py-8 text-center text-gray-500"
                                        >
                                            Tidak ada permintaan saat ini
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                    <div
                        class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition-shadow cursor-pointer"
                    >
                        <Link
                            :href="route('kepala-instalasi.index')"
                            class="block p-6"
                        >
                            <div class="flex items-center">
                                <div
                                    class="w-12 h-12 bg-[#028174] bg-opacity-10 rounded-full flex items-center justify-center mr-4"
                                >
                                    <svg
                                        class="w-6 h-6 text-[#028174]"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
                                        ></path>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900">
                                        Review Permintaan
                                    </h4>
                                    <p class="text-sm text-gray-600">
                                        Lihat dan proses permintaan
                                    </p>
                                </div>
                            </div>
                        </Link>
                    </div>

                    <div
                        class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition-shadow cursor-pointer"
                    >
                        <Link
                            :href="route('permintaan.index')"
                            class="block p-6"
                        >
                            <div class="flex items-center">
                                <div
                                    class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mr-4"
                                >
                                    <svg
                                        class="w-6 h-6 text-blue-600"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                        ></path>
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                                        ></path>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900">
                                        Semua Permintaan
                                    </h4>
                                    <p class="text-sm text-gray-600">
                                        Lihat riwayat lengkap
                                    </p>
                                </div>
                            </div>
                        </Link>
                    </div>

                    <div
                        class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition-shadow cursor-pointer"
                    >
                        <Link :href="route('profile.edit')" class="block p-6">
                            <div class="flex items-center">
                                <div
                                    class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mr-4"
                                >
                                    <svg
                                        class="w-6 h-6 text-gray-600"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                                        ></path>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900">
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
