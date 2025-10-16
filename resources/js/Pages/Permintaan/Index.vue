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
                                            class="py-3 px-3 text-left font-medium"
                                        >
                                            ID
                                        </th>
                                        <th
                                            class="py-3 px-3 text-left font-medium"
                                        >
                                            Tanggal
                                        </th>
                                        <th
                                            class="py-3 px-3 text-left font-medium"
                                        >
                                            Bidang
                                        </th>
                                        <th
                                            class="py-3 px-3 text-left font-medium"
                                        >
                                            Unit / User
                                        </th>
                                        <th
                                            class="py-3 px-3 text-left font-medium"
                                        >
                                            PIC Pimpinan
                                        </th>
                                        <th
                                            class="py-3 px-3 text-left font-medium"
                                        >
                                            No Nota Dinas
                                        </th>
                                        <th
                                            class="py-3 px-3 text-left font-medium"
                                        >
                                            Deskripsi
                                        </th>
                                        <th
                                            class="py-3 px-3 text-left font-medium"
                                        >
                                            Status
                                        </th>
                                        <th
                                            class="py-3 px-3 text-left font-medium"
                                        >
                                            Link Scan
                                        </th>
                                        <th
                                            class="py-3 px-3 text-left font-medium"
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
                                            class="py-2.5 px-3 whitespace-nowrap text-indigo-600"
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
                                                {{ item.permintaan_id }}
                                            </Link>
                                        </td>
                                        <td class="py-2.5 px-3 whitespace-nowrap">
                                            {{ item.tanggal_permintaan ? new Date(item.tanggal_permintaan).toLocaleDateString('id-ID') : "-" }}
                                        </td>
                                        <td class="py-2.5 px-3">
                                            {{ item.bidang ?? "-" }}
                                        </td>
                                        <td class="py-2.5 px-3">
                                            {{ item.user?.nama ?? "-" }}
                                        </td>
                                        <td class="py-2.5 px-3">
                                            {{ item.pic_pimpinan ?? "-" }}
                                        </td>
                                        <td class="py-2.5 px-3">
                                            {{ item.no_nota_dinas ?? "-" }}
                                        </td>
                                        <td class="py-2.5 px-3">
                                            {{ item.deskripsi }}
                                        </td>
                                        <td class="py-2.5 px-3">
                                            <span
                                                class="inline-block px-2 py-1 text-[11px] font-semibold rounded-full"
                                                :class="
                                                    statusClass(item.status)
                                                "
                                            >
                                                {{ item.status }}
                                            </span>
                                        </td>
                                        <td class="py-2.5 px-3">
                                            <a
                                                v-if="item.link_scan"
                                                :href="item.link_scan"
                                                target="_blank"
                                                class="text-indigo-600 hover:text-indigo-900 hover:underline"
                                            >
                                                Lihat Scan
                                            </a>
                                            <span v-else class="text-gray-400">-</span>
                                        </td>
                                        <td class="py-2.5 px-3">
                                            <template v-if="canEdit(item)">
                                                <Link
                                                    :href="
                                                        route(
                                                            'permintaan.edit',
                                                            item.permintaan_id
                                                        )
                                                    "
                                                    class="text-indigo-600 hover:text-indigo-900 mr-2"
                                                >
                                                    Edit
                                                </Link>
                                                <button
                                                    @click="
                                                        destroy(
                                                            item.permintaan_id
                                                        )
                                                    "
                                                    class="text-red-600 hover:text-red-900 disabled:opacity-50"
                                                    :disabled="
                                                        deleting ===
                                                        item.permintaan_id
                                                    "
                                                >
                                                    <span
                                                        v-if="
                                                            deleting ===
                                                            item.permintaan_id
                                                        "
                                                        class="animate-pulse"
                                                    >
                                                        ...
                                                    </span>
                                                    <span v-else>Hapus</span>
                                                </button>
                                            </template>
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
            return "bg-green-100 text-green-700";
        case "diajukan":
            return "bg-yellow-100 text-yellow-700";
        case "diproses":
        case "proses":
            return "bg-gray-100 text-gray-700";
        default:
            return "bg-slate-100 text-slate-700";
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
