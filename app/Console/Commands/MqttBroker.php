<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpMqtt\Client\Contracts\MqttClient;
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
    protected array $allLightsGroup = [
        'LIGHT_ENTRANCE_01',
        'LIGHT_ENTRANCE_02',
        'LIGHT_FRONT_PATIO',
        'LIGHT_BACK_PATIO_01',
        'LIGHT_BACK_PATIO_02',
        'LIGHT_GARAGE_SIDE'
    ];
    protected array $allLightsQuestion = [
        'ALL_LIGHTS_ON',
        'ALL_LIGHTS_OFF'
    ];
    protected string $stateOn = 'ON';
    protected string $stateOff = 'OFF';
    protected string $stateToggle = 'TOGGLE';
    protected function setLightState(string $topic, string $state): void
    {
        MQTT::publish("zigbee2mqtt/$topic/set", '{"state":"' . $state . '"}');
    }
    protected function getLightState(string $topic): void
    {
        // TODOSFV Find how exit loop after receiving this message
        MQTT::connection()->subscribe("zigbee2mqtt/$topic", function (string $topic, string $message) {
            echo sprintf('Received QoS level 1 message on topic [%s]: %s', $topic, $message);
        }, 1);
        MQTT::publish("zigbee2mqtt/$topic/get", '{"state":""}');
        echo 'STARTING LOOP: ';
        MQTT::connection()->loop(true, true, 5);
        echo 'EXIT LOOP';
        MQTT::disconnect();
    }
    protected function setAllLightsState(string $state): void
    {
        foreach ($this->allLightsGroup as $topic) {
            MQTT::publish("zigbee2mqtt/$topic/set", '{"state":"' . $state . '"}');
        }
    }
    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $request = $this->choice(
            'What light do you want to toggle?',
            array_merge($this->allLightsGroup, $this->allLightsQuestion),
            0
        );

//        $this->setLightState($request, $this->stateToggle);
        $this->getLightState($request);

        if ($request === 'ALL_LIGHTS_ON') {
            $this->setAllLightsState($this->stateOn);
        }
        if ($request === 'ALL_LIGHTS_OFF') {
            $this->setAllLightsState($this->stateOff);
        }
        /** @var MqttClient $mqtt */ // TODOSFV Need this???
    }
}
