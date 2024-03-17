<?php

namespace App\Interfaces\Devices;

use Illuminate\Database\Eloquent\Model;

interface DeviceStoreInterface
{
    public function store(Model $model): void;

    public function data(Model $model): string | array;

    public function fetchAndProcessMqttMessages($topicFilter): string | array;
}
