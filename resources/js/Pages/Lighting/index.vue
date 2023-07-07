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

const topics = Object.keys(props.subscribeTopicMessage.lightingSubscribeMessage);

// const state = ref(subscribe.value.state === 'ON'); todosfv 2 way binding
</script>

<template>
    <Head :title="trans('lighting.index')" />

    <AuthenticatedLayout>
        <template #header>
            <!-- todosfv add to lang and make component -->
            <div class="grid grid-cols-3 gap-6">
                <div
                    v-for="topic in topics"
                    :key="topic"
                    class="bg-yellow-100 shadow-lg rounded-lg overflow-hidden m-6 col-3"
                >
                    <div class="text-center pt-2 font-semibold text-xl text-gray-800 leading-tight">
                        {{ topic }}
                    </div>
                    <div class="p-4">
                        {{
                            trans('Link quality: ') + props.subscribeTopicMessage.lightingSubscribeMessage[topic].energy
                        }}<br>
                        {{
                            trans('Link quality: ') + props.subscribeTopicMessage.lightingSubscribeMessage[topic].linkquality
                        }}<br>
                        {{
                            trans('Link quality: ') + props.subscribeTopicMessage.lightingSubscribeMessage[topic].power
                        }}<br>
                        <InputSwitch v-model="state" />
                    </div>
                </div>
            </div>
        </template>
    </AuthenticatedLayout>
</template>
