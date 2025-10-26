<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Daftar Permintaan
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto px-2 sm:px-4">
                <!-- Success notification -->
                <div v-if="$page.props.flash?.success" class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ $page.props.flash.success }}</span>
                </div>

                <!-- Filter Bar -->
                <FilterBar 
                    route-name="permintaan.index"
                    :initial-filters="filters"
                    :show-bidang-filter="true"
                />

                <div
                    class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden"
                >
                    <!-- Header card -->
                    <div
                        class="flex items-center justify-between px-6 pt-6 pb-4 border-b border-gray-200"
                    >
                        <h3 class="font-semibold text-gray-800 text-lg">
                            Daftar Permintaan
                        </h3>

                        <Link
                            v-if="isAdmin"
                            :href="route('permintaan.create')"
                            class="inline-flex items-center px-3 py-1.5 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm"
                        >
                            + Buat Permintaan
                        </Link>
                    </div>

                    <!-- Body -->
                    <div class="p-6 text-gray-900">
                        <div v-if="permintaans && permintaans.length > 0">
                            <table
                                class="w-full table-auto text-sm border-collapse"
                            >
                                <thead class="bg-gray-50">
                                    <tr
                                        class="text-xs uppercase tracking-wide text-gray-600 border-b border-gray-200"
                                    >
                                        <th
                                            class="py-3 px-3 text-left font-medium w-16"
                                        >
                                            ID
                                        </th>
                                        <th
                                            class="py-3 px-3 text-left font-medium w-28"
                                        >
                                            Tanggal
                                        </th>
                                        <th
                                            class="py-3 px-3 text-left font-medium"
                                        >
                                            Bidang / Unit
                                        </th>
                                        <th
                                            class="py-3 px-3 text-left font-medium"
                                        >
                                            Deskripsi
                                        </th>
                                        <th
                                            class="py-3 px-3 text-left font-medium w-32"
                                        >
                                            Tracking
                                        </th>
                                        <th
                                            class="py-3 px-3 text-left font-medium w-24"
                                        >
                                            Status
                                        </th>
                                        <th
                                            class="py-3 px-3 text-left font-medium w-28"
                                        >
                                            Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="item in permintaans"
                                        :key="item.permintaan_id"
                                        class="border-b hover:bg-gray-50 transition-colors"
                                    >
                                        <td
                                            class="py-3 px-3 whitespace-nowrap text-indigo-600 font-medium"
                                        >
                                            <Link
                                                :href="
                                                    route(
                                                        'permintaan.show',
                                                        item.permintaan_id
                                                    )
                                                "
                                                class="hover:underline"
                                            >
                                                #{{ item.permintaan_id }}
                                            </Link>
                                        </td>
                                        <td class="py-3 px-3 whitespace-nowrap text-sm text-gray-600">
                                            {{ item.tanggal_permintaan ? new Date(item.tanggal_permintaan).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }) : "-" }}
                                        </td>
                                        <td class="py-3 px-3">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ item.bidang ?? "-" }}
                                            </div>
                                            <div class="text-xs text-gray-500 mt-0.5">
                                                {{ item.user?.nama ?? "-" }}
                                            </div>
                                        </td>
                                        <td class="py-3 px-3">
                                            <div class="text-sm text-gray-900 line-clamp-2">
                                                {{ item.deskripsi }}
                                            </div>
                                            <div v-if="item.no_nota_dinas" class="text-xs text-gray-500 mt-1">
                                                <span class="font-medium">No Nota:</span> {{ item.no_nota_dinas }}
                                            </div>
                                        </td>
                                        <td class="py-3 px-3">
                                            <div class="flex items-center justify-between">
                                                <div class="flex-1">
                                                    <div class="flex items-center gap-1 mb-1">
                                                        <div class="w-full bg-gray-200 rounded-full h-1.5">
                                                            <div class="bg-indigo-600 h-1.5 rounded-full transition-all" :style="{ width: item.progress + '%' }"></div>
                                                        </div>
                                                        <span class="text-xs font-medium text-gray-700 min-w-[35px]">{{ item.progress }}%</span>
                                                    </div>
                                                    <div class="text-xs text-gray-600">
                                                        {{ item.tracking_status }}
                                                    </div>
                                                    <div class="text-xs text-gray-500">
                                                        ({{ item.timeline_count }}/8 tahap)
                                                    </div>
                                                </div>
                                                <Link
                                                    :href="route('permintaan.tracking', item.permintaan_id)"
                                                    class="ml-2 text-blue-600 hover:text-blue-900 flex-shrink-0"
                                                    title="Lihat Timeline Lengkap"
                                                >
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                                    </svg>
                                                </Link>
                                            </div>
                                        </td>
                                        <td class="py-3 px-3 whitespace-nowrap">
                                            <span
                                                class="inline-flex items-center px-2.5 py-1 text-xs font-medium rounded-full"
                                                :class="statusClass(item.status)"
                                            >
                                                <span class="mr-1">{{ getStatusIcon(item.status) }}</span>
                                                {{ item.status }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-3 whitespace-nowrap text-sm">
                                            <div class="flex items-center gap-2">
                                                <Link
                                                    :href="route('permintaan.show', item.permintaan_id)"
                                                    class="inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-900 text-xs"
                                                    title="Lihat Detail"
                                                >
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                    </svg>
                                                </Link>
                                                <!-- Edit hanya tersedia jika status ditolak -->
                                                <Link
                                                    v-if="canEdit(item)"
                                                    :href="route('permintaan.edit', item.permintaan_id)"
                                                    class="inline-flex items-center gap-1 text-green-600 hover:text-green-900 text-xs"
                                                    title="Edit (Revisi)"
                                                >
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                </Link>
                                                <!-- Delete hanya tersedia jika status ditolak -->
                                                <button
                                                    v-if="canDelete(item)"
                                                    @click="destroy(item.permintaan_id)"
                                                    class="inline-flex items-center gap-1 text-red-600 hover:text-red-900 disabled:opacity-50 text-xs"
                                                    :disabled="deleting === item.permintaan_id"
                                                    title="Hapus"
                                                >
                                                    <svg v-if="deleting !== item.permintaan_id" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                    <span v-else class="animate-spin">⏳</span>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Empty State -->
                        <div
                            v-else
                            class="text-center py-16 text-gray-500 text-sm"
                        >
                            Tidak ada permintaan.
                            <div v-if="isAdmin" class="mt-4">
                                <Link
                                    :href="route('permintaan.create')"
                                    class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700"
                                >
                                    + Input Permintaan Baru
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div v-if="props.permintaans.data && props.permintaans.data.length > 0" class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-700">
                                Showing <span class="font-medium">{{ props.permintaans.from }}</span> to 
                                <span class="font-medium">{{ props.permintaans.to }}</span> of 
                                <span class="font-medium">{{ props.permintaans.total }}</span> results
                            </div>
                            <div class="flex gap-2">
                                <Link
                                    v-if="props.permintaans.prev_page_url"
                                    :href="props.permintaans.prev_page_url"
                                    class="px-3 py-1 text-sm border border-gray-300 rounded-md hover:bg-gray-100"
                                    preserve-state
                                >
                                    Previous
                                </Link>
                                <span v-else class="px-3 py-1 text-sm border border-gray-300 rounded-md bg-gray-100 text-gray-400 cursor-not-allowed">
                                    Previous
                                </span>

                                <span class="px-3 py-1 text-sm">
                                    Page {{ props.permintaans.current_page }} of {{ props.permintaans.last_page }}
                                </span>

                                <Link
                                    v-if="props.permintaans.next_page_url"
                                    :href="props.permintaans.next_page_url"
                                    class="px-3 py-1 text-sm border border-gray-300 rounded-md hover:bg-gray-100"
                                    preserve-state
                                >
                                    Next
                                </Link>
                                <span v-else class="px-3 py-1 text-sm border border-gray-300 rounded-md bg-gray-100 text-gray-400 cursor-not-allowed">
                                    Next
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div
                        v-if="permintaans && permintaans.length > 0 && isAdmin"
                        class="flex justify-end px-6 pt-4 pb-6 border-t border-gray-200"
                    >
                        <Link
                            :href="route('permintaan.create')"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm"
                        >
                            + Input Permintaan Baru
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import FilterBar from "@/Components/FilterBar.vue";
import { Link, useForm } from "@inertiajs/vue3";
import { ref, computed } from "vue";

const props = defineProps({
    permintaans: { type: Object, default: () => ({ data: [] }) }, // Changed from Array to Object for pagination
    userLogin: { type: Object, default: null },
    filters: { type: Object, default: () => ({}) },
});

// Access .data for paginated results
const permintaans = computed(() => props.permintaans.data || []);
const userLogin = props.userLogin;
const form = useForm();
const deleting = ref(null);

const isAdmin = computed(() => {
    if (!userLogin) return false;
    const role = (userLogin.role || "").toLowerCase();
    return role.includes("admin") || role.includes("rs");
});

const canEdit = (item) => {
    if (!userLogin) return false;
    // Hanya bisa edit jika status revisi (bukan ditolak)
    return isAdmin.value && item.status && item.status.toLowerCase() === 'revisi';
};

const canDelete = (item) => {
    if (!userLogin) return false;
    // Hanya bisa delete jika status ditolak
    return isAdmin.value && item.status && item.status.toLowerCase() === 'ditolak';
};

const statusClass = (status) => {
    switch ((status || "").toLowerCase()) {
        case "disetujui":
            return "bg-green-100 text-green-800 border border-green-300";
        case "diajukan":
            return "bg-yellow-100 text-yellow-800 border border-yellow-300";
        case "diproses":
        case "proses":
            return "bg-blue-100 text-blue-800 border border-blue-300";
        case "revisi":
            return "bg-orange-100 text-orange-800 border border-orange-300"; // Status revisi
        case "ditolak":
            return "bg-red-100 text-red-800 border border-red-300"; // Status ditolak
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
        case "revisi":
            return "🔄"; // Icon untuk revisi
        case "ditolak":
            return "❌";
        default:
            return "⚪";
    }
};

const destroy = (id) => {
    if (deleting.value) return;
    if (!confirm("Yakin ingin menghapus permintaan #" + id + "?")) return;
    deleting.value = id;
    form.delete(route("permintaan.destroy", id), {
        onFinish: () => {
            deleting.value = null;
        },
    });
};
</script>
