<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class PageController extends Controller
{
    public function dashboard(): View
    {
        $today = now()->toDateString();
        $activeEmployees = Employee::where('is_active', true)->count();
        $todayAttendances = Attendance::query()
            ->whereDate('date', $today)
            ->whereHas('employee', fn ($query) => $query->where('is_active', true));

        $presentCount = (clone $todayAttendances)->where('status', 'present')->count();
        $lateCount = (clone $todayAttendances)->where('status', 'late')->count();
        $checkedInCount = $presentCount + $lateCount;
        $missingCount = max(0, $activeEmployees - $checkedInCount);
        $latestAttendances = (clone $todayAttendances)
            ->with(['employee.position', 'officeLocation'])
            ->orderByDesc('check_in_time')
            ->limit(6)
            ->get();

        $weekStart = Carbon::parse($today)->subDays(6);
        $weeklyAttendanceByDate = Attendance::query()
            ->whereBetween('date', [$weekStart->toDateString(), $today])
            ->whereHas('employee', fn ($query) => $query->where('is_active', true))
            ->selectRaw('date, COUNT(*) as total')
            ->groupBy('date')
            ->pluck('total', 'date');

        $weekdayLabels = [1 => 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
        $weeklyLabels = [];
        $weeklyCounts = [];

        for ($day = $weekStart->copy(); $day->lte(Carbon::parse($today)); $day->addDay()) {
            $weeklyLabels[] = $weekdayLabels[$day->isoWeekday()];
            $weeklyCounts[] = (int) ($weeklyAttendanceByDate[$day->toDateString()] ?? 0);
        }

        return view('dashboard.index', [
            'today' => Carbon::parse($today),
            'activeEmployees' => $activeEmployees,
            'presentCount' => $presentCount,
            'lateCount' => $lateCount,
            'checkedInCount' => $checkedInCount,
            'missingCount' => $missingCount,
            'latestAttendances' => $latestAttendances,
            'weeklyLabels' => $weeklyLabels,
            'weeklyCounts' => $weeklyCounts,
            'weeklyTotal' => array_sum($weeklyCounts),
            'attendanceRate' => $activeEmployees > 0
                ? (int) round($checkedInCount / $activeEmployees * 100)
                : 0,
        ]);
    }

    public function accountSettings(): View
    {
        return view('pages.account-settings');
    }

    public function errorPage(): View
    {
        return view('pages.errors.404')->with('layout', 'blank');
    }
}
