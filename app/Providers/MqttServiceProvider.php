<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use PhpMqtt\Client\Exceptions\MqttClientException;
use PhpMqtt\Client\Facades\MQTT;

class MqttServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */

    public function register(): void
    {
        $this->app->bind(MqttServiceProvider::class, function ($app) {
            return new MqttServiceProvider($app);
        });

        $this->deviceSubscribe();
        $this->lightingSubscribeMessage();
        $this->lightingPublishToggle($topic = '');
    }

    public function deviceSubscribe(): void
    {
        try {
            $mqtt = MQTT::connection();
            $deviceSubscribe = null;

            $mqtt->subscribe(
                "zigbee2mqtt/bridge/devices",
                function (string $topic, string $message) use ($mqtt, &$deviceSubscribe) {
                    $deviceSubscribe = json_decode($message);

                    $mqtt->unsubscribe("zigbee2mqtt/bridge/devices");
                    $mqtt->disconnect();
                },
                1
            );

            $mqtt->loop(false, true);

            $this->app->instance('deviceSubscribe', $deviceSubscribe);
        } catch (MqttClientException $e) {
            echo " deviceSubscribe: " . $e->getMessage();
        }
    }

    public function lightingSubscribeMessage()
    {
        try {
            $mqtt = MQTT::connection();
            $lightingSubscribeMessage = null;
            (array)$deviceSubscribe = app('deviceSubscribe');
            $allDevices = collect($deviceSubscribe)->pluck('friendly_name')->except([0]);

            // Subscribe to all topics outside the loop
            $mqtt->subscribe(
                "zigbee2mqtt/+",
                function (string $topic, string $message) use ($mqtt, &$lightingSubscribeMessage, $allDevices) {
                    $lightingSubscribeTopic = substr($topic, strrpos($topic, '/') + 1);
                    $lightingSubscribeMessage[$lightingSubscribeTopic] = json_decode($message);

                    /** @var array $lightingSubscribeMessage */
                    // Check if all devices have received messages
                    if (count($lightingSubscribeMessage) === count($allDevices)) {
                        // Unsubscribe and disconnect after all devices have received messages
                        $mqtt->unsubscribe("zigbee2mqtt/+");
                        $mqtt->disconnect();
                    }
                },
                1
            );

            // Publish messages for all devices
            foreach ($allDevices as $device) {
                MQTT::publish("zigbee2mqtt/$device/get", '{"state":""}');
            }

            $mqtt->loop(false, true);

            $this->app->instance('lightingSubscribeMessage', $lightingSubscribeMessage);
        } catch (MqttClientException $e) {
            echo " lightingSubscribeMessage: " . $e->getMessage();
        }
    }

    public function lightingPublishToggle(string $topic): void
    {
        try {
            MQTT::publish("zigbee2mqtt/$topic/set", '{"state": "TOGGLE"}');
        } catch (MqttClientException $e) {
            echo " lightingPublishToggle: " . $e->getMessage();
        }
    }
}
