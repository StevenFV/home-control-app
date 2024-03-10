<?php

namespace App\Abstracts\Devices;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

abstract class AbstractDataConstructor
{
    private Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    protected function dataForFrontend(): Collection
    {
        return $this->model->all()->map(function ($model) {
            $deviceData = array_slice($this->model->getFillable(), 2);

            $data = [
                'friendlyName' => $model->friendly_name
            ];

            foreach ($deviceData as $name) {
                $data['data'][$name] = $model->$name;
            }

            return $data;
        });
    }
}
