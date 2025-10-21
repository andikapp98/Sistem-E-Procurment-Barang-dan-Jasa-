b
<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

const props = defineProps({
    permintaan: Object,
    perencanaan: Object,
});

const form = useForm({
    no_kso: "",
    tanggal_kso: new Date().toISOString().split("T")[0],
    pihak_pertama: "RSUD",
    pihak_kedua: "",
    isi_kerjasama: "",
    nilai_kontrak: "",
    status: "draft",
});

const submit = () => {
    form.post(route("kso.store", props.permintaan.permintaan_id), {
        onSuccess: () => {
            // Redirect will be handled by controller
        },
    });
};

const formatCurrency = (event) => {
    let value = event.target.value.replace(/\D/g, "");
    form.nilai_kontrak = value;
};
</script>

<template>
    <Head title="Buat Dokumen KSO" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Buat Dokumen KSO
                </h2>
                <Link
                    :href="route('kso.show', permintaan.permintaan_id)"
                    class="text-sm text-gray-600 hover:text-gray-900"
                >
                    ← Kembali
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <!-- Info Permintaan -->
                <div
                    class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6"
                >
                    <h3 class="text-sm font-semibold text-blue-900 mb-2">
                        Informasi Permintaan
                    </h3>
                    <div class="grid grid-cols-2 gap-2 text-sm">
                        <div>
                            <span class="text-blue-700">ID:</span>
                            <span class="text-blue-900 font-medium ml-1"
                                >#{{ permintaan.permintaan_id }}</span
                            >
                        </div>
                        <div>
                            <span class="text-blue-700">Bidang:</span>
                            <span class="text-blue-900 font-medium ml-1">{{
                                permintaan.bidang
                            }}</span>
                        </div>
                        <div class="col-span-2">
                            <span class="text-blue-700">Deskripsi:</span>
                            <span class="text-blue-900 font-medium ml-1"
                                >{{ permintaan.deskripsi?.substring(0, 100)
                                }}{{
                                    permintaan.deskripsi?.length > 100
                                        ? "..."
                                        : ""
                                }}</span
                            >
                        </div>
                    </div>
                </div>

                <!-- Form KSO -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-6">
                            Form Dokumen KSO
                        </h3>

                        <form @submit.prevent="submit">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- No KSO -->
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 mb-2"
                                    >
                                        No. KSO
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        v-model="form.no_kso"
                                        type="text"
                                        required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        placeholder="KSO/001/X/2025"
                                    />
                                    <p
                                        v-if="form.errors.no_kso"
                                        class="mt-1 text-sm text-red-600"
                                    >
                                        {{ form.errors.no_kso }}
                                    </p>
                                </div>

                                <!-- Tanggal KSO -->
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 mb-2"
                                    >
                                        Tanggal KSO
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        v-model="form.tanggal_kso"
                                        type="date"
                                        required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    />
                                    <p
                                        v-if="form.errors.tanggal_kso"
                                        class="mt-1 text-sm text-red-600"
                                    >
                                        {{ form.errors.tanggal_kso }}
                                    </p>
                                </div>

                                <!-- Pihak Pertama -->
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 mb-2"
                                    >
                                        Pihak Pertama
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        v-model="form.pihak_pertama"
                                        type="text"
                                        required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        placeholder="RSUD"
                                    />
                                    <p
                                        v-if="form.errors.pihak_pertama"
                                        class="mt-1 text-sm text-red-600"
                                    >
                                        {{ form.errors.pihak_pertama }}
                                    </p>
                                </div>

                                <!-- Pihak Kedua -->
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 mb-2"
                                    >
                                        Pihak Kedua
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        v-model="form.pihak_kedua"
                                        type="text"
                                        required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        placeholder="Nama Vendor/Partner"
                                    />
                                    <p
                                        v-if="form.errors.pihak_kedua"
                                        class="mt-1 text-sm text-red-600"
                                    >
                                        {{ form.errors.pihak_kedua }}
                                    </p>
                                </div>

                                <!-- Nilai Kontrak -->
                                <div class="md:col-span-2">
                                    <label
                                        class="block text-sm font-medium text-gray-700 mb-2"
                                    >
                                        Nilai Kontrak
                                    </label>
                                    <div class="relative">
                                        <span
                                            class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500"
                                        >
                                            Rp
                                        </span>
                                        <input
                                            :value="
                                                form.nilai_kontrak
                                                    ? new Intl.NumberFormat(
                                                          'id-ID'
                                                      ).format(
                                                          form.nilai_kontrak
                                                      )
                                                    : ''
                                            "
                                            @input="formatCurrency"
                                            type="text"
                                            class="w-full pl-12 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                            placeholder="50.000.000"
                                        />
                                    </div>
                                    <p
                                        v-if="form.errors.nilai_kontrak"
                                        class="mt-1 text-sm text-red-600"
                                    >
                                        {{ form.errors.nilai_kontrak }}
                                    </p>
                                </div>

                                <!-- Isi Kerjasama -->
                                <div class="md:col-span-2">
                                    <label
                                        class="block text-sm font-medium text-gray-700 mb-2"
                                    >
                                        Isi Kerjasama
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <textarea
                                        v-model="form.isi_kerjasama"
                                        required
                                        rows="6"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        placeholder="Jelaskan isi kerjasama secara detail..."
                                    ></textarea>
                                    <p
                                        v-if="form.errors.isi_kerjasama"
                                        class="mt-1 text-sm text-red-600"
                                    >
                                        {{ form.errors.isi_kerjasama }}
                                    </p>
                                </div>

                                <!-- Status -->
                                <div class="md:col-span-2">
                                    <label
                                        class="block text-sm font-medium text-gray-700 mb-2"
                                    >
                                        Status
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <select
                                        v-model="form.status"
                                        required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    >
                                        <option value="draft">Draft</option>
                                        <option value="aktif">Aktif</option>
                                        <option value="selesai">Selesai</option>
                                        <option value="batal">Batal</option>
                                    </select>
                                    <p class="mt-1 text-xs text-gray-500">
                                        Pilih "Aktif" atau "Selesai" untuk
                                        auto-forward ke Bagian Pengadaan
                                    </p>
                                    <p
                                        v-if="form.errors.status"
                                        class="mt-1 text-sm text-red-600"
                                    >
                                        {{ form.errors.status }}
                                    </p>
                                </div>
                            </div>

                            <!-- Buttons -->
                            <div class="mt-8 flex justify-end space-x-3">
                                <Link
                                    :href="
                                        route(
                                            'kso.show',
                                            permintaan.permintaan_id
                                        )
                                    "
                                    class="px-6 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300"
                                >
                                    Batal
                                </Link>
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    {{
                                        form.processing
                                            ? "Menyimpan..."
                                            : "Simpan"
                                    }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
