@extends('layouts.app')

@section('title', 'Jenis Izin')

@include('partials.datatables')


@push('styles')
    <style>
        .leave-type-actions { display: flex; align-items: center; justify-content: flex-end; gap: 0.25rem; }
        .leave-type-actions form { display: inline-flex; }
        .leave-type-name { display: block; color: var(--text-primary); font-weight: 500; }
    </style>
@endpush

@section('content')
    <div class="grid">
        <div class="grid-item col-12">
            <div class="d-flex align-center justify-between flex-wrap gap-3">
                <div>
                    <h5 class="mb-1">Jenis Izin</h5>
                    <p class="text-body2 mb-0">Alasan izin yang bisa dipilih karyawan, misalnya cuti, sakit, atau acara keluarga.</p>
                </div>
                <div class="page-toolbar">
                    <span class="chip chip-sm">{{ $leaveTypes->count() }} jenis izin</span>
                    <button type="button" class="btn btn-contained" data-modal-open="leave-type-modal">
                        <x-icon name="Plus" size="18" class="mr-1" /> Tambah Jenis Izin
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
                    <div class="card-header-title" style="font-size: 1.25rem;">Daftar Jenis Izin</div>
                </div>
                <table class="table" data-table
                       data-empty="Belum ada jenis izin. Klik tombol Tambah Jenis Izin untuk membuat yang pertama."
                       style="min-width: 560px;" aria-label="daftar jenis izin">
                    <thead>
                        <tr>
                            <th>Jenis Izin</th>
                            <th>Deskripsi</th>
                            <th class="align-center">Status</th>
                            <th class="align-right" data-no-sort data-no-search>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($leaveTypes as $leaveType)
                            <tr>
                                <td>
                                    <span class="leave-type-name">{{ $leaveType->name }}</span>
                                </td>
                                <td><span class="text-body2">{{ $leaveType->description ?: '—' }}</span></td>
                                <td class="align-center">
                                    @if ($leaveType->is_active)
                                        <span class="chip chip-sm chip-success">Aktif</span>
                                    @else
                                        <span class="chip chip-sm">Nonaktif</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="leave-type-actions">
                                        <a href="{{ route('leave-types.index', ['edit' => $leaveType->id]) }}"
                                           class="icon-btn icon-btn-sm" aria-label="edit {{ $leaveType->name }}">
                                            <x-icon name="PencilOutline" size="20" />
                                        </a>
                                        <form method="POST" action="{{ route('leave-types.destroy', $leaveType) }}"
                                              onsubmit="return confirm('Hapus jenis izin {{ $leaveType->name }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="icon-btn icon-btn-sm" aria-label="hapus {{ $leaveType->name }}">
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
    <x-crud-modal id="leave-type-modal"
                  :title="$editLeaveType ? 'Edit Jenis Izin' : 'Tambah Jenis Izin'"
                  :subheader="$editLeaveType ? 'Perbarui data jenis izin.' : 'Isi jenis izin baru.'"
                  :open="$editLeaveType || $errors->any()">
        <form method="POST"
              action="{{ $editLeaveType ? route('leave-types.update', $editLeaveType) : route('leave-types.store') }}">
            @csrf
            @if ($editLeaveType)
                @method('PUT')
            @endif

            <div class="modal-content">
                <div class="form-control">
                    <input id="name" name="name" type="text" placeholder=" "
                           value="{{ old('name', $editLeaveType->name ?? '') }}"
                           class="input @error('name') input-error @enderror" autocomplete="off">
                    <label class="form-label-float" for="name">Nama Jenis Izin</label>
                </div>

                <div class="form-control">
                    <textarea id="description" name="description" rows="3" placeholder=" "
                              class="input @error('description') input-error @enderror">{{ old('description', $editLeaveType->description ?? '') }}</textarea>
                    <label class="form-label-float" for="description">Deskripsi (opsional)</label>
                </div>

                <label class="checkbox-label">
                    <input type="checkbox" name="is_active" value="1"
                           @checked(old('is_active', $editLeaveType->is_active ?? true))>
                    Jenis izin aktif
                </label>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn btn-text" data-modal-close>Batal</button>
                <button type="submit" class="btn btn-contained">
                    {{ $editLeaveType ? 'Simpan Perubahan' : 'Tambah Jenis Izin' }}
                </button>
            </div>
        </form>
    </x-crud-modal>
@endsection
