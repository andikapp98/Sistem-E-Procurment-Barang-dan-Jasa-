<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { ref, computed } from 'vue';

const props = defineProps({
    permintaan: Object,
    perencanaan: Object,
    hasPerencanaan: {
        type: Boolean,
        default: true
    }
});

const form = useForm({
    no_kso: "",
    tanggal_kso: new Date().toISOString().split("T")[0],
    pihak_pertama: "RSUD Ibnu Sina Kabupaten Gresik",
    pihak_kedua: "",
    isi_kerjasama: "",
    nilai_kontrak: "",
    file_pks: null,
    file_mou: null,
    keterangan: "",
});

const pksFileName = ref('');
const mouFileName = ref('');

// Minimum date is today
const minDate = computed(() => {
    const today = new Date();
    return today.toISOString().split('T')[0];
});

const handlePKSUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.file_pks = file;
        pksFileName.value = file.name;
    }
};

const handleMoUUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.file_mou = file;
        mouFileName.value = file.name;
    }
};

const submit = () => {
    form.post(route("kso.store", props.permintaan.permintaan_id), {
        preserveScroll: true,
        forceFormData: true, // Force multipart form data for file uploads
        onSuccess: (page) => {
            console.log('KSO berhasil dibuat');
        },
        onError: (errors) => {
            console.error('Error creating KSO:', errors);
            // Log individual errors for debugging
            Object.keys(errors).forEach(key => {
                console.error(`Error ${key}:`, errors[key]);
            });
        },
        onFinish: () => {
            console.log('Form submission finished');
        },
    });
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
                    :href="route('kso.show', { permintaan: permintaan.permintaan_id })"
                    class="text-sm text-gray-600 hover:text-gray-900"
                >
                    ← Kembali
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <!-- Warning jika belum ada perencanaan -->
                <div v-if="!hasPerencanaan" class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                <strong class="font-bold">Peringatan!</strong><br>
                                Permintaan ini belum memiliki dokumen perencanaan (DPP). Anda tetap dapat mengisi form KSO, namun saat submit akan error jika belum ada DPP. Pastikan Staff Perencanaan sudah membuat dokumen perencanaan terlebih dahulu.
                            </p>
                        </div>
                    </div>
                </div>
                
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

                        <!-- Error Message -->
                        <div v-if="form.hasErrors" class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded relative">
                            <strong class="font-bold">Error!</strong>
                            <span class="block sm:inline ml-2">Terjadi kesalahan. Silakan periksa form Anda:</span>
                            <ul class="mt-2 ml-5 list-disc text-sm">
                                <li v-for="(error, key) in form.errors" :key="key">
                                    <strong>{{ key }}:</strong> {{ Array.isArray(error) ? error[0] : error }}
                                </li>
                            </ul>
                        </div>

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
                                        :min="minDate"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    />
                                    <p
                                        v-if="form.errors.tanggal_kso"
                                        class="mt-1 text-sm text-red-600"
                                    >
                                        {{ form.errors.tanggal_kso }}
                                    </p>
                                    <p class="mt-1 text-xs text-gray-500">Tanggal tidak boleh kurang dari hari ini</p>
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
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 bg-gray-50"
                                        readonly
                                    />
                                </div>

                                <!-- Pihak Kedua -->
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 mb-2"
                                    >
                                        Pihak Kedua (Vendor/Partner)
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
                                        rows="4"
                                        required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        placeholder="Jelaskan detail kerjasama yang akan dilakukan..."
                                    ></textarea>
                                    <p
                                        v-if="form.errors.isi_kerjasama"
                                        class="mt-1 text-sm text-red-600"
                                    >
                                        {{ form.errors.isi_kerjasama }}
                                    </p>
                                </div>

                                <!-- Nilai Kontrak -->
                                <div class="md:col-span-2">
                                    <label
                                        class="block text-sm font-medium text-gray-700 mb-2"
                                    >
                                        Nilai Kontrak (Rp)
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        v-model="form.nilai_kontrak"
                                        type="number"
                                        required
                                        min="0"
                                        step="1"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        placeholder="1000000"
                                    />
                                    <p
                                        v-if="form.errors.nilai_kontrak"
                                        class="mt-1 text-sm text-red-600"
                                    >
                                        {{ form.errors.nilai_kontrak }}
                                    </p>
                                    <p class="mt-1 text-xs text-gray-500">Masukkan nilai dalam Rupiah (angka saja)</p>
                                </div>

                                <!-- Upload PKS (Perjanjian Kerja Sama) -->
                                <div class="md:col-span-2">
                                    <label
                                        class="block text-sm font-medium text-gray-700 mb-2"
                                    >
                                        Upload PKS (Perjanjian Kerja Sama)
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-500 transition-colors">
                                        <input
                                            type="file"
                                            @change="handlePKSUpload"
                                            accept=".pdf,.doc,.docx"
                                            required
                                            class="hidden"
                                            id="pks-upload"
                                        />
                                        <label for="pks-upload" class="cursor-pointer">
                                            <div class="flex flex-col items-center">
                                                <svg class="w-12 h-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                                </svg>
                                                <p class="text-sm text-gray-600 mb-1">
                                                    <span class="text-blue-600 font-medium">Klik untuk upload</span> atau drag and drop
                                                </p>
                                                <p class="text-xs text-gray-500">PDF, DOC, DOCX (Max. 5MB)</p>
                                                <p v-if="pksFileName" class="mt-3 text-sm text-green-600 font-medium">
                                                    ✓ {{ pksFileName }}
                                                </p>
                                            </div>
                                        </label>
                                    </div>
                                    <p
                                        v-if="form.errors.file_pks"
                                        class="mt-1 text-sm text-red-600"
                                    >
                                        {{ form.errors.file_pks }}
                                    </p>
                                </div>

                                <!-- Upload MoU (Memorandum of Understanding) -->
                                <div class="md:col-span-2">
                                    <label
                                        class="block text-sm font-medium text-gray-700 mb-2"
                                    >
                                        Upload MoU (Memorandum of Understanding)
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-500 transition-colors">
                                        <input
                                            type="file"
                                            @change="handleMoUUpload"
                                            accept=".pdf,.doc,.docx"
                                            required
                                            class="hidden"
                                            id="mou-upload"
                                        />
                                        <label for="mou-upload" class="cursor-pointer">
                                            <div class="flex flex-col items-center">
                                                <svg class="w-12 h-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                                </svg>
                                                <p class="text-sm text-gray-600 mb-1">
                                                    <span class="text-blue-600 font-medium">Klik untuk upload</span> atau drag and drop
                                                </p>
                                                <p class="text-xs text-gray-500">PDF, DOC, DOCX (Max. 5MB)</p>
                                                <p v-if="mouFileName" class="mt-3 text-sm text-green-600 font-medium">
                                                    ✓ {{ mouFileName }}
                                                </p>
                                            </div>
                                        </label>
                                    </div>
                                    <p
                                        v-if="form.errors.file_mou"
                                        class="mt-1 text-sm text-red-600"
                                    >
                                        {{ form.errors.file_mou }}
                                    </p>
                                </div>

                                <!-- Keterangan -->
                                <div class="md:col-span-2">
                                    <label
                                        class="block text-sm font-medium text-gray-700 mb-2"
                                    >
                                        Keterangan
                                    </label>
                                    <textarea
                                        v-model="form.keterangan"
                                        rows="4"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        placeholder="Catatan tambahan (opsional)..."
                                    ></textarea>
                                    <p
                                        v-if="form.errors.keterangan"
                                        class="mt-1 text-sm text-red-600"
                                    >
                                        {{ form.errors.keterangan }}
                                    </p>
                                </div>
                            </div>

                            <!-- Info Box -->
                            <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <div class="flex">
                                    <svg class="w-5 h-5 text-blue-600 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                    </svg>
                                    <div class="text-sm text-blue-800">
                                        <p class="font-medium mb-1">Catatan:</p>
                                        <ul class="list-disc list-inside space-y-1">
                                            <li>Upload file PKS (Perjanjian Kerja Sama) yang telah ditandatangani</li>
                                            <li>Upload file MoU (Memorandum of Understanding) yang telah ditandatangani</li>
                                            <li>Pastikan file dalam format PDF, DOC, atau DOCX</li>
                                            <li>Setelah submit, data akan otomatis diteruskan ke Bagian Pengadaan</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- Buttons -->
                            <div class="mt-8 flex justify-end space-x-3">
                                <Link
                                    :href="route('kso.show', { permintaan: permintaan.permintaan_id })"
                                    class="px-6 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300"
                                >
                                    Batal
                                </Link>
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center"
                                >
                                    <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    {{
                                        form.processing
                                            ? "Mengupload..."
                                            : "Simpan & Upload"
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
