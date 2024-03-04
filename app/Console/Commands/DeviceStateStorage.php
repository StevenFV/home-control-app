<?php

namespace App\Console\Commands;

use App\Enums\Zigbee2MqttUtility;
use App\Interfaces\Devices\DeviceDataStoreInterface;
use App\Traits\Devices\StorageModel;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use PhpMqtt\Client\Exceptions\DataTransferException;
use PhpMqtt\Client\Exceptions\RepositoryException;
use PhpMqtt\Client\Facades\MQTT;

class DeviceStateStorage extends Command implements DeviceDataStoreInterface
{
    use StorageModel;
    protected $signature = 'app:device-state-storage {model}';
    protected $description = 'Get device states from mqtt broker and put to home-control-app database';


    public function handle(): void
    {
        $stringModel = $this->argument('model');

        $model = $this->argumentModel($stringModel);

        $deviceStateStorage = app(self::class);
        $deviceStateStorage->store($model);
    }

    public function store(Model $model): void
    {
        $informations = $this->data($model);

        array_map(function ($information) use ($model) {
            $this->updateOrCreateDeviceState($model, $information);
        }, $informations);
    }

    public function communicate($topicFilter): string|array
    {
        try {
            MQTT::connection()->subscribe(
                Zigbee2MqttUtility::BASE_TOPIC->value . $topicFilter,
                function (string $topic, string $message) use (&$callback) {
                    $callback = [
                        'topic' => $topic,
                        'message' => $message
                    ];
                    MQTT::connection()->interrupt();
                },
                0
            );
            MQTT::publish(
                Zigbee2MqttUtility::BASE_TOPIC->value . $topicFilter . Zigbee2MqttUtility::GET->value,
                Zigbee2MqttUtility::STATE_DEVICE_PAYLOAD->value
            );
            MQTT::connection()->loop();

            return $callback;
        } catch (DataTransferException | RepositoryException | Exception $e) {
            $this->error('Error message: ' . $e->getMessage());
            exit();
        }
    }

    private function updateOrCreateDeviceState(Model $model, array $information): void
    {
        $friendlyName = str_replace("zigbee2mqtt/", "", $information['topic']);
        $states = json_decode($information['message']);
        $modelFillables = $model->getFillable();
        $friendlyNameAttribute = $modelFillables[1];
        $modelFillableStates = array_slice($modelFillables, 2);

        foreach ($modelFillableStates as $state) {
            $model->updateOrCreate(
                [$friendlyNameAttribute => $friendlyName],
                [
                    $state => $states->$state ?? null,
                ]
            );
        }
    }

    public function data(Model $model): array
    {
        $friendlyNames = $model::distinct('friendly_name')->pluck('friendly_name')->toArray();
        return array_map([$this, 'communicate'], $friendlyNames);
    }
}
