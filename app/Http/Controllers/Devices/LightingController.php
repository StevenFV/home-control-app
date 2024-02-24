<?php

namespace App\Http\Controllers\Devices;

use App\Abstracts\Devices\AbstractDeviceMessenger;
use App\DevicePolicy;
use App\Http\Requests\Devices\LightingRequest;
use Inertia\Inertia;
use Inertia\Response;

class LightingController extends AbstractDeviceMessenger
{
    public function __construct()
    {
        $devicePolicy = new DevicePolicy();
        $devicePolicy->check();
    }

    public function index(): Response
    {
        return Inertia::render('Lighting/Index', [
            'subscribeTopicMessage' => $this->getTopicMessage()
        ]);
    }

    public function toggleLight(LightingRequest $request): void
    {
        $topic = $request['topic'] . $request['set'];
        $message = json_encode(['state' => $request['toggle']]);

        $this->publishMessage($topic, $message);
    }
}
