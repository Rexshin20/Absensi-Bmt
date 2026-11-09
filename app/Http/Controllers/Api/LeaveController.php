<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Support\WorkingDayCalculator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class LeaveController extends Controller
{
    public function types(): JsonResponse
    {
        return response()->json([
            'leave_types' => LeaveType::where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name', 'description']),
        ]);
    }

    public function index(Request $request): JsonResponse
    {
        $employee = $request->user()->employee;

        $leaves = LeaveRequest::with('leaveType')
            ->where('employee_id', $employee?->id)
            ->orderByDesc('start_date')
            ->orderByDesc('id')
            ->limit(60)
            ->get();

        return response()->json([
            'leaves' => $leaves->map(fn (LeaveRequest $leave) => [
                'id' => $leave->id,
                'type' => $leave->leaveType?->name,
                'start_date' => $leave->start_date->format('Y-m-d'),
                'end_date' => $leave->end_date->format('Y-m-d'),
                'days' => $leave->workingDays(),
                'note' => $leave->note,
                'status' => $leave->statusLabel(),
                'reason' => $leave->rejection_reason,
            ]),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $employee = $request->user()->employee;

        if (! $employee) {
            return response()->json([
                'message' => 'Akun ini belum terhubung ke data karyawan. Hubungi admin.',
            ], 422);
        }

        $data = $request->validate([
            'leave_type_id' => ['required', Rule::exists('leave_types', 'id')->where('is_active', true)],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'note' => ['required', 'string', 'max:1000'],
        ], [
            'leave_type_id.required' => 'Jenis izin wajib dipilih.',
            'leave_type_id.exists' => 'Jenis izin tidak valid atau sudah nonaktif.',
            'start_date.required' => 'Tanggal mulai wajib diisi.',
            'start_date.date' => 'Tanggal mulai tidak valid.',
            'end_date.required' => 'Tanggal selesai wajib diisi.',
            'end_date.date' => 'Tanggal selesai tidak valid.',
            'end_date.after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
            'note.required' => 'Keterangan wajib diisi.',
            'note.max' => 'Keterangan maksimal 1000 karakter.',
        ]);

        if (WorkingDayCalculator::count(
            Carbon::parse($data['start_date']),
            Carbon::parse($data['end_date'])
        ) === 0) {
            throw ValidationException::withMessages([
                'end_date' => 'Rentang tanggal tidak memiliki hari kerja. Periksa hari kerja dan hari libur kantor.',
            ]);
        }

        $leave = LeaveRequest::create([
            ...$data,
            'employee_id' => $employee->id,
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Pengajuan '.strtolower($leave->leaveType?->name).' dikirim, menunggu verifikasi admin.',
            'leave' => [
                'id' => $leave->id,
                'type' => $leave->leaveType?->name,
                'start_date' => $leave->start_date->format('Y-m-d'),
                'end_date' => $leave->end_date->format('Y-m-d'),
                'status' => $leave->statusLabel(),
            ],
        ], 201);
    }
}
