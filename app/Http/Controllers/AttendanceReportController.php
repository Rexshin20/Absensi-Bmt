<?php

namespace App\Http\Controllers;

use App\Models\OfficeLocation;
use App\Services\AttendanceRecapService;
use App\Support\RecapExporter;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AttendanceReportController extends Controller
{
    public function __construct(
        private readonly AttendanceRecapService $recap,
    ) {
    }

    public function index(Request $request): View
    {
        [$month, $locationId] = $this->filters($request);

        return view('attendances.recap', $this->recap->build($month, $locationId) + [
            'officeLocations' => OfficeLocation::orderBy('name')->get(),
        ]);
    }

    public function export(Request $request, string $format, RecapExporter $exporter)
    {
        abort_unless(in_array($format, ['csv', 'xlsx', 'pdf'], true), 404);

        [$month, $locationId] = $this->filters($request);
        $report = $this->recap->build($month, $locationId);

        return match ($format) {
            'csv' => $exporter->csv($report),
            'xlsx' => $exporter->xlsx($report),
            'pdf' => $exporter->pdf($report),
        };
    }

    private function filters(Request $request): array
    {
        $validated = $request->validate([
            'month' => ['nullable', 'regex:/^\d{4}-\d{2}$/'],
            'location_id' => ['nullable', 'integer', 'exists:office_locations,id'],
        ], [
            'month.regex' => 'Format bulan tidak valid.',
            'location_id.exists' => 'Lokasi kantor tidak ditemukan.',
        ]);

        return [
            $validated['month'] ?? null,
            isset($validated['location_id']) ? (int) $validated['location_id'] : null,
        ];
    }
}
