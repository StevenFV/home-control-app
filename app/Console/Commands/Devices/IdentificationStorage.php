<?php

namespace App\Console\Commands\Devices;

use App\Enums\Zigbee2MqttUtility;
use App\Interfaces\Devices\DeviceDataStoreInterface;
use App\Traits\Devices\StorageModel;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use PhpMqtt\Client\Exceptions\DataTransferException;
use PhpMqtt\Client\Exceptions\RepositoryException;
use PhpMqtt\Client\Facades\MQTT;

class IdentificationStorage extends Command implements DeviceDataStoreInterface
{
    use StorageModel;

    protected $signature = 'device:identification-storage {model}';
    protected $description = 'Get device identifications from mqtt broker and put to home-control-app database';
    private string $message;

    public function handle(): void
    {
        $stringModel = $this->argument('model');

        $model = $this->argumentModel($stringModel);

        $deviceIdentificationStorage = app(self::class);
        $deviceIdentificationStorage->store($model);
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
        $deviceInformation = $this->communicate(Zigbee2MqttUtility::ZIGBEE2MQTT_BRIDGE_DEVICES->value);
        $decodedDeviceInformation = json_decode($deviceInformation);

        $modelBaseName = $this->getModelBaseName($model);

        return $this->getFilteredAndFormattedIdentifications($decodedDeviceInformation, $modelBaseName);
    }

    public function communicate($topicFilter): string|array
    {
        try {
            MQTT::connection()->subscribe(
                $topicFilter,
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

    private function getModelBaseName(Model $model): string
    {
        $tableName = $model->getTable();
        return Str::singular(Str::after($tableName, '.'));
    }
    private function getFilteredAndFormattedIdentifications(
        mixed $decodedDeviceInformation,
        string $modelBaseName
    ): array {
        $filteredIdentifications = array_filter($decodedDeviceInformation, function ($info) use ($modelBaseName) {
            return Str::startsWith($info->friendly_name, $modelBaseName);
        });

        return array_map(function ($info) {
            return [
                'ieee_address' => $info->ieee_address,
                'friendly_name' => $info->friendly_name
            ];
        }, $filteredIdentifications);
    }
}
