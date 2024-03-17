<?php

namespace App\Traits\Devices;

trait DeviceModelNamespaceResolver
{
    public function getDeviceModelClassNameWithNameSpace($deviceModelClassName)
    {
        $namespace = 'App\Models\Devices\\';

        if (!class_exists($namespace . $deviceModelClassName)) {
            $this->error('Invalid model class name provided.');
            exit();
        }

        return app($namespace . $deviceModelClassName);
    }
}
