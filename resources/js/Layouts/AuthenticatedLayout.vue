<script setup>
import { ref } from 'vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);
const sidebarOpen = ref(true);
</script>

<template>
    <div>
        <div class="min-h-screen bg-gray-100">
            <nav
                class="border-b border-gray-100 bg-white"
            >
                <!-- Primary Navigation Menu -->
                <div class="mx-auto max-w-full px-4 sm:px-6 lg:px-8">
                    <div class="flex h-16 justify-between">
                        <div class="flex items-center">
                            <!-- Logo -->
                            <Link 
                                :href="
                                    $page.props.auth.user.role === 'kso' ? route('kso.dashboard') :
                                    $page.props.auth.user.role === 'pengadaan' ? route('pengadaan.dashboard') :
                                    $page.props.auth.user.role === 'kepala_bidang' ? route('kepala-bidang.dashboard') :
                                    $page.props.auth.user.role === 'wakil_direktur' ? route('wakil-direktur.dashboard') :
                                    $page.props.auth.user.role === 'direktur' ? route('direktur.dashboard') :
                                    $page.props.auth.user.role === 'staff_perencanaan' ? route('staff-perencanaan.dashboard') :
                                    route('dashboard')
                                " 
                                class="flex items-center space-x-3"
                            >
                                <img
                                    src="/images/logorsis.png"
                                    alt="RSUD Ibnu Sina Kabupaten Gresik"
                                    class="h-10 w-auto object-contain"
                                />
                                <h2 class="text-lg font-semibold text-[#028174] tracking-wide whitespace-nowrap hidden sm:block">
                                    Sistem e-Procurement
                                </h2>
                            </Link>
                        </div>

                        <div class="hidden sm:ms-6 sm:flex sm:items-center">
                            <!-- Settings Dropdown -->
                            <div class="relative ms-3">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button
                                                type="button"
                                                class="inline-flex items-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out hover:text-gray-700 focus:outline-none"
                                            >
                                                {{ $page.props.auth.user.nama }}
                                                <span class="ml-1 text-xs text-gray-400">
                                                    ({{ 
                                                        $page.props.auth.user.role === 'admin' ? 'Admin' : 
                                                        $page.props.auth.user.role === 'kepala_instalasi' ? 'Kepala Instalasi' : 
                                                        $page.props.auth.user.role === 'unit' ? 'Unit' : 
                                                        $page.props.auth.user.jabatan || 'User' 
                                                    }})
                                                </span>

                                                <svg
                                                    class="-me-0.5 ms-2 h-4 w-4"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <DropdownLink
                                            :href="route('profile.edit')"
                                        >
                                            Profile
                                        </DropdownLink>
                                        <DropdownLink
                                            :href="route('logout')"
                                            method="post"
                                            as="button"
                                        >
                                            Log Out
                                        </DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>

                        <!-- Hamburger -->
                        <div class="-me-2 flex items-center sm:hidden">
                            <button
                                @click="
                                    showingNavigationDropdown =
                                        !showingNavigationDropdown
                                "
                                class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 transition duration-150 ease-in-out hover:bg-gray-100 hover:text-gray-500 focus:bg-gray-100 focus:text-gray-500 focus:outline-none"
                            >
                                <svg
                                    class="h-6 w-6"
                                    stroke="currentColor"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        :class="{
                                            hidden: showingNavigationDropdown,
                                            'inline-flex':
                                                !showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{
                                            hidden: !showingNavigationDropdown,
                                            'inline-flex':
                                                showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div
                    :class="{
                        block: showingNavigationDropdown,
                        hidden: !showingNavigationDropdown,
                    }"
                    class="sm:hidden"
                >
                    <div class="space-y-1 pb-3 pt-2">
                        <!-- Dashboard - Admin, Unit, Kepala Instalasi -->
                        <ResponsiveNavLink
                            v-if="$page.props.auth.user.role === 'admin' || 
                                  $page.props.auth.user.role === 'unit' || 
                                  $page.props.auth.user.role === 'kepala_instalasi'"
                            :href="route('dashboard')"
                            :active="route().current('dashboard')"
                        >
                            Dashboard
                        </ResponsiveNavLink>

                        <!-- Menu untuk Unit/Kepala Instalasi -->
                        <template v-if="$page.props.auth.user.role === 'unit' || $page.props.auth.user.role === 'kepala_instalasi'">
                            <ResponsiveNavLink
                                :href="route('permintaan.index')"
                                :active="route().current('permintaan.*')"
                            >
                                Permintaan Saya
                            </ResponsiveNavLink>
                        </template>

                        <!-- Menu khusus Kepala Instalasi -->
                        <template v-if="$page.props.auth.user.role === 'kepala_instalasi'">
                            <ResponsiveNavLink
                                :href="route('kepala-instalasi.index')"
                                :active="route().current('kepala-instalasi.index')"
                            >
                                Review Permintaan
                            </ResponsiveNavLink>
                        </template>

                        <!-- Menu khusus Kepala Bidang -->
                        <template v-if="$page.props.auth.user.role === 'kepala_bidang'">
                            <ResponsiveNavLink
                                :href="route('kepala-bidang.dashboard')"
                                :active="route().current('kepala-bidang.dashboard')"
                            >
                                Dashboard
                            </ResponsiveNavLink>
                            <ResponsiveNavLink
                                :href="route('kepala-bidang.index')"
                                :active="route().current('kepala-bidang.index') || route().current('kepala-bidang.show')"
                            >
                                Daftar Permintaan
                            </ResponsiveNavLink>
                            <ResponsiveNavLink
                                :href="route('kepala-bidang.approved')"
                                :active="route().current('kepala-bidang.approved') || route().current('kepala-bidang.tracking')"
                            >
                                Tracking & History
                            </ResponsiveNavLink>
                        </template>

                        <!-- Menu khusus Direktur -->
                        <template v-if="$page.props.auth.user.role === 'direktur'">
                            <ResponsiveNavLink
                                :href="route('direktur.dashboard')"
                                :active="route().current('direktur.dashboard')"
                            >
                                Dashboard
                            </ResponsiveNavLink>
                            <ResponsiveNavLink
                                :href="route('direktur.index')"
                                :active="route().current('direktur.index') || route().current('direktur.show')"
                            >
                                Daftar Permintaan
                            </ResponsiveNavLink>
                            <ResponsiveNavLink
                                :href="route('direktur.approved')"
                                :active="route().current('direktur.approved') || route().current('direktur.tracking')"
                            >
                                Tracking & History
                            </ResponsiveNavLink>
                        </template>

                        <!-- Menu khusus Wakil Direktur -->
                        <template v-if="$page.props.auth.user.role === 'wakil_direktur'">
                            <ResponsiveNavLink
                                :href="route('wakil-direktur.dashboard')"
                                :active="route().current('wakil-direktur.dashboard')"
                            >
                                Dashboard
                            </ResponsiveNavLink>
                            <ResponsiveNavLink
                                :href="route('wakil-direktur.index')"
                                :active="route().current('wakil-direktur.index') || route().current('wakil-direktur.show')"
                            >
                                Daftar Permintaan
                            </ResponsiveNavLink>
                            <ResponsiveNavLink
                                :href="route('wakil-direktur.approved')"
                                :active="route().current('wakil-direktur.approved') || route().current('wakil-direktur.tracking')"
                            >
                                Tracking & History
                            </ResponsiveNavLink>
                        </template>

                        <!-- Menu khusus Staff Perencanaan -->
                        <template v-if="$page.props.auth.user.role === 'staff_perencanaan'">
                            <ResponsiveNavLink
                                :href="route('staff-perencanaan.dashboard')"
                                :active="route().current('staff-perencanaan.dashboard')"
                            >
                                Dashboard
                            </ResponsiveNavLink>
                            <ResponsiveNavLink
                                :href="route('staff-perencanaan.index')"
                                :active="route().current('staff-perencanaan.index') || route().current('staff-perencanaan.show')"
                            >
                                Daftar Permintaan
                            </ResponsiveNavLink>
                            <ResponsiveNavLink
                                :href="route('staff-perencanaan.approved')"
                                :active="route().current('staff-perencanaan.approved') || route().current('staff-perencanaan.tracking')"
                            >
                                History Perencanaan
                            </ResponsiveNavLink>
                        </template>

                        <!-- Menu khusus KSO -->
                        <template v-if="$page.props.auth.user.role === 'kso'">
                            <ResponsiveNavLink
                                :href="route('kso.dashboard')"
                                :active="route().current('kso.dashboard')"
                            >
                                Dashboard
                            </ResponsiveNavLink>
                            <ResponsiveNavLink
                                :href="route('kso.index')"
                                :active="route().current('kso.index') || route().current('kso.show')"
                            >
                                KSO
                            </ResponsiveNavLink>
                        </template>

                        <!-- Menu khusus Pengadaan -->
                        <template v-if="$page.props.auth.user.role === 'pengadaan'">
                            <ResponsiveNavLink
                                :href="route('pengadaan.dashboard')"
                                :active="route().current('pengadaan.dashboard')"
                            >
                                Dashboard
                            </ResponsiveNavLink>
                            <ResponsiveNavLink
                                :href="route('pengadaan.index')"
                                :active="route().current('pengadaan.index') || route().current('pengadaan.show')"
                            >
                                Pengadaan
                            </ResponsiveNavLink>
                        </template>

                        <!-- Menu untuk Admin -->
                        <template v-if="$page.props.auth.user.role === 'admin'">
                            <ResponsiveNavLink
                                :href="route('permintaan.index')"
                                :active="route().current('permintaan.*')"
                            >
                                Kelola Permintaan
                            </ResponsiveNavLink>
                        </template>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div
                        class="border-t border-gray-200 pb-1 pt-4"
                    >
                        <div class="px-4">
                            <div
                                class="text-base font-medium text-gray-800"
                            >
                                {{ $page.props.auth.user.nama }}
                            </div>
                            <div class="text-sm font-medium text-gray-500">
                                {{ $page.props.auth.user.email }}
                            </div>
                            <div class="text-xs text-gray-400 mt-1">
                                {{ 
                                    $page.props.auth.user.role === 'admin' ? 'Administrator Sistem' : 
                                    $page.props.auth.user.role === 'kepala_instalasi' ? 'Kepala Instalasi' : 
                                    $page.props.auth.user.role === 'unit' ? 'Unit Kerja' : 
                                    $page.props.auth.user.jabatan || 'User' 
                                }}
                            </div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('profile.edit')">
                                Profile
                            </ResponsiveNavLink>
                            <ResponsiveNavLink
                                :href="route('logout')"
                                method="post"
                                as="button"
                            >
                                Log Out
                            </ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Main Content with Sidebar -->
            <div class="flex">
                <!-- Sidebar -->
                <aside
                    :class="sidebarOpen ? 'w-64' : 'w-0'"
                    class="bg-white border-r border-gray-200 min-h-screen transition-all duration-300 overflow-hidden hidden sm:block"
                >
                    <nav class="mt-5 px-2">
                        <!-- Dashboard - Admin, Unit, Kepala Instalasi -->
                        <Link
                            v-if="$page.props.auth.user.role === 'admin' || 
                                  $page.props.auth.user.role === 'unit' || 
                                  $page.props.auth.user.role === 'kepala_instalasi'"
                            :href="route('dashboard')"
                            :class="route().current('dashboard') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-100'"
                            class="group flex items-center px-3 py-2 text-sm font-medium rounded-md mb-1 transition-colors"
                        >
                            <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            Dashboard
                        </Link>

                        <!-- Menu untuk Unit/Kepala Instalasi -->
                        <template v-if="$page.props.auth.user.role === 'unit' || $page.props.auth.user.role === 'kepala_instalasi'">
                            <Link
                                :href="route('permintaan.index')"
                                :class="route().current('permintaan.*') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-100'"
                                class="group flex items-center px-3 py-2 text-sm font-medium rounded-md mb-1 transition-colors"
                            >
                                <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Permintaan Saya
                            </Link>
                        </template>

                        <!-- Menu khusus Kepala Instalasi -->
                        <template v-if="$page.props.auth.user.role === 'kepala_instalasi'">
                            <Link
                                :href="route('kepala-instalasi.index')"
                                :class="route().current('kepala-instalasi.index') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-100'"
                                class="group flex items-center px-3 py-2 text-sm font-medium rounded-md mb-1 transition-colors"
                            >
                                <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                </svg>
                                Review Permintaan
                            </Link>
                        </template>

                        <!-- Menu khusus Kepala Bidang -->
                        <template v-if="$page.props.auth.user.role === 'kepala_bidang'">
                            <Link
                                :href="route('kepala-bidang.dashboard')"
                                :class="route().current('kepala-bidang.dashboard') ? 'bg-purple-100 text-purple-700' : 'text-gray-600 hover:bg-gray-100'"
                                class="group flex items-center px-3 py-2 text-sm font-medium rounded-md mb-1 transition-colors"
                            >
                                <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                                Dashboard
                            </Link>
                            <Link
                                :href="route('kepala-bidang.index')"
                                :class="route().current('kepala-bidang.index') || route().current('kepala-bidang.show') ? 'bg-purple-100 text-purple-700' : 'text-gray-600 hover:bg-gray-100'"
                                class="group flex items-center px-3 py-2 text-sm font-medium rounded-md mb-1 transition-colors"
                            >
                                <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                </svg>
                                Daftar Permintaan
                            </Link>
                            <Link
                                :href="route('kepala-bidang.approved')"
                                :class="route().current('kepala-bidang.approved') || route().current('kepala-bidang.tracking') ? 'bg-purple-100 text-purple-700' : 'text-gray-600 hover:bg-gray-100'"
                                class="group flex items-center px-3 py-2 text-sm font-medium rounded-md mb-1 transition-colors"
                            >
                                <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                                Tracking & History
                            </Link>
                        </template>

                        <!-- Menu khusus Direktur -->
                        <template v-if="$page.props.auth.user.role === 'direktur'">
                            <Link
                                :href="route('direktur.dashboard')"
                                :class="route().current('direktur.dashboard') ? 'bg-red-100 text-red-700' : 'text-gray-600 hover:bg-gray-100'"
                                class="group flex items-center px-3 py-2 text-sm font-medium rounded-md mb-1 transition-colors"
                            >
                                <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                                Dashboard
                            </Link>
                            <Link
                                :href="route('direktur.index')"
                                :class="route().current('direktur.index') || route().current('direktur.show') ? 'bg-red-100 text-red-700' : 'text-gray-600 hover:bg-gray-100'"
                                class="group flex items-center px-3 py-2 text-sm font-medium rounded-md mb-1 transition-colors"
                            >
                                <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                </svg>
                                Daftar Permintaan
                            </Link>
                            <Link
                                :href="route('direktur.approved')"
                                :class="route().current('direktur.approved') || route().current('direktur.tracking') ? 'bg-red-100 text-red-700' : 'text-gray-600 hover:bg-gray-100'"
                                class="group flex items-center px-3 py-2 text-sm font-medium rounded-md mb-1 transition-colors"
                            >
                                <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                                Tracking & History
                            </Link>
                        </template>

                        <!-- Menu khusus Wakil Direktur -->
                        <template v-if="$page.props.auth.user.role === 'wakil_direktur'">
                            <Link
                                :href="route('wakil-direktur.dashboard')"
                                :class="route().current('wakil-direktur.dashboard') ? 'bg-orange-100 text-orange-700' : 'text-gray-600 hover:bg-gray-100'"
                                class="group flex items-center px-3 py-2 text-sm font-medium rounded-md mb-1 transition-colors"
                            >
                                <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                                Dashboard
                            </Link>
                            <Link
                                :href="route('wakil-direktur.index')"
                                :class="route().current('wakil-direktur.index') || route().current('wakil-direktur.show') ? 'bg-orange-100 text-orange-700' : 'text-gray-600 hover:bg-gray-100'"
                                class="group flex items-center px-3 py-2 text-sm font-medium rounded-md mb-1 transition-colors"
                            >
                                <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                </svg>
                                Daftar Permintaan
                            </Link>
                            <Link
                                :href="route('wakil-direktur.approved')"
                                :class="route().current('wakil-direktur.approved') || route().current('wakil-direktur.tracking') ? 'bg-orange-100 text-orange-700' : 'text-gray-600 hover:bg-gray-100'"
                                class="group flex items-center px-3 py-2 text-sm font-medium rounded-md mb-1 transition-colors"
                            >
                                <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                                Tracking & History
                            </Link>
                        </template>

                        <!-- Menu khusus Staff Perencanaan -->
                        <template v-if="$page.props.auth.user.role === 'staff_perencanaan'">
                            <Link
                                :href="route('staff-perencanaan.dashboard')"
                                :class="route().current('staff-perencanaan.dashboard') ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-100'"
                                class="group flex items-center px-3 py-2 text-sm font-medium rounded-md mb-1 transition-colors"
                            >
                                <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                                Dashboard
                            </Link>
                            <Link
                                :href="route('staff-perencanaan.index')"
                                :class="route().current('staff-perencanaan.index') || route().current('staff-perencanaan.show') ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-100'"
                                class="group flex items-center px-3 py-2 text-sm font-medium rounded-md mb-1 transition-colors"
                            >
                                <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                </svg>
                                Daftar Permintaan
                            </Link>
                            <Link
                                :href="route('staff-perencanaan.approved')"
                                :class="route().current('staff-perencanaan.approved') || route().current('staff-perencanaan.tracking') ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-100'"
                                class="group flex items-center px-3 py-2 text-sm font-medium rounded-md mb-1 transition-colors"
                            >
                                <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                                History Perencanaan
                            </Link>
                        </template>

                        <!-- Menu khusus KSO -->
                        <template v-if="$page.props.auth.user.role === 'kso'">
                            <Link
                                :href="route('kso.dashboard')"
                                :class="route().current('kso.dashboard') ? 'bg-teal-100 text-teal-700' : 'text-gray-600 hover:bg-gray-100'"
                                class="group flex items-center px-3 py-2 text-sm font-medium rounded-md mb-1 transition-colors"
                            >
                                <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                                Dashboard
                            </Link>
                            <Link
                                :href="route('kso.index')"
                                :class="route().current('kso.index') || route().current('kso.show') ? 'bg-teal-100 text-teal-700' : 'text-gray-600 hover:bg-gray-100'"
                                class="group flex items-center px-3 py-2 text-sm font-medium rounded-md mb-1 transition-colors"
                            >
                                <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                KSO
                            </Link>
                        </template>

                        <!-- Menu khusus Pengadaan -->
                        <template v-if="$page.props.auth.user.role === 'pengadaan'">
                            <Link
                                :href="route('pengadaan.dashboard')"
                                :class="route().current('pengadaan.dashboard') ? 'bg-green-100 text-green-700' : 'text-gray-600 hover:bg-gray-100'"
                                class="group flex items-center px-3 py-2 text-sm font-medium rounded-md mb-1 transition-colors"
                            >
                                <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                                Dashboard
                            </Link>
                            <Link
                                :href="route('pengadaan.index')"
                                :class="route().current('pengadaan.index') || route().current('pengadaan.show') ? 'bg-green-100 text-green-700' : 'text-gray-600 hover:bg-gray-100'"
                                class="group flex items-center px-3 py-2 text-sm font-medium rounded-md mb-1 transition-colors"
                            >
                                <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                Pengadaan
                            </Link>
                        </template>

                        <!-- Menu untuk Admin -->
                        <template v-if="$page.props.auth.user.role === 'admin'">
                            <Link
                                :href="route('permintaan.index')"
                                :class="route().current('permintaan.*') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-100'"
                                class="group flex items-center px-3 py-2 text-sm font-medium rounded-md mb-1 transition-colors"
                            >
                                <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Kelola Permintaan
                            </Link>
                        </template>
                    </nav>
                </aside>

                <!-- Main Content Area -->
                <div class="flex-1">
                    <!-- Page Heading -->
                    <header
                        class="bg-white shadow"
                        v-if="$slots.header"
                    >
                        <div class="mx-auto max-w-full px-6 py-6 sm:px-8 lg:px-10">
                            <slot name="header" />
                        </div>
                    </header>

                    <!-- Page Content -->
                    <main>
                        <slot />
                    </main>
                </div>
            </div>
        </div>
    </div>
</template>
