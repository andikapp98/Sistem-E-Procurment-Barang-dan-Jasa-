<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        Detail Permintaan #{{ permintaan.permintaan_id }}
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">
                        {{ permintaan.bidang }}
                    </p>
                </div>
                <div class="flex gap-3">
                    <!-- Tombol Cetak Nota Dinas -->
                    <a
                        v-if="permintaan.nota_dinas && permintaan.nota_dinas.length > 0"
                        :href="route('permintaan.cetak-nota-dinas', permintaan.permintaan_id)"
                        target="_blank"
                        class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        Cetak Nota Dinas
                    </a>
                    
                    <Link
                        :href="route('permintaan.index')"
                        class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    >
                        ← Kembali
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
                
                <!-- Success/Error Messages -->
                <div v-if="$page.props.flash?.success" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ $page.props.flash.success }}</span>
                </div>
                <div v-if="$page.props.flash?.error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ $page.props.flash.error }}</span>
                </div>

                <!-- Action Buttons untuk Admin -->
                <div v-if="userLogin?.role === 'admin' && (permintaan.status === 'revisi' || permintaan.status === 'ditolak')" class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-1">
                                    {{ permintaan.status === 'revisi' ? 'Permintaan Perlu Revisi' : 'Permintaan Ditolak' }}
                                </h3>
                                <p class="text-sm text-gray-600">
                                    {{ permintaan.status === 'revisi' 
                                        ? 'Anda dapat mengedit permintaan ini untuk memperbaikinya dan mengajukan kembali.' 
                                        : 'Anda dapat mengedit untuk mengajukan ulang atau menghapus permintaan ini.' 
                                    }}
                                </p>
                            </div>
                            <div class="flex gap-3">
                                <!-- Edit Button -->
                                <Link
                                    :href="route('permintaan.edit', permintaan.permintaan_id)"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors"
                                >
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Edit & Ajukan Ulang
                                </Link>

                                <!-- Delete Button - Hanya untuk status Ditolak -->
                                <button
                                    v-if="permintaan.status === 'ditolak'"
                                    @click="confirmDelete"
                                    type="button"
                                    class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors"
                                >
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Card Informasi Utama -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-medium text-gray-900">Informasi Permintaan</h3>
                            <span
                                class="inline-flex items-center px-3 py-1.5 text-sm font-medium rounded-full"
                                :class="statusClass(permintaan.status)"
                            >
                                <span class="mr-1.5">{{ getStatusIcon(permintaan.status) }}</span>
                                {{ permintaan.status }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="p-6">
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- ID Permintaan -->
                            <div>
                                <dt class="text-sm font-medium text-gray-500 mb-1">ID Permintaan</dt>
                                <dd class="text-base text-gray-900 font-semibold">#{{ permintaan.permintaan_id }}</dd>
                            </div>

                            <!-- Tanggal Permintaan -->
                            <div>
                                <dt class="text-sm font-medium text-gray-500 mb-1">Tanggal Permintaan</dt>
                                <dd class="text-base text-gray-900">
                                    {{ formatDate(permintaan.tanggal_permintaan) }}
                                </dd>
                            </div>

                            <!-- Bidang/Unit -->
                            <div>
                                <dt class="text-sm font-medium text-gray-500 mb-1">Bidang / Unit</dt>
                                <dd class="text-base text-gray-900">{{ permintaan.bidang ?? "-" }}</dd>
                            </div>

                            <!-- User Pengaju -->
                            <div>
                                <dt class="text-sm font-medium text-gray-500 mb-1">Pengaju</dt>
                                <dd class="text-base text-gray-900">{{ permintaan.user?.nama ?? "-" }}</dd>
                            </div>

                            <!-- PIC Pimpinan -->
                            <div>
                                <dt class="text-sm font-medium text-gray-500 mb-1">PIC Pimpinan</dt>
                                <dd class="text-base text-gray-900">{{ permintaan.pic_pimpinan ?? "-" }}</dd>
                            </div>

                            <!-- No Nota Dinas -->
                            <div>
                                <dt class="text-sm font-medium text-gray-500 mb-1">No Nota Dinas</dt>
                                <dd class="text-base text-gray-900">{{ permintaan.no_nota_dinas ?? "-" }}</dd>
                            </div>

                            <!-- Link Scan (Full Width) -->
                            <div class="md:col-span-2">
                                <dt class="text-sm font-medium text-gray-500 mb-1">Link Scan Dokumen</dt>
                                <dd class="text-base">
                                    <a 
                                        v-if="permintaan.link_scan" 
                                        :href="permintaan.link_scan" 
                                        target="_blank" 
                                        class="inline-flex items-center text-indigo-600 hover:text-indigo-900 hover:underline"
                                    >
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                        </svg>
                                        Lihat Dokumen
                                    </a>
                                    <span v-else class="text-gray-400">-</span>
                                </dd>
                            </div>

                            <!-- Deskripsi (Full Width) -->
                            <div class="md:col-span-2">
                                <dt class="text-sm font-medium text-gray-500 mb-2">Deskripsi Permintaan</dt>
                                <dd class="text-base text-gray-900 bg-gray-50 p-4 rounded-lg border border-gray-200">
                                    {{ permintaan.deskripsi }}
                                </dd>
                            </div>

                            <!-- Disposisi Tujuan -->
                            <div>
                                <dt class="text-sm font-medium text-gray-500 mb-1">Disposisi</dt>
                                <dd class="text-base text-gray-900 font-semibold">
                                    {{ permintaan.disposisi_tujuan ?? "-" }}
                                </dd>
                            </div>

                            <!-- Wadir Tujuan -->
                            <div v-if="permintaan.wadir_tujuan">
                                <dt class="text-sm font-medium text-gray-500 mb-1">Wakil Direktur</dt>
                                <dd class="text-base text-gray-900 font-semibold">
                                    {{ permintaan.wadir_tujuan }}
                                </dd>
                            </div>

                            <!-- Catatan Disposisi -->
                            <div class="md:col-span-2" v-if="permintaan.catatan_disposisi">
                                <dt class="text-sm font-medium text-gray-500 mb-1">Detail / Catatan Disposisi</dt>
                                <dd class="text-base text-gray-900 bg-gray-50 p-3 rounded border border-gray-200">
                                    {{ permintaan.catatan_disposisi }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Card Nota Dinas (jika ada) -->
                <div v-if="permintaan.nota_dinas && permintaan.nota_dinas.length > 0" class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">Nota Dinas</h3>
                                <p class="text-sm text-gray-600 mt-1">Informasi nota dinas terkait permintaan</p>
                            </div>
                            <a
                                :href="route('permintaan.cetak-nota-dinas', permintaan.permintaan_id)"
                                target="_blank"
                                class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                </svg>
                                Cetak Nota Dinas
                            </a>
                        </div>
                    </div>
                    
                    <div class="p-6">
                        <div v-for="nota in permintaan.nota_dinas" :key="nota.nota_id" class="mb-4 last:mb-0">
                            <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 mb-1">Kepada</dt>
                                    <dd class="text-base text-gray-900 font-semibold">{{ nota.kepada }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 mb-1">Dari</dt>
                                    <dd class="text-base text-gray-900">{{ nota.dari }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 mb-1">Tanggal</dt>
                                    <dd class="text-base text-gray-900">{{ formatDate(nota.tanggal_nota) }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 mb-1">Nomor</dt>
                                    <dd class="text-base text-gray-900 font-semibold">{{ nota.no_nota }}</dd>
                                </div>
                                <div v-if="nota.sifat">
                                    <dt class="text-sm font-medium text-gray-500 mb-1">Sifat</dt>
                                    <dd class="text-base text-gray-900">{{ nota.sifat }}</dd>
                                </div>
                                <div v-if="nota.lampiran" class="md:col-span-2">
                                    <dt class="text-sm font-medium text-gray-500 mb-2">Lampiran</dt>
                                    <dd>
                                        <a 
                                            :href="route('nota-dinas.lampiran', nota.nota_id)" 
                                            target="_blank" 
                                            class="inline-flex items-center px-4 py-2 bg-indigo-50 border border-indigo-200 rounded-lg text-indigo-700 hover:bg-indigo-100 hover:border-indigo-300 transition-colors duration-150"
                                        >
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                            </svg>
                                            <span class="font-medium">Lihat Lampiran</span>
                                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                            </svg>
                                        </a>
                                    </dd>
                                </div>
                                <div class="md:col-span-2">
                                    <dt class="text-sm font-medium text-gray-500 mb-1">Perihal</dt>
                                    <dd class="text-base text-gray-900 font-semibold">{{ nota.perihal }}</dd>
                                </div>
                                <div class="md:col-span-2" v-if="nota.detail">
                                    <dt class="text-sm font-medium text-gray-500 mb-1">Detail</dt>
                                    <dd class="text-base text-gray-900 bg-white p-3 rounded border border-gray-200 whitespace-pre-line">{{ nota.detail }}</dd>
                                </div>
                                <div class="md:col-span-2" v-if="nota.mengetahui">
                                    <dt class="text-sm font-medium text-gray-500 mb-1">Mengetahui</dt>
                                    <dd class="text-base text-gray-900 font-medium">{{ nota.mengetahui }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>

                <!-- Card Tracking Tahapan -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Timeline Tracking</h3>
                        <p class="text-sm text-gray-600 mt-1">Progres tahapan pengadaan</p>
                    </div>
                    
                    <div class="p-6">
                        <!-- Timeline Vertical -->
                        <div class="relative">
                            <!-- Vertical Line -->
                            <div class="absolute left-6 top-0 bottom-0 w-0.5 bg-gray-200"></div>

                            <!-- Timeline Items -->
                            <div class="space-y-6">
                                <!-- 1. Permintaan -->
                                <div class="relative flex items-start">
                                    <div class="flex items-center justify-center w-12 h-12 rounded-full bg-green-100 border-4 border-white z-10">
                                        <span class="text-xl">✅</span>
                                    </div>
                                    <div class="ml-6 flex-1">
                                        <div class="bg-white rounded-lg border border-gray-200 p-4 shadow-sm">
                                            <div class="flex items-center justify-between mb-2">
                                                <h4 class="text-base font-semibold text-gray-900">Permintaan</h4>
                                                <span class="text-xs text-gray-500">{{ formatDate(permintaan.tanggal_permintaan) }}</span>
                                            </div>
                                            <p class="text-sm text-gray-600">
                                                Status: <span class="font-medium text-green-600">{{ permintaan.status }}</span>
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- 2. Nota Dinas -->
                                <div class="relative flex items-start">
                                    <div class="flex items-center justify-center w-12 h-12 rounded-full bg-gray-100 border-4 border-white z-10">
                                        <span class="text-xl">📄</span>
                                    </div>
                                    <div class="ml-6 flex-1">
                                        <div class="bg-gray-50 rounded-lg border border-gray-200 p-4">
                                            <div class="flex items-center justify-between mb-2">
                                                <h4 class="text-base font-semibold text-gray-700">Nota Dinas</h4>
                                                <span class="text-xs text-gray-400">Menunggu</span>
                                            </div>
                                            <p class="text-sm text-gray-500">Belum ada data</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- 3. Disposisi -->
                                <div class="relative flex items-start">
                                    <div class="flex items-center justify-center w-12 h-12 rounded-full bg-gray-100 border-4 border-white z-10">
                                        <span class="text-xl">📋</span>
                                    </div>
                                    <div class="ml-6 flex-1">
                                        <div class="bg-gray-50 rounded-lg border border-gray-200 p-4">
                                            <div class="flex items-center justify-between mb-2">
                                                <h4 class="text-base font-semibold text-gray-700">Disposisi</h4>
                                                <span class="text-xs text-gray-400">Menunggu</span>
                                            </div>
                                            <p class="text-sm text-gray-500">Belum ada data</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- 4. Perencanaan -->
                                <div class="relative flex items-start">
                                    <div class="flex items-center justify-center w-12 h-12 rounded-full bg-gray-100 border-4 border-white z-10">
                                        <span class="text-xl">📊</span>
                                    </div>
                                    <div class="ml-6 flex-1">
                                        <div class="bg-gray-50 rounded-lg border border-gray-200 p-4">
                                            <div class="flex items-center justify-between mb-2">
                                                <h4 class="text-base font-semibold text-gray-700">Perencanaan</h4>
                                                <span class="text-xs text-gray-400">Menunggu</span>
                                            </div>
                                            <p class="text-sm text-gray-500">Belum ada data</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- 5. KSO -->
                                <div class="relative flex items-start">
                                    <div class="flex items-center justify-center w-12 h-12 rounded-full bg-gray-100 border-4 border-white z-10">
                                        <span class="text-xl">🤝</span>
                                    </div>
                                    <div class="ml-6 flex-1">
                                        <div class="bg-gray-50 rounded-lg border border-gray-200 p-4">
                                            <div class="flex items-center justify-between mb-2">
                                                <h4 class="text-base font-semibold text-gray-700">KSO</h4>
                                                <span class="text-xs text-gray-400">Menunggu</span>
                                            </div>
                                            <p class="text-sm text-gray-500">Belum ada data</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- 6. Pengadaan -->
                                <div class="relative flex items-start">
                                    <div class="flex items-center justify-center w-12 h-12 rounded-full bg-gray-100 border-4 border-white z-10">
                                        <span class="text-xl">🛒</span>
                                    </div>
                                    <div class="ml-6 flex-1">
                                        <div class="bg-gray-50 rounded-lg border border-gray-200 p-4">
                                            <div class="flex items-center justify-between mb-2">
                                                <h4 class="text-base font-semibold text-gray-700">Pengadaan</h4>
                                                <span class="text-xs text-gray-400">Menunggu</span>
                                            </div>
                                            <p class="text-sm text-gray-500">Belum ada data</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- 7. Nota Penerimaan -->
                                <div class="relative flex items-start">
                                    <div class="flex items-center justify-center w-12 h-12 rounded-full bg-gray-100 border-4 border-white z-10">
                                        <span class="text-xl">📦</span>
                                    </div>
                                    <div class="ml-6 flex-1">
                                        <div class="bg-gray-50 rounded-lg border border-gray-200 p-4">
                                            <div class="flex items-center justify-between mb-2">
                                                <h4 class="text-base font-semibold text-gray-700">Nota Penerimaan</h4>
                                                <span class="text-xs text-gray-400">Menunggu</span>
                                            </div>
                                            <p class="text-sm text-gray-500">Belum ada data</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- 8. Serah Terima -->
                                <div class="relative flex items-start">
                                    <div class="flex items-center justify-center w-12 h-12 rounded-full bg-gray-100 border-4 border-white z-10">
                                        <span class="text-xl">🎯</span>
                                    </div>
                                    <div class="ml-6 flex-1">
                                        <div class="bg-gray-50 rounded-lg border border-gray-200 p-4">
                                            <div class="flex items-center justify-between mb-2">
                                                <h4 class="text-base font-semibold text-gray-700">Serah Terima</h4>
                                                <span class="text-xs text-gray-400">Menunggu</span>
                                            </div>
                                            <p class="text-sm text-gray-500">Belum ada data</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Link, router } from "@inertiajs/vue3";
import { ref, computed } from "vue";

const props = defineProps({
    permintaan: Object,
    trackingStatus: String,
    timeline: Array,
    progress: Number,
    userLogin: Object,
});

const deleting = ref(false);

const statusClass = (status) => {
    switch ((status || "").toLowerCase()) {
        case "disetujui":
            return "bg-green-100 text-green-800 border border-green-300";
        case "diajukan":
            return "bg-yellow-100 text-yellow-800 border border-yellow-300";
        case "diproses":
        case "proses":
            return "bg-blue-100 text-blue-800 border border-blue-300";
        case "ditolak":
            return "bg-red-100 text-red-800 border border-red-300";
        case "revisi":
            return "bg-orange-100 text-orange-800 border border-orange-300";
        default:
            return "bg-gray-100 text-gray-800 border border-gray-300";
    }
};

const getStatusIcon = (status) => {
    switch ((status || "").toLowerCase()) {
        case "disetujui":
            return "✅";
        case "diajukan":
            return "🟡";
        case "diproses":
        case "proses":
            return "🔄";
        case "ditolak":
            return "❌";
        case "revisi":
            return "⚠️";
        default:
            return "⚪";
    }
};

const formatDate = (date) => {
    if (!date) return "-";
    return new Date(date).toLocaleDateString("id-ID", {
        day: "2-digit",
        month: "long",
        year: "numeric",
    });
};

const confirmDelete = () => {
    if (confirm('Apakah Anda yakin ingin menghapus permintaan ini? Tindakan ini tidak dapat dibatalkan.')) {
        deletePermintaan();
    }
};

const deletePermintaan = () => {
    if (deleting.value) return;
    
    deleting.value = true;
    router.delete(route('permintaan.destroy', props.permintaan.permintaan_id), {
        onSuccess: () => {
            // Will redirect to index with success message
        },
        onError: () => {
            deleting.value = false;
            alert('Gagal menghapus permintaan. Silakan coba lagi.');
        }
    });
};
</script>
