<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import axios from 'axios';
import Button from 'primevue/button';
import {computed, reactive, watch} from 'vue';
import {Head} from "@inertiajs/vue3";
import InputSwitch from 'primevue/inputswitch';
import {trans} from "laravel-vue-i18n";
import Zigbee2MqttUtility from "@/Enums/Devices/Zigbee2MqttUtility";


const INDEX = 'lighting.index';
const TOPIC_TITLE = 'lighting.topic_title';
const MESSAGE_LABEL = 'lighting.message_label';

const props = defineProps({
    subscribeTopicMessage: {
        type: Object,
        default: null,
    }
})

const transText = (strKey, varKey = null) => {
  return varKey ? trans(`${strKey}.${varKey}`).toUpperCase() : trans(`${strKey}`).toUpperCase();
}

const displayText = (strKey, varKey = null, value = null) => {
  return value ? `${transText(strKey, varKey)}${value}` : transText(strKey, varKey);
}

const state = reactive({});
const data = reactive(props.subscribeTopicMessage);

const updateLightingState = () => {
  for (const index of data) {
    const topic = index?.topic;
    state[topic] = index?.message?.state === 'ON';
  }
}

updateLightingState();

const lightingStates = computed(() => {
    return Object.keys(state).map(topic => ({
        topic,
        state: state[topic] ? 'ON' : 'OFF'
    }));
});

watch(lightingStates, (newStates, oldStates) => {
    const changedKey = Object.keys(newStates).find(key => newStates[key]?.state !== oldStates[key]?.state);

    if (changedKey) {
      toggleLight(newStates, changedKey)
    }
  }, {deep: true}
);

const toggleLight = async (newStates, changedKey) => {
  const changedItem = newStates[changedKey];
  changedItem[Zigbee2MqttUtility.KEY_SET] = Zigbee2MqttUtility.TOPIC_SET;
  changedItem[Zigbee2MqttUtility.KEY_COMMAND_TOGGLE] = Zigbee2MqttUtility.COMMAND_TOGGLE;

  try {
    await axios.post(route('lighting.set'), changedItem);
  } catch (error) {
    console.error(error);
  }
}

const storeIdentification = async () => {
  try {
    await axios.post(route('lighting.storeIdentifications'))
  } catch (error){
    console.error(error);
  }
}
</script>

<template>
    <Head :title="displayText(INDEX)"/>

    <AuthenticatedLayout>
        <template #header>
            <Button
                label="Submit"
                @click="storeIdentification"
            />
            <div class="grid grid-cols-3 gap-6">
                <div
                    v-for="(item, indexItem) in subscribeTopicMessage"
                    :key="indexItem"
                    class="bg-yellow-100 shadow-lg rounded-lg overflow-hidden m-6 col-3"
                >
                    <div class="text-center pt-2 font-semibold text-xl text-gray-800 leading-tight">
                        {{ displayText(TOPIC_TITLE, item.topic) }}
                    </div>
                    <div
                        v-for="(value, label, indexValue) in item.message"
                        :key="indexValue"
                        class="p-1"
                    >
                        <template v-if="label">
                            {{ displayText(MESSAGE_LABEL, label, value) }}
                        </template>
                    </div>
                    <InputSwitch
                        v-model="state[item.topic]"
                        class="ml-1"
                    />
                </div>
            </div>
        </template>
    </AuthenticatedLayout>
</template>
