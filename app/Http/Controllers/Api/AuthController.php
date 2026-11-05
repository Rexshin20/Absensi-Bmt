<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use App\Services\AttendanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'message' => 'Email atau password salah.',
            ], 422);
        }

        $user->load('employee.officeLocation', 'employee.position', 'employee.division');

        if (! $user->employee) {
            return response()->json([
                'message' => 'Akun ini belum terhubung ke data karyawan. Hubungi admin.',
            ], 422);
        }

        if (! $user->employee->is_active) {
            return response()->json([
                'message' => 'Data karyawan ' . $user->employee->name . ' sudah tidak aktif.',
            ], 422);
        }

        $token = $user->createToken('android')->plainTextToken;

        return response()->json([
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => $this->userPayload($user),
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user()->load('employee.officeLocation', 'employee.position', 'employee.division');
        $employee = $user->employee;

        $today = Attendance::where('employee_id', $employee?->id)
            ->where('date', now()->toDateString())
            ->first();

        return response()->json([
            'user' => $this->userPayload($user),
            'work_start' => AttendanceService::workStart(),
            'work_days' => AttendanceService::workDays(),
            'today' => $today ? [
                'date' => $today->date->format('Y-m-d'),
                'check_in_time' => $today->check_in_time->format('H:i'),
                'status' => $today->statusLabel(),
                'office' => $today->officeLocation?->name,
                'distance_m' => $today->distance_m,
            ] : null,
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logout berhasil.']);
    }

    private function userPayload($user): array
    {
        $employee = $user->employee;

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'employee' => $employee ? [
                'id' => $employee->id,
                'name' => $employee->name,
                'position' => $employee->position?->name,
                'division' => $employee->division?->name,
                'office' => $employee->officeLocation ? [
                    'id' => $employee->officeLocation->id,
                    'name' => $employee->officeLocation->name,
                    'latitude' => (float) $employee->officeLocation->latitude,
                    'longitude' => (float) $employee->officeLocation->longitude,
                    'radius_m' => (int) $employee->officeLocation->radius_m,
                ] : null,
            ] : null,
        ];
    }
}
