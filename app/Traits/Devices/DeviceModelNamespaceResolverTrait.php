<?php

namespace App\Traits\Devices;

use Illuminate\Database\Eloquent\Model;

trait DeviceModelNamespaceResolverTrait
{
    public function getDeviceModelClassNameWithNameSpace($deviceModelClassName): Model
    {
        $namespace = 'App\Models\Devices\\';

        if (!class_exists($namespace . $deviceModelClassName)) {
            abort(404, 'Model class not found.');
        }

        return app($namespace . $deviceModelClassName);
    }
}
