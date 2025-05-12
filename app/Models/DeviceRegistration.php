<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceRegistration extends Model
{
    protected $fillable = [
        'station_id',
        'station_name',
        'device_serial_number',
        'device_model',
        'api_token'
    ];
}
