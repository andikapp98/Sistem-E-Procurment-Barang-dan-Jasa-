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
                        <div v-if="permintaans.length">
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

                    <!-- Footer -->
                    <div
                        v-if="permintaans.length && isAdmin"
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
import { Link, useForm } from "@inertiajs/vue3";
import { ref, computed } from "vue";

const props = defineProps({
    permintaans: { type: Array, default: () => [] },
    userLogin: { type: Object, default: null },
});

const permintaans = props.permintaans;
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
            return "bg-green-100 text-green-700 border border-green-200";
        case "diajukan":
            return "bg-yellow-100 text-yellow-700 border border-yellow-200";
        case "diproses":
        case "proses":
            return "bg-blue-100 text-blue-700 border border-blue-200";
        case "ditolak":
            return "bg-red-100 text-red-700 border border-red-200";
        default:
            return "bg-gray-100 text-gray-700 border border-gray-200";
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
