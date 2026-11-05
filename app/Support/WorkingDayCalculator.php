<?php

namespace App\Support;

use App\Models\Holiday;
use App\Models\Setting;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;

class WorkingDayCalculator
{
    public static function count(CarbonInterface $start, CarbonInterface $end): int
    {
        $cursor = Carbon::parse($start->toDateString());
        $last = Carbon::parse($end->toDateString());

        if ($last->lt($cursor)) {
            return 0;
        }

        $workDays = array_map(
            'intval',
            Setting::get('work_days', [1, 2, 3, 4, 5, 6])
        );

        $holidays = self::holidayDates($cursor, $last);
        $count = 0;

        while ($cursor->lte($last)) {
            $date = $cursor->toDateString();

            if (in_array($cursor->isoWeekday(), $workDays, true) && ! isset($holidays[$date])) {
                $count++;
            }

            $cursor->addDay();
        }

        return $count;
    }

    /**
     * @return array<string, true>
     */
    private static function holidayDates(CarbonInterface $start, CarbonInterface $end): array
    {
        $dates = [];

        $holidays = Holiday::query()
            ->where('start_date', '<=', $end->toDateString())
            ->where(function ($query) use ($start) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>=', $start->toDateString());
            })
            ->get();

        $rangeStart = Carbon::parse($start->toDateString());
        $rangeEnd = Carbon::parse($end->toDateString());

        foreach ($holidays as $holiday) {
            $from = $holiday->start_date->copy();
            $to = ($holiday->end_date ?? $holiday->start_date)->copy();

            if ($from->lt($rangeStart)) {
                $from = $rangeStart->copy();
            }

            if ($to->gt($rangeEnd)) {
                $to = $rangeEnd->copy();
            }

            while ($from->lte($to)) {
                $dates[$from->toDateString()] = true;
                $from->addDay();
            }
        }

        return $dates;
    }
}
