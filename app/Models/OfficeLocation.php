<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfficeLocation extends Model
{
    protected $fillable = [
        'name',
        'address',
        'latitude',
        'longitude',
        'radius_m',
        'work_days',
        'work_start_time',
        'is_active',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'radius_m' => 'integer',
        'work_days' => 'array',
        'is_active' => 'boolean',
    ];
}
