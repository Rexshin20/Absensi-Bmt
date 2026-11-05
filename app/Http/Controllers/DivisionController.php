<?php

namespace App\Http\Controllers;

use App\Models\Division;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DivisionController extends Controller
{
    public function index(Request $request): View
    {
        $divisions = Division::orderBy('name')->get();

        $editDivision = $request->filled('edit')
            ? Division::findOrFail($request->integer('edit'))
            : null;

        return view('divisions.index', compact('divisions', 'editDivision'));
    }

    public function store(Request $request): RedirectResponse
    {
        Division::create($this->validated($request));

        return redirect()
            ->route('divisions.index')
            ->with('status', 'Divisi berhasil ditambahkan.');
    }

    public function update(Request $request, Division $division): RedirectResponse
    {
        $division->update($this->validated($request));

        return redirect()
            ->route('divisions.index')
            ->with('status', 'Divisi berhasil diperbarui.');
    }

    public function destroy(Division $division): RedirectResponse
    {
        $division->delete();

        return redirect()
            ->route('divisions.index')
            ->with('status', 'Divisi berhasil dihapus.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'name.required' => 'Nama divisi wajib diisi.',
            'name.max' => 'Nama divisi maksimal 100 karakter.',
            'description.max' => 'Deskripsi maksimal 500 karakter.',
            'is_active.boolean' => 'Status tidak valid.',
        ]);

        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
