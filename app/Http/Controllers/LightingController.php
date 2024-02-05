<?php

namespace App\Http\Controllers;

use App\Abstracts\AbstractDeviceMessenger;
use App\DevicePolicy;
use Inertia\Inertia;
use Inertia\Response;

class LightingController extends AbstractDeviceMessenger
{
    public function __construct()
    {
        parent::__construct();

        $devicePolicy = new DevicePolicy();
        $devicePolicy->check();
    }

    public function index(): Response
    {
        return Inertia::render('Lighting/Index', [
            'subscribeTopicMessage' => $this->fetchDeviceTopicMessage()
        ]);
    }
}
