<?php

namespace App\Abstracts\Devices;

use App\Enums\Zigbee2MqttUtility;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use PhpMqtt\Client\Exceptions\DataTransferException;
use PhpMqtt\Client\Exceptions\MqttClientException;
use PhpMqtt\Client\Facades\MQTT;

class AbstractDeviceMessenger
{
    protected function getTopicMessage(): array
    {
        $deviceFriendlyNames = $this->getFriendlyNames();
        $deviceTopicsMessages = [];

        foreach ($deviceFriendlyNames as $deviceFriendlyName) {
            $deviceTopicsMessages[] = $this->getSubscribeTopicMessage($deviceFriendlyName);
        }

        return $deviceTopicsMessages;
    }

    private function getFriendlyNames(): array
    {
        return $this->getMqttBridgeFriendlyNames()->filter(function ($name) {
            return str_starts_with($name, Zigbee2MqttUtility::LIGHTING_TOPIC_FILTER->value);
        })->toArray();
    }

    private function getSubscribeTopicMessage(string $friendlyName): array
    {
        try {
            MQTT::connection()->subscribe(
                Zigbee2MqttUtility::BASE_TOPIC->value . $friendlyName,
                function (string $topic, string $message) use (&$callback) {
                    $callback = [
                        'topic' => $topic,
                        'message' => json_decode($message)
                    ];
                    MQTT::connection()->interrupt();
                },
                0
            );
            MQTT::publish(
                Zigbee2MqttUtility::BASE_TOPIC->value . $friendlyName . Zigbee2MqttUtility::GET->value,
                Zigbee2MqttUtility::STATE_DEVICE_PAYLOAD->value
            );
            MQTT::connection()->loop();

            return $callback;
        } catch (MqttClientException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    private function getMqttBridgeFriendlyNames(): Collection
    {
        try {
            MQTT::connection()->subscribe(
                Zigbee2MqttUtility::ZIGBEE2MQTT_BRIDGE_DEVICES->value,
                function (string $topic, string $message) use (&$friendlyNames) {
                    $deviceSubscribe = json_decode($message);
                    $friendlyNames = collect($deviceSubscribe)->pluck('friendly_name')->except([0]);

                    MQTT::connection()->interrupt();
                },
                1
            );
            MQTT::connection()->loop(false, true);

            return $friendlyNames;
        } catch (DataTransferException $e) {
            return ['error' => $e->getMessage()]; // todosfv and test all error return
        }
    }

    public function publishMessage(
        Request $request,
        $command = Zigbee2MqttUtility::SET->value,
        $state = Zigbee2MqttUtility::TOGGLE->value
    ): void {
        $topicWithCommand = $request['topic'] . $command;
        $messageState = json_encode(['state' => $state]);

        MQTT::publish($topicWithCommand, $messageState);
        MQTT::disconnect();
    }
}
