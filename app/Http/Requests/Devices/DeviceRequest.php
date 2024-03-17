<?php

namespace App\Http\Requests\Devices;

use App\DevicePolicy;
use Illuminate\Foundation\Http\FormRequest;

class DeviceRequest extends FormRequest
{
    public function __construct()
    {
        parent::__construct();

        $devicePolicy = new DevicePolicy();
        $devicePolicy->check();
    }

    public function rules(): array
    {
        $startWithCapitalLetter = 'regex:/^[A-Z]/';

        return [
            'friendlyName' => ['required', 'string', 'max:81'],
            'set' => ['required', 'string', 'size:4', 'in:/set'],
            'state' => ['required', 'string'],
            'deviceModelClassName' => ['required', 'string', $startWithCapitalLetter]
        ];
    }
}
