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
                        <!-- Dashboard - Semua Role kecuali KSO dan Pengadaan -->
                        <ResponsiveNavLink
                            v-if="$page.props.auth.user.role !== 'kso' && $page.props.auth.user.role !== 'pengadaan'"
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
                        <!-- Dashboard - Semua Role kecuali KSO dan Pengadaan -->
                        <Link
                            v-if="$page.props.auth.user.role !== 'kso' && $page.props.auth.user.role !== 'pengadaan'"
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
