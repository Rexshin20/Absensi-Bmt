<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Models\Employee;
use App\Models\OfficeLocation;
use App\Models\Position;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    public function index(Request $request): View
    {
        $employees = Employee::with(['position', 'division', 'officeLocation'])->orderBy('name')->get();

        $editEmployee = $request->filled('edit')
            ? Employee::findOrFail($request->integer('edit'))
            : null;

        $positions = Position::orderBy('name')->get();
        $divisions = Division::orderBy('name')->get();
        $locations = OfficeLocation::orderBy('name')->get();

        return view('employees.index', compact('employees', 'editEmployee', 'positions', 'divisions', 'locations'));
    }

    public function store(Request $request): RedirectResponse
    {
        Employee::create($this->validated($request));

        return redirect()
            ->route('employees.index')
            ->with('status', 'Karyawan berhasil ditambahkan.');
    }

    public function update(Request $request, Employee $employee): RedirectResponse
    {
        $employee->update($this->validated($request));

        return redirect()
            ->route('employees.index')
            ->with('status', 'Data karyawan berhasil diperbarui.');
    }

    public function destroy(Employee $employee): RedirectResponse
    {
        $employee->delete();

        return redirect()
            ->route('employees.index')
            ->with('status', 'Karyawan berhasil dihapus.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'position_id' => ['nullable', 'integer', 'exists:positions,id'],
            'division_id' => ['nullable', 'integer', 'exists:divisions,id'],
            'office_location_id' => ['nullable', 'integer', 'exists:office_locations,id'],
            'phone' => ['nullable', 'string', 'max:20'],
            'join_date' => ['nullable', 'date'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'name.required' => 'Nama karyawan wajib diisi.',
            'name.max' => 'Nama karyawan maksimal 100 karakter.',
            'position_id.exists' => 'Jabatan tidak ditemukan.',
            'division_id.exists' => 'Divisi tidak ditemukan.',
            'office_location_id.exists' => 'Lokasi kantor tidak ditemukan.',
            'phone.max' => 'No. HP maksimal 20 karakter.',
            'join_date.date' => 'Tanggal masuk tidak valid.',
            'is_active.boolean' => 'Status tidak valid.',
        ]);

        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
