<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\OfficeLocation;
use App\Services\AttendanceService;
use App\Support\WorkingDayCalculator;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;
use OpenSpout\Common\Entity\Cell;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Border;
use OpenSpout\Common\Entity\Style\BorderPart;
use OpenSpout\Common\Entity\Style\CellAlignment;
use OpenSpout\Common\Entity\Style\CellVerticalAlignment;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\XLSX\Options;
use OpenSpout\Writer\XLSX\Options\HeaderFooter;
use OpenSpout\Writer\XLSX\Options\PageMargin;
use OpenSpout\Writer\XLSX\Options\PageOrientation;
use OpenSpout\Writer\XLSX\Options\PageSetup;
use OpenSpout\Writer\XLSX\Entity\SheetView;
use OpenSpout\Writer\XLSX\Options\PaperSize;
use OpenSpout\Writer\XLSX\Writer;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AttendanceReportController extends Controller
{
    private const MONTHS = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
    ];

    public function index(Request $request): View
    {
        return view('attendances.recap', $this->build($request) + [
            'officeLocations' => OfficeLocation::orderBy('name')->get(),
        ]);
    }

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

    public function export(Request $request, string $format)
    {
        abort_unless(in_array($format, ['csv', 'xlsx', 'pdf'], true), 404);

        $report = $this->build($request);

        return match ($format) {
            'csv' => $this->csv($report),
            'xlsx' => $this->xlsx($report),
            'pdf' => $this->pdf($report),
        };
    }

    private function build(Request $request): array
    {
        $validated = $request->validate([
            'month' => ['nullable', 'regex:/^\d{4}-\d{2}$/'],
            'location_id' => ['nullable', 'integer', 'exists:office_locations,id'],
        ], [
            'month.regex' => 'Format bulan tidak valid.',
            'location_id.exists' => 'Lokasi kantor tidak ditemukan.',
        ]);

        $month = $validated['month'] ?? now()->format('Y-m');
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

        $locationId = $validated['location_id'] ?? null;
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

        $groups = $locationId
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

        $periodLabel = self::MONTHS[(int) $start->month].' '.$start->year;

        return [
            'month' => $month,
            'periodLabel' => $periodLabel,
            'officeLabel' => $office?->name ?? 'Semua lokasi kantor',
            'locationId' => $locationId,
            'cutoffLabel' => $cutoffLabel,
            'groups' => $groupedRows,
            'generatedAt' => now()->format('d/m/Y H:i'),
        ];
    }

    private function groupByOffice($employees): array
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

    private function csv(array $report): StreamedResponse
    {
        $filename = 'rekap-absensi-'.$report['month'].'.csv';

        return response()->streamDownload(function () use ($report) {
            $out = fopen('php://output', 'w');

            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['REKAP ABSENSI BULANAN'], ';');
            fputcsv($out, [
                'Periode', $report['periodLabel'].($report['cutoffLabel'] ? ' ('.$report['cutoffLabel'].')' : ''),
            ], ';');

            foreach ($report['groups'] as $group) {
                fwrite($out, "\r\n");
                fputcsv($out, [$group['label']], ';');
                fputcsv($out, ['No', 'Nama Santri', 'Masuk Tepat Waktu', 'Masuk Terlambat', 'Tidak Masuk', 'Keterangan'], ';');

                foreach ($group['rows'] as $row) {
                    fputcsv($out, [
                        $row['no'], $row['name'], $row['present'], $row['late'], $row['absent'], $row['note'],
                    ], ';');
                }
            }

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function xlsx(array $report)
    {
        $filename = 'rekap-absensi-'.$report['month'].'.xlsx';

        $fontName = 'Times New Roman';

        $options = new Options();
        $options->DEFAULT_ROW_STYLE->setFontName($fontName)->setFontSize(11);
        $options->setColumnWidth(12, 1);
        $options->setColumnWidth(30, 2);
        $options->setColumnWidth(17, 3);
        $options->setColumnWidth(17, 4);
        $options->setColumnWidth(13, 5);
        $options->setColumnWidth(38, 6);
        $options->setPageSetup(new PageSetup(PageOrientation::PORTRAIT, PaperSize::A4, 0, 1));
        $options->setPageMargin(new PageMargin());
        // OpenSpout tidak meng-escape header/footer, jadi &P dan &N harus ditulis sebagai entitas XML.
        $options->setHeaderFooter(new HeaderFooter(null, 'Halaman &amp;P dari &amp;N'));

        $writer = new Writer($options);
        $writer->openToBrowser($filename);
        $writer->getCurrentSheet()
            ->setName('Rekap Absensi')
            ->setSheetView((new SheetView())->setShowGridLines(false));

        $border = new Border(
            new BorderPart(Border::LEFT, 'FF000000', Border::WIDTH_THIN),
            new BorderPart(Border::RIGHT, 'FF000000', Border::WIDTH_THIN),
            new BorderPart(Border::TOP, 'FF000000', Border::WIDTH_THIN),
            new BorderPart(Border::BOTTOM, 'FF000000', Border::WIDTH_THIN),
        );

        $titleStyle = (new Style())
            ->setFontName($fontName)
            ->setFontBold()
            ->setFontSize(14)
            ->setCellAlignment(CellAlignment::CENTER)
            ->setCellVerticalAlignment(CellVerticalAlignment::CENTER)
            ->setBorder($border);
        $labelStyle = (new Style())
            ->setFontName($fontName)
            ->setFontBold()
            ->setCellVerticalAlignment(CellVerticalAlignment::CENTER)
            ->setBorder($border);
        $valueStyle = (new Style())
            ->setFontName($fontName)
            ->setCellVerticalAlignment(CellVerticalAlignment::CENTER)
            ->setBorder($border);
        $headerStyle = (new Style())
            ->setFontName($fontName)
            ->setFontBold()
            ->setBackgroundColor('FF7AC143')
            ->setCellAlignment(CellAlignment::CENTER)
            ->setCellVerticalAlignment(CellVerticalAlignment::CENTER)
            ->setShouldWrapText()
            ->setBorder($border);
        $nameStyle = (new Style())
            ->setFontName($fontName)
            ->setFontBold()
            ->setCellVerticalAlignment(CellVerticalAlignment::CENTER)
            ->setBorder($border);
        $centerStyle = (new Style())
            ->setFontName($fontName)
            ->setCellAlignment(CellAlignment::CENTER)
            ->setCellVerticalAlignment(CellVerticalAlignment::CENTER)
            ->setBorder($border);
        $noteStyle = (new Style())
            ->setFontName($fontName)
            ->setCellVerticalAlignment(CellVerticalAlignment::CENTER)
            ->setShouldWrapText()
            ->setBorder($border);
        $officeStyle = (new Style())
            ->setFontName($fontName)
            ->setFontBold()
            ->setFontSize(12)
            ->setCellAlignment(CellAlignment::CENTER)
            ->setCellVerticalAlignment(CellVerticalAlignment::CENTER);
        $footerStyle = (new Style())
            ->setFontName($fontName)
            ->setFontSize(9)
            ->setFontColor('6B7280')
            ->setCellAlignment(CellAlignment::RIGHT)
            ->setCellVerticalAlignment(CellVerticalAlignment::CENTER)
            ->setBorder($border);

        $row = 1;

        $options->mergeCells(0, $row, 5, $row);
        $writer->addRow((new Row([
            Cell::fromValue('REKAP ABSENSI BULANAN', $titleStyle),
            Cell::fromValue(null, $titleStyle),
            Cell::fromValue(null, $titleStyle),
            Cell::fromValue(null, $titleStyle),
            Cell::fromValue(null, $titleStyle),
            Cell::fromValue(null, $titleStyle),
        ]))->setHeight(28));
        $row++;

        $periodText = $report['periodLabel'].($report['cutoffLabel'] ? ' ('.$report['cutoffLabel'].')' : '');

        $options->mergeCells(1, $row, 5, $row);
        $writer->addRow((new Row([
            Cell::fromValue('Periode', $labelStyle),
            Cell::fromValue($periodText, $valueStyle),
            Cell::fromValue(null, $valueStyle),
            Cell::fromValue(null, $valueStyle),
            Cell::fromValue(null, $valueStyle),
            Cell::fromValue(null, $valueStyle),
        ]))->setHeight(18));
        $row++;

        $writer->addRow((new Row([Cell::fromValue(null)]))->setHeight(8));
        $row++;

        foreach ($report['groups'] as $group) {
            $options->mergeCells(0, $row, 5, $row);
            $writer->addRow((new Row([
                Cell::fromValue($group['label'], $officeStyle),
                Cell::fromValue(null, $officeStyle),
                Cell::fromValue(null, $officeStyle),
                Cell::fromValue(null, $officeStyle),
                Cell::fromValue(null, $officeStyle),
                Cell::fromValue(null, $officeStyle),
            ]))->setHeight(22));
            $row++;

            $options->mergeCells(0, $row, 0, $row + 1);
            $options->mergeCells(1, $row, 1, $row + 1);
            $options->mergeCells(2, $row, 3, $row);
            $options->mergeCells(4, $row, 4, $row + 1);
            $options->mergeCells(5, $row, 5, $row + 1);

            $writer->addRow((new Row([
                Cell::fromValue('No', $headerStyle),
                Cell::fromValue('Nama Santri', $headerStyle),
                Cell::fromValue('Masuk', $headerStyle),
                Cell::fromValue(null, $headerStyle),
                Cell::fromValue('Tidak Masuk', $headerStyle),
                Cell::fromValue('Keterangan', $headerStyle),
            ]))->setHeight(20));

            $writer->addRow((new Row([
                Cell::fromValue(null, $headerStyle),
                Cell::fromValue(null, $headerStyle),
                Cell::fromValue('Tepat Waktu', $headerStyle),
                Cell::fromValue('Terlambat', $headerStyle),
                Cell::fromValue(null, $headerStyle),
                Cell::fromValue(null, $headerStyle),
            ]))->setHeight(22));
            $row += 2;

            foreach ($group['rows'] as $data) {
                $writer->addRow((new Row([
                    Cell::fromValue($data['no'], $centerStyle),
                    Cell::fromValue($data['name'], $nameStyle),
                    Cell::fromValue($data['present'], $centerStyle),
                    Cell::fromValue($data['late'], $centerStyle),
                    Cell::fromValue($data['absent'], $centerStyle),
                    Cell::fromValue($data['note'], $noteStyle),
                ]))->setHeight(20));
                $row++;
            }

            $writer->addRow((new Row([Cell::fromValue(null)]))->setHeight(8));
            $row++;
        }

        $options->mergeCells(0, $row, 5, $row);
        $writer->addRow(new Row([
            Cell::fromValue('Dicetak '.$report['generatedAt'].' WIB · BMT Cinde Barokah', $footerStyle),
            Cell::fromValue(null, $footerStyle),
            Cell::fromValue(null, $footerStyle),
            Cell::fromValue(null, $footerStyle),
            Cell::fromValue(null, $footerStyle),
            Cell::fromValue(null, $footerStyle),
        ]));

        $writer->close();
    }

    private function pdf(array $report)
    {
        $pdf = Pdf::loadView('attendances.recap-pdf', $report)
            ->setPaper('a4', 'portrait');

        return $pdf->download('rekap-absensi-'.$report['month'].'.pdf');
    }
}
