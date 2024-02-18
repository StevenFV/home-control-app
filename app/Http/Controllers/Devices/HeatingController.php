<?php

namespace App\Http\Controllers\Devices;

use App\Abstracts\Devices\AbstractDeviceMessenger;
use App\DevicePolicy;
use Inertia\Inertia;
use Inertia\Response;

class HeatingController extends AbstractDeviceMessenger
{
    public function __construct()
    {
        $devicePolicy = new DevicePolicy();
        $devicePolicy->check();
    }

    public function index(): Response
    {
        // todosfv ajuste AbstractDeviceMessenger to can be
        // todosfv implement from all devices class without change

        return Inertia::render('Heating/Index', [
            'subscribeTopicMessage' => $this->getTopicMessage()
        ]);
    }
}
