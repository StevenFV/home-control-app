<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { trans } from 'laravel-vue-i18n';
</script>

<template>
    <Head :title="trans('dashboard.dashboard')" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ trans('dashboard.dashboard') }}</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <!-- todosfv - change for Welcome to your Home Control App $page.props.auth.user.name -->
                    <div class="p-6 text-gray-900">{{ trans('app.logged') }}</div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
