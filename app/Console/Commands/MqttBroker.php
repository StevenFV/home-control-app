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
    protected $signature = 'app:mqtt';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'MQTT broker publish subscribe tests';
    protected array $allLightsGroup = [
        'lgt/ho/outd/pat_nth',
        'lgt/ho/ind/grfl/ent_nth',
        'lgt/ho/ind/grfl/ent_wst',
        'lgt/ho/outd/pat_sth_01',
        'lgt/ho/outd/pat_sth_02',
        'lgt/ho/outd/gar_sth',
        'lgt/shd/outd/fld_wst',
//        'ht/ga/ind/bsbd_elc'
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
