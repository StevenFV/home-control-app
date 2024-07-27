<?php

namespace App\Http\Controllers\Devices;

use App\Abstracts\Devices\AbstractDataConstructor;
use App\DevicePolicy;
use App\Models\Devices\Lighting;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
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

    public function fetchDataForFrontend(): Collection
    {
        Artisan::call('device:store-data', ['deviceModelClassName' => 'Lighting']);

        return $this->dataForFrontend();
    }
}
