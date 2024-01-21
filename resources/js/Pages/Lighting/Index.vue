<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InputSwitch from 'primevue/inputswitch';
import axios from 'axios';
import {Head} from "@inertiajs/vue3";
import {computed, reactive, watch} from 'vue';
import {trans} from "laravel-vue-i18n";


const INDEX = 'lighting.index';
const TOPIC_TITLE = 'lighting.topic_title';
const MESSAGE_LABEL = 'lighting.message_label';

const props = defineProps({
    subscribeTopicMessage: {
        type: Object,
        default: null,
    }
})

const data = props.subscribeTopicMessage;

const transText = (strKey, varKey = null) => {
  return varKey ? trans(`${strKey}.${varKey}`).toUpperCase() : trans(`${strKey}`).toUpperCase();
}

const displayText = (strKey, varKey = null, value = null) => {
  return value ? `${transText(strKey, varKey)}${value}` : transText(strKey, varKey);
}

const state = reactive({});

for (const index of data) {
  const topic = index?.topic;
  state[topic] = index?.message?.state === 'ON';
}

const lightingStates = computed(() => {
    return Object.keys(state).map(topic => ({
        topic,
        state: state[topic] ? 'ON' : 'OFF'
    }));
});

watch(
    lightingStates,
    async (newStates, oldStates) => {
        const changedKey = Object.keys(newStates).find(key => newStates[key]?.state !== oldStates[key]?.state);

        if (changedKey) {
            const changedItem = newStates[changedKey];

            try {
                await axios.post(route('lighting.set'), changedItem);
            } catch (error) {
                console.error(error);
            }
        }
    },
    {deep: true}
);
</script>

<template>
    <Head :title="displayText(INDEX)"/>

    <AuthenticatedLayout>
        <template #header>
            <div class="grid grid-cols-3 gap-6">
                <div
                    v-for="(item, index) in subscribeTopicMessage"
                    :key="index"
                    class="bg-yellow-100 shadow-lg rounded-lg overflow-hidden m-6 col-3"
                >
                    <div class="text-center pt-2 font-semibold text-xl text-gray-800 leading-tight">
                        {{ displayText(TOPIC_TITLE, item.topic) }}
                    </div>
                    <div v-for="(value, label) in item.message" class="p-1">
                        <template v-if="label">
                          {{ displayText(MESSAGE_LABEL, label, value) }}
                        </template>
                    </div>
                  <InputSwitch v-model="state[item.topic]" class="ml-1"/>
                </div>
            </div>
        </template>
    </AuthenticatedLayout>
</template>
