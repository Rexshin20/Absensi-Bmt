<?php

namespace App\Http\Controllers;

use App\Models\LeaveType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeaveTypeController extends Controller
{
    public function index(Request $request): View
    {
        $leaveTypes = LeaveType::orderBy('name')->get();

        $editLeaveType = $request->filled('edit')
            ? LeaveType::findOrFail($request->integer('edit'))
            : null;

        return view('leave-types.index', compact('leaveTypes', 'editLeaveType'));
    }

    public function store(Request $request): RedirectResponse
    {
        LeaveType::create($this->validated($request));

        return redirect()
            ->route('leave-types.index')
            ->with('status', 'Jenis izin berhasil ditambahkan.');
    }

    public function update(Request $request, LeaveType $leaveType): RedirectResponse
    {
        $leaveType->update($this->validated($request));

        return redirect()
            ->route('leave-types.index')
            ->with('status', 'Jenis izin berhasil diperbarui.');
    }

    public function destroy(LeaveType $leaveType): RedirectResponse
    {
        if ($leaveType->requests()->exists()) {
            return redirect()
                ->route('leave-types.index')
                ->with('error', 'Jenis izin tidak bisa dihapus karena sudah dipakai pada pengajuan izin.');
        }

        $leaveType->delete();

        return redirect()
            ->route('leave-types.index')
            ->with('status', 'Jenis izin berhasil dihapus.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'name.required' => 'Nama jenis izin wajib diisi.',
            'name.max' => 'Nama jenis izin maksimal 100 karakter.',
            'description.max' => 'Deskripsi maksimal 500 karakter.',
            'is_active.boolean' => 'Status tidak valid.',
        ]);

        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
