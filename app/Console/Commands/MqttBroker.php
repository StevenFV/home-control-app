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
    protected $signature = 'app:mqtt';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'MQTT broker publish subscribe tests';
    // todosfv find a way for not hardcoded friendly name but get from zigbee2mqtt message
    protected array $allLightsGroup = [
        'light/home/outside/front_patio/recessed_light',
        'light/home/indoor/ground_floor/entrance_west/ceiling_light',
        'light/home/indoor/ground_floor/entrance_north/ceiling_light',
        'light/home/outside/back_patio/wall_light',
        'light/garage/outside/side/wall_light',
        'light/home/outside/back_patio/recessed_light',
        'light/shed/outside/side/wall_light',
        'heat/garage/indoor/electric_baseboard'
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
        MQTT::connection()->subscribe("zigbee2mqtt/$topic", function (string $topic, string $message) {
            // phpcs:disable
            echo sprintf('Received QoS level 1 message on topic [%s]: %s', $topic, $message);
            // phpcs:enable
            MQTT::unsubscribe("zigbee2mqtt/+");
            MQTT::disconnect();
        }, 1);
        MQTT::publish("zigbee2mqtt/$topic/get", '{"state":""}');
        MQTT::connection()->loop(true, true, 5);
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
    // todosfv update handle for use not only with lighting but with other devices too
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
    }
}
