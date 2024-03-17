<?php

namespace App\Console\Commands\Devices;

use App\Enums\Zigbee2MqttUtility;
use App\Interfaces\Devices\DeviceStoreInterface;
use App\Traits\Devices\DeviceModelNamespaceResolver;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use PhpMqtt\Client\Contracts\MqttClient;
use PhpMqtt\Client\Exceptions\DataTransferException;
use PhpMqtt\Client\Exceptions\InvalidMessageException;
use PhpMqtt\Client\Exceptions\MqttClientException;
use PhpMqtt\Client\Exceptions\ProtocolViolationException;
use PhpMqtt\Client\Exceptions\RepositoryException;
use PhpMqtt\Client\Facades\MQTT;

class StoreData extends Command implements DeviceStoreInterface
{
    use DeviceModelNamespaceResolver;

    protected $signature = 'device:store-data {deviceModelClassName}';
    protected $description = 'Get device data from mqtt broker and put to home-control-app database';
    private MqttClient $client;

    public function __construct()
    {
        parent::__construct();

        $this->client = MQTT::connection();
    }

    public function handle(): void
    {
        $deviceModelClassName = $this->argument('deviceModelClassName');

        $deviceModelClassNameWithNameSpace = $this->getDeviceModelClassNameWithNameSpace($deviceModelClassName);

        $deviceStoreData = app(self::class);

        $deviceStoreData->store($deviceModelClassNameWithNameSpace);
    }

    public function store(Model $model): void
    {
        $informations = $this->data($model);

        array_map(function ($information) use ($model) {
            $this->updateOrCreateDeviceData($model, $information);
        }, $informations);
    }

    public function data(Model $model): array
    {
        $friendlyNames = $model::distinct('friendly_name')->pluck('friendly_name')->toArray();
        $topicFilters = array_map(function ($friendlyName) {
            return Zigbee2MqttUtility::BASE_TOPIC->value . $friendlyName;
        }, $friendlyNames);
        return array_map([$this, 'fetchAndProcessMqttMessages'], $topicFilters);
    }

    public function fetchAndProcessMqttMessages($topicFilter): string | array
    {
        try {
            $this->client->subscribe(
                $topicFilter,
                function (string $topic, string $message) use (&$messageDetails) {
                    $messageDetails = $this->extractMessageDetails($topic, $message);
                    $this->client->interrupt();
                },
            );
            $this->client->publish(
                $topicFilter . Zigbee2MqttUtility::GET->value,
                Zigbee2MqttUtility::STATE_DEVICE_PAYLOAD->value
            );
            $this->client->loop();

            return $messageDetails;
        } catch (
            ProtocolViolationException |
            InvalidMessageException |
            MqttClientException |
            RepositoryException |
            DataTransferException
            $exception
        ) {
            $this->error('Class: "StoreData" Method: "fetchAndProcessMqttMessages" error: ' . $exception->getMessage());
            exit();
        }
    }

    private function extractMessageDetails(string $topic, string $message): array
    {
        return [
            'topic' => $topic,
            'message' => $message
        ];
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
}
