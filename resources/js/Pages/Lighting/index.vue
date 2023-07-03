<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InputSwitch from 'primevue/inputswitch';
import {Head} from "@inertiajs/vue3";
import {ref} from 'vue';
import {trans} from "laravel-vue-i18n";

const props = defineProps({
    subscribeTopicMessage: {
        type: Object, default: () => {
        }
    }
})

const subscribe = ref({
    topic: props?.subscribeTopicMessage?.subscribeLightingTopic,
    energy: props?.subscribeTopicMessage?.subscribeLightingMessage?.energy,
    linkquality: props?.subscribeTopicMessage?.subscribeLightingMessage?.linkquality,
    power: props?.subscribeTopicMessage?.subscribeLightingMessage?.power,
    state: props?.subscribeTopicMessage?.subscribeLightingMessage?.state
})

const state = ref(subscribe.value.state === 'ON');
</script>

<template>
    <Head :title="trans('lighting.index')"/>

    <AuthenticatedLayout>
        <template #header>
            <!-- todosfv add to lang make component -->
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ trans('lighting.{subscribe.topic}: ') + subscribe.energy }}
            </h2>
            {{ trans('Energy: ') + subscribe.energy }}<br>
            {{ trans('Link quality: ') + subscribe.linkquality }}<br>
            {{ trans('Kwh: ') + subscribe.power }}<br>
            <InputSwitch v-model="state" />
        </template>
    </AuthenticatedLayout>
</template>
