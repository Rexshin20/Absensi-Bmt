<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    public const STATUSES = [
        'present' => 'Hadir',
        'late' => 'Terlambat',
    ];

    public const SOURCES = [
        'web' => 'Web',
        'android' => 'Android',
    ];

    protected $fillable = [
        'employee_id',
        'office_location_id',
        'date',
        'check_in_time',
        'status',
        'latitude',
        'longitude',
        'distance_m',
        'source',
    ];

    protected $casts = [
        'date' => 'date',
        'check_in_time' => 'datetime:H:i',
        'latitude' => 'float',
        'longitude' => 'float',
        'distance_m' => 'integer',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function officeLocation(): BelongsTo
    {
        return $this->belongsTo(OfficeLocation::class);
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function sourceLabel(): string
    {
        return self::SOURCES[$this->source] ?? $this->source;
    }
}
