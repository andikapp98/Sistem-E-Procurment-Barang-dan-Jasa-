<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Buat Perencanaan Pengadaan - Permintaan #{{ permintaan.permintaan_id }}
                </h2>
                <Link :href="route('staff-perencanaan.show', permintaan.permintaan_id)" class="text-sm text-gray-600 hover:text-gray-900">
                    ← Kembali
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <!-- Info Permintaan -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            Informasi Permintaan
                        </h3>
                        <dl class="grid grid-cols-2 gap-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Bidang</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ permintaan.bidang }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Tanggal</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ new Date(permintaan.tanggal_permintaan).toLocaleDateString('id-ID') }}</dd>
                            </div>
                            <div class="col-span-2">
                                <dt class="text-sm font-medium text-gray-500">Deskripsi</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ permintaan.deskripsi }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Pemohon</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ permintaan.user?.nama || '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Status</dt>
                                <dd class="mt-1">
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        {{ permintaan.status.toUpperCase() }}
                                    </span>
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Form Perencanaan -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900">
                                Form Perencanaan Pengadaan
                            </h3>
                            <p class="text-sm text-gray-600 mt-1">
                                Isi data perencanaan dengan lengkap dan akurat
                            </p>
                        </div>

                        <form @submit.prevent="submit">
                            <!-- Metode Pengadaan -->
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Metode Pengadaan <span class="text-red-500">*</span>
                                </label>
                                <select v-model="form.metode_pengadaan" required
                                    class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">
                                    <option value="">-- Pilih Metode Pengadaan --</option>
                                    <option value="Lelang Umum">Lelang Umum (> 200 juta)</option>
                                    <option value="Lelang Terbatas">Lelang Terbatas (50-200 juta)</option>
                                    <option value="Seleksi Umum">Seleksi Umum (Jasa Konsultansi)</option>
                                    <option value="Penunjukan Langsung">Penunjukan Langsung (Darurat/Supplier Tunggal)</option>
                                    <option value="E-Purchasing">E-Purchasing (Barang Katalog)</option>
                                    <option value="Swakelola">Swakelola (Dikerjakan Sendiri)</option>
                                </select>
                                <p class="mt-1 text-xs text-gray-500">Pilih metode sesuai nilai dan jenis pengadaan</p>
                            </div>

                            <!-- Estimasi Biaya -->
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Estimasi Biaya (Rp) <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3 top-3 text-gray-500">Rp</span>
                                    <input type="number" v-model.number="form.estimasi_biaya" required min="0" step="1000"
                                        class="w-full pl-12 rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500"
                                        placeholder="0">
                                </div>
                                <p class="mt-1 text-xs text-gray-500">
                                    <span v-if="form.estimasi_biaya" class="font-medium text-gray-700">
                                        {{ formatCurrency(form.estimasi_biaya) }}
                                    </span>
                                    <span v-else>Masukkan estimasi biaya pengadaan</span>
                                </p>
                            </div>

                            <!-- Sumber Dana -->
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Sumber Dana <span class="text-red-500">*</span>
                                </label>
                                <select v-model="form.sumber_dana" required
                                    class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">
                                    <option value="">-- Pilih Sumber Dana --</option>
                                    <option value="APBN">APBN - Anggaran Pendapatan dan Belanja Negara</option>
                                    <option value="APBD">APBD - Anggaran Pendapatan dan Belanja Daerah</option>
                                    <option value="Hibah">Hibah - Dana Hibah dari Donor</option>
                                    <option value="BLUD">BLUD - Badan Layanan Umum Daerah</option>
                                    <option value="Lainnya">Lainnya - Sumber Dana Lain</option>
                                </select>
                                <p class="mt-1 text-xs text-gray-500">Tentukan sumber pendanaan pengadaan</p>
                            </div>

                            <!-- Jadwal Pelaksanaan -->
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Jadwal Pelaksanaan (Target Selesai) <span class="text-red-500">*</span>
                                </label>
                                <input type="date" v-model="form.jadwal_pelaksanaan" required :min="minDate"
                                    class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">
                                <p class="mt-1 text-xs text-gray-500">Target waktu penyelesaian pengadaan</p>
                            </div>

                            <!-- Catatan Perencanaan -->
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Catatan Perencanaan
                                </label>
                                <textarea v-model="form.catatan_perencanaan" rows="4"
                                    class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500"
                                    placeholder="Tambahkan catatan atau pertimbangan khusus dalam perencanaan ini"></textarea>
                                <p class="mt-1 text-xs text-gray-500">Opsional: Berikan catatan tambahan jika diperlukan</p>
                            </div>

                            <!-- Disposisi Ke -->
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Disposisi ke Bagian <span class="text-red-500">*</span>
                                </label>
                                <select v-model="form.disposisi_ke" required
                                    class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">
                                    <option value="">-- Pilih Bagian Tujuan --</option>
                                    <option value="Bagian Pengadaan">Bagian Pengadaan - Untuk proses lelang/pembelian</option>
                                    <option value="Bagian KSO">Bagian KSO - Untuk kerjasama operasional</option>
                                    <option value="Bagian Keuangan">Bagian Keuangan - Untuk verifikasi anggaran</option>
                                    <option value="Kepala Bidang">Kepala Bidang - Untuk koordinasi lebih lanjut</option>
                                </select>
                                <p class="mt-1 text-xs text-gray-500">Pilih bagian yang akan melaksanakan pengadaan</p>
                            </div>

                            <!-- Preview Section -->
                            <div v-if="form.metode_pengadaan && form.estimasi_biaya && form.sumber_dana" 
                                class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                                <h4 class="text-sm font-semibold text-green-900 mb-3 flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Preview Perencanaan
                                </h4>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-gray-700">Metode:</span>
                                        <span class="font-medium text-gray-900">{{ form.metode_pengadaan }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-700">Estimasi Biaya:</span>
                                        <span class="font-medium text-gray-900">{{ formatCurrency(form.estimasi_biaya) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-700">Sumber Dana:</span>
                                        <span class="font-medium text-gray-900">{{ form.sumber_dana }}</span>
                                    </div>
                                    <div v-if="form.jadwal_pelaksanaan" class="flex justify-between">
                                        <span class="text-gray-700">Target Selesai:</span>
                                        <span class="font-medium text-gray-900">{{ new Date(form.jadwal_pelaksanaan).toLocaleDateString('id-ID') }}</span>
                                    </div>
                                    <div v-if="form.disposisi_ke" class="flex justify-between">
                                        <span class="text-gray-700">Disposisi ke:</span>
                                        <span class="font-medium text-gray-900">{{ form.disposisi_ke }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Buttons -->
                            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                                <Link :href="route('staff-perencanaan.show', permintaan.permintaan_id)"
                                    class="px-6 py-2 text-gray-700 hover:text-gray-900">
                                    Batal
                                </Link>
                                <button type="submit" :disabled="processing"
                                    class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                                    <svg v-if="processing" class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span v-if="!processing">Simpan & Disposisi</span>
                                    <span v-else>Menyimpan...</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, router } from '@inertiajs/vue3';

const props = defineProps({
    permintaan: Object,
});

const processing = ref(false);

const form = ref({
    metode_pengadaan: '',
    estimasi_biaya: null,
    sumber_dana: '',
    jadwal_pelaksanaan: '',
    catatan_perencanaan: '',
    disposisi_ke: '',
});

// Minimum date is today
const minDate = computed(() => {
    const today = new Date();
    return today.toISOString().split('T')[0];
});

const formatCurrency = (value) => {
    if (!value) return 'Rp 0';
    return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
};

const submit = () => {
    processing.value = true;
    
    router.post(route('staff-perencanaan.perencanaan.store', props.permintaan.permintaan_id), form.value, {
        onFinish: () => {
            processing.value = false;
        },
    });
};
</script>
