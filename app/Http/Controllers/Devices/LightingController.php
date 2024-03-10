<?php

namespace App\Http\Controllers\Devices;

use App\Abstracts\Devices\AbstractDataConstructor;
use App\DevicePolicy;
use App\Models\Devices\Lighting;
use Inertia\Inertia;
use Inertia\Response;

class LightingController extends AbstractDataConstructor
{
    public function __construct(Lighting $lighting)
    {
        parent::__construct($lighting);

        $devicePolicy = new DevicePolicy();
        $devicePolicy->check();
    }

    public function index(): Response
    {
        return Inertia::render('Lighting/Index', [
            'lightingData' => $this->dataForFrontend()
        ]);
    }
}
