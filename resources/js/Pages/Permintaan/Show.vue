<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Detail Permintaan
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto px-2 sm:px-4">
                <div
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6"
                >
                    <dl>
                        <dt class="font-medium">ID</dt>
                        <dd class="mb-2">{{ permintaan.permintaan_id }}</dd>

                        <dt class="font-medium">User</dt>
                        <dd class="mb-2">{{ permintaan.user?.nama ?? "-" }}</dd>

                        <dt class="font-medium">Bidang</dt>
                        <dd class="mb-2">{{ permintaan.bidang ?? "-" }}</dd>

                        <dt class="font-medium">Tanggal</dt>
                        <dd class="mb-2">
                            {{ permintaan.tanggal_permintaan }}
                        </dd>

                        <dt class="font-medium">PIC Pimpinan</dt>
                        <dd class="mb-2">{{ permintaan.pic_pimpinan ?? "-" }}</dd>

                        <dt class="font-medium">No Nota Dinas</dt>
                        <dd class="mb-2">{{ permintaan.no_nota_dinas ?? "-" }}</dd>

                        <dt class="font-medium">Deskripsi</dt>
                        <dd class="mb-2">{{ permintaan.deskripsi }}</dd>

                        <dt class="font-medium">Status</dt>
                        <dd class="mb-2">{{ permintaan.status }}</dd>

                        <dt class="font-medium">Link Scan Dokumen</dt>
                        <dd class="mb-2">
                            <a v-if="permintaan.link_scan" :href="permintaan.link_scan" target="_blank" class="text-indigo-600 hover:underline">
                                {{ permintaan.link_scan }}
                            </a>
                            <span v-else>-</span>
                        </dd>
                    </dl>

                    <div class="mt-4">
                        <Link
                            :href="
                                route(
                                    'permintaan.edit',
                                    permintaan.permintaan_id
                                )
                            "
                            class="text-indigo-600 mr-3"
                            >Edit</Link
                        >
                        <button @click.prevent="destroy()" class="text-red-600">
                            Hapus
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { usePage } from "@inertiajs/vue3";
import { Link } from "@inertiajs/vue3";
import { useForm } from "@inertiajs/vue3";

const props = usePage().props.value;
const permintaan = props.permintaan;
const form = useForm();

const destroy = () => {
    if (
        !confirm(
            "Yakin ingin menghapus permintaan #" +
                permintaan.permintaan_id +
                "?"
        )
    )
        return;
    form.delete(route("permintaan.destroy", permintaan.permintaan_id));
};
</script>
