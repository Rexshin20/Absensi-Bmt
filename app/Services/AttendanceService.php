<?php

namespace App\Services;

use App\Exceptions\CheckInFailed;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Holiday;
use App\Models\Setting;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;

class AttendanceService
{
    public const DEFAULT_WORK_START = '08:00';

    private const DAY_NAMES = [
        1 => 'Senin',
        2 => 'Selasa',
        3 => 'Rabu',
        4 => 'Kamis',
        5 => 'Jumat',
        6 => 'Sabtu',
        7 => 'Minggu',
    ];

    public static function workStart(): string
    {
        return Setting::get('work_start_time', self::DEFAULT_WORK_START);
    }

    public static function workDays(): array
    {
        return collect(Setting::get('work_days', [1, 2, 3, 4, 5, 6]))
            ->map(fn ($day) => (int) $day)
            ->all();
    }

    public function checkIn(
        Employee $employee,
        float $latitude,
        float $longitude,
        ?CarbonInterface $at = null,
        string $source = 'web'
    ): Attendance {
        $at = Carbon::parse($at ?? now());
        $date = $at->toDateString();

        if (! $employee->is_active) {
            throw new CheckInFailed('Karyawan ini sudah tidak aktif.');
        }

        if (! in_array((int) $at->isoWeekday(), self::workDays(), true)) {
            throw new CheckInFailed(self::DAY_NAMES[(int) $at->isoWeekday()] . ' adalah hari libur kantor.');
        }

        $holiday = Holiday::where('start_date', '<=', $date)
            ->where(function ($query) use ($date) {
                $query->whereNull('end_date')->orWhere('end_date', '>=', $date);
            })
            ->first();

        if ($holiday) {
            throw new CheckInFailed($holiday->typeLabel() . ': ' . $holiday->name);
        }

        if (Attendance::where('employee_id', $employee->id)->where('date', $date)->exists()) {
            throw new CheckInFailed($employee->name . ' sudah absen hari ini.');
        }

        $location = $employee->officeLocation;

        if (! $location || $location->latitude === null || $location->longitude === null) {
            throw new CheckInFailed('Lokasi kantor ' . $employee->name . ' belum diatur. Hubungi admin.');
        }

        $distance = self::distanceInMeters(
            $latitude,
            $longitude,
            (float) $location->latitude,
            (float) $location->longitude
        );

        if ($distance > (int) $location->radius_m) {
            throw new CheckInFailed(sprintf(
                'Absen ditolak: %s m dari titik kantor, maksimal %s m.',
                number_format($distance, 0, ',', '.'),
                number_format((int) $location->radius_m, 0, ',', '.')
            ));
        }

        $workStart = self::workStart();

        return Attendance::create([
            'employee_id' => $employee->id,
            'office_location_id' => $location->id,
            'date' => $date,
            'check_in_time' => $at->format('H:i:s'),
            'status' => $at->format('H:i') > $workStart ? 'late' : 'present',
            'latitude' => $latitude,
            'longitude' => $longitude,
            'distance_m' => (int) round($distance),
            'source' => $source,
        ]);
    }

    public static function distanceInMeters(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;

        return 2 * 6371000 * asin(min(1, sqrt($a)));
    }
}
