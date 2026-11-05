<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Services\AttendanceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WorkSettingController extends Controller
{
    private const DAYS = [
        1 => 'Senin',
        2 => 'Selasa',
        3 => 'Rabu',
        4 => 'Kamis',
        5 => 'Jumat',
        6 => 'Sabtu',
        7 => 'Minggu',
    ];

    public function edit(): View
    {
        $workDays = collect(Setting::get('work_days', [1, 2, 3, 4, 5, 6]))
            ->map(fn ($day) => (int) $day)
            ->all();

        $workStart = Setting::get('work_start_time', AttendanceService::DEFAULT_WORK_START);

        $days = self::DAYS;

        return view('settings.work-days', compact('workDays', 'workStart', 'days'));
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'work_days' => ['required', 'array', 'min:1'],
            'work_days.*' => ['integer', 'between:1,7'],
            'work_start_time' => ['required', 'date_format:H:i'],
        ], [
            'work_days.required' => 'Pilih minimal satu hari kerja.',
            'work_days.array' => 'Format hari kerja tidak valid.',
            'work_days.min' => 'Pilih minimal satu hari kerja.',
            'work_days.*.integer' => 'Hari kerja tidak valid.',
            'work_days.*.between' => 'Hari kerja tidak valid.',
            'work_start_time.required' => 'Jam masuk wajib diisi.',
            'work_start_time.date_format' => 'Format jam masuk tidak valid.',
        ]);

        $days = collect($data['work_days'])
            ->map(fn ($day) => (int) $day)
            ->unique()
            ->sort()
            ->values()
            ->all();

        Setting::put('work_days', $days);
        Setting::put('work_start_time', $data['work_start_time']);

        return redirect()
            ->route('work-settings.edit')
            ->with('status', 'Pengaturan jam kerja berhasil disimpan.');
    }
}
