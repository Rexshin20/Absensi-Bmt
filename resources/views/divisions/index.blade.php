@extends('layouts.app')

@section('title', 'Divisi')

@include('partials.datatables')


@push('styles')
    <style>
        .division-actions { display: flex; align-items: center; justify-content: flex-end; gap: 0.25rem; }
        .division-actions form { display: inline-flex; }
        .division-name { display: block; color: var(--text-primary); font-weight: 500; }
    </style>
@endpush

@section('content')
    <div class="grid">
        <div class="grid-item col-12">
            <div class="d-flex align-center justify-between flex-wrap gap-3">
                <div>
                    <h5 class="mb-1">Divisi</h5>
                    <p class="text-body2 mb-0">Kelola divisi tempat karyawan ditempatkan.</p>
                </div>
                <div class="page-toolbar">
                    <span class="chip chip-sm">{{ $divisions->count() }} divisi</span>
                    <button type="button" class="btn btn-contained" data-modal-open="division-modal">
                        <x-icon name="Plus" size="18" class="mr-1" /> Tambah Divisi
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
                    <div class="card-header-title" style="font-size: 1.25rem;">Daftar Divisi</div>
                </div>
                <table class="table" data-table
                       data-empty="Belum ada divisi. Klik tombol Tambah Divisi untuk membuat yang pertama."
                       style="min-width: 560px;" aria-label="daftar divisi">
                    <thead>
                        <tr>
                            <th>Divisi</th>
                            <th>Deskripsi</th>
                            <th class="align-center">Status</th>
                            <th class="align-right" data-no-sort data-no-search>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($divisions as $division)
                            <tr>
                                <td>
                                    <span class="division-name">{{ $division->name }}</span>
                                </td>
                                <td><span class="text-body2">{{ $division->description ?: '—' }}</span></td>
                                <td class="align-center">
                                    @if ($division->is_active)
                                        <span class="chip chip-sm chip-success">Aktif</span>
                                    @else
                                        <span class="chip chip-sm">Nonaktif</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="division-actions">
                                        <a href="{{ route('divisions.index', ['edit' => $division->id]) }}"
                                           class="icon-btn icon-btn-sm" aria-label="edit {{ $division->name }}">
                                            <x-icon name="PencilOutline" size="20" />
                                        </a>
                                        <form method="POST" action="{{ route('divisions.destroy', $division) }}"
                                              onsubmit="return confirm('Hapus divisi {{ $division->name }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="icon-btn icon-btn-sm" aria-label="hapus {{ $division->name }}">
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
    <x-crud-modal id="division-modal"
                  :title="$editDivision ? 'Edit Divisi' : 'Tambah Divisi'"
                  :subheader="$editDivision ? 'Perbarui data divisi.' : 'Isi data divisi baru.'"
                  :open="$editDivision || $errors->any()">
        <form method="POST"
              action="{{ $editDivision ? route('divisions.update', $editDivision) : route('divisions.store') }}">
            @csrf
            @if ($editDivision)
                @method('PUT')
            @endif

            <div class="modal-content">
                <div class="form-control">
                    <input id="name" name="name" type="text" placeholder=" "
                           value="{{ old('name', $editDivision->name ?? '') }}"
                           class="input @error('name') input-error @enderror" autocomplete="off">
                    <label class="form-label-float" for="name">Nama Divisi</label>
                </div>

                <div class="form-control">
                    <textarea id="description" name="description" rows="3" placeholder=" "
                              class="input @error('description') input-error @enderror">{{ old('description', $editDivision->description ?? '') }}</textarea>
                    <label class="form-label-float" for="description">Deskripsi (opsional)</label>
                </div>

                <label class="checkbox-label">
                    <input type="checkbox" name="is_active" value="1"
                           @checked(old('is_active', $editDivision->is_active ?? true))>
                    Divisi aktif
                </label>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn btn-text" data-modal-close>Batal</button>
                <button type="submit" class="btn btn-contained">
                    {{ $editDivision ? 'Simpan Perubahan' : 'Tambah Divisi' }}
                </button>
            </div>
        </form>
    </x-crud-modal>
@endsection
