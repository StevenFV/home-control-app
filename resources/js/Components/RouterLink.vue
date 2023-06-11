<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    to: String,
});
</script>

<template>
    <Link :href="to">
        <slot />
    </Link>
</template>
