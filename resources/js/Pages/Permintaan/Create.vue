<template>
	<AuthenticatedLayout>
		<template #header>
			<h2 class="font-semibold text-xl text-gray-800 leading-tight">Buat Permintaan Baru</h2>
		</template>

		<div class="py-12">
			<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
				<div class="bg-white overflow-hidden shadow-sm rounded-lg">
					<div class="p-6 border-b border-gray-200">
						<h3 class="text-lg font-medium text-gray-900">Form Permintaan Pengadaan</h3>
						<p class="mt-1 text-sm text-gray-600">
							Isi form di bawah untuk membuat permintaan pengadaan barang/jasa
						</p>
					</div>

					<form @submit.prevent="submit" class="p-6 space-y-6">
						<!-- Bidang -->
						<div>
							<label class="block text-sm font-medium text-gray-700 mb-2">
								Bidang / Unit <span class="text-red-500">*</span>
							</label>
							<select 
								v-model="form.bidang" 
								class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
								:class="{ 'bg-gray-100 cursor-not-allowed': userRole === 'kepala_ruang' || userRole === 'kepala_poli' }"
								:disabled="userRole === 'kepala_ruang' || userRole === 'kepala_poli'"
								required
							>
								<option value="">-- Pilih Bidang / Unit --</option>
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
							<p v-if="userRole === 'kepala_ruang'" class="mt-1 text-xs text-gray-500">
								📌 Bidang otomatis diatur untuk Kepala Ruang: Instalasi Rawat Inap
							</p>
							<p v-if="userRole === 'kepala_poli'" class="mt-1 text-xs text-gray-500">
								📌 Bidang otomatis diatur untuk Kepala Poli: Instalasi Rawat Jalan
							</p>
							<InputError :message="form.errors.bidang" class="mt-2" />
						</div>

						<!-- Klasifikasi Permintaan -->
						<div>
							<label class="block text-sm font-medium text-gray-700 mb-2">
								Klasifikasi Permintaan <span class="text-red-500">*</span>
							</label>
							<select 
								v-model="form.klasifikasi_permintaan" 
								class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
								required
							>
								<option value="">-- Pilih Klasifikasi --</option>
								<option value="Medis">Medis</option>
								<option value="Non Medis">Non Medis</option>
								<option value="Penunjang">Penunjang</option>
							</select>
							<p class="mt-1 text-xs text-gray-500">
								Pilih klasifikasi sesuai jenis kebutuhan
							</p>
							<InputError :message="form.errors.klasifikasi_permintaan" class="mt-2" />
						</div>

						<!-- Deskripsi -->
						<div>
							<label class="block text-sm font-medium text-gray-700 mb-2">
								Deskripsi Permintaan <span class="text-red-500">*</span>
							</label>
							<textarea 
								v-model="form.deskripsi" 
								class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
								rows="6"
								placeholder="Jelaskan detail permintaan pengadaan (nama barang/jasa, spesifikasi, jumlah, alasan)"
								required
							></textarea>
							<p class="mt-1 text-xs text-gray-500">
								Sertakan: nama barang/jasa, spesifikasi, jumlah, dan alasan permintaan
							</p>
							<InputError :message="form.errors.deskripsi" class="mt-2" />
						</div>

						<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
							<!-- Tanggal Permintaan -->
							<div>
								<label class="block text-sm font-medium text-gray-700 mb-2">
									Tanggal Permintaan <span class="text-red-500">*</span>
								</label>
								<TextInput 
									type="date" 
									v-model="form.tanggal_permintaan" 
									class="mt-1 block w-full" 
									:min="minDate"
									required
								/>
								<p class="mt-1 text-xs text-gray-500">Tanggal tidak boleh kurang dari hari ini</p>
								<InputError :message="form.errors.tanggal_permintaan" class="mt-2" />
							</div>

							<!-- PIC Pimpinan -->
							<div>
								<label class="block text-sm font-medium text-gray-700 mb-2">
									PIC Pimpinan <span class="text-red-500">*</span>
								</label>
								<TextInput 
									type="text" 
									v-model="form.pic_pimpinan" 
									class="mt-1 block w-full" 
									placeholder="Contoh: Dr. Ahmad Yani, Sp.PD"
									required
								/>
								<p class="mt-1 text-xs text-gray-500">Nama lengkap dengan gelar</p>
								<InputError :message="form.errors.pic_pimpinan" class="mt-2" />
							</div>
						</div>

						<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
							<!-- No Nota Dinas -->
							<div>
								<label class="block text-sm font-medium text-gray-700 mb-2">
									No Nota Dinas <span class="text-red-500">*</span>
								</label>
								<TextInput 
									type="text" 
									v-model="form.no_nota_dinas" 
									class="mt-1 block w-full" 
									placeholder="Contoh: ND/IGD/2025/001/X"
									required
								/>
								<p class="mt-1 text-xs text-gray-500">Format: ND/[Unit]/[Tahun]/[Nomor]/[Bulan]</p>
								<InputError :message="form.errors.no_nota_dinas" class="mt-2" />
							</div>

							<!-- Link Scan -->
							<div>
								<label class="block text-sm font-medium text-gray-700 mb-2">
									Link Scan Dokumen
								</label>
								<TextInput 
									type="url" 
									v-model="form.link_scan" 
									class="mt-1 block w-full" 
									placeholder="https://drive.google.com/..."
								/>
								<p class="mt-1 text-xs text-gray-500">Link Google Drive atau dokumen scan</p>
								<InputError :message="form.errors.link_scan" class="mt-2" />
							</div>
						</div>

						<!-- Disposisi Section -->
						<div class="border-t border-gray-200 pt-6 mt-6">
							<h4 class="text-md font-semibold text-gray-800 mb-4">📋 Disposisi Permintaan</h4>
							
							<!-- Info Auto Routing -->
							<div v-if="form.klasifikasi_permintaan" class="mb-4 bg-blue-50 border-l-4 border-blue-500 p-4 rounded-r-lg">
								<div class="flex">
									<div class="flex-shrink-0">
										<svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
											<path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
										</svg>
									</div>
									<div class="ml-3">
										<p class="text-sm text-blue-700">
											<strong>Routing Otomatis:</strong> {{ getRoutingInfo() }}
										</p>
									</div>
								</div>
							</div>

							<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
								<!-- Disposisi Tujuan (Auto-filled, readonly) -->
								<div>
									<label class="block text-sm font-medium text-gray-700 mb-2">
										Disposisi <span class="text-red-500">*</span>
									</label>
									<input 
										type="text"
										:value="getDisposisiTujuan()"
										class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 shadow-sm"
										readonly
									/>
									<p class="mt-1 text-xs text-gray-500">Otomatis ditentukan berdasarkan klasifikasi permintaan</p>
								</div>

								<!-- Wadir Tujuan (Auto-filled based on klasifikasi) -->
								<div v-if="shouldShowWadirField()">
									<label class="block text-sm font-medium text-gray-700 mb-2">
										Wakil Direktur <span class="text-red-500">*</span>
									</label>
									<input 
										type="text"
										:value="getWadirTujuan()"
										class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 shadow-sm"
										readonly
									/>
								</div>
							</div>

							<!-- Hidden fields for actual form submission -->
							<input type="hidden" v-model="form.disposisi_tujuan" />
							<input type="hidden" v-model="form.wadir_tujuan" />
							<input type="hidden" v-model="form.kabid_tujuan" />

							<!-- Catatan Disposisi -->
							<div class="mt-4">
								<label class="block text-sm font-medium text-gray-700 mb-2">
									Detail / Catatan Disposisi
								</label>
								<textarea 
									v-model="form.catatan_disposisi" 
									class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
									rows="3"
									placeholder="Catatan atau instruksi khusus untuk disposisi"
								></textarea>
								<InputError :message="form.errors.catatan_disposisi" class="mt-2" />
							</div>
						</div>

						<!-- Nota Dinas Section -->
						<div class="border-t border-gray-200 pt-6 mt-6">
							<h4 class="text-md font-semibold text-gray-800 mb-4">📄 Form Nota Dinas <span class="text-red-500">(Wajib Diisi)</span></h4>
							
							<div class="space-y-6">
								<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
									<!-- Kepada -->
									<div>
										<label class="block text-sm font-medium text-gray-700 mb-2">
											Kepada <span class="text-red-500">*</span>
										</label>
										<TextInput 
											type="text" 
											v-model="form.nota_kepada" 
											class="mt-1 block w-full" 
											placeholder="Contoh: Direktur RSUD"
											required
										/>
										<InputError :message="form.errors.nota_kepada" class="mt-2" />
									</div>

									<!-- Dari -->
									<div>
										<label class="block text-sm font-medium text-gray-700 mb-2">
											Dari <span class="text-red-500">*</span>
										</label>
										<TextInput 
											type="text" 
											v-model="form.nota_dari" 
											class="mt-1 block w-full" 
											placeholder="Contoh: Kepala Instalasi Gawat Darurat"
											required
										/>
										<InputError :message="form.errors.nota_dari" class="mt-2" />
									</div>
								</div>

								<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
									<!-- Tanggal -->
									<div>
										<label class="block text-sm font-medium text-gray-700 mb-2">
											Tanggal <span class="text-red-500">*</span>
										</label>
										<TextInput 
											type="date" 
											v-model="form.nota_tanggal_nota" 
											class="mt-1 block w-full" 
											required
										/>
										<InputError :message="form.errors.nota_tanggal_nota" class="mt-2" />
									</div>

									<!-- Nomor -->
									<div>
										<label class="block text-sm font-medium text-gray-700 mb-2">
											Nomor <span class="text-red-500">*</span>
										</label>
										<TextInput 
											type="text" 
											v-model="form.nota_no_nota" 
											class="mt-1 block w-full" 
											placeholder="Contoh: 001/ND/IGD/X/2025"
											required
										/>
										<InputError :message="form.errors.nota_no_nota" class="mt-2" />
									</div>
								</div>

								<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
									<!-- Sifat -->
									<div>
										<label class="block text-sm font-medium text-gray-700 mb-2">
											Sifat
										</label>
										<select 
											v-model="form.nota_sifat" 
											class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
										>
											<option value="">-- Pilih Sifat --</option>
											<option value="Biasa">Biasa</option>
											<option value="Segera">Segera</option>
											<option value="Sangat Segera">Sangat Segera</option>
											<option value="Rahasia">Rahasia</option>
										</select>
										<InputError :message="form.errors.nota_sifat" class="mt-2" />
									</div>

									<!-- Lampiran (Link Scan) -->
									<div>
										<label class="block text-sm font-medium text-gray-700 mb-2">
											Lampiran (Link Scan)
										</label>
										<TextInput 
											type="url" 
											v-model="form.nota_lampiran" 
											class="mt-1 block w-full" 
											placeholder="https://drive.google.com/..."
										/>
										<p class="mt-1 text-xs text-gray-500">Link dokumen scan nota dinas</p>
										<InputError :message="form.errors.nota_lampiran" class="mt-2" />
									</div>
								</div>

								<!-- Perihal -->
								<div>
									<label class="block text-sm font-medium text-gray-700 mb-2">
										Perihal <span class="text-red-500">*</span>
									</label>
									<TextInput 
										type="text" 
										v-model="form.nota_perihal" 
										class="mt-1 block w-full" 
										placeholder="Contoh: Permohonan Pengadaan Alat Medis"
										required
									/>
									<InputError :message="form.errors.nota_perihal" class="mt-2" />
								</div>

								<!-- Detail -->
								<div>
									<label class="block text-sm font-medium text-gray-700 mb-2">
										Detail
									</label>
									<textarea 
										v-model="form.nota_detail" 
										class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
										rows="4"
										placeholder="Detail isi nota dinas / permintaan"
									></textarea>
									<InputError :message="form.errors.nota_detail" class="mt-2" />
								</div>

								<!-- Mengetahui (Kepala Instalasi) -->
								<div>
									<label class="block text-sm font-medium text-gray-700 mb-2">
										Mengetahui (Kepala Instalasi)
									</label>
									<TextInput 
										type="text" 
										v-model="form.nota_mengetahui" 
										class="mt-1 block w-full" 
										placeholder="Contoh: Dr. Ahmad Yani, Sp.PD"
									/>
									<InputError :message="form.errors.nota_mengetahui" class="mt-2" />
								</div>
							</div>
						</div>

						<!-- Status - Auto set untuk admin -->
						<input type="hidden" v-model="form.status" />

						<!-- Actions -->
						<div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200">
							<Link 
								:href="route('permintaan.index')" 
								class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
							>
								Batal
							</Link>
							<PrimaryButton 
								:class="{ 'opacity-25': form.processing }" 
								:disabled="form.processing"
								class="px-6 py-2"
							>
								<span v-if="form.processing">⏳ Menyimpan...</span>
								<span v-else>💾 Simpan Permintaan</span>
							</PrimaryButton>
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
import { Link, useForm, usePage } from "@inertiajs/vue3";
import { watch, onMounted, computed } from "vue";

const page = usePage();
const userRole = computed(() => page.props.auth.user.role);

// Minimum date is today
const minDate = computed(() => {
	const today = new Date();
	return today.toISOString().split('T')[0];
});

const form = useForm({
	bidang: "",
	klasifikasi_permintaan: "",
	deskripsi: "",
	tanggal_permintaan: new Date().toISOString().split('T')[0], // Default to today
	pic_pimpinan: "",
	no_nota_dinas: "",
	link_scan: "",
	status: "diajukan", // Auto set status diajukan untuk admin
	disposisi_tujuan: "",
	catatan_disposisi: "",
	wadir_tujuan: "",
	kabid_tujuan: "",
	// Nota Dinas fields
	nota_kepada: "",
	nota_dari: "",
	nota_tanggal_nota: new Date().toISOString().split('T')[0], // Default to today
	nota_no_nota: "",
	nota_sifat: "",
	nota_lampiran: "",
	nota_perihal: "",
	nota_detail: "",
	nota_mengetahui: "",
});

// Auto-set bidang untuk Kepala Ruang dan Kepala Poli
onMounted(() => {
	if (userRole.value === 'kepala_ruang') {
		form.bidang = 'Instalasi Rawat Inap';
	} else if (userRole.value === 'kepala_poli') {
		form.bidang = 'Instalasi Rawat Jalan';
	}
});

// Watch klasifikasi_permintaan changes and auto-update disposisi
watch(() => form.klasifikasi_permintaan, (newValue) => {
	if (newValue === 'Non Medis') {
		form.disposisi_tujuan = 'Wakil Direktur → Kepala Bidang';
		form.wadir_tujuan = 'Wadir Umum';
		form.kabid_tujuan = 'Kabid Umum';
	} else if (newValue === 'Medis') {
		form.disposisi_tujuan = 'Wakil Direktur → Kepala Bidang';
		form.wadir_tujuan = 'Wadir Pelayanan';
		form.kabid_tujuan = 'Kabid Yanmed';
	} else if (newValue === 'Penunjang') {
		form.disposisi_tujuan = 'Wakil Direktur → Kepala Bidang';
		form.wadir_tujuan = 'Wadir Pelayanan';
		form.kabid_tujuan = 'Kabid Penunjang';
	} else {
		form.disposisi_tujuan = '';
		form.wadir_tujuan = '';
		form.kabid_tujuan = '';
	}
});

// Helper functions
const getDisposisiTujuan = () => {
	return form.disposisi_tujuan || 'Pilih klasifikasi terlebih dahulu';
};

const getWadirTujuan = () => {
	return form.wadir_tujuan || '';
};

const shouldShowWadirField = () => {
	return form.klasifikasi_permintaan && form.wadir_tujuan;
};

const getRoutingInfo = () => {
	if (form.klasifikasi_permintaan === 'Non Medis') {
		return 'Permintaan Non Medis akan diteruskan ke Wadir Umum → Kabid Umum';
	} else if (form.klasifikasi_permintaan === 'Medis') {
		return 'Permintaan Medis akan diteruskan ke Wadir Pelayanan → Kabid Yanmed';
	} else if (form.klasifikasi_permintaan === 'Penunjang') {
		return 'Permintaan Penunjang akan diteruskan ke Wadir Pelayanan → Kabid Penunjang';
	}
	return '';
};

const submit = () => {
	form.transform((data) => ({
		...data,
		_token: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
	})).post(route("permintaan.store"), {
		preserveScroll: true,
		onError: (errors) => {
			console.error('Form errors:', errors);
		}
	});
};
</script>
