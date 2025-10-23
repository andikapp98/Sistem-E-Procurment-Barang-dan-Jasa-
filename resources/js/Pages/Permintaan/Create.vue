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
							<InputError :message="form.errors.bidang" class="mt-2" />
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
									required
								/>
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
							
							<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
								<!-- Disposisi Tujuan -->
								<div>
									<label class="block text-sm font-medium text-gray-700 mb-2">
										Disposisi <span class="text-red-500">*</span>
									</label>
									<select 
										v-model="form.disposisi_tujuan" 
										class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
										required
									>
										<option value="">-- Pilih Tujuan Disposisi --</option>
										<option value="Direktur">Direktur</option>
										<option value="Wakil Direktur → Kepala Bidang">Wakil Direktur → Kepala Bidang</option>
										<option value="Kepala Bidang → Kepala Bagian Perlengkapan">Kepala Bidang → Kepala Bagian Perlengkapan</option>
									</select>
									<p class="mt-1 text-xs text-gray-500">Alur disposisi: Direktur → Wadir → Kabid → Kabag Perlengkapan</p>
									<InputError :message="form.errors.disposisi_tujuan" class="mt-2" />
								</div>

								<!-- Wadir Tujuan (jika disposisi ke Wadir) -->
								<div v-if="form.disposisi_tujuan && form.disposisi_tujuan.includes('Wakil Direktur')">
									<label class="block text-sm font-medium text-gray-700 mb-2">
										Wakil Direktur <span class="text-red-500">*</span>
									</label>
									<select 
										v-model="form.wadir_tujuan" 
										class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
										required
									>
										<option value="">-- Pilih Wakil Direktur --</option>
										<option value="Wadir Umum">Wadir Umum</option>
										<option value="Wadir Pelayanan">Wadir Pelayanan</option>
									</select>
									<InputError :message="form.errors.wadir_tujuan" class="mt-2" />
								</div>
							</div>

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
import { Link, useForm } from "@inertiajs/vue3";

const form = useForm({
	bidang: "",
	deskripsi: "",
	tanggal_permintaan: "",
	pic_pimpinan: "",
	no_nota_dinas: "",
	link_scan: "",
	status: "diajukan", // Auto set status diajukan untuk admin
	disposisi_tujuan: "",
	catatan_disposisi: "",
	wadir_tujuan: "",
	// Nota Dinas fields
	nota_kepada: "",
	nota_dari: "",
	nota_tanggal_nota: "",
	nota_no_nota: "",
	nota_sifat: "",
	nota_lampiran: "",
	nota_perihal: "",
	nota_detail: "",
	nota_mengetahui: "",
});

const submit = () => {
	form.post(route("permintaan.store"));
};
</script>
