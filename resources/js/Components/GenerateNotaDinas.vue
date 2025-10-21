<template>
    <div>
        <!-- Button Trigger -->
        <button
            @click="showModal = true"
            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150"
        >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Generate Nota Dinas
        </button>

        <!-- Modal -->
        <TransitionRoot appear :show="showModal" as="template">
            <Dialog as="div" @close="closeModal" class="relative z-50">
                <TransitionChild
                    as="template"
                    enter="duration-300 ease-out"
                    enter-from="opacity-0"
                    enter-to="opacity-100"
                    leave="duration-200 ease-in"
                    leave-from="opacity-100"
                    leave-to="opacity-0"
                >
                    <div class="fixed inset-0 bg-black bg-opacity-25" />
                </TransitionChild>

                <div class="fixed inset-0 overflow-y-auto">
                    <div class="flex min-h-full items-center justify-center p-4 text-center">
                        <TransitionChild
                            as="template"
                            enter="duration-300 ease-out"
                            enter-from="opacity-0 scale-95"
                            enter-to="opacity-100 scale-100"
                            leave="duration-200 ease-in"
                            leave-from="opacity-100 scale-100"
                            leave-to="opacity-0 scale-95"
                        >
                            <DialogPanel class="w-full max-w-4xl transform overflow-hidden rounded-2xl bg-white p-6 text-left align-middle shadow-xl transition-all">
                                <DialogTitle as="h3" class="text-lg font-medium leading-6 text-gray-900 mb-4">
                                    Generate Nota Dinas - Staff Perencanaan
                                </DialogTitle>

                                <!-- Pilih Jenis Nota Dinas -->
                                <div class="mb-6 border-b pb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-3">
                                        Jenis Nota Dinas <span class="text-red-500">*</span>
                                    </label>
                                    <div class="grid grid-cols-2 gap-4">
                                        <button
                                            type="button"
                                            @click="jenisNotaDinas = 'usulan'"
                                            :class="[
                                                'p-4 border-2 rounded-lg transition-all',
                                                jenisNotaDinas === 'usulan' 
                                                    ? 'border-blue-500 bg-blue-50' 
                                                    : 'border-gray-200 hover:border-blue-300'
                                            ]"
                                        >
                                            <div class="flex items-center justify-center mb-2">
                                                <svg class="w-8 h-8" :class="jenisNotaDinas === 'usulan' ? 'text-blue-500' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                            </div>
                                            <div class="font-semibold text-gray-900">Nota Dinas Usulan</div>
                                            <div class="text-xs text-gray-500 mt-1">Untuk mengusulkan rencana pengadaan</div>
                                        </button>
                                        
                                        <button
                                            type="button"
                                            @click="jenisNotaDinas = 'pembelian'"
                                            :class="[
                                                'p-4 border-2 rounded-lg transition-all',
                                                jenisNotaDinas === 'pembelian' 
                                                    ? 'border-green-500 bg-green-50' 
                                                    : 'border-gray-200 hover:border-green-300'
                                            ]"
                                        >
                                            <div class="flex items-center justify-center mb-2">
                                                <svg class="w-8 h-8" :class="jenisNotaDinas === 'pembelian' ? 'text-green-500' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                                </svg>
                                            </div>
                                            <div class="font-semibold text-gray-900">Nota Dinas Pembelian</div>
                                            <div class="text-xs text-gray-500 mt-1">Untuk proses pembelian yang disetujui</div>
                                        </button>
                                    </div>
                                </div>

                                <!-- Form -->
                                <form @submit.prevent="generateNotaDinas" class="space-y-4">
                                    <!-- Tujuan -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Tujuan Nota Dinas <span class="text-red-500">*</span>
                                        </label>
                                        <textarea
                                            v-model="form.tujuan"
                                            rows="2"
                                            required
                                            class="w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm"
                                            placeholder="Contoh: Permohonan persetujuan pengadaan alat kesehatan"
                                        ></textarea>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <!-- Tanggal -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Tanggal Nota Dinas <span class="text-red-500">*</span>
                                            </label>
                                            <input
                                                type="date"
                                                v-model="form.tanggal"
                                                required
                                                class="w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm"
                                            />
                                        </div>

                                        <!-- Nomor -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Nomor Nota Dinas <span class="text-red-500">*</span>
                                            </label>
                                            <input
                                                type="text"
                                                v-model="form.nomor"
                                                required
                                                class="w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm"
                                                placeholder="ND/PEREN/001/2024"
                                            />
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <!-- Kepada -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Kepada (Penerima) <span class="text-red-500">*</span>
                                            </label>
                                            <select
                                                v-model="form.kepada"
                                                required
                                                class="w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm"
                                            >
                                                <option value="">-- Pilih Penerima --</option>
                                                <option value="Direktur">Direktur</option>
                                                <option value="Wakil Direktur">Wakil Direktur</option>
                                                <option value="Kepala Bidang">Kepala Bidang</option>
                                                <option value="Bagian KSO">Bagian KSO</option>
                                                <option value="Bagian Pengadaan">Bagian Pengadaan</option>
                                            </select>
                                        </div>

                                        <!-- Dari -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Dari
                                            </label>
                                            <input
                                                type="text"
                                                v-model="form.dari"
                                                readonly
                                                class="w-full border-gray-300 bg-gray-50 rounded-md shadow-sm"
                                            />
                                        </div>
                                    </div>

                                    <!-- Perihal -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Perihal (Topik Singkat) <span class="text-red-500">*</span>
                                        </label>
                                        <input
                                            type="text"
                                            v-model="form.perihal"
                                            required
                                            class="w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm"
                                            placeholder="Rencana Pengadaan Alat Kesehatan"
                                        />
                                    </div>

                                    <!-- Fields khusus Nota Dinas Pembelian -->
                                    <div v-if="jenisNotaDinas === 'pembelian'" class="space-y-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                                        <h4 class="font-semibold text-green-800 mb-3">Informasi Pembelian</h4>
                                        
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <!-- Usulan Ruangan -->
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                                    Usulan Ruangan <span class="text-red-500">*</span>
                                                </label>
                                                <input
                                                    type="text"
                                                    v-model="form.usulan_ruangan"
                                                    :required="jenisNotaDinas === 'pembelian'"
                                                    class="w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm"
                                                    placeholder="IGD, Poli, Rawat Inap, dll"
                                                />
                                            </div>

                                            <!-- Sifat -->
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                                    Sifat <span class="text-red-500">*</span>
                                                </label>
                                                <select
                                                    v-model="form.sifat"
                                                    :required="jenisNotaDinas === 'pembelian'"
                                                    class="w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm"
                                                >
                                                    <option value="">-- Pilih Sifat --</option>
                                                    <option value="Segera">Segera</option>
                                                    <option value="Biasa">Biasa</option>
                                                    <option value="Sangat Segera">Sangat Segera</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Dasar -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Dasar (Regulasi/SPT/Rapat)
                                        </label>
                                        <textarea
                                            v-model="form.dasar"
                                            rows="3"
                                            class="w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm"
                                            placeholder="1. Peraturan...&#10;2. Surat Tugas...&#10;3. Hasil Rapat..."
                                        ></textarea>
                                    </div>

                                    <!-- Uraian -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Uraian / Penjelasan <span class="text-red-500">*</span>
                                        </label>
                                        <textarea
                                            v-model="form.uraian"
                                            rows="5"
                                            required
                                            class="w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm"
                                            placeholder="Jelaskan detail rencana pengadaan, analisis kebutuhan, spesifikasi, dll"
                                        ></textarea>
                                    </div>

                                    <!-- Rekomendasi -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Rekomendasi / Permohonan Tindak Lanjut <span class="text-red-500">*</span>
                                        </label>
                                        <textarea
                                            v-model="form.rekomendasi"
                                            rows="3"
                                            required
                                            class="w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm"
                                            placeholder="Mohon persetujuan / perkenan / arahan..."
                                        ></textarea>
                                    </div>

                                    <!-- Penutup -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Penutup
                                        </label>
                                        <textarea
                                            v-model="form.penutup"
                                            rows="2"
                                            class="w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm"
                                        ></textarea>
                                    </div>

                                    <!-- Penandatangan -->
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Nama Penandatangan <span class="text-red-500">*</span>
                                            </label>
                                            <input
                                                type="text"
                                                v-model="form.ttd_nama"
                                                required
                                                class="w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm"
                                            />
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Jabatan Penandatangan <span class="text-red-500">*</span>
                                            </label>
                                            <input
                                                type="text"
                                                v-model="form.ttd_jabatan"
                                                required
                                                class="w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm"
                                            />
                                        </div>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="flex justify-between items-center pt-4 border-t border-gray-200">
                                        <button
                                            type="button"
                                            @click="previewNotaDinas"
                                            class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700"
                                        >
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                            Preview
                                        </button>

                                        <div class="flex gap-2">
                                            <button
                                                type="button"
                                                @click="closeModal"
                                                class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50"
                                            >
                                                Batal
                                            </button>
                                            <button
                                                type="submit"
                                                :disabled="processing"
                                                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 disabled:opacity-50"
                                            >
                                                <svg v-if="processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                </svg>
                                                Generate & Download
                                            </button>
                                        </div>
                                    </div>
                                </form>

                                <!-- Preview Area -->
                                <div v-if="showPreview" class="mt-6 border-t border-gray-200 pt-6">
                                    <h4 class="text-md font-semibold text-gray-900 mb-4">Preview Nota Dinas</h4>
                                    <div class="bg-white border border-gray-300 p-8 rounded-lg shadow-sm max-h-96 overflow-y-auto">
                                        <div v-html="previewContent" class="prose prose-sm max-w-none"></div>
                                    </div>
                                </div>
                            </DialogPanel>
                        </TransitionChild>
                    </div>
                </div>
            </Dialog>
        </TransitionRoot>
    </div>
</template>

<script setup>
import { ref, reactive, computed, watch } from 'vue';
import { TransitionRoot, TransitionChild, Dialog, DialogPanel, DialogTitle } from '@headlessui/vue';

const props = defineProps({
    permintaan: Object,
    userLogin: Object,
});

const showModal = ref(false);
const showPreview = ref(false);
const processing = ref(false);
const jenisNotaDinas = ref('usulan'); // 'usulan' or 'pembelian'

const form = reactive({
    tujuan: '',
    tanggal: new Date().toISOString().split('T')[0],
    nomor: '',
    kepada: '',
    dari: 'Staff Perencanaan / Bagian Perencanaan',
    perihal: '',
    usulan_ruangan: '', // Khusus pembelian
    sifat: '', // Khusus pembelian
    dasar: '',
    uraian: '',
    rekomendasi: '',
    penutup: 'Demikian nota dinas ini kami sampaikan, atas perhatian dan kerjasamanya kami ucapkan terima kasih.',
    ttd_nama: props.userLogin?.nama || '',
    ttd_jabatan: 'Staff Perencanaan',
});

const previewContent = ref('');

// Reset form ketika jenis nota dinas berubah
watch(jenisNotaDinas, (newVal) => {
    if (newVal === 'usulan') {
        form.usulan_ruangan = '';
        form.sifat = '';
    }
});

const closeModal = () => {
    showModal.value = false;
    showPreview.value = false;
};

const formatTanggal = (tanggal) => {
    const date = new Date(tanggal);
    const options = { day: 'numeric', month: 'long', year: 'numeric' };
    return date.toLocaleDateString('id-ID', options);
};

const previewNotaDinas = () => {
    // Build table header
    let headerTable = `
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            <tr>
                <td style="width: 150px; vertical-align: top;">Tanggal</td>
                <td style="width: 20px;">:</td>
                <td>${formatTanggal(form.tanggal)}</td>
            </tr>
            <tr>
                <td style="vertical-align: top;">Nomor</td>
                <td>:</td>
                <td>${form.nomor}</td>
            </tr>`;
    
    // Add fields khusus pembelian
    if (jenisNotaDinas.value === 'pembelian') {
        headerTable += `
            <tr>
                <td style="vertical-align: top;">Usulan Ruangan</td>
                <td>:</td>
                <td>${form.usulan_ruangan}</td>
            </tr>
            <tr>
                <td style="vertical-align: top;">Sifat</td>
                <td>:</td>
                <td><strong style="color: ${form.sifat === 'Sangat Segera' ? '#dc2626' : form.sifat === 'Segera' ? '#ea580c' : '#000'}">${form.sifat}</strong></td>
            </tr>`;
    }
    
    // Continue header table
    headerTable += `
            <tr>
                <td style="vertical-align: top;">Kepada</td>
                <td>:</td>
                <td>${form.kepada}</td>
            </tr>
            <tr>
                <td style="vertical-align: top;">Dari</td>
                <td>:</td>
                <td>${form.dari}</td>
            </tr>
            <tr>
                <td style="vertical-align: top;">Perihal</td>
                <td>:</td>
                <td><strong>${form.perihal}</strong></td>
            </tr>
        </table>`;

    const content = `
        <div style="font-family: 'Times New Roman', Times, serif; font-size: 12pt; line-height: 1.6;">
            <div style="text-align: center; margin-bottom: 30px;">
                <h2 style="margin: 0; font-weight: bold; text-decoration: underline;">NOTA DINAS ${jenisNotaDinas.value === 'pembelian' ? 'PEMBELIAN' : 'USULAN'}</h2>
            </div>

            ${headerTable}

            ${form.dasar ? `
            <div style="margin-bottom: 20px;">
                <strong>DASAR:</strong>
                <div style="margin-left: 20px; white-space: pre-line;">${form.dasar}</div>
            </div>
            ` : ''}

            <div style="margin-bottom: 20px;">
                <p style="text-align: justify; margin-bottom: 10px;">
                    Sehubungan dengan ${form.tujuan}, bersama ini kami sampaikan hal-hal sebagai berikut:
                </p>
            </div>

            <div style="margin-bottom: 20px;">
                <strong>URAIAN:</strong>
                <div style="margin-left: 20px; text-align: justify; white-space: pre-line;">${form.uraian}</div>
            </div>

            <div style="margin-bottom: 20px;">
                <strong>REKOMENDASI:</strong>
                <div style="margin-left: 20px; text-align: justify; white-space: pre-line;">${form.rekomendasi}</div>
            </div>

            <div style="margin-bottom: 40px; text-align: justify;">
                ${form.penutup}
            </div>

            <div style="margin-top: 40px; text-align: right;">
                <div style="display: inline-block; text-align: center;">
                    <p style="margin-bottom: 80px;">${form.dari}</p>
                    <p style="margin: 0; text-decoration: underline;"><strong>${form.ttd_nama}</strong></p>
                    <p style="margin: 0;">${form.ttd_jabatan}</p>
                </div>
            </div>
        </div>
    `;
    
    previewContent.value = content;
    showPreview.value = true;
};

const generateNotaDinas = () => {
    processing.value = true;

    // Build header table HTML
    let headerTableHTML = `
    <table>
        <tr>
            <td class="field-label">Tanggal</td>
            <td class="separator">:</td>
            <td>${formatTanggal(form.tanggal)}</td>
        </tr>
        <tr>
            <td class="field-label">Nomor</td>
            <td class="separator">:</td>
            <td>${form.nomor}</td>
        </tr>`;
    
    // Add fields khusus pembelian
    if (jenisNotaDinas.value === 'pembelian') {
        headerTableHTML += `
        <tr>
            <td class="field-label">Usulan Ruangan</td>
            <td class="separator">:</td>
            <td>${form.usulan_ruangan}</td>
        </tr>
        <tr>
            <td class="field-label">Sifat</td>
            <td class="separator">:</td>
            <td><strong style="color: ${form.sifat === 'Sangat Segera' ? '#dc2626' : form.sifat === 'Segera' ? '#ea580c' : '#000'}">${form.sifat}</strong></td>
        </tr>`;
    }
    
    headerTableHTML += `
        <tr>
            <td class="field-label">Kepada</td>
            <td class="separator">:</td>
            <td>${form.kepada}</td>
        </tr>
        <tr>
            <td class="field-label">Dari</td>
            <td class="separator">:</td>
            <td>${form.dari}</td>
        </tr>
        <tr>
            <td class="field-label">Perihal</td>
            <td class="separator">:</td>
            <td><strong>${form.perihal}</strong></td>
        </tr>
    </table>`;

    // Generate HTML content
    const htmlContent = `
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota Dinas ${jenisNotaDinas.value === 'pembelian' ? 'Pembelian' : 'Usulan'} - ${form.nomor}</title>
    <style>
        @page { 
            size: A4; 
            margin: 2.5cm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.6;
            color: #000;
        }
        h2 {
            text-align: center;
            margin-bottom: 30px;
            text-decoration: underline;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        td {
            padding: 2px 0;
        }
        .field-label {
            width: 150px;
            vertical-align: top;
        }
        .separator {
            width: 20px;
        }
        .content {
            margin-bottom: 20px;
            text-align: justify;
        }
        .section-title {
            font-weight: bold;
            margin-bottom: 10px;
        }
        .signature {
            margin-top: 40px;
            text-align: right;
        }
        .signature-box {
            display: inline-block;
            text-align: center;
        }
        .signature-space {
            margin-bottom: 80px;
        }
    </style>
</head>
<body>
    <h2>NOTA DINAS ${jenisNotaDinas.value === 'pembelian' ? 'PEMBELIAN' : 'USULAN'}</h2>

    ${headerTableHTML}

    ${form.dasar ? `
    <div class="content">
        <div class="section-title">DASAR:</div>
        <div style="margin-left: 20px; white-space: pre-line;">${form.dasar}</div>
    </div>
    ` : ''}

    <div class="content">
        <p>Sehubungan dengan ${form.tujuan}, bersama ini kami sampaikan hal-hal sebagai berikut:</p>
    </div>

    <div class="content">
        <div class="section-title">URAIAN:</div>
        <div style="margin-left: 20px; white-space: pre-line;">${form.uraian}</div>
    </div>

    <div class="content">
        <div class="section-title">REKOMENDASI:</div>
        <div style="margin-left: 20px; white-space: pre-line;">${form.rekomendasi}</div>
    </div>

    <div class="content">
        ${form.penutup}
    </div>

    <div class="signature">
        <div class="signature-box">
            <div class="signature-space">${form.dari}</div>
            <div style="text-decoration: underline;"><strong>${form.ttd_nama}</strong></div>
            <div>${form.ttd_jabatan}</div>
        </div>
    </div>
</body>
</html>
    `;

    // Create blob and download
    const blob = new Blob([htmlContent], { type: 'text/html' });
    const url = window.URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    const jenis = jenisNotaDinas.value === 'pembelian' ? 'Pembelian' : 'Usulan';
    link.download = `Nota_Dinas_${jenis}_${form.nomor.replace(/\//g, '_')}_${Date.now()}.html`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    window.URL.revokeObjectURL(url);

    processing.value = false;
    
    // Show success message
    alert(`Nota Dinas ${jenis} berhasil di-generate! File HTML telah didownload. Anda bisa membukanya di browser dan print ke PDF.`);
    
    closeModal();
};
</script>
