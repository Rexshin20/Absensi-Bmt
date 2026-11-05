<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    public const TYPES = [
        'nasional' => 'Libur Nasional',
        'cuti_bersama' => 'Cuti Bersama',
        'kantor' => 'Libur Kantor',
    ];

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'type',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function typeLabel(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }
}
