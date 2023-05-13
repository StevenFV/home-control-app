<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpMqtt\Client\Facades\MQTT;

class MqttBroker extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:mqtt-broker';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'MQTT broker publish subscribe tests';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        echo 'SWITCH ALL LIGHTS ON';

        MQTT::publish('zigbee2mqtt/LIGHT_BACK_PATIO_01/set', '{"state":"TOGGLE"}'); // ON OFF TOGGLE
        MQTT::publish('zigbee2mqtt/LIGHT_ENTRANCE_01/set', '{"state":"TOGGLE"}'); // ON OFF TOGGLE
        MQTT::publish('zigbee2mqtt/0x680ae2fffe43784f/set', '{"state":"TOGGLE"}'); // ON OFF TOGGLE
        MQTT::publish('zigbee2mqtt/LIGHT_BACK_PATIO_02/set', '{"state":"TOGGLE"}'); // ON OFF TOGGLE
        MQTT::publish('zigbee2mqtt/LIGHT_GARAGE_SIDE/set', '{"state":"TOGGLE"}'); // ON OFF TOGGLE
        MQTT::publish('zigbee2mqtt/LIGHT_ENTRANCE_02', '{"state":"TOGGLE"}'); // ON OFF TOGGLE

//        MQTT::publish('zigbee2mqtt/0x680ae2fffe42d1ec/get', '{"state":""}');

        /** @var \PhpMqtt\Client\Contracts\MqttClient $mqtt */
//        $mqtt = MQTT::connection();
//        $mqtt->subscribe('zigbee2mqtt/0x680ae2fffe42d1ec', function (string $topic, string $message) {
//            echo sprintf('Received QoS level 1 message on topic [%s]: %s', $topic, $message);
//        }, 1);
//        $mqtt->loop(true);

    }
}
