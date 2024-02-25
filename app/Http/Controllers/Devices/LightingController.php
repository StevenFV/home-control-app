<?php

namespace App\Http\Controllers\Devices;

use App\Abstracts\Devices\AbstractDeviceMessenger;
use Inertia\Inertia;
use Inertia\Response;

class LightingController extends AbstractDeviceMessenger
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index(): Response
    {
        return Inertia::render('Lighting/Index', [
            'subscribeTopicMessage' => $this->getTopicMessage()
        ]);
    }
}
