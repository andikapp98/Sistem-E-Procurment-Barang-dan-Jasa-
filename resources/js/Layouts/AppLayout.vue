<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
const hasUser = !!(
    typeof $page !== "undefined" &&
    $page.props &&
    $page.props.auth &&
    $page.props.auth.user
);
</script>

<template>
    <component :is="hasUser ? AuthenticatedLayout : GuestLayout">
        <template v-if="$slots.header" #header>
            <slot name="header" />
        </template>
        <slot />
    </component>
</template>
