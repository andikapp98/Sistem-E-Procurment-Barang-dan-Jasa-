<template>
	<AuthenticatedLayout>
		<template #header>
			<h2 class="font-semibold text-xl text-gray-800 leading-tight">Daftar Permintaan - Instalasi Rawat Inap</h2>
		</template>

		<div class="py-12">
			<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
				<!-- Header with Create Button -->
				<div class="mb-6 flex justify-between items-center">
					<div>
						<h3 class="text-lg font-medium text-gray-900">Permintaan Pengadaan</h3>
						<p class="mt-1 text-sm text-gray-600">
							Kelola permintaan pengadaan untuk unit Instalasi Rawat Inap
						</p>
					</div>
					<Link
						:href="route('kepala-ruang.create')"
						class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring focus:ring-indigo-300 transition"
					>
						<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
						</svg>
						Buat Permintaan Baru
					</Link>
				</div>

				<!-- Filters -->
				<div class="bg-white rounded-lg shadow-sm p-4 mb-6">
					<form @submit.prevent="applyFilters" class="grid grid-cols-1 md:grid-cols-4 gap-4">
						<div>
							<label class="block text-sm font-medium text-gray-700 mb-1">Pencarian</label>
							<input
								v-model="filterForm.search"
								type="text"
								placeholder="ID, Deskripsi, No. Nota..."
								class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
							/>
						</div>
						<div>
							<label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
							<select
								v-model="filterForm.status"
								class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
							>
								<option value="">Semua Status</option>
								<option value="diajukan">Diajukan</option>
								<option value="proses">Proses</option>
								<option value="disetujui">Disetujui</option>
								<option value="ditolak">Ditolak</option>
								<option value="selesai">Selesai</option>
							</select>
						</div>
						<div>
							<label class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
							<input
								v-model="filterForm.tanggal_dari"
								type="date"
								class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
							/>
						</div>
						<div>
							<label class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
							<input
								v-model="filterForm.tanggal_sampai"
								type="date"
								class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
							/>
						</div>
						<div class="md:col-span-4 flex justify-end gap-2">
							<button
								type="button"
								@click="resetFilters"
								class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
							>
								Reset
							</button>
							<button
								type="submit"
								class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-700"
							>
								Terapkan Filter
							</button>
						</div>
					</form>
				</div>

				<!-- Table -->
				<div class="bg-white overflow-hidden shadow-sm rounded-lg">
					<div class="overflow-x-auto">
						<table class="min-w-full divide-y divide-gray-200">
							<thead class="bg-gray-50">
								<tr>
									<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
									<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
									<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
									<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
									<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Progress</th>
									<th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
								</tr>
							</thead>
							<tbody class="bg-white divide-y divide-gray-200">
								<tr v-if="permintaans.data.length === 0">
									<td colspan="6" class="px-6 py-12 text-center text-sm text-gray-500">
										<svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
										</svg>
										<p class="mt-2">Tidak ada permintaan</p>
										<Link :href="route('kepala-ruang.create')" class="mt-2 inline-block text-indigo-600 hover:text-indigo-900">
											Buat permintaan pertama
										</Link>
									</td>
								</tr>
								<tr v-for="permintaan in permintaans.data" :key="permintaan.permintaan_id" class="hover:bg-gray-50">
									<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
										#{{ permintaan.permintaan_id }}
									</td>
									<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
										{{ formatDate(permintaan.tanggal_permintaan) }}
									</td>
									<td class="px-6 py-4 text-sm text-gray-900">
										<div class="max-w-xs truncate">{{ permintaan.deskripsi }}</div>
										<div class="text-xs text-gray-500 mt-1">
											<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
												{{ permintaan.klasifikasi_permintaan }}
											</span>
										</div>
									</td>
									<td class="px-6 py-4 whitespace-nowrap">
										<span
											:class="{
												'bg-yellow-100 text-yellow-800': permintaan.status === 'diajukan',
												'bg-blue-100 text-blue-800': permintaan.status === 'proses',
												'bg-green-100 text-green-800': permintaan.status === 'disetujui',
												'bg-red-100 text-red-800': permintaan.status === 'ditolak',
												'bg-gray-100 text-gray-800': permintaan.status === 'selesai'
											}"
											class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
										>
											{{ permintaan.status.toUpperCase() }}
										</span>
									</td>
									<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
										<div class="flex items-center">
											<div class="flex-1">
												<div class="h-2 bg-gray-200 rounded-full">
													<div
														:class="{
															'bg-yellow-500': permintaan.progress < 30,
															'bg-blue-500': permintaan.progress >= 30 && permintaan.progress < 70,
															'bg-green-500': permintaan.progress >= 70
														}"
														class="h-2 rounded-full transition-all duration-300"
														:style="{ width: permintaan.progress + '%' }"
													></div>
												</div>
											</div>
											<span class="ml-2 text-xs font-medium text-gray-700">{{ permintaan.progress }}%</span>
										</div>
									</td>
									<td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
										<div class="flex justify-end gap-2">
											<Link
												:href="route('kepala-ruang.show', permintaan.permintaan_id)"
												class="text-indigo-600 hover:text-indigo-900"
												title="Lihat Detail"
											>
												<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
													<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
													<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
												</svg>
											</Link>
											<Link
												v-if="permintaan.status === 'diajukan'"
												:href="route('kepala-ruang.edit', permintaan.permintaan_id)"
												class="text-blue-600 hover:text-blue-900"
												title="Edit"
											>
												<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
													<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
												</svg>
											</Link>
											<button
												v-if="permintaan.status === 'diajukan'"
												@click="confirmDelete(permintaan)"
												class="text-red-600 hover:text-red-900"
												title="Hapus"
											>
												<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
													<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
												</svg>
											</button>
										</div>
									</td>
								</tr>
							</tbody>
						</table>
					</div>

					<!-- Pagination -->
					<div v-if="permintaans.data.length > 0" class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
						<div class="flex items-center justify-between">
							<div class="flex-1 flex justify-between sm:hidden">
								<Link
									v-if="permintaans.prev_page_url"
									:href="permintaans.prev_page_url"
									class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
								>
									Previous
								</Link>
								<Link
									v-if="permintaans.next_page_url"
									:href="permintaans.next_page_url"
									class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
								>
									Next
								</Link>
							</div>
							<div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
								<div>
									<p class="text-sm text-gray-700">
										Menampilkan
										<span class="font-medium">{{ permintaans.from }}</span>
										sampai
										<span class="font-medium">{{ permintaans.to }}</span>
										dari
										<span class="font-medium">{{ permintaans.total }}</span>
										permintaan
									</p>
								</div>
								<div>
									<nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
										<Link
											v-for="link in permintaans.links"
											:key="link.label"
											:href="link.url || '#'"
											:class="[
												link.active ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
												'relative inline-flex items-center px-4 py-2 border text-sm font-medium'
											]"
											v-html="link.label"
										>
										</Link>
									</nav>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Link, router, usePage } from "@inertiajs/vue3";
import { ref, reactive } from "vue";

const props = defineProps({
	permintaans: Object,
	userLogin: Object,
	filters: Object,
});

const filterForm = reactive({
	search: props.filters?.search || "",
	status: props.filters?.status || "",
	tanggal_dari: props.filters?.tanggal_dari || "",
	tanggal_sampai: props.filters?.tanggal_sampai || "",
});

const applyFilters = () => {
	router.get(route("kepala-ruang.index"), filterForm, {
		preserveState: true,
		preserveScroll: true,
	});
};

const resetFilters = () => {
	filterForm.search = "";
	filterForm.status = "";
	filterForm.tanggal_dari = "";
	filterForm.tanggal_sampai = "";
	applyFilters();
};

const formatDate = (dateString) => {
	if (!dateString) return "-";
	const date = new Date(dateString);
	return date.toLocaleDateString("id-ID", {
		day: "2-digit",
		month: "short",
		year: "numeric",
	});
};

const confirmDelete = (permintaan) => {
	if (confirm(`Yakin ingin menghapus permintaan #${permintaan.permintaan_id}?`)) {
		router.delete(route("kepala-ruang.destroy", permintaan.permintaan_id), {
			onSuccess: () => {
				// Success handled by flash message
			},
		});
	}
};
</script>
