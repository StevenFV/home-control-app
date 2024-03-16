<?php

namespace App\Console\Commands\Devices;

use App\Enums\Zigbee2MqttUtility;
use App\Interfaces\Devices\DeviceDataStoreInterface;
use App\Traits\Devices\StorageModel;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use PhpMqtt\Client\Exceptions\DataTransferException;
use PhpMqtt\Client\Exceptions\RepositoryException;
use PhpMqtt\Client\Facades\MQTT;

class StoreData extends Command implements DeviceDataStoreInterface
{
    use StorageModel;

    protected $signature = 'device:store-data {deviceModel}';
    protected $description = 'Get device data from mqtt broker and put to home-control-app database';


    public function handle(): void
    {
        $stringModel = $this->argument('deviceModel');

        $model = $this->argumentModel($stringModel);

        $deviceStoreData = app(self::class);
        $deviceStoreData->store($model);
    }

    public function store(Model $model): void
    {
        $informations = $this->data($model);

        array_map(function ($information) use ($model) {
            $this->updateOrCreateDeviceData($model, $information);
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

    private function updateOrCreateDeviceData(Model $model, array $information): void
    {
        $friendlyName = str_replace("zigbee2mqtt/", "", $information['topic']);
        $message = json_decode($information['message']);
        $deviceData = array_slice($model->getFillable(), 2);

        foreach ($deviceData as $data) {
            $model->updateOrCreate(
                ['friendly_name' => $friendlyName],
                [
                    $data => $message->$data ?? null,
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
