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
    // todosfv find a way for not hardcoded friendly name but get from zigbee2mqtt message
    protected array $allLightsGroup = [
        'lgt_ho_outd_pat_nth',
        'lgt_ho_ind_grfl_ent_nth',
        'lgt_ho_ind_grfl_ent_wst',
        'lgt_ho_outd_pat_sth_01',
        'lgt_ho_outd_pat_sth_02',
        'lgt_ho_outd_gar_sth',
        'lgt/shd/outd/fld_wst',
        'ht_ga_ind_bsbd_elc'
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
