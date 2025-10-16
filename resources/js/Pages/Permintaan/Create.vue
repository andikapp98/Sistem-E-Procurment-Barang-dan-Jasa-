<template>
	<AuthenticatedLayout>
		<template #header>
			<h2 class="font-semibold text-xl text-gray-800 leading-tight">Buat Permintaan</h2>
		</template>

		<div class="py-12">
			<div class="mx-auto px-2 sm:px-4">
				<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
					<form @submit.prevent="submit">
						<!-- Bidang -->
						<div class="mb-4">
							<label class="block text-sm font-medium text-gray-700">Bidang</label>
							<select v-model="form.bidang" class="mt-1 block w-full rounded-md border-gray-300">
								<option value="">-- Pilih bidang --</option>
								<option value="Instalasi Gawat Darurat">Instalasi Gawat Darurat</option>
								<option value="Instalasi Rawat Jalan">Instalasi Rawat Jalan</option>
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

						<!-- Deskripsi -->
						<div class="mb-4">
							<label class="block text-sm font-medium text-gray-700">Deskripsi</label>
							<textarea v-model="form.deskripsi" class="mt-1 block w-full rounded-md border-gray-300" rows="5"></textarea>
							<InputError :message="form.errors.deskripsi" class="mt-2" />
						</div>

						<!-- Tanggal Permintaan -->
						<div class="mb-4">
							<label class="block text-sm font-medium text-gray-700">Tanggal Permintaan</label>
							<TextInput type="date" v-model="form.tanggal_permintaan" class="mt-1 block w-full" />
							<InputError :message="form.errors.tanggal_permintaan" class="mt-2" />
						</div>

						<!-- PIC Pimpinan -->
						<div class="mb-4">
							<label class="block text-sm font-medium text-gray-700">PIC Pimpinan</label>
							<TextInput type="text" v-model="form.pic_pimpinan" class="mt-1 block w-full" placeholder="Nama pimpinan" />
							<InputError :message="form.errors.pic_pimpinan" class="mt-2" />
						</div>

						<!-- No Nota Dinas -->
						<div class="mb-4">
							<label class="block text-sm font-medium text-gray-700">No Nota Dinas</label>
							<TextInput type="text" v-model="form.no_nota_dinas" class="mt-1 block w-full" placeholder="Nomor nota dinas" />
							<InputError :message="form.errors.no_nota_dinas" class="mt-2" />
						</div>

						<!-- Link Scan -->
						<div class="mb-4">
							<label class="block text-sm font-medium text-gray-700">Link Scan Dokumen</label>
							<TextInput type="url" v-model="form.link_scan" class="mt-1 block w-full" placeholder="https://..." />
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
						</div>

						<!-- Actions -->
						<div class="flex items-center justify-end space-x-3">
							<Link :href="route('permintaan.index')" class="text-gray-600">Batal</Link>
							<PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">Buat</PrimaryButton>
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
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { Link, useForm } from "@inertiajs/vue3";

const form = useForm({
	bidang: "",
	deskripsi: "",
	tanggal_permintaan: "",
	pic_pimpinan: "",
	no_nota_dinas: "",
	link_scan: "",
	status: "",
});

const submit = () => {
	form.post(route("permintaan.store"));
};
</script>
