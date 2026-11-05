<?php

namespace App\Http\Controllers;

use App\Models\Holiday;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class HolidayController extends Controller
{
    public function index(Request $request): View
    {
        $holidays = Holiday::orderBy('start_date')->get();

        $editHoliday = $request->filled('edit')
            ? Holiday::findOrFail($request->integer('edit'))
            : null;

        return view('holidays.index', compact('holidays', 'editHoliday'));
    }

    public function store(Request $request): RedirectResponse
    {
        Holiday::create($this->validated($request));

        return redirect()
            ->route('holidays.index')
            ->with('status', 'Hari libur berhasil ditambahkan.');
    }

    public function update(Request $request, Holiday $holiday): RedirectResponse
    {
        $holiday->update($this->validated($request));

        return redirect()
            ->route('holidays.index')
            ->with('status', 'Hari libur berhasil diperbarui.');
    }

    public function destroy(Holiday $holiday): RedirectResponse
    {
        $holiday->delete();

        return redirect()
            ->route('holidays.index')
            ->with('status', 'Hari libur berhasil dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'type' => ['required', Rule::in(array_keys(Holiday::TYPES))],
        ], [
            'name.required' => 'Nama hari libur wajib diisi.',
            'name.max' => 'Nama hari libur maksimal 100 karakter.',
            'start_date.required' => 'Tanggal wajib diisi.',
            'start_date.date' => 'Tanggal tidak valid.',
            'end_date.date' => 'Tanggal selesai tidak valid.',
            'end_date.after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
            'type.required' => 'Jenis libur wajib dipilih.',
            'type.in' => 'Jenis libur tidak valid.',
        ]);
    }
}
