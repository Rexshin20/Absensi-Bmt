<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\CheckInFailed;
use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Services\AttendanceService;
use App\Support\WorkingDayCalculator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class AttendanceController extends Controller
{
    private const MAX_EDGE = 900;

    public function checkIn(Request $request, AttendanceService $service): JsonResponse
    {
        $data = $request->validate([
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'photo' => ['required', 'file', 'image', 'mimes:jpg,jpeg,png', 'max:6144'],
        ], [
            'latitude.required' => 'Latitude wajib dikirim.',
            'longitude.required' => 'Longitude wajib dikirim.',
            'latitude.numeric' => 'Latitude harus berupa angka.',
            'longitude.numeric' => 'Longitude harus berupa angka.',
            'latitude.between' => 'Latitude di luar rentang yang valid.',
            'longitude.between' => 'Longitude di luar rentang yang valid.',
            'photo.required' => 'Foto selfie wajib diambil sebelum absen.',
            'photo.image' => 'Lampiran harus berupa foto.',
            'photo.mimes' => 'Format foto harus JPG atau PNG.',
            'photo.max' => 'Ukuran foto maksimal 6 MB.',
        ]);

        $employee = $request->user()->employee;

        if (! $employee) {
            return response()->json([
                'message' => 'Akun ini belum terhubung ke data karyawan. Hubungi admin.',
            ], 422);
        }

        try {
            $attendance = $service->checkIn(
                $employee,
                (float) $data['latitude'],
                (float) $data['longitude'],
                now(),
                'android'
            );
        } catch (CheckInFailed $exception) {
            return response()->json(['message' => $exception->getMessage()], 422);
        }

        $photoPath = $this->storeSelfie($attendance, $request->file('photo'));

        if ($photoPath) {
            $attendance->update(['photo_path' => $photoPath]);
        }

        return response()->json([
            'message' => 'Absen berhasil dicatat.',
            'attendance' => $this->attendancePayload($attendance, $request),
            'work_start' => AttendanceService::workStart(),
        ], 201);
    }

    public function index(Request $request): JsonResponse
    {
        $employee = $request->user()->employee;

        if (! $employee) {
            return response()->json([
                'message' => 'Akun ini belum terhubung ke data karyawan. Hubungi admin.',
            ], 422);
        }

        $month = preg_match('/^\d{4}-\d{2}$/', (string) $request->query('month'))
            ? $request->query('month')
            : now()->format('Y-m');

        $start = Carbon::createFromFormat('Y-m-d', $month.'-01')->startOfMonth();
        $end = (clone $start)->copy()->endOfMonth();

        $attendances = Attendance::with('officeLocation')
            ->where('employee_id', $employee->id)
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->orderByDesc('date')
            ->get();

        $leaveDays = LeaveRequest::where('employee_id', $employee->id)
            ->where('status', 'approved')
            ->where('start_date', '<=', $end)
            ->where('end_date', '>=', $start)
            ->get()
            ->sum(fn (LeaveRequest $leave) => WorkingDayCalculator::count(
                $leave->start_date->max($start),
                $leave->end_date->min($end)
            ));

        return response()->json([
            'month' => $month,
            'summary' => [
                'work_days' => WorkingDayCalculator::count($start, $end),
                'present' => $attendances->where('status', 'present')->count(),
                'late' => $attendances->where('status', 'late')->count(),
                'leave' => (int) $leaveDays,
            ],
            'records' => $attendances->map(fn (Attendance $attendance) => $this->attendancePayload($attendance, $request)),
        ]);
    }

    private function attendancePayload(Attendance $attendance, Request $request): array
    {
        return [
            'id' => $attendance->id,
            'date' => $attendance->date->format('Y-m-d'),
            'check_in_time' => $attendance->check_in_time->format('H:i'),
            'status' => $attendance->statusLabel(),
            'office' => $attendance->officeLocation?->name,
            'distance_m' => $attendance->distance_m,
            'source' => $attendance->sourceLabel(),
            'photo_url' => $attendance->photo_path
                ? $request->getSchemeAndHttpHost().'/storage/'.$attendance->photo_path
                : null,
        ];
    }

    private function storeSelfie(Attendance $attendance, object $file): ?string
    {
        $path = sprintf(
            'absensi/%d/%s.jpg',
            $attendance->employee_id,
            $attendance->date->format('Y-m-d')
        );

        $bytes = file_get_contents($file->getRealPath());

        if (extension_loaded('gd')) {
            $resized = $this->resizeToJpeg($bytes);
            if ($resized !== null) {
                $bytes = $resized;
            }
        }

        return Storage::disk('public')->put($path, $bytes) ? $path : null;
    }

    private function resizeToJpeg(string $bytes): ?string
    {
        $image = @imagecreatefromstring($bytes);

        if (! $image) {
            return null;
        }

        $width = imagesx($image);
        $height = imagesy($image);
        $scale = min(1, self::MAX_EDGE / max($width, $height));

        $canvas = imagescale($image, (int) round($width * $scale), (int) round($height * $scale));

        ob_start();
        imagejpeg($canvas ?: $image, null, 72);
        $out = ob_get_clean();

        imagedestroy($image);
        if ($canvas) {
            imagedestroy($canvas);
        }

        return $out === false || $out === '' ? null : $out;
    }
}
