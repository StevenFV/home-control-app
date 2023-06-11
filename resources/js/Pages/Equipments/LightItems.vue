<script setup>
import { trans } from "laravel-vue-i18n";
import { Head } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InputSwitch from 'primevue/inputswitch';
import { ref } from 'vue';

const checked = ref(false);
</script>

<template>
    <Head :title="trans('equipments.light.lights')" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ trans('equipments.light.lights') }}</h2>
        </template>
    </AuthenticatedLayout>

    <InputSwitch v-model="checked" />
</template>
