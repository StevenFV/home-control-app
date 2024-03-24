<?php

namespace App\Console\Commands\Devices;

use App\Enums\Zigbee2MqttUtility;
use App\Interfaces\Devices\DeviceStoreInterface;
use App\Traits\Devices\DeviceModelNamespaceResolverTrait;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use PhpMqtt\Client\Exceptions\DataTransferException;
use PhpMqtt\Client\Exceptions\InvalidMessageException;
use PhpMqtt\Client\Exceptions\MqttClientException;
use PhpMqtt\Client\Exceptions\ProtocolViolationException;
use PhpMqtt\Client\Exceptions\RepositoryException;
use PhpMqtt\Client\Facades\MQTT;

class StoreIdentification extends Command implements DeviceStoreInterface
{
    use DeviceModelNamespaceResolverTrait;

    protected $signature = 'device:store-identification {deviceModelClassName}';
    protected $description = 'Get device identifications from mqtt broker and put to home-control-app database';
    private string $message;

    public function handle(): void
    {
        $deviceModelClassName = $this->argument('deviceModelClassName');

        $deviceModelClassNameWithNameSpace = $this->getDeviceModelClassNameWithNameSpace($deviceModelClassName);

        $this->store($deviceModelClassNameWithNameSpace);
    }

    public function store(Model $model): void
    {
        $identifications = $this->data($model);

        foreach ($identifications as $identification) {
            $model->updateOrCreate(
                ['ieee_address' => $identification['ieee_address']],
                ['friendly_name' => $identification['friendly_name']]
            );
        }
    }

    public function data(Model $model): array
    {
        $mqttMessages = $this->fetchAndProcessMqttMessages(Zigbee2MqttUtility::ZIGBEE2MQTT_BRIDGE_DEVICES->value);

        $parsedMqttMessages = json_decode($mqttMessages);

        $modelBaseName = $this->getModelBaseName($model);

        return $this->getFilteredAndFormattedIdentifications($parsedMqttMessages, $modelBaseName);
    }

    public function fetchAndProcessMqttMessages($topicFilter): string | array
    {
        $mqtt = MQTT::connection();

        try {
            $mqtt->subscribe(
                $topicFilter,
                function (string $topic, string $message) use ($mqtt) {
                    $this->message = $message;

                    $mqtt->interrupt();
                },
            );
            $mqtt->loop();

            return $this->message;
        } catch (
            ProtocolViolationException |
            InvalidMessageException |
            MqttClientException |
            RepositoryException |
            DataTransferException
            $exception
        ) {
            return $exception->getMessage();
        }
    }

    private function getModelBaseName(Model $model): string
    {
        $tableName = $model->getTable();
        return Str::singular(Str::after($tableName, '.'));
    }

    private function getFilteredAndFormattedIdentifications(
        array $parsedMqttMessages,
        string $modelBaseName
    ): array {
        $filteredIdentifications = $this->filterIdentifications($parsedMqttMessages, $modelBaseName);

        return $this->formatIdentifications($filteredIdentifications);
    }

    private function filterIdentifications(array $parsedMqttMessages, string $modelBaseName): array
    {
        return array_filter($parsedMqttMessages, function ($identification) use ($modelBaseName) {
            return Str::startsWith($identification->friendly_name, $modelBaseName);
        });
    }

    private function formatIdentifications(array $filteredIdentifications): array
    {
        return array_map(function ($identification) {
            return [
                'ieee_address' => $identification->ieee_address,
                'friendly_name' => $identification->friendly_name
            ];
        }, $filteredIdentifications);
    }
}
