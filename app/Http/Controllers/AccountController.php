<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function index(Request $request): View
    {
        $accounts = User::with('employee')->orderBy('name')->get();

        $editAccount = $request->filled('edit')
            ? User::findOrFail($request->integer('edit'))
            : null;

        $linkedEmployeeIds = User::whereNotNull('employee_id')
            ->when($editAccount, fn ($query) => $query->where('id', '!=', $editAccount->id))
            ->pluck('employee_id');

        $employees = Employee::whereNotIn('id', $linkedEmployeeIds)->orderBy('name')->get();

        return view('accounts.index', compact('accounts', 'editAccount', 'employees'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request, true);
        $data['password'] = Hash::make($data['password']);

        User::create($data);

        return redirect()
            ->route('accounts.index')
            ->with('status', 'Akun berhasil ditambahkan.');
    }

    public function update(Request $request, User $account): RedirectResponse
    {
        $data = $this->validated($request, false, $account);

        if ($account->id === $request->user()->id && $data['role'] !== 'admin') {
            return back()
                ->withInput()
                ->withErrors(['role' => 'Tidak bisa mengubah role akun sendiri.']);
        }

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->input('password'));
        } else {
            unset($data['password']);
        }

        $account->update($data);

        return redirect()
            ->route('accounts.index')
            ->with('status', 'Akun berhasil diperbarui.');
    }

    public function destroy(Request $request, User $account): RedirectResponse
    {
        if ($account->id === $request->user()->id) {
            return redirect()
                ->route('accounts.index')
                ->with('error', 'Tidak bisa menghapus akun yang sedang dipakai.');
        }

        $account->delete();

        return redirect()
            ->route('accounts.index')
            ->with('status', 'Akun berhasil dihapus.');
    }

    private function validated(Request $request, bool $creating, ?User $account = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($account?->id)],
            'password' => $creating ? ['required', 'string', 'min:8'] : ['nullable', 'string', 'min:8'],
            'role' => ['required', Rule::in(array_keys(User::ROLES))],
            'employee_id' => [
                'nullable',
                'integer',
                'exists:employees,id',
                Rule::unique('users', 'employee_id')->ignore($account?->id),
                'required_if:role,employee',
            ],
        ], [
            'name.required' => 'Nama akun wajib diisi.',
            'name.max' => 'Nama akun maksimal 100 karakter.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah dipakai akun lain.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'role.required' => 'Role wajib dipilih.',
            'role.in' => 'Role tidak valid.',
            'employee_id.exists' => 'Karyawan tidak ditemukan.',
            'employee_id.unique' => 'Karyawan tersebut sudah punya akun.',
            'employee_id.required_if' => 'Karyawan wajib dipilih untuk role karyawan.',
        ]);
    }
}
