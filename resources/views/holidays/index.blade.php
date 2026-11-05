@extends('layouts.app')

@section('title', 'Hari Libur')

@include('partials.datatables')


@push('styles')
    <style>
        .holiday-actions { display: flex; align-items: center; justify-content: flex-end; gap: 0.25rem; }
        .holiday-actions form { display: inline-flex; }
        .holiday-name { display: block; color: var(--text-primary); font-weight: 500; }
    </style>
@endpush

@section('content')
    <div class="grid">
        <div class="grid-item col-12">
            <div class="d-flex align-center justify-between flex-wrap gap-3">
                <div>
                    <h5 class="mb-1">Hari Libur</h5>
                    <p class="text-body2 mb-0">Tanggal khusus di luar libur mingguan: tanggal merah, cuti bersama, dan libur kantor.</p>
                </div>
                <div class="page-toolbar">
                    <span class="chip chip-sm">{{ $holidays->count() }} hari libur</span>
                    <button type="button" class="btn btn-contained" data-modal-open="holiday-modal">
                        <x-icon name="Plus" size="18" class="mr-1" /> Tambah Hari Libur
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
                    <div class="card-header-title" style="font-size: 1.25rem;">Daftar Hari Libur</div>
                </div>
                <table class="table" data-table
                       data-empty="Belum ada hari libur khusus. Klik tombol Tambah Hari Libur untuk membuat yang pertama."
                       style="min-width: 560px;" aria-label="daftar hari libur">
                    <thead>
                        <tr>
                            <th>Hari Libur</th>
                            <th>Tanggal</th>
                            <th class="align-right" data-no-sort data-no-search>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($holidays as $holiday)
                            @php
                                $dateLabel = $holiday->start_date->format('d/m/Y');
                                if ($holiday->end_date && ! $holiday->end_date->equalTo($holiday->start_date)) {
                                    $dateLabel .= ' – ' . $holiday->end_date->format('d/m/Y');
                                }
                                $typeChip = [
                                    'nasional' => 'chip-error',
                                    'cuti_bersama' => 'chip-warning',
                                    'kantor' => 'chip-info',
                                ][$holiday->type] ?? '';
                            @endphp
                            <tr>
                                <td>
                                    <span class="holiday-name">{{ $holiday->name }}</span>
                                    <span class="chip chip-sm {{ $typeChip }}">{{ $holiday->typeLabel() }}</span>
                                </td>
                                <td data-order="{{ $holiday->start_date->format('YmdHis') }}"><span class="text-body2">{{ $dateLabel }}</span></td>
                                <td>
                                    <div class="holiday-actions">
                                        <a href="{{ route('holidays.index', ['edit' => $holiday->id]) }}"
                                           class="icon-btn icon-btn-sm" aria-label="edit {{ $holiday->name }}">
                                            <x-icon name="PencilOutline" size="20" />
                                        </a>
                                        <form method="POST" action="{{ route('holidays.destroy', $holiday) }}"
                                              onsubmit="return confirm('Hapus hari libur {{ $holiday->name }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="icon-btn icon-btn-sm" aria-label="hapus {{ $holiday->name }}">
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
    <x-crud-modal id="holiday-modal"
                  :title="$editHoliday ? 'Edit Hari Libur' : 'Tambah Hari Libur'"
                  :subheader="$editHoliday ? 'Perbarui data hari libur.' : 'Isi tanggal libur khusus.'"
                  :open="$editHoliday || $errors->any()">
        <form method="POST"
              action="{{ $editHoliday ? route('holidays.update', $editHoliday) : route('holidays.store') }}">
            @csrf
            @if ($editHoliday)
                @method('PUT')
            @endif

            <div class="modal-content">
                <div class="form-control">
                    <input id="name" name="name" type="text" placeholder=" "
                           value="{{ old('name', $editHoliday->name ?? '') }}"
                           class="input @error('name') input-error @enderror" autocomplete="off">
                    <label class="form-label-float" for="name">Nama Hari Libur</label>
                </div>

                <div class="form-control">
                    <select id="type" name="type" class="input @error('type') input-error @enderror">
                        <option value="">Pilih jenis</option>
                        @foreach (\App\Models\Holiday::TYPES as $value => $label)
                            <option value="{{ $value }}"
                                @selected(old('type', $editHoliday->type ?? '') === $value)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    <label class="form-label-float" for="type">Jenis Libur</label>
                </div>

                <div class="form-control">
                    <input id="start_date" name="start_date" type="date"
                           value="{{ old('start_date', $editHoliday?->start_date?->format('Y-m-d')) }}"
                           class="input @error('start_date') input-error @enderror">
                    <label class="form-label-float" for="start_date">Tanggal</label>
                </div>

                <div>
                    <div class="form-control">
                        <input id="end_date" name="end_date" type="date"
                               value="{{ old('end_date', $editHoliday?->end_date?->format('Y-m-d')) }}"
                               class="input @error('end_date') input-error @enderror">
                        <label class="form-label-float" for="end_date">Sampai Tanggal (opsional)</label>
                    </div>
                    <p class="form-helper">Kosongkan jika liburnya hanya 1 hari.</p>
                </div>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn btn-text" data-modal-close>Batal</button>
                <button type="submit" class="btn btn-contained">
                    {{ $editHoliday ? 'Simpan Perubahan' : 'Tambah Hari Libur' }}
                </button>
            </div>
        </form>
    </x-crud-modal>
@endsection
