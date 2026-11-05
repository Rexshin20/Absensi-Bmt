<?php

namespace App\Http\Controllers;

use App\Models\Position;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PositionController extends Controller
{
    public function index(Request $request): View
    {
        $positions = Position::orderBy('name')->get();

        $editPosition = $request->filled('edit')
            ? Position::findOrFail($request->integer('edit'))
            : null;

        return view('positions.index', compact('positions', 'editPosition'));
    }

    public function store(Request $request): RedirectResponse
    {
        Position::create($this->validated($request));

        return redirect()
            ->route('positions.index')
            ->with('status', 'Jabatan berhasil ditambahkan.');
    }

    public function update(Request $request, Position $position): RedirectResponse
    {
        $position->update($this->validated($request));

        return redirect()
            ->route('positions.index')
            ->with('status', 'Jabatan berhasil diperbarui.');
    }

    public function destroy(Position $position): RedirectResponse
    {
        $position->delete();

        return redirect()
            ->route('positions.index')
            ->with('status', 'Jabatan berhasil dihapus.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'name.required' => 'Nama jabatan wajib diisi.',
            'name.max' => 'Nama jabatan maksimal 100 karakter.',
            'description.max' => 'Deskripsi maksimal 500 karakter.',
            'is_active.boolean' => 'Status tidak valid.',
        ]);

        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
