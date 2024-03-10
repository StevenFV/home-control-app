<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import axios from 'axios';
import {computed, reactive, watch} from 'vue';
import {Head} from "@inertiajs/vue3";
import InputSwitch from 'primevue/inputswitch';
import {trans} from "laravel-vue-i18n";
import Zigbee2MqttUtility from "@/Enums/Devices/Zigbee2MqttUtility";


const INDEX = 'lighting.index';
const TOPIC_TITLE = 'lighting.topic_title';
const MESSAGE_LABEL = 'lighting.message_label';

const props = defineProps({
    lightingData: {
        type: Object,
        default: null,
    }
})

const translateText = (strKey, varKey = null) => {
  return varKey ? trans(`${strKey}.${varKey}`).toUpperCase() : trans(`${strKey}`).toUpperCase();
}

const displayText = (strKey, varKey = null, value = null) => {
  return value ? `${translateText(strKey, varKey)}${value}` : translateText(strKey, varKey);
}

const state = reactive({});
const data = reactive(props.lightingData);

const updateLightingState = () => {
  for (const index of data) {
    const friendlyName = index?.friendlyName;
    state[friendlyName] = index?.data?.state === 'ON';
  }
}

updateLightingState();

const lightingStates = computed(() => {
    return Object.keys(state).map(friendlyName => ({
        friendlyName,
        state: state[friendlyName] ? 'ON' : 'OFF'
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
</script>

<template>
    <Head :title="displayText(INDEX)"/>

    <AuthenticatedLayout>
        <template #header>
            <div class="grid grid-cols-3 gap-6">
                <div
                    v-for="item in lightingData"
                    :key="item.friendlyName"
                    class="bg-yellow-100 shadow-lg rounded-lg overflow-hidden m-6 col-3"
                >
                    <div class="text-center pt-2 font-semibold text-xl text-gray-800 leading-tight">
                        {{ displayText(TOPIC_TITLE, item.friendlyName) }}
                    </div>
                    <div
                        v-for="(value, label, indexValue) in item.data"
                        :key="indexValue"
                        class="p-1"
                    >
                        <template v-if="label && value">
                            {{ displayText(MESSAGE_LABEL, label, value) }}
                        </template>
                    </div>
                    <InputSwitch
                        v-model="state[item.friendlyName]"
                        class="ml-1"
                    />
                </div>
            </div>
        </template>
    </AuthenticatedLayout>
</template>
