<?php

namespace App\Http\Controllers;

use App\Exceptions\CheckInFailed;
use App\Models\Attendance;
use App\Models\Employee;
use App\Services\AttendanceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    private const DAY_NAMES = [
        1 => 'Senin',
        2 => 'Selasa',
        3 => 'Rabu',
        4 => 'Kamis',
        5 => 'Jumat',
        6 => 'Sabtu',
        7 => 'Minggu',
    ];

    public function __construct(private readonly AttendanceService $attendanceService)
    {
    }

    public function index(Request $request): View
    {
        $validated = $request->validate([
            'date' => ['nullable', 'date'],
        ], [
            'date.date' => 'Format tanggal tidak valid.',
        ]);

        $date = Carbon::parse($validated['date'] ?? now()->toDateString())->toDateString();

        $attendances = Attendance::with(['employee.position', 'officeLocation'])
            ->where('date', $date)
            ->orderBy('check_in_time')
            ->get();

        $employees = Employee::with('officeLocation')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $activeCount = $employees->count();
        $presentCount = $attendances->where('status', 'present')->count();
        $lateCount = $attendances->where('status', 'late')->count();
        $missingCount = max(0, $activeCount - $attendances->count());

        $day = Carbon::parse($date);

        return view('attendances.index', [
            'date' => $date,
            'dateLabel' => self::DAY_NAMES[$day->isoWeekday()] . ', ' . $day->format('d/m/Y'),
            'previousDate' => $day->copy()->subDay()->toDateString(),
            'nextDate' => $day->copy()->addDay()->toDateString(),
            'workStart' => AttendanceService::workStart(),
            'attendances' => $attendances,
            'employees' => $employees,
            'activeCount' => $activeCount,
            'presentCount' => $presentCount,
            'lateCount' => $lateCount,
            'missingCount' => $missingCount,
        ]);
    }

    public function checkIn(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'employee_id' => ['required', 'integer', 'exists:employees,id'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ], [
            'employee_id.required' => 'Pilih karyawan yang akan absen.',
            'employee_id.exists' => 'Karyawan tidak ditemukan.',
            'latitude.required' => 'Lokasi belum terbaca. Aktifkan izin lokasi pada browser.',
            'longitude.required' => 'Lokasi belum terbaca. Aktifkan izin lokasi pada browser.',
            'latitude.numeric' => 'Koordinat lokasi tidak valid.',
            'longitude.numeric' => 'Koordinat lokasi tidak valid.',
        ]);

        $employee = Employee::with('officeLocation')->findOrFail($validated['employee_id']);

        try {
            $attendance = $this->attendanceService->checkIn(
                $employee,
                (float) $validated['latitude'],
                (float) $validated['longitude'],
                now(),
                'web'
            );
        } catch (CheckInFailed $exception) {
            return redirect()
                ->route('attendances.index', ['date' => now()->toDateString()])
                ->with('error', $exception->getMessage());
        }

        return redirect()
            ->route('attendances.index', ['date' => $attendance->date->format('Y-m-d')])
            ->with('status', sprintf(
                '%s absen jam %s — %s (%s m dari titik kantor).',
                $attendance->employee->name,
                $attendance->check_in_time->format('H:i'),
                $attendance->statusLabel(),
                number_format($attendance->distance_m, 0, ',', '.')
            ));
    }
}
