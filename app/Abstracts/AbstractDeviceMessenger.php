<?php

namespace App\Abstracts;

use App\Enums\Zigbee2MqttUtility;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;
use PhpMqtt\Client\Contracts\MqttClient;
use PhpMqtt\Client\Exceptions\MqttClientException;
use PhpMqtt\Client\Facades\MQTT;

class AbstractDeviceMessenger
{
    private MqttClient $mqtt;

    public function __construct()
    {
        $this->mqtt = MQTT::connection('default');
    }

    protected function fetchDeviceTopicMessage(): array
    {
        $lightingFriendlyNames = $this->fetchDeviceFriendlyNames();
        $lightingTopicsMessages = [];

        foreach ($lightingFriendlyNames as $lightingFriendlyName) {
            $lightingTopicsMessages[] = $this->fetchSubscribeTopicMessage($lightingFriendlyName);
        }

        return $lightingTopicsMessages;
    }

    private function fetchDeviceFriendlyNames(): array
    {
        return $this->fetchFriendlyNames()->filter(function ($name) {
            return str_starts_with($name, Zigbee2MqttUtility::LIGHTING_TOPIC_FILTER->value);
        })->toArray();
    }

    private function fetchSubscribeTopicMessage(string $friendlyName): array
    {
        try {
            $this->mqtt->subscribe(
                Zigbee2MqttUtility::BASE_TOPIC->value . $friendlyName,
                function (string $topic, string $message) use (&$callback) {
                    $callback = [
                        'topic' => $topic,
                        'message' => json_decode($message)
                    ];
                    $this->mqtt->interrupt();
                },
                0
            );
            $this->mqtt->publish(
                Zigbee2MqttUtility::BASE_TOPIC->value . $friendlyName . Zigbee2MqttUtility::GET->value,
                Zigbee2MqttUtility::STATE_DEVICE_PAYLOAD->value
            );
            $this->mqtt->loop();

            return $callback;
        } catch (MqttClientException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    private function fetchFriendlyNames(): Collection
    {
        try {
            $this->mqtt->subscribe(
                Zigbee2MqttUtility::ZIGBEE2MQTT_BRIDGE_DEVICES->value,
                function (string $topic, string $message) use (&$friendlyNames) {
                    $deviceSubscribe = json_decode($message);
                    $friendlyNames = collect($deviceSubscribe)->pluck('friendly_name')->except([0]);

                    $this->mqtt->interrupt();
                },
                1
            );
            $this->mqtt->loop(false, true);

            return $friendlyNames;
        } catch (MqttClientException $e) {
            return ['error' => $e->getMessage()]; // todosfv and test all error return
        }
    }

    public function publishDeviceToggle(Request $request): void
    {
        $topic = $request['topic'];
        $this->publishMessage($this->createSetTopic($topic), $this->createStateJson(Zigbee2MqttUtility::TOGGLE->value));
    }

    private function publishMessage(string $topic, string $message): void
    {
        try {
            $this->mqtt->publish($topic, $message);
            $this->mqtt->disconnect();
        } catch (MqttClientException $e) {
            Session::flash('error', $e->getMessage());
        }
    }

    private function createSetTopic(string $topic): string
    {
        return $topic . Zigbee2MqttUtility::SET->value;
    }

    private function createStateJson(string $state): bool|string
    {
        return json_encode(['state' => $state]);
    }
}
