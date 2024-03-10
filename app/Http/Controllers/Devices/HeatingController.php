<?php

namespace App\Http\Controllers\Devices;

use App\Abstracts\Devices\AbstractDataConstructor;
use App\DevicePolicy;
use Inertia\Inertia;
use Inertia\Response;

class HeatingController extends AbstractDataConstructor
{
    public function __construct(Heating $heating)
    {
        parent::__construct($heating);

        $devicePolicy = new DevicePolicy();
        $devicePolicy->check();
    }

    public function index(): Response
    {
        return Inertia::render('Heating/Index', [
            'heatingData' => $this->dataForFrontend()
        ]);
    }
}
