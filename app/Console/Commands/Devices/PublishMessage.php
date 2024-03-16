<?php

namespace App\Console\Commands\Devices;

use App\Enums\Zigbee2MqttUtility;
use App\Http\Requests\Devices\DeviceRequest;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use PhpMqtt\Client\Facades\MQTT;

class PublishMessage extends Command
{
    protected $signature = 'device:publish-message';

    protected $description = 'Publish message to device';

    private string $topic;

    private string $message;

    private string $deviceModel;

    public function handle(DeviceRequest $request): void
    {
        $this->topic = $this->createTopic($request);
        $this->message = $this->createMessage($request);
        $this->deviceModel = $this->getDeviceModel($request);

        $this->publishMessage();
        $this->updateDeviceDataInDatabase();
    }

    private function createTopic(DeviceRequest $request): string
    {
        return Zigbee2MqttUtility::BASE_TOPIC->value . $request['friendlyName'] . $request['set'];
    }

    private function createMessage(DeviceRequest $request): string
    {
        return json_encode(['state' => $request['state']]);
    }

    private function getDeviceModel(DeviceRequest $request): string
    {
        return $request['deviceModel'];
    }

    private function publishMessage(): void
    {
        MQTT::publish($this->topic, $this->message);
        MQTT::disconnect();
    }

    private function updateDeviceDataInDatabase(): void
    {
        sleep(env('DEVICE_STATE_UPDATE_DELAY'));

        Artisan::call('device:store-data', ['deviceModel' => $this->deviceModel]);
    }
}
