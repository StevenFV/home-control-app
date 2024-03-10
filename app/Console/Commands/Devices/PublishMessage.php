<?php

namespace App\Console\Commands\Devices;

use App\Enums\Zigbee2MqttUtility;
use App\Http\Requests\Devices\LightingRequest;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use PhpMqtt\Client\Facades\MQTT;

class PublishMessage extends Command
{
    protected $signature = 'app:publish-message';

    protected $description = 'Publish message to device';

    public function handle(LightingRequest $request): void
    {
        $topic = Zigbee2MqttUtility::BASE_TOPIC->value . $request['friendlyName'] . $request['set'];
        $message = json_encode(['state' => $request['toggle']]);

        MQTT::publish($topic, $message);
        MQTT::disconnect();

        sleep(1);

        Artisan::call('app:device-data-storage', ['model' => 'Lighting']);
    }
}
