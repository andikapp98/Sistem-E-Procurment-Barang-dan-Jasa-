<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        {{ permintaan.status === 'ditolak' ? 'Edit & Ajukan Ulang Permintaan' : 'Edit Permintaan' }}
                    </h2>
                    <p v-if="permintaan.status === 'ditolak'" class="text-sm text-red-600 font-medium mt-1">
                        ⚠️ Permintaan ini ditolak. Lakukan perbaikan dan ajukan ulang.
                    </p>
                </div>
                <Link
                    :href="route('kepala-ruang.show', permintaan.permintaan_id)"
                    class="text-gray-600 hover:text-gray-900"
                >
                    ← Kembali
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto px-2 sm:px-4">
                <!-- Alert Ditolak -->
                <div v-if="permintaan.status === 'ditolak'" class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-bold text-red-800">
                                Permintaan Ditolak
                            </h3>
                            <div class="mt-2 text-sm text-red-700">
                                <p>Silakan periksa dan perbaiki detail permintaan di bawah ini, kemudian simpan untuk mengajukan ulang.</p>
                                <p class="mt-1 font-medium">Status akan otomatis berubah menjadi "Diajukan" setelah Anda menyimpan perubahan.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <form @submit.prevent="submit">
                        <!-- Bidang -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Bidang</label>
                            <select v-model="form.bidang" class="mt-1 block w-full rounded-md border-gray-300">
                                <option value="">-- Pilih bidang --</option>
                                <option value="Instalasi Gawat Darurat">Instalasi Gawat Darurat</option>
                                <option value="Instalasi Rawat Inap">Instalasi Rawat Inap</option>
                                <option value="Instalasi Rawat Inap">Instalasi Rawat Inap</option>
                                <option value="Instalasi Bedah Sentral">Instalasi Bedah Sentral</option>
                                <option value="Instalasi Intensif Care">Instalasi Intensif Care</option>
                                <option value="Instalasi Farmasi">Instalasi Farmasi</option>
                                <option value="Instalasi Laboratorium Patologi Klinik">Instalasi Laboratorium Patologi Klinik</option>
                                <option value="Instalasi Radiologi">Instalasi Radiologi</option>
                                <option value="Instalasi Rehabilitasi Medik">Instalasi Rehabilitasi Medik</option>
                                <option value="Instalasi Gizi">Instalasi Gizi</option>
                                <option value="Instalasi Kedokteran Forensik dan Medikolegal">Instalasi Kedokteran Forensik dan Medikolegal</option>
                                <option value="Unit Hemodialisa">Unit Hemodialisa</option>
                                <option value="Unit Bank Darah Rumah Sakit">Unit Bank Darah Rumah Sakit</option>
                                <option value="Unit Laboratorium Patologi Anatomi">Unit Laboratorium Patologi Anatomi</option>
                                <option value="Unit Sterilisasi Sentral">Unit Sterilisasi Sentral</option>
                                <option value="Unit Endoskopi">Unit Endoskopi</option>
                                <option value="Unit Pemasaran dan Promosi Kesehatan Rumah Sakit">Unit Pemasaran dan Promosi Kesehatan Rumah Sakit</option>
                                <option value="Unit Rekam Medik">Unit Rekam Medik</option>
                                <option value="Instalasi Pendidikan dan Penelitian">Instalasi Pendidikan dan Penelitian</option>
                                <option value="Instalasi Pemeliharaan Sarana">Instalasi Pemeliharaan Sarana</option>
                                <option value="Instalasi Penyehatan Lingkungan">Instalasi Penyehatan Lingkungan</option>
                                <option value="Unit Teknologi Informasi">Unit Teknologi Informasi</option>
                                <option value="Unit Keselamatan dan Kesehatan Kerja Rumah Sakit">Unit Keselamatan dan Kesehatan Kerja Rumah Sakit</option>
                                <option value="Unit Pengadaan">Unit Pengadaan</option>
                                <option value="Unit Aset & Logistik">Unit Aset & Logistik</option>
                                <option value="Unit Penjaminan">Unit Penjaminan</option>
                                <option value="Unit Pengaduan">Unit Pengaduan</option>
                            </select>
                            <InputError :message="form.errors.bidang" class="mt-2" />
                        </div>

                        <!-- Klasifikasi Permintaan -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Klasifikasi Permintaan <span class="text-red-500">*</span></label>
                            <select v-model="form.klasifikasi_permintaan" class="mt-1 block w-full rounded-md border-gray-300" required>
                                <option value="">-- Pilih Klasifikasi --</option>
                                <option value="Medis">Medis</option>
                                <option value="Non Medis">Non Medis</option>
                                <option value="Penunjang">Penunjang</option>
                            </select>
                            <p class="mt-1 text-xs text-gray-500">Pilih klasifikasi sesuai jenis kebutuhan</p>
                            <InputError :message="form.errors.klasifikasi_permintaan" class="mt-2" />
                        </div>

                        <!-- Deskripsi -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
                            <textarea
                                v-model="form.deskripsi"
                                class="mt-1 block w-full rounded-md border-gray-300"
                                rows="5"
                            ></textarea>
                            <InputError :message="form.errors.deskripsi" class="mt-2" />
                        </div>

                        <!-- Tanggal Permintaan -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Tanggal Permintaan</label>
                            <TextInput
                                type="date"
                                v-model="form.tanggal_permintaan"
                                class="mt-1 block w-full"
                            />
                            <InputError :message="form.errors.tanggal_permintaan" class="mt-2" />
                        </div>

                        <!-- PIC Pimpinan -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">PIC Pimpinan</label>
                            <TextInput
                                type="text"
                                v-model="form.pic_pimpinan"
                                class="mt-1 block w-full"
                                placeholder="Nama pimpinan"
                            />
                            <InputError :message="form.errors.pic_pimpinan" class="mt-2" />
                        </div>

                        <!-- No Nota Dinas -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">No Nota Dinas</label>
                            <TextInput
                                type="text"
                                v-model="form.no_nota_dinas"
                                class="mt-1 block w-full"
                                placeholder="Nomor nota dinas"
                            />
                            <InputError :message="form.errors.no_nota_dinas" class="mt-2" />
                        </div>

                        <!-- Link Scan -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Link Scan Dokumen</label>
                            <TextInput
                                type="url"
                                v-model="form.link_scan"
                                class="mt-1 block w-full"
                                placeholder="https://..."
                            />
                            <InputError :message="form.errors.link_scan" class="mt-2" />
                        </div>

                        <!-- Status -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Status</label>
                            <select v-model="form.status" class="mt-1 block w-full rounded-md border-gray-300">
                                <option value="">-- Pilih status --</option>
                                <option value="diajukan">Diajukan</option>
                                <option value="proses">Proses</option>
                                <option value="disetujui">Disetujui</option>
                            </select>
                            <InputError :message="form.errors.status" class="mt-2" />
                            <p v-if="permintaan.status === 'ditolak'" class="mt-2 text-sm text-red-600">
                                💡 Tip: Ubah status ke "Diajukan" untuk mengajukan ulang permintaan ini.
                            </p>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center justify-end space-x-3">
                            <Link 
                                :href="route('kepala-ruang.show', permintaan.permintaan_id)" 
                                class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300"
                            >
                                Batal
                            </Link>
                            <button
                                type="submit"
                                :class="[
                                    'inline-flex items-center px-6 py-2 rounded-md font-semibold text-white',
                                    permintaan.status === 'ditolak' 
                                        ? 'bg-red-600 hover:bg-red-700' 
                                        : 'bg-indigo-600 hover:bg-indigo-700',
                                    { 'opacity-50 cursor-not-allowed': form.processing }
                                ]"
                                :disabled="form.processing"
                            >
                                <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                {{ permintaan.status === 'ditolak' ? 'Simpan & Ajukan Ulang' : 'Simpan Perubahan' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InputError from "@/Components/InputError.vue";
import TextInput from "@/Components/TextInput.vue";
import { Link, useForm } from "@inertiajs/vue3";

const props = defineProps({
    permintaan: Object,
});

const permintaan = props.permintaan || {};

const form = useForm({
    bidang: permintaan.bidang || "",
    klasifikasi_permintaan: permintaan.klasifikasi_permintaan || "",
    deskripsi: permintaan.deskripsi || "",
    tanggal_permintaan: permintaan.tanggal_permintaan ? permintaan.tanggal_permintaan.split('T')[0] : "",
    pic_pimpinan: permintaan.pic_pimpinan || "",
    no_nota_dinas: permintaan.no_nota_dinas || "",
    link_scan: permintaan.link_scan || "",
    status: permintaan.status === 'ditolak' ? 'diajukan' : permintaan.status || "", // Auto set ke diajukan jika ditolak
    disposisi_tujuan: permintaan.disposisi_tujuan || "",
    wadir_tujuan: permintaan.wadir_tujuan || "",
    kabid_tujuan: permintaan.kabid_tujuan || "",
});

const submit = () => {
    form.put(route("kepala-ruang.update", permintaan.permintaan_id), {
        preserveScroll: true,
        onSuccess: () => {
            // Redirect to show page after success
        },
        onError: (errors) => {
            console.error('Form errors:', errors);
        }
    });
};
</script>
