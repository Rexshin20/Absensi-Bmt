<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Support\WorkingDayCalculator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LeaveRequestController extends Controller
{
    public function pendingCount(): JsonResponse
    {
        return response()
            ->json(['count' => LeaveRequest::where('status', 'pending')->count()])
            ->header('Cache-Control', 'no-store');
    }

    public function index(Request $request): View
    {
        $status = $request->query('status');

        if ($status !== null && ! array_key_exists($status, LeaveRequest::STATUSES)) {
            $status = null;
        }

        $leaveRequests = LeaveRequest::with(['employee', 'leaveType'])
            ->when($status, fn ($query) => $query->where('status', $status))
            ->orderByDesc('start_date')
            ->orderByDesc('id')
            ->get();

        $counts = LeaveRequest::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $employees = Employee::where('is_active', true)->orderBy('name')->get();
        $leaveTypes = LeaveType::where('is_active', true)->orderBy('name')->get();

        return view('leave-requests.index', compact('leaveRequests', 'counts', 'status', 'employees', 'leaveTypes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        LeaveRequest::create([
            ...$data,
            'status' => 'approved',
            'verified_by' => $request->user()->id,
            'verified_at' => now(),
        ]);

        return redirect()
            ->route('leave-requests.index')
            ->with('status', 'Izin berhasil dicatat dan langsung disetujui.');
    }

    public function show(LeaveRequest $leaveRequest): View
    {
        $leaveRequest->load(['employee.position', 'employee.division', 'leaveType', 'verifier']);

        return view('leave-requests.show', compact('leaveRequest'));
    }

    public function approve(Request $request, LeaveRequest $leaveRequest): RedirectResponse
    {
        if ($leaveRequest->status !== 'pending') {
            return redirect()
                ->route('leave-requests.show', $leaveRequest)
                ->with('error', 'Pengajuan ini sudah diverifikasi.');
        }

        $leaveRequest->update([
            'status' => 'approved',
            'verified_by' => $request->user()->id,
            'verified_at' => now(),
            'rejection_reason' => null,
        ]);

        return redirect()
            ->route('leave-requests.show', $leaveRequest)
            ->with('status', 'Pengajuan izin disetujui.');
    }

    public function reject(Request $request, LeaveRequest $leaveRequest): RedirectResponse
    {
        if ($leaveRequest->status !== 'pending') {
            return redirect()
                ->route('leave-requests.show', $leaveRequest)
                ->with('error', 'Pengajuan ini sudah diverifikasi.');
        }

        $data = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:500'],
        ], [
            'rejection_reason.required' => 'Alasan penolakan wajib diisi.',
            'rejection_reason.max' => 'Alasan penolakan maksimal 500 karakter.',
        ]);

        $leaveRequest->update([
            'status' => 'rejected',
            'verified_by' => $request->user()->id,
            'verified_at' => now(),
            'rejection_reason' => $data['rejection_reason'],
        ]);

        return redirect()
            ->route('leave-requests.show', $leaveRequest)
            ->with('status', 'Pengajuan izin ditolak.');
    }

    public function destroy(LeaveRequest $leaveRequest): RedirectResponse
    {
        $leaveRequest->delete();

        return redirect()
            ->route('leave-requests.index')
            ->with('status', 'Catatan izin berhasil dihapus.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'employee_id' => ['required', Rule::exists('employees', 'id')->where('is_active', true)],
            'leave_type_id' => ['required', Rule::exists('leave_types', 'id')->where('is_active', true)],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'note' => ['nullable', 'string', 'max:1000'],
        ], [
            'employee_id.required' => 'Karyawan wajib dipilih.',
            'employee_id.exists' => 'Karyawan tidak valid atau sudah nonaktif.',
            'leave_type_id.required' => 'Jenis izin wajib dipilih.',
            'leave_type_id.exists' => 'Jenis izin tidak valid atau sudah nonaktif.',
            'start_date.required' => 'Tanggal mulai wajib diisi.',
            'start_date.date' => 'Tanggal mulai tidak valid.',
            'end_date.required' => 'Tanggal selesai wajib diisi.',
            'end_date.date' => 'Tanggal selesai tidak valid.',
            'end_date.after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
            'note.max' => 'Keterangan maksimal 1000 karakter.',
        ]);

        $workingDays = WorkingDayCalculator::count(
            Carbon::parse($data['start_date']),
            Carbon::parse($data['end_date'])
        );

        if ($workingDays === 0) {
            throw ValidationException::withMessages([
                'end_date' => 'Rentang tanggal tidak memiliki hari kerja. Periksa pengaturan hari kerja dan hari libur.',
            ]);
        }

        return $data;
    }
}
