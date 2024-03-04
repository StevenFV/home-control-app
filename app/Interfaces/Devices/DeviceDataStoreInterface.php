<?php

namespace App\Interfaces\Devices;

use Illuminate\Database\Eloquent\Model;

interface DeviceDataStoreInterface
{
    public function store(Model $model): void;

    public function data(Model $model): string|array;

    public function communicate($topicFilter): string|array;
}
