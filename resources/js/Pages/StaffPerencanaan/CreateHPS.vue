<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Harga Perkiraan Satuan (HPS) - Permintaan #{{ permintaan.permintaan_id }}
                </h2>
                <Link
                    :href="route('staff-perencanaan.show', permintaan.permintaan_id)"
                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50"
                >
                    ← Kembali
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Informasi Permintaan -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg mb-6">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Informasi Permintaan</h3>
                    </div>
                    <div class="p-6">
                        <dl class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">ID Permintaan</dt>
                                <dd class="mt-1 text-sm text-gray-900">#{{ permintaan.permintaan_id }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Bidang</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ permintaan.bidang }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Status</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ permintaan.status }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Form HPS -->
                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Header HPS -->
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Informasi HPS</h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- PPK -->
                                <div>
                                    <label for="ppk" class="block text-sm font-medium text-gray-700">
                                        PPK (Pejabat Pembuat Komitmen) <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        id="ppk"
                                        v-model="form.ppk"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        :class="{ 'border-red-500': errors.ppk }"
                                        placeholder="Nama PPK"
                                    />
                                    <p v-if="errors.ppk" class="mt-1 text-sm text-red-600">{{ errors.ppk }}</p>
                                </div>

                                <!-- Surat Penawaran Harga -->
                                <div>
                                    <label for="surat_penawaran_harga" class="block text-sm font-medium text-gray-700">
                                        Surat Penawaran Harga <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        id="surat_penawaran_harga"
                                        v-model="form.surat_penawaran_harga"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        :class="{ 'border-red-500': errors.surat_penawaran_harga }"
                                        placeholder="Nomor Surat Penawaran Harga"
                                    />
                                    <p v-if="errors.surat_penawaran_harga" class="mt-1 text-sm text-red-600">{{ errors.surat_penawaran_harga }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabel Item HPS -->
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                        <div class="p-6 border-b border-gray-200">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-medium text-gray-900">Daftar Item</h3>
                                <button
                                    type="button"
                                    @click="addItem"
                                    class="inline-flex items-center px-3 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700"
                                >
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Tambah Item
                                </button>
                            </div>
                        </div>

                        <div class="p-6">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item <span class="text-red-500">*</span></th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Volume <span class="text-red-500">*</span></th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Satuan <span class="text-red-500">*</span></th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga Satuan <span class="text-red-500">*</span></th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Merk</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <tr v-for="(item, index) in form.items" :key="index" class="hover:bg-gray-50">
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ index + 1 }}</td>
                                            <td class="px-4 py-3">
                                                <input
                                                    type="text"
                                                    v-model="item.nama_item"
                                                    class="block w-full min-w-[200px] rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                                    :class="{ 'border-red-500': itemErrors[index]?.nama_item }"
                                                    placeholder="Nama Item"
                                                />
                                            </td>
                                            <td class="px-4 py-3">
                                                <input
                                                    type="number"
                                                    v-model="item.volume"
                                                    @input="calculateTotal(index)"
                                                    class="block w-24 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                                    :class="{ 'border-red-500': itemErrors[index]?.volume }"
                                                    placeholder="0"
                                                    min="1"
                                                />
                                            </td>
                                            <td class="px-4 py-3">
                                                <select
                                                    v-model="item.satuan"
                                                    class="block w-28 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                                    :class="{ 'border-red-500': itemErrors[index]?.satuan }"
                                                >
                                                    <option value="">Pilih</option>
                                                    <option value="Unit">Unit</option>
                                                    <option value="Pcs">Pcs</option>
                                                    <option value="Set">Set</option>
                                                    <option value="Box">Box</option>
                                                    <option value="Pack">Pack</option>
                                                    <option value="Kg">Kg</option>
                                                    <option value="Liter">Liter</option>
                                                    <option value="Meter">Meter</option>
                                                    <option value="Buah">Buah</option>
                                                    <option value="Lembar">Lembar</option>
                                                </select>
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="relative">
                                                    <span class="absolute left-2 top-2 text-gray-500 text-xs">Rp</span>
                                                    <input
                                                        type="text"
                                                        v-model="item.harga_satuan_formatted"
                                                        @input="handleHargaSatuanInput(index, $event)"
                                                        class="block w-32 pl-8 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                                        :class="{ 'border-red-500': itemErrors[index]?.harga_satuan }"
                                                        placeholder="0"
                                                    />
                                                </div>
                                            </td>
                                            <td class="px-4 py-3">
                                                <input
                                                    type="text"
                                                    v-model="item.type"
                                                    class="block w-32 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                                    placeholder="Type"
                                                />
                                            </td>
                                            <td class="px-4 py-3">
                                                <input
                                                    type="text"
                                                    v-model="item.merk"
                                                    class="block w-32 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                                    placeholder="Merk"
                                                />
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {{ formatRupiahDisplay(item.total) }}
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <button
                                                    type="button"
                                                    @click="removeItem(index)"
                                                    class="text-red-600 hover:text-red-900"
                                                    :disabled="form.items.length === 1"
                                                    :class="{ 'opacity-50 cursor-not-allowed': form.items.length === 1 }"
                                                >
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot class="bg-gray-50">
                                        <tr>
                                            <td colspan="7" class="px-4 py-3 text-right font-semibold text-gray-900">
                                                Grand Total:
                                            </td>
                                            <td colspan="2" class="px-4 py-3 font-bold text-lg text-green-600">
                                                {{ formatRupiahDisplay(grandTotal) }}
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <p v-if="form.items.length === 0" class="mt-4 text-center text-sm text-gray-500">
                                Belum ada item. Klik "Tambah Item" untuk menambahkan.
                            </p>
                        </div>
                    </div>

                    <!-- Info Box -->
                    <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800">Informasi HPS</h3>
                                <div class="mt-2 text-sm text-blue-700">
                                    <p>Harga Perkiraan Satuan (HPS) digunakan sebagai acuan harga dalam proses pengadaan. Total akan dihitung otomatis berdasarkan volume × harga satuan. Pastikan semua data sudah akurat.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="flex items-center justify-end gap-3">
                        <Link
                            :href="route('staff-perencanaan.show', permintaan.permintaan_id)"
                            class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50"
                        >
                            Batal
                        </Link>
                        <button
                            type="submit"
                            :disabled="processing || form.items.length === 0"
                            class="inline-flex items-center px-4 py-2 bg-[#028174] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#03a089] focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50"
                        >
                            <svg v-if="processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            {{ processing ? 'Menyimpan...' : 'Simpan HPS' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    permintaan: Object,
});

const form = useForm({
    ppk: '',
    surat_penawaran_harga: '',
    items: [
        {
            nama_item: '',
            volume: '',
            satuan: '',
            harga_satuan: '',
            harga_satuan_formatted: '',
            type: '',
            merk: '',
            total: 0
        }
    ]
});

const processing = ref(false);
const errors = ref({});
const itemErrors = ref([]);

// Format rupiah
const formatRupiah = (value) => {
    if (!value) return '';
    const number = value.toString().replace(/[^,\d]/g, '');
    const split = number.split(',');
    const sisa = split[0].length % 3;
    let rupiah = split[0].substr(0, sisa);
    const ribuan = split[0].substr(sisa).match(/\d{3}/gi);

    if (ribuan) {
        const separator = sisa ? '.' : '';
        rupiah += separator + ribuan.join('.');
    }

    rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
    return rupiah;
};

const formatRupiahDisplay = (value) => {
    if (!value) return 'Rp 0';
    return 'Rp ' + formatRupiah(value.toString());
};

const handleHargaSatuanInput = (index, event) => {
    const value = event.target.value.replace(/\./g, '').replace(/,/g, '.');
    form.items[index].harga_satuan = value;
    form.items[index].harga_satuan_formatted = formatRupiah(value);
    calculateTotal(index);
};

const calculateTotal = (index) => {
    const item = form.items[index];
    const volume = parseFloat(item.volume) || 0;
    const harga = parseFloat(item.harga_satuan) || 0;
    item.total = volume * harga;
};

const grandTotal = computed(() => {
    return form.items.reduce((sum, item) => sum + (parseFloat(item.total) || 0), 0);
});

const addItem = () => {
    form.items.push({
        nama_item: '',
        volume: '',
        satuan: '',
        harga_satuan: '',
        harga_satuan_formatted: '',
        type: '',
        merk: '',
        total: 0
    });
};

const removeItem = (index) => {
    if (form.items.length > 1) {
        form.items.splice(index, 1);
    }
};

const submit = () => {
    // Reset errors
    errors.value = {};
    itemErrors.value = [];

    // Validasi header
    if (!form.ppk) {
        errors.value.ppk = 'PPK harus diisi';
        return;
    }
    if (!form.surat_penawaran_harga) {
        errors.value.surat_penawaran_harga = 'Surat Penawaran Harga harus diisi';
        return;
    }

    // Validasi items
    let hasItemError = false;
    form.items.forEach((item, index) => {
        itemErrors.value[index] = {};
        
        if (!item.nama_item) {
            itemErrors.value[index].nama_item = true;
            hasItemError = true;
        }
        if (!item.volume || item.volume <= 0) {
            itemErrors.value[index].volume = true;
            hasItemError = true;
        }
        if (!item.satuan) {
            itemErrors.value[index].satuan = true;
            hasItemError = true;
        }
        if (!item.harga_satuan || item.harga_satuan <= 0) {
            itemErrors.value[index].harga_satuan = true;
            hasItemError = true;
        }
    });

    if (hasItemError) {
        alert('Mohon lengkapi semua field yang wajib diisi (bertanda *)');
        return;
    }

    processing.value = true;

    form.post(route('staff-perencanaan.hps.store', props.permintaan.permintaan_id), {
        onSuccess: () => {
            processing.value = false;
        },
        onError: (err) => {
            errors.value = err;
            processing.value = false;
        },
    });
};
</script>
