<script setup>
import { trans } from "laravel-vue-i18n";
import { Head } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InputSwitch from 'primevue/inputswitch';
import { ref } from 'vue';

const checked = ref(false);
</script>

<template>
    <Head :title="trans('lighting.index')" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ trans('lighting.index') }}</h2>
            <InputSwitch v-model="checked" />
        </template>
    </AuthenticatedLayout>
</template>
