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
                                            <div class="text-xs">
                                                <div class="flex items-center gap-1 mb-1">
                                                    <div class="w-full bg-gray-200 rounded-full h-1.5">
                                                        <div class="bg-indigo-600 h-1.5 rounded-full" :style="{ width: item.progress + '%' }"></div>
                                                    </div>
                                                    <span class="text-xs font-medium text-gray-700 min-w-[35px]">{{ item.progress }}%</span>
                                                </div>
                                                <div class="text-gray-600">
                                                    {{ item.tracking_status }} ({{ item.timeline_count }}/8)
                                                </div>
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
                                                    class="text-indigo-600 hover:text-indigo-900"
                                                    title="Lihat Detail"
                                                >
                                                    👁️
                                                </Link>
                                                <Link
                                                    :href="route('permintaan.tracking', item.permintaan_id)"
                                                    class="text-blue-600 hover:text-blue-900"
                                                    title="Lihat Tracking"
                                                >
                                                    📊
                                                </Link>
                                                <template v-if="canEdit(item)">
                                                    <Link
                                                        :href="route('permintaan.edit', item.permintaan_id)"
                                                        class="text-green-600 hover:text-green-900"
                                                        title="Edit"
                                                    >
                                                        ✏️
                                                    </Link>
                                                    <button
                                                        @click="destroy(item.permintaan_id)"
                                                        class="text-red-600 hover:text-red-900 disabled:opacity-50"
                                                        :disabled="deleting === item.permintaan_id"
                                                        title="Hapus"
                                                    >
                                                        <span v-if="deleting === item.permintaan_id">⏳</span>
                                                        <span v-else>🗑️</span>
                                                    </button>
                                                </template>
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
    return (
        isAdmin.value || (item.user && item.user.user_id === userLogin.user_id)
    );
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
        case "ditolak":
            return "bg-red-100 text-red-800 border border-red-300"; // ✅ MERAH untuk ditolak
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
