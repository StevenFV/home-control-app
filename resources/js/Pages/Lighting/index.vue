<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InputSwitch from 'primevue/inputswitch';
import axios from 'axios';
import {Head} from "@inertiajs/vue3";
import {computed, reactive, watch} from 'vue';
import {trans} from "laravel-vue-i18n";

const props = defineProps({
    subscribeTopicMessage: {
        type: Object,
        default: null,
    }
})

const state = reactive({});
// Assign initial state values
for (const topic in props.subscribeTopicMessage.lightingSubscribeMessage) {
    state[topic] = props.subscribeTopicMessage.lightingSubscribeMessage[topic].state === 'ON';
}

const lightingStates = computed(() => {
    return Object.keys(state).map(topic => ({
        topic,
        state: state[topic] ? 'ON' : 'OFF'
    }));
});

// todosfv review all this solution (foreach in mqtt controller and two way binding/watch/setPublishTopicMessage&&lightingPublishToggle vue.js)
// todosfv ajuste try and catch and LightingController@setPublishTopicMessage response
watch(
    lightingStates,
    async (newStates, oldStates) => {
        const changedKey = Object.keys(newStates).find(key => newStates[key].state !== oldStates[key].state);

        if (changedKey) {
            const changedItem = newStates[changedKey];
            console.log('Item state changed:', changedItem.state);

            try {
                // Make the HTTP request to your Laravel route
                const response = await axios.post(route('lighting.set'), {
                    changedItem: changedItem
                });
                // Handle the response from the controller if needed
                console.log(response.data);
            } catch (error) {
                // Handle any error that occurs during the request
                console.error(error);
            }
        }
    },
    {deep: true}
);
</script>

<template>
    <Head :title="trans('lighting.index')"/>

    <AuthenticatedLayout>
        <template #header>
            <!-- todosfv add to lang and make component -->
            <div class="grid grid-cols-3 gap-6">
                <div
                    v-for="lighting in lightingStates"
                    :key="lighting.topic"
                    class="bg-yellow-100 shadow-lg rounded-lg overflow-hidden m-6 col-3"
                >
                    <div class="text-center pt-2 font-semibold text-xl text-gray-800 leading-tight">
                        {{ lighting.topic }}
                    </div>
                    <div class="p-4">
                        {{
                            trans('Link quality: ') + props.subscribeTopicMessage.lightingSubscribeMessage[lighting.topic].energy
                        }}<br>
                        {{
                            trans('Link quality: ') + props.subscribeTopicMessage.lightingSubscribeMessage[lighting.topic].linkquality
                        }}<br>
                        {{
                            trans('Link quality: ') + props.subscribeTopicMessage.lightingSubscribeMessage[lighting.topic].power
                        }}<br>
                        <InputSwitch v-model="state[lighting.topic]"/>
                    </div>
                </div>
            </div>
        </template>
    </AuthenticatedLayout>
</template>
