<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    to: {
        type: String,
        default: null,
    }
});
</script>

<template>
    <Link :href="to">
        <slot/>
    </Link>
</template>
