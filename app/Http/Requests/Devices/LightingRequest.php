<?php

namespace App\Http\Requests\Devices;

use Illuminate\Foundation\Http\FormRequest;

class LightingRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'friendlyName' => ['required', 'string', 'max:81'],
            'set' => ['required', 'string', 'size:4', 'in:/set'],
            'toggle' => ['required', 'string', 'size:6', 'in:TOGGLE'],
        ];
    }
}
