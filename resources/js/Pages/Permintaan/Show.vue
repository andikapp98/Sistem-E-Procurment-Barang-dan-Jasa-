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
                <Link
                    :href="route('permintaan.index')"
                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                >
                    ← Kembali
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
                
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
                        </dl>
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

                <!-- Card Actions -->
                <div v-if="canEdit || permintaan.status === 'ditolak'" class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">Aksi</h3>
                                <p class="text-sm text-gray-600 mt-1">
                                    <span v-if="permintaan.status === 'ditolak'" class="text-red-600 font-medium">
                                        ⚠️ Permintaan ditolak. Anda dapat mengedit dan mengajukan ulang.
                                    </span>
                                    <span v-else>
                                        Kelola permintaan Anda
                                    </span>
                                </p>
                            </div>
                            <div class="flex gap-3">
                                <Link
                                    v-if="canEdit"
                                    :href="route('permintaan.edit', permintaan.permintaan_id)"
                                    :class="[
                                        'inline-flex items-center px-4 py-2 rounded-md font-semibold text-sm',
                                        permintaan.status === 'ditolak' 
                                            ? 'bg-red-600 text-white hover:bg-red-700 border-2 border-red-700'
                                            : 'bg-indigo-600 text-white hover:bg-indigo-700'
                                    ]"
                                >
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    {{ permintaan.status === 'ditolak' ? 'Edit & Ajukan Ulang' : 'Edit Permintaan' }}
                                </Link>
                                <button
                                    v-if="canEdit && permintaan.status !== 'ditolak'"
                                    @click="deletePermintaan"
                                    class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 font-semibold text-sm"
                                >
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    Hapus
                                </button>
                            </div>
                        </div>
                        
                        <!-- Alert Ditolak -->
                        <div v-if="permintaan.status === 'ditolak'" class="mt-4 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-800">
                                        Permintaan Ditolak
                                    </h3>
                                    <div class="mt-2 text-sm text-red-700">
                                        <p>Permintaan Anda telah ditolak. Silakan periksa kembali detail permintaan, lakukan perbaikan yang diperlukan, dan ajukan ulang.</p>
                                        <p class="mt-2 font-medium">Alasan penolakan dapat dilihat pada deskripsi permintaan di atas.</p>
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
            return "bg-red-100 text-red-800 border border-red-300"; // ✅ MERAH TEBAL
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

const formatDateTime = (datetime) => {
    if (!datetime) return "-";
    return new Date(datetime).toLocaleString("id-ID", {
        day: "2-digit",
        month: "long",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
};

// Check if user can edit
const canEdit = computed(() => {
    const user = props.permintaan?.user;
    const userLogin = window.$page?.props?.auth?.user;
    
    if (!userLogin) return false;
    
    // Admin always can edit
    if (userLogin.role === 'admin') return true;
    
    // Owner can edit their own
    if (user && user.user_id === userLogin.user_id) return true;
    
    return false;
});

const deletePermintaan = () => {
    if (!confirm(`Yakin ingin menghapus permintaan #${props.permintaan.permintaan_id}?`)) {
        return;
    }
    
    deleting.value = true;
    
    router.delete(route("permintaan.destroy", props.permintaan.permintaan_id), {
        onFinish: () => {
            deleting.value = false;
        },
    });
};
</script>
