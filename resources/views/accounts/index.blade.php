@extends('layouts.app')

@section('title', 'Akun')

@include('partials.datatables')


@push('styles')
    <style>
        .account-name { display: block; color: var(--text-primary); font-weight: 500; }
        .account-actions { display: flex; align-items: center; justify-content: flex-end; gap: 0.25rem; }
        .account-actions form { display: inline-flex; }
    </style>
@endpush

@section('content')
    <div class="grid">
        <div class="grid-item col-12">
            <div class="d-flex align-center justify-between flex-wrap gap-3">
                <div>
                    <h5 class="mb-1">Akun</h5>
                    <p class="text-body2 mb-0">Akun untuk masuk panel admin dan aplikasi karyawan.</p>
                </div>
                <div class="page-toolbar">
                    <span class="chip chip-sm">{{ $accounts->count() }} akun</span>
                    <button type="button" class="btn btn-contained" data-modal-open="account-modal">
                        <x-icon name="Plus" size="18" class="mr-1" /> Tambah Akun
                    </button>
                </div>
            </div>
        </div>

        @if (session('status'))
            <div class="grid-item col-12">
                <div class="alert alert-success">
                    <span class="alert-icon"><x-icon name="CheckCircleOutline" /></span>
                    <div class="alert-message">{{ session('status') }}</div>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="grid-item col-12">
                <div class="alert alert-error">
                    <span class="alert-icon"><x-icon name="AlertCircleOutline" /></span>
                    <div class="alert-message">{{ session('error') }}</div>
                </div>
            </div>
        @endif

        {{-- Table --}}
        <div class="grid-item col-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-header-title" style="font-size: 1.25rem;">Daftar Akun</div>
                </div>
                <table class="table" data-table
                       data-empty="Belum ada akun. Klik tombol Tambah Akun untuk membuat akun pertama."
                       style="min-width: 720px;" aria-label="daftar akun">
                    <thead>
                        <tr>
                            <th>Akun</th>
                            <th>Karyawan</th>
                            <th class="align-center">Role</th>
                            <th class="align-right" data-no-sort data-no-search>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($accounts as $account)
                            <tr>
                                <td>
                                    <span class="account-name">{{ $account->name }}</span>
                                    <span class="text-caption">{{ $account->email }}</span>
                                </td>
                                <td><span class="text-body2">{{ $account->employee?->name ?? '—' }}</span></td>
                                <td class="align-center">
                                    <span class="chip chip-sm {{ $account->isAdmin() ? 'chip-primary' : 'chip-secondary' }}">
                                        {{ $account->roleLabel() }}
                                    </span>
                                </td>
                                <td>
                                    <div class="account-actions">
                                        <a href="{{ route('accounts.index', ['edit' => $account->id]) }}"
                                           class="icon-btn icon-btn-sm" aria-label="edit {{ $account->name }}">
                                            <x-icon name="PencilOutline" size="20" />
                                        </a>
                                        @if ($account->id !== auth()->id())
                                            <form method="POST" action="{{ route('accounts.destroy', $account) }}"
                                                  onsubmit="return confirm('Hapus akun {{ $account->name }}?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="icon-btn icon-btn-sm" aria-label="hapus {{ $account->name }}">
                                                    <x-icon name="DeleteOutline" size="20" />
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Modal form --}}
    <x-crud-modal id="account-modal"
                  :title="$editAccount ? 'Edit Akun' : 'Tambah Akun'"
                  :subheader="$editAccount ? 'Perbarui data akun.' : 'Buat akun baru untuk admin atau karyawan.'"
                  :open="$editAccount || $errors->any()">
            <form method="POST"
                  action="{{ $editAccount ? route('accounts.update', $editAccount) : route('accounts.store') }}">
                @csrf
                @if ($editAccount)
                    @method('PUT')
                @endif

                <div class="modal-content">
                    <div class="form-control">
                        <input id="name" name="name" type="text" placeholder=" "
                               value="{{ old('name', $editAccount->name ?? '') }}"
                               class="input @error('name') input-error @enderror" autocomplete="off">
                        <label class="form-label-float" for="name">Nama Akun</label>
                    </div>

                    <div class="form-control">
                        <input id="email" name="email" type="email" placeholder=" "
                               value="{{ old('email', $editAccount->email ?? '') }}"
                               class="input @error('email') input-error @enderror" autocomplete="off">
                        <label class="form-label-float" for="email">Email</label>
                    </div>

                    <div class="form-control">
                        <input id="password" name="password" type="password" placeholder=" "
                               class="input @error('password') input-error @enderror" autocomplete="new-password">
                        <label class="form-label-float" for="password">Password</label>
                    </div>

                    <div class="form-control">
                        <select id="role" name="role" class="input @error('role') input-error @enderror">
                            @foreach (\App\Models\User::ROLES as $value => $label)
                                <option value="{{ $value }}"
                                    @selected(old('role', $editAccount->role ?? 'employee') === $value)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        <label class="form-label-float" for="role">Role</label>
                    </div>

                    <div class="form-control">
                        <select id="employee_id" name="employee_id" class="input @error('employee_id') input-error @enderror">
                            <option value="">Tanpa karyawan</option>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}"
                                    @selected((string) old('employee_id', $editAccount->employee_id ?? '') === (string) $employee->id)>
                                    {{ $employee->name }}
                                </option>
                            @endforeach
                        </select>
                        <label class="form-label-float" for="employee_id">Karyawan</label>
                    </div>

                    <p class="form-helper">
                        @if ($editAccount)
                            Kosongkan password jika tidak ingin mengubahnya.
                        @else
                            Password minimal 8 karakter.
                        @endif
                        Pilih karyawan jika akun dipakai untuk absensi.
                    </p>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn btn-text" data-modal-close>Batal</button>
                    <button type="submit" class="btn btn-contained">
                        {{ $editAccount ? 'Simpan Perubahan' : 'Tambah Akun' }}
                    </button>
                </div>
            </form>
    </x-crud-modal>
@endsection
