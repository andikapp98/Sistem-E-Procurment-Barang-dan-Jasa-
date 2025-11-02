<template>
    <div class="bg-white p-8 max-w-4xl mx-auto">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold mb-2">RSUD IBNU SINA KABUPATEN GRESIK</h1>
            <h2 class="text-xl font-semibold mb-4">NOTA DINAS</h2>
            <div class="border-b-2 border-black mb-6"></div>
        </div>

        <div v-if="permintaan.nota_dinas" class="space-y-4">
            <div class="grid grid-cols-3 gap-4">
                <div class="col-span-1 font-semibold">Nomor</div>
                <div class="col-span-2">: {{ permintaan.nota_dinas.no_nota }}</div>
                
                <div class="col-span-1 font-semibold">Tanggal</div>
                <div class="col-span-2">: {{ formatDate(permintaan.nota_dinas.tanggal_nota) }}</div>
                
                <div class="col-span-1 font-semibold">Kepada</div>
                <div class="col-span-2">: {{ permintaan.nota_dinas.kepada }}</div>
                
                <div class="col-span-1 font-semibold">Dari</div>
                <div class="col-span-2">: {{ permintaan.nota_dinas.dari }}</div>
                
                <div class="col-span-1 font-semibold">Sifat</div>
                <div class="col-span-2">: {{ permintaan.nota_dinas.sifat || '-' }}</div>
                
                <div class="col-span-1 font-semibold">Lampiran</div>
                <div class="col-span-2">: {{ permintaan.nota_dinas.lampiran || '-' }}</div>
                
                <div class="col-span-1 font-semibold">Perihal</div>
                <div class="col-span-2">: {{ permintaan.nota_dinas.perihal }}</div>
            </div>

            <div class="mt-8">
                <div class="mb-4">
                    <h3 class="font-semibold mb-2">Detail Permintaan:</h3>
                    <div class="whitespace-pre-wrap">{{ permintaan.nota_dinas.detail || permintaan.deskripsi }}</div>
                </div>
            </div>

            <div class="mt-12 grid grid-cols-2 gap-8">
                <div></div>
                <div class="text-center">
                    <p class="mb-16">Mengetahui,</p>
                    <p class="font-semibold">{{ permintaan.nota_dinas.mengetahui || permintaan.pic_pimpinan }}</p>
                    <p>{{ permintaan.user?.jabatan || 'Kepala Ruang' }}</p>
                </div>
            </div>
        </div>

        <div v-else class="text-center py-12 text-gray-500">
            <p>Nota Dinas tidak ditemukan</p>
        </div>

        <div class="mt-8 text-center no-print">
            <button
                @click="printPage"
                class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 mr-2"
            >
                Cetak
            </button>
            <Link
                :href="route('kepala-ruang.show', permintaan.permintaan_id)"
                class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700"
            >
                Kembali
            </Link>
        </div>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    permintaan: Object,
});

const formatDate = (dateString) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
    });
};

const printPage = () => {
    window.print();
};
</script>

<style>
@media print {
    .no-print {
        display: none;
    }
    
    body {
        margin: 0;
        padding: 0;
    }
}
</style>
