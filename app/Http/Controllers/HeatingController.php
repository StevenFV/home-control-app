<?php

namespace App\Http\Controllers;

use App\Abstracts\AbstractDeviceMessenger;
use App\DevicePolicy;
use Inertia\Inertia;
use Inertia\Response;

class HeatingController extends AbstractDeviceMessenger
{
    public function __construct()
    {
        parent::__construct();

        $devicePolicy = new DevicePolicy();
        $devicePolicy->check();
    }

    public function index(): Response
    {
        // todosfv ajuste AbstractDeviceMessenger to can be
        // todosfv implement from all devices class without change

        return Inertia::render('Heating/Index', [
            'subscribeTopicMessage' => $this->fetchDeviceTopicMessage()
        ]);
    }
}
