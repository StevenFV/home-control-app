<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use PhpMqtt\Client\Facades\MQTT;

class MqttServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */

    public function register(): void
    {
        $this->deviceSubscribe();
        $this->lightingSubscribe();
    }

    public function deviceSubscribe(): void
    {
        $mqtt = MQTT::connection();
        $deviceSubscribe = null;

        $mqtt->subscribe("zigbee2mqtt/bridge/devices", function (string $topic, string $message) use ($mqtt, &$deviceSubscribe) {
            $deviceSubscribe = json_decode($message);

            $mqtt->unsubscribe("zigbee2mqtt/bridge/devices");
            $mqtt->disconnect();
        }, 1);

        $mqtt->loop(false, true);

        $this->app->instance('deviceSubscribe', $deviceSubscribe);
    }

    public function lightingSubscribe()
    {
        $mqtt = MQTT::connection();
        $lightingSubscribeTopic = null;
        $lightingSubscribeMessage = null;
        (array)$deviceSubscribe = app('deviceSubscribe');
        $allLightingDevice = collect($deviceSubscribe)->pluck('friendly_name')->except([0]);
        // todosfv find methode for make a collection with all devices with messages or create a new function in the LightingController
        foreach ($allLightingDevice as $lighting) {
            $mqtt->subscribe("zigbee2mqtt/+", function (string $topic, string $message) use ($mqtt, &$lightingSubscribeTopic, &$lightingSubscribeMessage) {
                // Remove elements before and including the last forward slash
                $lightingSubscribeTopic = substr($topic, strrpos($topic, '/') + 1);
                $lightingSubscribeMessage = json_decode($message);

                $mqtt->unsubscribe("zigbee2mqtt/+");
                $mqtt->disconnect();
            }, 1);
        }
        MQTT::publish("zigbee2mqtt/$lighting/get", '{"state":""}');

        $mqtt->loop(false, true);

        $this->app->instance('lightingSubscribeTopic', $lightingSubscribeTopic);
        $this->app->instance('lightingSubscribeMessage', $lightingSubscribeMessage);
    }
}
