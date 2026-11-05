@extends('layouts.app')

@section('title', 'Karyawan')

@include('partials.datatables')

@push('styles')
    <style>
        .employee-name { display: block; color: var(--text-primary); font-weight: 500; }
        .employee-actions { display: flex; align-items: center; justify-content: flex-end; gap: 0.25rem; }
        .employee-actions form { display: inline-flex; }
    </style>
@endpush

@section('content')
    <div class="grid">
        <div class="grid-item col-12">
            <div class="d-flex align-center justify-between flex-wrap gap-3">
                <div>
                    <h5 class="mb-1">Karyawan</h5>
                    <p class="text-body2 mb-0">Data induk karyawan yang akan terhubung ke aplikasi absensi.</p>
                </div>
                <div class="page-toolbar">
                    <span class="chip chip-sm">{{ $employees->count() }} karyawan</span>
                    <button type="button" class="btn btn-contained" data-modal-open="employee-modal">
                        <x-icon name="Plus" size="18" class="mr-1" /> Tambah Karyawan
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

        {{-- Table --}}
        <div class="grid-item col-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-header-title" style="font-size: 1.25rem;">Daftar Karyawan</div>
                </div>
                <table class="table" data-table
                       data-empty="Belum ada karyawan. Klik tombol Tambah Karyawan untuk membuat yang pertama."
                       style="min-width: 780px;" aria-label="daftar karyawan">
                    <thead>
                        <tr>
                            <th>Karyawan</th>
                            <th>Jabatan</th>
                            <th>Divisi</th>
                            <th>Lokasi Kantor</th>
                            <th class="align-center">Status</th>
                            <th class="align-right" data-no-sort data-no-search>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($employees as $employee)
                            <tr>
                                <td>
                                    <span class="employee-name">{{ $employee->name }}</span>
                                </td>
                                <td><span class="text-body2">{{ $employee->position?->name ?? '—' }}</span></td>
                                <td><span class="text-body2">{{ $employee->division?->name ?? '—' }}</span></td>
                                <td><span class="text-body2">{{ $employee->officeLocation?->name ?? '—' }}</span></td>
                                <td class="align-center">
                                    @if ($employee->is_active)
                                        <span class="chip chip-sm chip-success">Aktif</span>
                                    @else
                                        <span class="chip chip-sm">Nonaktif</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="employee-actions">
                                        <a href="{{ route('employees.index', ['edit' => $employee->id]) }}"
                                           class="icon-btn icon-btn-sm" aria-label="edit {{ $employee->name }}">
                                            <x-icon name="PencilOutline" size="20" />
                                        </a>
                                        <form method="POST" action="{{ route('employees.destroy', $employee) }}"
                                              onsubmit="return confirm('Hapus karyawan {{ $employee->name }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="icon-btn icon-btn-sm" aria-label="hapus {{ $employee->name }}">
                                                <x-icon name="DeleteOutline" size="20" />
                                            </button>
                                        </form>
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
    <x-crud-modal id="employee-modal" wide
                  :title="$editEmployee ? 'Edit Karyawan' : 'Tambah Karyawan'"
                  :subheader="$editEmployee ? 'Perbarui data karyawan.' : 'Isi data karyawan baru.'"
                  :open="$editEmployee || $errors->any()">
        <form method="POST"
              action="{{ $editEmployee ? route('employees.update', $editEmployee) : route('employees.store') }}">
            @csrf
            @if ($editEmployee)
                @method('PUT')
            @endif

            <div class="modal-content">
                <div class="grid">
                    <div class="grid-item col-12 col-md-6">
                        <div class="form-control">
                            <input id="name" name="name" type="text" placeholder=" "
                                   value="{{ old('name', $editEmployee->name ?? '') }}"
                                   class="input @error('name') input-error @enderror" autocomplete="off">
                            <label class="form-label-float" for="name">Nama Lengkap</label>
                        </div>
                    </div>

                    <div class="grid-item col-12 col-md-6">
                        <div class="form-control">
                            <select id="position_id" name="position_id" class="input @error('position_id') input-error @enderror">
                                <option value="">Tanpa jabatan</option>
                                @foreach ($positions as $position)
                                    <option value="{{ $position->id }}"
                                        @selected((string) old('position_id', $editEmployee->position_id ?? '') === (string) $position->id)>
                                        {{ $position->name }}
                                    </option>
                                @endforeach
                            </select>
                            <label class="form-label-float" for="position_id">Jabatan</label>
                        </div>
                    </div>

                    <div class="grid-item col-12 col-md-6">
                        <div class="form-control">
                            <select id="division_id" name="division_id" class="input @error('division_id') input-error @enderror">
                                <option value="">Tanpa divisi</option>
                                @foreach ($divisions as $division)
                                    <option value="{{ $division->id }}"
                                        @selected((string) old('division_id', $editEmployee->division_id ?? '') === (string) $division->id)>
                                        {{ $division->name }}
                                    </option>
                                @endforeach
                            </select>
                            <label class="form-label-float" for="division_id">Divisi</label>
                        </div>
                    </div>

                    <div class="grid-item col-12 col-md-6">
                        <div class="form-control">
                            <select id="office_location_id" name="office_location_id" class="input @error('office_location_id') input-error @enderror">
                                <option value="">Tanpa lokasi</option>
                                @foreach ($locations as $location)
                                    <option value="{{ $location->id }}"
                                        @selected((string) old('office_location_id', $editEmployee->office_location_id ?? '') === (string) $location->id)>
                                        {{ $location->name }}
                                    </option>
                                @endforeach
                            </select>
                            <label class="form-label-float" for="office_location_id">Lokasi Kantor</label>
                        </div>
                    </div>

                    <div class="grid-item col-12 col-md-6">
                        <div class="form-control">
                            <input id="phone" name="phone" type="text" placeholder=" "
                                   value="{{ old('phone', $editEmployee->phone ?? '') }}"
                                   class="input @error('phone') input-error @enderror" autocomplete="off">
                            <label class="form-label-float" for="phone">No. HP</label>
                        </div>
                    </div>

                    <div class="grid-item col-12 col-md-6">
                        <div class="form-control">
                            <input id="join_date" name="join_date" type="date"
                                   value="{{ old('join_date', $editEmployee?->join_date?->format('Y-m-d')) }}"
                                   class="input @error('join_date') input-error @enderror">
                            <label class="form-label-float" for="join_date">Tanggal Masuk</label>
                        </div>
                    </div>

                    <div class="grid-item col-12">
                        <label class="checkbox-label">
                            <input type="checkbox" name="is_active" value="1"
                                   @checked(old('is_active', $editEmployee->is_active ?? true))>
                            Karyawan aktif
                        </label>
                    </div>
                </div>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn btn-text" data-modal-close>Batal</button>
                <button type="submit" class="btn btn-contained">
                    {{ $editEmployee ? 'Simpan Perubahan' : 'Tambah Karyawan' }}
                </button>
            </div>
        </form>
    </x-crud-modal>
@endsection
