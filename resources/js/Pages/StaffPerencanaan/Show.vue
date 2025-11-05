<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Detail Permintaan #{{ permintaan.permintaan_id }}
                </h2>
                <div class="flex items-center gap-4">
                    <Link :href="route('staff-perencanaan.history', permintaan.permintaan_id)" 
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Lihat History
                    </Link>
                    <Link :href="route('staff-perencanaan.index')" class="text-sm text-gray-600 hover:text-gray-900">
                        ← Kembali ke Daftar
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                
                <!-- Alert Info -->
                <div class="bg-blue-50 border-l-4 border-blue-400 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm text-blue-700">
                                <strong>Permintaan dari {{ permintaan.bidang }}</strong> - 
                                Status: <span class="font-semibold">{{ permintaan.status.toUpperCase() }}</span>
                            </p>
                            <p class="text-xs text-blue-600 mt-1">
                                Tracking Status: {{ trackingStatus }} ({{ progress }}% selesai)
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Progress Timeline -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900">
                                Progress Tracking
                            </h3>
                            <span class="text-lg font-bold text-green-600">
                                {{ progress }}%
                            </span>
                        </div>
                    </div>
                    
                    <div class="p-6">
                        <!-- Progress Bar -->
                        <div class="w-full bg-gray-200 rounded-full h-4 mb-6">
                            <div class="bg-green-600 h-4 rounded-full transition-all duration-500" :style="{ width: progress + '%' }"></div>
                        </div>

                        <!-- Timeline -->
                        <div class="space-y-4">
                            <div v-for="(step, index) in timeline" :key="index" class="flex items-start">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center ring-4 ring-white">
                                        <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4 flex-1 pb-4" :class="{ 'border-l-2 border-gray-200 pl-4 -ml-4': index < timeline.length - 1 }">
                                    <div class="flex items-center justify-between mb-1">
                                        <p class="font-semibold text-gray-900">{{ step.tahapan }}</p>
                                        <span class="text-xs text-gray-500">
                                            {{ formatDate(step.tanggal) }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-600 mb-2">{{ step.keterangan }}</p>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        {{ step.status }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detail Permintaan -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">
                            Informasi Permintaan
                        </h3>
                    </div>
                    
                    <div class="p-6">
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">ID Permintaan</dt>
                                <dd class="mt-1 text-sm font-semibold text-gray-900">#{{ permintaan.permintaan_id }}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Status</dt>
                                <dd class="mt-1">
                                    <span :class="{
                                        'bg-yellow-100 text-yellow-800': permintaan.status === 'disetujui',
                                        'bg-blue-100 text-blue-800': permintaan.status === 'proses',
                                        'bg-green-100 text-green-800': permintaan.status === 'selesai',
                                        'bg-gray-100 text-gray-800': permintaan.status === 'diajukan',
                                        'bg-red-100 text-red-800': permintaan.status === 'ditolak',
                                    }" class="px-3 py-1 inline-flex text-sm font-semibold rounded-full">
                                        {{ permintaan.status.toUpperCase() }}
                                    </span>
                                </dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Bidang / Unit</dt>
                                <dd class="mt-1 text-sm font-semibold text-gray-900">{{ permintaan.bidang }}</dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Tanggal Permintaan</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ formatDate(permintaan.tanggal_permintaan) }}</dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Pemohon</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ permintaan.user?.nama || '-' }}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">PIC Pimpinan Saat Ini</dt>
                                <dd class="mt-1">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ permintaan.pic_pimpinan || '-' }}
                                    </span>
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">No. Nota Dinas</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ permintaan.no_nota_dinas || '-' }}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Tracking Status</dt>
                                <dd class="mt-1 text-sm font-medium text-gray-900">{{ trackingStatus }}</dd>
                            </div>
                            
                            <div class="sm:col-span-2">
                                <dt class="text-sm font-medium text-gray-500 mb-2">Deskripsi Permintaan</dt>
                                <dd class="mt-1 text-sm text-gray-900 bg-gray-50 p-4 rounded-lg whitespace-pre-line border border-gray-200">{{ permintaan.deskripsi }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Status Dokumen & Aksi -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">
                            Status Dokumen & Aksi
                        </h3>
                        <p class="text-sm text-gray-600 mt-1">
                            Pantau status dokumen yang sudah dibuat dan lakukan aksi selanjutnya
                        </p>
                    </div>
                    
                    <div class="p-6">
                        <!-- Status Progress -->
                        <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-blue-900">Progress Dokumen</span>
                                <span class="text-sm font-bold text-blue-700">{{ documentProgress }}%</span>
                            </div>
                            <div class="w-full bg-blue-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full transition-all duration-500" :style="{ width: documentProgress + '%' }"></div>
                            </div>
                            <p class="text-xs text-blue-700 mt-2">{{ completedDocuments }} dari 6 dokumen telah dibuat</p>
                        </div>

                        <!-- Grid Dokumen -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <!-- Nota Dinas -->
                            <div class="border rounded-lg p-5 transition-all hover:shadow-md" :class="hasNotaDinas ? 'border-green-300 bg-green-50' : 'border-gray-300 bg-white'">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex items-center gap-3">
                                        <div class="p-2 rounded-lg" :class="hasNotaDinas ? 'bg-green-100' : 'bg-gray-100'">
                                            <svg class="w-6 h-6" :class="hasNotaDinas ? 'text-green-600' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-base" :class="hasNotaDinas ? 'text-green-900' : 'text-gray-700'">Nota Dinas Usulan</h4>
                                            <p class="text-xs text-gray-500 mt-0.5">Dokumen usulan pengadaan</p>
                                        </div>
                                    </div>
                                    <span v-if="hasNotaDinas" class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        Selesai
                                    </span>
                                    <span v-else class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                        Belum
                                    </span>
                                </div>
                                <div class="mt-4">
                                    <Link v-if="!hasNotaDinas" :href="route('staff-perencanaan.nota-dinas.create', permintaan.permintaan_id)"
                                        class="w-full inline-flex justify-center items-center px-4 py-2.5 bg-[#028174] text-white text-sm font-medium rounded-lg hover:bg-[#03a089] transition-colors gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        Buat Nota Dinas
                                    </Link>
                                    <div v-else class="flex gap-2">
                                        <Link :href="route('staff-perencanaan.nota-dinas.edit', permintaan.permintaan_id)"
                                            class="flex-1 inline-flex justify-center items-center px-3 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                            Edit
                                        </Link>
                                        <button @click="confirmDelete('nota-dinas')" class="flex-1 inline-flex justify-center items-center px-3 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                            Hapus
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- DPP -->
                            <div class="border rounded-lg p-5 transition-all hover:shadow-md" :class="hasDPP ? 'border-green-300 bg-green-50' : 'border-gray-300 bg-white'">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex items-center gap-3">
                                        <div class="p-2 rounded-lg" :class="hasDPP ? 'bg-green-100' : 'bg-gray-100'">
                                            <svg class="w-6 h-6" :class="hasDPP ? 'text-green-600' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-base" :class="hasDPP ? 'text-green-900' : 'text-gray-700'">DPP</h4>
                                            <p class="text-xs text-gray-500 mt-0.5">Dokumen Persiapan Pengadaan</p>
                                        </div>
                                    </div>
                                    <span v-if="hasDPP" class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        Selesai
                                    </span>
                                    <span v-else class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                        Belum
                                    </span>
                                </div>
                                <div class="mt-4">
                                    <Link v-if="!hasDPP" :href="route('staff-perencanaan.dpp.create', permintaan.permintaan_id)"
                                        class="w-full inline-flex justify-center items-center px-4 py-2.5 bg-orange-600 text-white text-sm font-medium rounded-lg hover:bg-orange-700 transition-colors gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        Buat DPP
                                    </Link>
                                    <div v-else class="flex gap-2">
                                        <Link :href="route('staff-perencanaan.dpp.edit', permintaan.permintaan_id)"
                                            class="flex-1 inline-flex justify-center items-center px-3 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                            Edit
                                        </Link>
                                        <button @click="confirmDelete('dpp')" class="flex-1 inline-flex justify-center items-center px-3 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                            Hapus
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- HPS -->
                            <div class="border rounded-lg p-5 transition-all hover:shadow-md" :class="hasHPS ? 'border-green-300 bg-green-50' : 'border-gray-300 bg-white'">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex items-center gap-3">
                                        <div class="p-2 rounded-lg" :class="hasHPS ? 'bg-green-100' : 'bg-gray-100'">
                                            <svg class="w-6 h-6" :class="hasHPS ? 'text-green-600' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-base" :class="hasHPS ? 'text-green-900' : 'text-gray-700'">HPS</h4>
                                            <p class="text-xs text-gray-500 mt-0.5">Harga Perkiraan Satuan</p>
                                        </div>
                                    </div>
                                    <span v-if="hasHPS" class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        Selesai
                                    </span>
                                    <span v-else class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                        Belum
                                    </span>
                                </div>
                                <div class="mt-4">
                                    <Link v-if="!hasHPS" :href="route('staff-perencanaan.hps.create', permintaan.permintaan_id)"
                                        class="w-full inline-flex justify-center items-center px-4 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        Buat HPS
                                    </Link>
                                    <div v-else class="flex gap-2">
                                        <Link :href="route('staff-perencanaan.hps.edit', permintaan.permintaan_id)"
                                            class="flex-1 inline-flex justify-center items-center px-3 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                            Edit
                                        </Link>
                                        <button @click="confirmDelete('hps')" class="flex-1 inline-flex justify-center items-center px-3 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                            Hapus
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Nota Dinas Pembelian -->
                            <div class="border rounded-lg p-5 transition-all hover:shadow-md" :class="hasNotaDinasPembelian ? 'border-green-300 bg-green-50' : 'border-gray-300 bg-white'">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex items-center gap-3">
                                        <div class="p-2 rounded-lg" :class="hasNotaDinasPembelian ? 'bg-green-100' : 'bg-gray-100'">
                                            <svg class="w-6 h-6" :class="hasNotaDinasPembelian ? 'text-green-600' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-base" :class="hasNotaDinasPembelian ? 'text-green-900' : 'text-gray-700'">Nota Dinas Pembelian</h4>
                                            <p class="text-xs text-gray-500 mt-0.5">Dokumen nota pembelian barang/jasa</p>
                                        </div>
                                    </div>
                                    <span v-if="hasNotaDinasPembelian" class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        Selesai
                                    </span>
                                    <span v-else class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                        Belum
                                    </span>
                                </div>
                                <div class="mt-4">
                                    <Link v-if="!hasNotaDinasPembelian" :href="route('staff-perencanaan.nota-dinas-pembelian.create', permintaan.permintaan_id)"
                                        class="w-full inline-flex justify-center items-center px-4 py-2.5 bg-teal-600 text-white text-sm font-medium rounded-lg hover:bg-teal-700 transition-colors gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        Buat Nota Dinas Pembelian
                                    </Link>
                                    <div v-else class="flex gap-2">
                                        <Link :href="route('staff-perencanaan.nota-dinas-pembelian.edit', permintaan.permintaan_id)"
                                            class="flex-1 inline-flex justify-center items-center px-3 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                            Edit
                                        </Link>
                                        <button @click="confirmDelete('nota-dinas-pembelian')" class="flex-1 inline-flex justify-center items-center px-3 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                            Hapus
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Spesifikasi Teknis -->
                            <div class="border rounded-lg p-5 transition-all hover:shadow-md" :class="hasSpesifikasiTeknis ? 'border-green-300 bg-green-50' : 'border-gray-300 bg-white'">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex items-center gap-3">
                                        <div class="p-2 rounded-lg" :class="hasSpesifikasiTeknis ? 'bg-green-100' : 'bg-gray-100'">
                                            <svg class="w-6 h-6" :class="hasSpesifikasiTeknis ? 'text-green-600' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-base" :class="hasSpesifikasiTeknis ? 'text-green-900' : 'text-gray-700'">Spesifikasi Teknis</h4>
                                            <p class="text-xs text-gray-500 mt-0.5">Detail teknis pengadaan barang/jasa</p>
                                        </div>
                                    </div>
                                    <span v-if="hasSpesifikasiTeknis" class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        Selesai
                                    </span>
                                    <span v-else class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                        Belum
                                    </span>
                                </div>
                                <div class="mt-4">
                                    <Link v-if="!hasSpesifikasiTeknis" :href="route('staff-perencanaan.spesifikasi-teknis.create', permintaan.permintaan_id)"
                                        class="w-full inline-flex justify-center items-center px-4 py-2.5 bg-cyan-600 text-white text-sm font-medium rounded-lg hover:bg-cyan-700 transition-colors gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        Buat Spesifikasi Teknis
                                    </Link>
                                    <div v-else class="flex gap-2">
                                        <Link :href="route('staff-perencanaan.spesifikasi-teknis.edit', permintaan.permintaan_id)"
                                            class="flex-1 inline-flex justify-center items-center px-3 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                            Edit
                                        </Link>
                                        <button @click="confirmDelete('spesifikasi-teknis')" class="flex-1 inline-flex justify-center items-center px-3 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                            Hapus
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Disposisi -->
                            <div class="border rounded-lg p-5 transition-all hover:shadow-md" :class="hasDisposisi ? 'border-green-300 bg-green-50' : 'border-gray-300 bg-white'">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex items-center gap-3">
                                        <div class="p-2 rounded-lg" :class="hasDisposisi ? 'bg-green-100' : 'bg-gray-100'">
                                            <svg class="w-6 h-6" :class="hasDisposisi ? 'text-green-600' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-base" :class="hasDisposisi ? 'text-green-900' : 'text-gray-700'">Disposisi</h4>
                                            <p class="text-xs text-gray-500 mt-0.5">Lembar disposisi dokumen</p>
                                        </div>
                                    </div>
                                    <span v-if="hasDisposisi" class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        Selesai
                                    </span>
                                    <span v-else class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                        Belum
                                    </span>
                                </div>
                                <div class="mt-4">
                                    <Link :href="route('staff-perencanaan.disposisi.create', permintaan.permintaan_id)"
                                        class="w-full inline-flex justify-center items-center px-4 py-2.5 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 transition-colors gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        {{ hasDisposisi ? 'Tambah Disposisi' : 'Buat Disposisi' }}
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tombol Forward ke Bagian Pengadaan -->
                <div v-if="allDocumentsComplete" class="bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-300 overflow-hidden shadow-lg sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="p-3 bg-green-100 rounded-full">
                                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-green-900">
                                        Semua Dokumen Sudah Lengkap!
                                    </h3>
                                    <p class="text-sm text-green-700 mt-1">
                                        Nota Dinas, DPP, HPS, Nota Dinas Pembelian, dan Spesifikasi Teknis telah dibuat.
                                        Anda dapat melanjutkan ke Bagian Pengadaan.
                                    </p>
                                </div>
                            </div>
                            <button
                                v-if="permintaan.pic_pimpinan === 'Staff Perencanaan' || permintaan.pic_pimpinan === userLogin.nama"
                                @click="showForwardModal = true"
                                class="inline-flex items-center px-6 py-3 bg-[#028174] border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-widest hover:bg-[#03a089] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 shadow-lg transform transition hover:scale-105 gap-2"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                </svg>
                                Kirim ke Bagian Pengadaan
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Alert jika dokumen belum lengkap -->
                <div v-else-if="permintaan.pic_pimpinan === 'Staff Perencanaan' || permintaan.pic_pimpinan === userLogin.nama" class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                <strong>Dokumen Belum Lengkap.</strong> Silakan lengkapi semua dokumen berikut sebelum mengirim ke Bagian Pengadaan:
                            </p>
                            <ul class="list-disc list-inside text-xs text-yellow-600 mt-2 space-y-1">
                                <li v-if="!hasNotaDinas">Nota Dinas</li>
                                <li v-if="!hasDPP">DPP (Dokumen Persiapan Pengadaan)</li>
                                <li v-if="!hasHPS">HPS (Harga Perkiraan Satuan)</li>
                                <li v-if="!hasNotaDinasPembelian">Nota Dinas Pembelian</li>
                                <li v-if="!hasSpesifikasiTeknis">Spesifikasi Teknis</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- History / Data Perencanaan yang Sudah Diinput -->
                <div v-if="perencanaan || disposisi || notaDinas || hps || spesifikasiTeknis" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            History Data Perencanaan
                        </h3>
                        <p class="text-sm text-gray-600 mt-1">Data dokumen yang sudah diinput untuk permintaan ini</p>
                    </div>

                    <div class="p-6 space-y-6">
                        <!-- Nota Dinas Data -->
                        <div v-if="notaDinas" class="border-l-4 border-blue-500 bg-blue-50 p-4 rounded-r-lg">
                            <h4 class="font-semibold text-blue-900 mb-3 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V8z" clip-rule="evenodd"></path>
                                </svg>
                                📄 Nota Dinas
                            </h4>
                            <dl class="grid grid-cols-2 gap-x-4 gap-y-3 text-sm">
                                <div>
                                    <dt class="font-medium text-gray-700">No. Nota:</dt>
                                    <dd class="text-gray-900">{{ notaDinas.no_nota }}</dd>
                                </div>
                                <div>
                                    <dt class="font-medium text-gray-700">Tanggal:</dt>
                                    <dd class="text-gray-900">{{ formatDate(notaDinas.tanggal_nota) }}</dd>
                                </div>
                                <div class="col-span-2">
                                    <dt class="font-medium text-gray-700">Perihal:</dt>
                                    <dd class="text-gray-900">{{ notaDinas.perihal }}</dd>
                                </div>
                                <div>
                                    <dt class="font-medium text-gray-700">Dari:</dt>
                                    <dd class="text-gray-900">{{ notaDinas.dari }}</dd>
                                </div>
                                <div>
                                    <dt class="font-medium text-gray-700">Kepada:</dt>
                                    <dd class="text-gray-900">{{ notaDinas.kepada }}</dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Disposisi Data -->
                        <div v-if="disposisi" class="border-l-4 border-purple-500 bg-purple-50 p-4 rounded-r-lg">
                            <h4 class="font-semibold text-purple-900 mb-3 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path>
                                </svg>
                                📋 Disposisi
                            </h4>
                            <dl class="grid grid-cols-2 gap-x-4 gap-y-3 text-sm">
                                <div>
                                    <dt class="font-medium text-gray-700">Tanggal Disposisi:</dt>
                                    <dd class="text-gray-900">{{ formatDate(disposisi.tanggal_disposisi) }}</dd>
                                </div>
                                <div>
                                    <dt class="font-medium text-gray-700">Status:</dt>
                                    <dd class="text-gray-900">
                                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                            {{ disposisi.status_disposisi }}
                                        </span>
                                    </dd>
                                </div>
                                <div class="col-span-2" v-if="disposisi.catatan">
                                    <dt class="font-medium text-gray-700">Catatan:</dt>
                                    <dd class="text-gray-900 mt-1">{{ disposisi.catatan }}</dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Perencanaan (DPP) Data -->
                        <div v-if="perencanaan" class="border-l-4 border-orange-500 bg-orange-50 p-4 rounded-r-lg">
                            <h4 class="font-semibold text-orange-900 mb-3 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                                </svg>
                                📦 Data Perencanaan (DPP)
                            </h4>
                            <dl class="grid grid-cols-2 gap-x-4 gap-y-3 text-sm">
                                <div class="col-span-2" v-if="perencanaan.nama_paket">
                                    <dt class="font-medium text-gray-700">Nama Paket:</dt>
                                    <dd class="text-gray-900 font-semibold">{{ perencanaan.nama_paket }}</dd>
                                </div>
                                <div v-if="perencanaan.ppk_ditunjuk">
                                    <dt class="font-medium text-gray-700">PPK Ditunjuk:</dt>
                                    <dd class="text-gray-900">{{ perencanaan.ppk_ditunjuk }}</dd>
                                </div>
                                <div v-if="perencanaan.metode_pengadaan">
                                    <dt class="font-medium text-gray-700">Metode Pengadaan:</dt>
                                    <dd class="text-gray-900">{{ perencanaan.metode_pengadaan }}</dd>
                                </div>
                                <div v-if="perencanaan.pagu_paket">
                                    <dt class="font-medium text-gray-700">Pagu Paket:</dt>
                                    <dd class="text-gray-900 font-semibold">Rp {{ Number(perencanaan.pagu_paket).toLocaleString('id-ID') }}</dd>
                                </div>
                                <div v-if="perencanaan.nilai_hps">
                                    <dt class="font-medium text-gray-700">Nilai HPS:</dt>
                                    <dd class="text-gray-900 font-semibold">Rp {{ Number(perencanaan.nilai_hps).toLocaleString('id-ID') }}</dd>
                                </div>
                                <div v-if="perencanaan.sumber_dana">
                                    <dt class="font-medium text-gray-700">Sumber Dana:</dt>
                                    <dd class="text-gray-900">{{ perencanaan.sumber_dana }}</dd>
                                </div>
                                <div v-if="perencanaan.kode_rekening">
                                    <dt class="font-medium text-gray-700">Kode Rekening:</dt>
                                    <dd class="text-gray-900">{{ perencanaan.kode_rekening }}</dd>
                                </div>
                                <div v-if="perencanaan.lokasi" class="col-span-2">
                                    <dt class="font-medium text-gray-700">Lokasi:</dt>
                                    <dd class="text-gray-900">{{ perencanaan.lokasi }}</dd>
                                </div>
                                <div v-if="perencanaan.jangka_waktu_pelaksanaan">
                                    <dt class="font-medium text-gray-700">Jangka Waktu:</dt>
                                    <dd class="text-gray-900">{{ perencanaan.jangka_waktu_pelaksanaan }}</dd>
                                </div>
                                <div v-if="perencanaan.jenis_kontrak">
                                    <dt class="font-medium text-gray-700">Jenis Kontrak:</dt>
                                    <dd class="text-gray-900">{{ perencanaan.jenis_kontrak }}</dd>
                                </div>
                                <div v-if="perencanaan.tanggal_mulai || perencanaan.tanggal_selesai" class="col-span-2">
                                    <dt class="font-medium text-gray-700">Periode Pelaksanaan:</dt>
                                    <dd class="text-gray-900">
                                        <span v-if="perencanaan.tanggal_mulai">{{ formatDate(perencanaan.tanggal_mulai) }}</span>
                                        <span v-if="perencanaan.tanggal_mulai && perencanaan.tanggal_selesai"> s/d </span>
                                        <span v-if="perencanaan.tanggal_selesai">{{ formatDate(perencanaan.tanggal_selesai) }}</span>
                                    </dd>
                                </div>
                            </dl>
                        </div>

                        <!-- HPS Data -->
                        <div v-if="hps" class="border-l-4 border-green-500 bg-green-50 p-4 rounded-r-lg">
                            <h4 class="font-semibold text-green-900 mb-3 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"></path>
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"></path>
                                </svg>
                                💰 HPS (Harga Perkiraan Satuan)
                            </h4>
                            <dl class="grid grid-cols-2 gap-x-4 gap-y-3 text-sm">
                                <div v-if="hps.metode_pengadaan">
                                    <dt class="font-medium text-gray-700">Metode Pengadaan:</dt>
                                    <dd class="text-gray-900">{{ hps.metode_pengadaan }}</dd>
                                </div>
                                <div v-if="hps.total_hps">
                                    <dt class="font-medium text-gray-700">Total HPS:</dt>
                                    <dd class="text-gray-900 font-bold text-lg">Rp {{ Number(hps.total_hps).toLocaleString('id-ID') }}</dd>
                                </div>
                                <div v-if="hps.items && hps.items.length > 0" class="col-span-2">
                                    <dt class="font-medium text-gray-700 mb-2">Detail Item:</dt>
                                    <dd>
                                        <div class="space-y-2">
                                            <div v-for="item in hps.items" :key="item.item_id" 
                                                class="bg-white p-3 rounded border border-green-200">
                                                <div class="flex justify-between items-start">
                                                    <div>
                                                        <p class="font-medium text-gray-900">{{ item.nama_item }}</p>
                                                        <p class="text-xs text-gray-600">
                                                            {{ item.kuantitas }} {{ item.satuan }} × Rp {{ Number(item.harga_satuan).toLocaleString('id-ID') }}
                                                        </p>
                                                    </div>
                                                    <p class="font-semibold text-green-700">
                                                        Rp {{ Number(item.total_harga).toLocaleString('id-ID') }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Spesifikasi Teknis Data -->
                        <div v-if="spesifikasiTeknis" class="border-l-4 border-cyan-500 bg-cyan-50 p-4 rounded-r-lg">
                            <h4 class="font-semibold text-cyan-900 mb-3 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path>
                                </svg>
                                ⚙️ Spesifikasi Teknis
                            </h4>
                            <dl class="grid grid-cols-1 gap-y-3 text-sm">
                                <div v-if="spesifikasiTeknis.nama_barang">
                                    <dt class="font-medium text-gray-700">Nama Barang/Jasa:</dt>
                                    <dd class="text-gray-900 font-semibold">{{ spesifikasiTeknis.nama_barang }}</dd>
                                </div>
                                <div v-if="spesifikasiTeknis.spesifikasi">
                                    <dt class="font-medium text-gray-700">Spesifikasi:</dt>
                                    <dd class="text-gray-900 whitespace-pre-line mt-1">{{ spesifikasiTeknis.spesifikasi }}</dd>
                                </div>
                                <div v-if="spesifikasiTeknis.kuantitas || spesifikasiTeknis.satuan" class="grid grid-cols-2 gap-4">
                                    <div v-if="spesifikasiTeknis.kuantitas">
                                        <dt class="font-medium text-gray-700">Kuantitas:</dt>
                                        <dd class="text-gray-900">{{ spesifikasiTeknis.kuantitas }}</dd>
                                    </div>
                                    <div v-if="spesifikasiTeknis.satuan">
                                        <dt class="font-medium text-gray-700">Satuan:</dt>
                                        <dd class="text-gray-900">{{ spesifikasiTeknis.satuan }}</dd>
                                    </div>
                                </div>
                            </dl>
                        </div>

                        <!-- Empty State -->
                        <div v-if="!perencanaan && !disposisi && !notaDinas && !hps && !spesifikasiTeknis" 
                            class="text-center py-8 text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p class="mt-2">Belum ada data perencanaan yang diinput</p>
                        </div>
                    </div>
                </div>

                <!-- Riwayat Nota Dinas & Disposisi -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">
                            Riwayat Nota Dinas & Disposisi
                        </h3>
                    </div>

                    <div class="p-6">
                        <div v-if="permintaan.nota_dinas && permintaan.nota_dinas.length > 0" class="space-y-4">
                            <div v-for="nota in permintaan.nota_dinas" :key="nota.nota_id"
                                class="border border-gray-200 rounded-lg p-5 hover:shadow-md transition-shadow">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-1">
                                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            <h4 class="font-semibold text-gray-900">{{ nota.no_nota }}</h4>
                                        </div>
                                        <p class="text-sm text-gray-700 font-medium mt-2">{{ nota.perihal }}</p>
                                    </div>
                                    <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">
                                        {{ formatDate(nota.tanggal_nota) }}
                                    </span>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm bg-gray-50 p-3 rounded">
                                    <div>
                                        <span class="text-gray-500 font-medium">Dari:</span>
                                        <span class="ml-2 text-gray-900">{{ nota.dari }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500 font-medium">Kepada:</span>
                                        <span class="ml-2 text-gray-900">{{ nota.kepada }}</span>
                                    </div>
                                </div>

                                <!-- Disposisi for this Nota -->
                                <div v-if="nota.disposisi && nota.disposisi.length > 0" class="mt-4 pl-4 border-l-4 border-green-300 bg-green-50 p-3 rounded-r">
                                    <p class="text-xs font-semibold text-gray-700 mb-3 flex items-center gap-2">
                                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                        </svg>
                                        Disposisi
                                    </p>
                                    <div v-for="disp in nota.disposisi" :key="disp.disposisi_id" class="text-sm mb-3 bg-white p-3 rounded">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-gray-900 font-medium">→ {{ disp.jabatan_tujuan }}</span>
                                            <span :class="{
                                                'px-2.5 py-0.5 text-xs rounded-full font-medium': true,
                                                'bg-green-100 text-green-800': disp.status === 'disetujui',
                                                'bg-yellow-100 text-yellow-800': disp.status === 'dalam_proses',
                                                'bg-red-100 text-red-800': disp.status === 'ditolak'
                                            }">
                                                {{ disp.status }}
                                            </span>
                                        </div>
                                        <p class="text-xs text-gray-600 mb-1" v-if="disp.catatan">
                                            <span class="font-medium">Catatan:</span> {{ disp.catatan }}
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            <span class="font-medium">Tanggal:</span> {{ formatDate(disp.tanggal_disposisi) }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p class="mt-2 text-sm text-gray-500">Belum ada nota dinas untuk permintaan ini</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Modal Forward ke Bagian Pengadaan -->
        <Modal :show="showForwardModal" @close="showForwardModal = false">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    Kirim ke Bagian Pengadaan
                </h3>
                
                <div class="mb-4 bg-blue-50 border-l-4 border-blue-400 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-blue-700">
                                Permintaan ini akan dikirim ke <strong>Bagian Pengadaan</strong> untuk diproses lebih lanjut.
                                Pastikan semua dokumen sudah benar sebelum mengirim.
                            </p>
                        </div>
                    </div>
                </div>
                
                <form @submit.prevent="submitForward">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Catatan untuk Bagian Pengadaan
                        </label>
                        <textarea
                            v-model="forwardForm.catatan"
                            rows="4"
                            class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500"
                            placeholder="Tambahkan catatan atau instruksi untuk Bagian Pengadaan (opsional)"
                        ></textarea>
                    </div>

                    <div class="flex justify-end gap-3">
                        <button
                            type="button"
                            @click="showForwardModal = false"
                            class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            :disabled="forwardForm.processing"
                            class="px-4 py-2 bg-[#028174] border border-transparent rounded-md text-sm font-medium text-white hover:bg-[#03a089] disabled:opacity-50"
                        >
                            {{ forwardForm.processing ? 'Mengirim...' : 'Kirim ke Pengadaan' }}
                        </button>
                    </div>
                </form>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Modal from '@/Components/Modal.vue';
import { Link, router, useForm } from '@inertiajs/vue3';

const props = defineProps({
    permintaan: Object,
    trackingStatus: String,
    timeline: Array,
    progress: Number,
    userLogin: Object,
    hasNotaDinas: Boolean,
    hasDPP: Boolean,
    hasHPS: Boolean,
    hasDisposisi: Boolean,
    hasNotaDinasPembelian: Boolean,
    hasSpesifikasiTeknis: Boolean,
    // Actual document data
    notaDinas: Object,
    disposisi: Object,
    perencanaan: Object,
    hps: Object,
    notaDinasPembelian: Object,
    spesifikasiTeknis: Object,
});

const showForwardModal = ref(false);

const forwardForm = useForm({
    catatan: '',
});

const formatDate = (date) => {
    if (!date) return '-';
    return new Date(date).toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
    });
};

// Hitung jumlah dokumen yang sudah selesai
const completedDocuments = computed(() => {
    let count = 0;
    if (props.hasNotaDinas) count++;
    if (props.hasDPP) count++;
    if (props.hasHPS) count++;
    if (props.hasNotaDinasPembelian) count++;
    if (props.hasSpesifikasiTeknis) count++;
    if (props.hasDisposisi) count++;
    return count;
});

// Hitung progress persentase dokumen
const documentProgress = computed(() => {
    return Math.round((completedDocuments.value / 6) * 100);
});

// Cek apakah semua dokumen sudah lengkap (kecuali Disposisi yang opsional)
const allDocumentsComplete = computed(() => {
    return props.hasNotaDinas && 
           props.hasDPP && 
           props.hasHPS && 
           props.hasNotaDinasPembelian && 
           props.hasSpesifikasiTeknis;
});

// Submit forward ke Bagian Pengadaan
const submitForward = () => {
    forwardForm.post(route('staff-perencanaan.forward-to-pengadaan', props.permintaan.permintaan_id), {
        preserveScroll: true,
        onSuccess: () => {
            showForwardModal.value = false;
            forwardForm.reset();
        },
    });
};

// Fungsi untuk konfirmasi delete
const confirmDelete = (type) => {
    const typeNames = {
        'nota-dinas': 'Nota Dinas Usulan',
        'nota-dinas-pembelian': 'Nota Dinas Pembelian',
        'dpp': 'DPP',
        'hps': 'HPS',
        'spesifikasi-teknis': 'Spesifikasi Teknis'
    };
    
    if (confirm(`Apakah Anda yakin ingin menghapus ${typeNames[type]}? Data yang sudah dihapus tidak dapat dikembalikan.`)) {
        const routes = {
            'nota-dinas': 'staff-perencanaan.nota-dinas.delete',
            'nota-dinas-pembelian': 'staff-perencanaan.nota-dinas-pembelian.delete',
            'dpp': 'staff-perencanaan.dpp.delete',
            'hps': 'staff-perencanaan.hps.delete',
            'spesifikasi-teknis': 'staff-perencanaan.spesifikasi-teknis.delete'
        };
        
        router.delete(route(routes[type], props.permintaan.permintaan_id), {
            onSuccess: () => {
                alert(`${typeNames[type]} berhasil dihapus`);
            },
            onError: () => {
                alert(`Gagal menghapus ${typeNames[type]}`);
            }
        });
    }
};
</script>

