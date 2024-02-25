<?php

namespace App\Abstracts\Devices;

use App\DevicePolicy;
use App\Enums\Zigbee2MqttUtility;
use App\Http\Requests\Devices\DeviceRequest;
use App\Http\Requests\Devices\LightingRequest;
use App\Models\Devices\Lighting;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use PhpMqtt\Client\Exceptions\DataTransferException;
use PhpMqtt\Client\Exceptions\RepositoryException;
use PhpMqtt\Client\Facades\MQTT;

class AbstractDeviceMessenger
{
    private string $message;

    public function __construct()
    {
        $devicePolicy = new DevicePolicy();
        $devicePolicy->check();
    }

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
        } catch (DataTransferException | RepositoryException | Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    private function getMqttBridgeFriendlyNames(): Collection|array
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
        } catch (DataTransferException | RepositoryException | Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function storeIdentifications(): void
    {
        $identifications = $this->getIdentifications();
        $jsonIdentifications = json_decode($identifications);

        foreach ($jsonIdentifications as $identification) {
            if (Str::startsWith($identification->friendly_name, 'light')) {
                $lighting = new Lighting();

                $lighting->ieee_address = $identification->ieee_address;
                $lighting->friendly_name = $identification->friendly_name;
                $lighting->save();
            }
        }
    }

    protected function getIdentifications(): string|array
    {
        try {
            MQTT::connection()->subscribe(
                Zigbee2MqttUtility::ZIGBEE2MQTT_BRIDGE_DEVICES->value,
                function (string $topic, string $message) {
                    $this->message = $message;

                    MQTT::connection()->interrupt();
                },
                1
            );
            MQTT::connection()->loop(false, true);

            return $this->message;
        } catch (DataTransferException | RepositoryException | Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function publishMessage(LightingRequest $request): void
    {
        $topic = $request['topic'] . $request['set'];
        $message = json_encode(['state' => $request['toggle']]);

        MQTT::publish($topic, $message);
        MQTT::disconnect();
    }
}
