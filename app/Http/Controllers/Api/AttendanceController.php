<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\CheckInFailed;
use App\Http\Controllers\Controller;
use App\Services\AttendanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function checkIn(Request $request, AttendanceService $service): JsonResponse
    {
        $data = $request->validate([
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ], [
            'latitude.required' => 'Latitude wajib dikirim.',
            'longitude.required' => 'Longitude wajib dikirim.',
            'latitude.numeric' => 'Latitude harus berupa angka.',
            'longitude.numeric' => 'Longitude harus berupa angka.',
            'latitude.between' => 'Latitude di luar rentang yang valid.',
            'longitude.between' => 'Longitude di luar rentang yang valid.',
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

        return response()->json([
            'message' => 'Absen berhasil dicatat.',
            'attendance' => [
                'date' => $attendance->date->format('Y-m-d'),
                'check_in_time' => $attendance->check_in_time->format('H:i'),
                'status' => $attendance->statusLabel(),
                'office' => $attendance->officeLocation?->name,
                'distance_m' => $attendance->distance_m,
            ],
            'work_start' => AttendanceService::workStart(),
        ], 201);
    }
}
