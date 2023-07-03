<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use PhpMqtt\Client\Facades\MQTT;

class MqttSubscribeLightingServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $mqtt = MQTT::connection();
        $subscribeLightingTopic = null;
        $subscribeLightingMessage = null;

        // todosfv search for get "zigbee2mqtt/LIGHT_ENTRANCE_01" directly from mqtt and loop on each instance for populate topicFilter
        $mqtt->subscribe("zigbee2mqtt/LIGHT_ENTRANCE_01", function (string $topic, string $message) use ($mqtt, &$subscribeLightingTopic, &$subscribeLightingMessage) {
            // Remove elements before and including the last forward slash
            $subscribeLightingTopic = substr($topic, strrpos($topic, '/') + 1);
            $subscribeLightingMessage = json_decode($message);

            $mqtt->unsubscribe("zigbee2mqtt/LIGHT_ENTRANCE_01");
            $mqtt->disconnect();
        }, 1);

        MQTT::publish("zigbee2mqtt/LIGHT_ENTRANCE_01/get", '{"state":""}');

        $mqtt->loop(false, true);

        $this->app->instance('subscribeLightingTopic', $subscribeLightingTopic);
        $this->app->instance('subscribeLightingMessage', $subscribeLightingMessage);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
