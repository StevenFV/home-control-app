<?php

namespace App\Models\Devices;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lighting extends Model
{
    use HasFactory;

    protected $table = 'devices.lights';
}
