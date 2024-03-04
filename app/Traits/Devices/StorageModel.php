<?php

namespace App\Traits\Devices;

trait StorageModel
{
    public function argumentModel($stringModel)
    {
        $namespace = 'App\Models\Devices\\';

        if (!class_exists($namespace . $stringModel)) {
            $this->error('DeviceStateUpdater: Invalid model class provided.');
            exit();
        }

        return app($namespace . $stringModel);
    }
}
