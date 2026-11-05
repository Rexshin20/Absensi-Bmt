<?php

namespace App\Http\Controllers;

use App\Models\OfficeLocation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OfficeLocationController extends Controller
{
    public function index(Request $request): View
    {
        $locations = OfficeLocation::orderBy('name')->get();

        $editLocation = $request->filled('edit')
            ? OfficeLocation::findOrFail($request->integer('edit'))
            : null;

        return view('office-locations.index', compact('locations', 'editLocation'));
    }

    public function store(Request $request): RedirectResponse
    {
        OfficeLocation::create($this->validated($request));

        return redirect()
            ->route('office-locations.index')
            ->with('status', 'Lokasi kantor berhasil ditambahkan.');
    }

    public function update(Request $request, OfficeLocation $officeLocation): RedirectResponse
    {
        $officeLocation->update($this->validated($request));

        return redirect()
            ->route('office-locations.index')
            ->with('status', 'Lokasi kantor berhasil diperbarui.');
    }

    public function destroy(OfficeLocation $officeLocation): RedirectResponse
    {
        $officeLocation->delete();

        return redirect()
            ->route('office-locations.index')
            ->with('status', 'Lokasi kantor berhasil dihapus.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'address' => ['required', 'string', 'max:500'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'radius_m' => ['required', 'integer', 'min:10', 'max:10000'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'name.required' => 'Nama lokasi wajib diisi.',
            'name.max' => 'Nama lokasi maksimal 100 karakter.',
            'address.required' => 'Alamat wajib diisi.',
            'address.max' => 'Alamat maksimal 500 karakter.',
            'latitude.required' => 'Latitude wajib diisi.',
            'latitude.numeric' => 'Latitude harus berupa angka.',
            'latitude.between' => 'Latitude harus di antara -90 dan 90.',
            'longitude.required' => 'Longitude wajib diisi.',
            'longitude.numeric' => 'Longitude harus berupa angka.',
            'longitude.between' => 'Longitude harus di antara -180 dan 180.',
            'radius_m.required' => 'Radius wajib diisi.',
            'radius_m.integer' => 'Radius harus berupa angka bulat.',
            'radius_m.min' => 'Radius minimal 10 meter.',
            'radius_m.max' => 'Radius maksimal 10.000 meter.',
            'is_active.boolean' => 'Status tidak valid.',
        ]);

        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
