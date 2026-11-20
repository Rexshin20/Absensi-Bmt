<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\OfficeLocation;
use App\Support\WorkingDayCalculator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class AttendanceRecapService
{
    private const MONTHS = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
    ];

    public static function periodOptions(): array
    {
        $dates = array_filter([
            Attendance::min('date'),
            LeaveRequest::min('start_date'),
        ]);

        $first = $dates === [] ? null : Carbon::parse(min($dates));
        $firstYear = $first?->year ?? now()->year;
        $firstMonth = $first?->month ?? 1;
        $today = now();

        $years = range($firstYear, max($firstYear, $today->year));
        $months = [];

        foreach ($years as $year) {
            $from = $year === $firstYear ? $firstMonth : 1;
            $to = $year === $today->year ? max($from, $today->month) : 12;

            $months[$year] = range($from, $to);
        }

        return [
            'recapYears' => $years,
            'recapMonths' => $months,
            'recapMonthNames' => array_values(self::MONTHS),
        ];
    }

    public function build(?string $month, ?int $locationId): array
    {
        $month = $month ?: now()->format('Y-m');
        $start = Carbon::createFromFormat('Y-m-d', $month.'-01')->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $today = Carbon::today();
        $cutoffLabel = null;

        if ($start->gt($today)) {
            $end = $start->copy()->subDay();
            $cutoffLabel = 'belum berjalan';
        } elseif ($end->gt($today)) {
            $end = $today->copy();
            $cutoffLabel = 's.d. '.$today->format('d/m/Y');
        }

        $office = $locationId ? OfficeLocation::find($locationId) : null;

        $employees = Employee::query()
            ->where('is_active', true)
            ->when($locationId, fn ($query) => $query->where('office_location_id', $locationId))
            ->orderBy('name')
            ->get();

        $employeeIds = $employees->pluck('id');

        $attendances = Attendance::query()
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->whereIn('employee_id', $employeeIds)
            ->get()
            ->groupBy('employee_id');

        $leaves = LeaveRequest::with('leaveType')
            ->where('status', 'approved')
            ->whereIn('employee_id', $employeeIds)
            ->where('start_date', '<=', $end->toDateString())
            ->where('end_date', '>=', $start->toDateString())
            ->get()
            ->groupBy('employee_id');

        $groups = $office
            ? [['label' => $office->name, 'office' => $office, 'members' => $employees]]
            : $this->groupByOffice($employees);

        $groupedRows = [];

        foreach ($groups as $group) {
            $groupWorkDays = AttendanceService::workDays($group['office']);
            $rows = [];

            foreach ($group['members']->values() as $index => $employee) {
                $from = $start->copy();
                if ($employee->join_date && $employee->join_date->gt($from)) {
                    $from = $employee->join_date->copy();
                }

                $workDays = $from->gt($end) ? 0 : WorkingDayCalculator::count($from, $end, $groupWorkDays);

                $records = $attendances->get($employee->id, collect());
                $present = $records->where('status', 'present')->count();
                $late = $records->where('status', 'late')->count();
                $absent = max(0, $workDays - $present - $late);

                $leaveDays = 0;
                $leaveDetails = [];

                foreach ($leaves->get($employee->id, collect()) as $leave) {
                    $days = WorkingDayCalculator::count(
                        $leave->start_date->max($from),
                        $leave->end_date->min($end),
                        $groupWorkDays
                    );

                    if ($days > 0) {
                        $name = $leave->leaveType?->name ?? 'Izin';
                        $leaveDetails[$name] = ($leaveDetails[$name] ?? 0) + $days;
                        $leaveDays += $days;
                    }
                }

                $notes = [];
                foreach ($leaveDetails as $name => $days) {
                    $notes[] = $name.' '.$days.' hari';
                }

                $unexplained = max(0, $absent - $leaveDays);
                if ($unexplained > 0) {
                    $notes[] = 'tanpa keterangan '.$unexplained.' hari';
                }

                if ($absent === 0) {
                    $note = '—';
                } else {
                    $note = $notes === [] ? 'tanpa keterangan '.$absent.' hari' : implode(', ', $notes);
                }

                $rows[] = [
                    'no' => $index + 1,
                    'name' => $employee->name,
                    'present' => $present,
                    'late' => $late,
                    'absent' => $absent,
                    'note' => $note,
                ];
            }

            $groupedRows[] = [
                'label' => $group['label'],
                'rows' => $rows,
            ];
        }

        return [
            'month' => $month,
            'periodLabel' => self::MONTHS[(int) $start->month].' '.$start->year,
            'officeLabel' => $office?->name ?? 'Semua lokasi kantor',
            'locationId' => $locationId,
            'cutoffLabel' => $cutoffLabel,
            'groups' => $groupedRows,
            'generatedAt' => now()->format('d/m/Y H:i'),
        ];
    }

    private function groupByOffice(Collection $employees): array
    {
        $groups = [];

        foreach (OfficeLocation::orderBy('id')->get() as $office) {
            $members = $employees->filter(
                fn ($employee) => (string) $employee->office_location_id === (string) $office->id
            );

            if ($members->isNotEmpty()) {
                $groups[] = ['label' => $office->name, 'office' => $office, 'members' => $members];
            }
        }

        $withoutOffice = $employees->filter(fn ($employee) => $employee->office_location_id === null);

        if ($withoutOffice->isNotEmpty()) {
            $groups[] = ['label' => 'Tanpa lokasi kantor', 'office' => null, 'members' => $withoutOffice];
        }

        return $groups;
    }
}
