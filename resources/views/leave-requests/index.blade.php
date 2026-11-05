@extends('layouts.app')

@section('title', 'Izin & Cuti')

@include('partials.datatables')


@push('styles')
    <style>
        .leave-filter { display: flex; flex-wrap: wrap; align-items: center; gap: 0.5rem; }
        .leave-filter .chip { text-decoration: none; }
        .leave-filter .chip:hover { text-decoration: none; }
        .leave-name { display: block; color: var(--text-primary); font-weight: 500; }
        .leave-actions { display: flex; align-items: center; justify-content: flex-end; gap: 0.25rem; }
        .leave-actions form { display: inline-flex; }
    </style>
@endpush

@section('content')
    <div class="grid">
        <div class="grid-item col-12">
            <div class="d-flex align-center justify-between flex-wrap gap-3">
                <div>
                    <h5 class="mb-1">Izin &amp; Cuti</h5>
                    <p class="text-body2 mb-0">Catat karyawan yang tidak masuk beserta alasannya. Entri manual oleh admin langsung disetujui.</p>
                </div>
                <div class="page-toolbar">
                    <span class="chip chip-sm">{{ $counts->sum() }} pengajuan</span>
                    <button type="button" class="btn btn-contained" data-modal-open="leave-modal">
                        <x-icon name="Plus" size="18" class="mr-1" /> Catat Izin / Cuti
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
                    <div class="card-header-title" style="font-size: 1.25rem;">Daftar Izin</div>
                </div>
                <div class="card-content">
                    <div class="leave-filter">
                        <a href="{{ route('leave-requests.index') }}"
                           class="chip chip-sm {{ $status === null ? 'chip-primary' : '' }}">
                            Semua ({{ $counts->sum() }})
                        </a>
                        @foreach (\App\Models\LeaveRequest::STATUSES as $value => $label)
                            <a href="{{ route('leave-requests.index', ['status' => $value]) }}"
                               class="chip chip-sm {{ $status === $value ? 'chip-primary' : '' }}">
                                {{ $label }} ({{ $counts->get($value, 0) }})
                            </a>
                        @endforeach
                    </div>
                </div>
                <table class="table" data-table
                       data-export
                       data-export-title="Rekap Izin dan Cuti"
                       data-export-message="{{ $status ? 'Filter status: ' . \App\Models\LeaveRequest::STATUSES[$status] : '' }}"
                       data-export-filename="izin-cuti{{ $status ? '-' . $status : '' }}"
                       data-empty="Belum ada catatan izin. Klik tombol Catat Izin / Cuti untuk membuat yang pertama."
                       style="min-width: 720px;" aria-label="daftar izin">
                    <thead>
                        <tr>
                            <th>Karyawan</th>
                            <th>Jenis Izin</th>
                            <th>Tanggal</th>
                            <th class="align-center">Hari Kerja</th>
                            <th class="align-center">Status</th>
                            <th class="align-right" data-no-sort data-no-search data-no-export>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($leaveRequests as $leaveRequest)
                            @php
                                $dateLabel = $leaveRequest->start_date->format('d/m/Y');
                                if (! $leaveRequest->end_date->equalTo($leaveRequest->start_date)) {
                                    $dateLabel .= ' – ' . $leaveRequest->end_date->format('d/m/Y');
                                }
                                $statusChip = [
                                    'pending' => 'chip-warning',
                                    'approved' => 'chip-success',
                                    'rejected' => 'chip-error',
                                ][$leaveRequest->status] ?? '';
                            @endphp
                            <tr>
                                <td>
                                    <span class="leave-name">{{ $leaveRequest->employee?->name ?? '—' }}</span>
                                </td>
                                <td><span class="text-body2">{{ $leaveRequest->leaveType?->name ?? '—' }}</span></td>
                                <td data-order="{{ $leaveRequest->start_date->format('Ymd') }}"><span class="text-body2">{{ $dateLabel }}</span></td>
                                <td class="align-center" data-order="{{ $leaveRequest->workingDays() }}"><span class="text-body2">{{ $leaveRequest->workingDays() }} hari</span></td>
                                <td class="align-center">
                                    <span class="chip chip-sm {{ $statusChip }}">{{ $leaveRequest->statusLabel() }}</span>
                                </td>
                                <td>
                                    <div class="leave-actions">
                                        <a href="{{ route('leave-requests.show', $leaveRequest) }}"
                                           class="icon-btn icon-btn-sm" aria-label="lihat izin {{ $leaveRequest->employee?->name }}">
                                            <x-icon name="EyeOutline" size="20" />
                                        </a>
                                        <form method="POST" action="{{ route('leave-requests.destroy', $leaveRequest) }}"
                                              onsubmit="return confirm('Hapus catatan izin ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="icon-btn icon-btn-sm" aria-label="hapus catatan izin">
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
    <x-crud-modal id="leave-modal"
                  title="Catat Izin / Cuti"
                  subheader="Pilih karyawan, jenis izin, dan rentang tanggalnya."
                  :open="$errors->any()">
        <form method="POST" action="{{ route('leave-requests.store') }}">
            @csrf

            <div class="modal-content">
                @if ($employees->isEmpty() || $leaveTypes->isEmpty())
                    <div class="alert alert-warning">
                        <span class="alert-icon"><x-icon name="AlertCircleOutline" /></span>
                        <div class="alert-message">
                            Tambahkan karyawan aktif dan jenis izin terlebih dahulu di menu Master Data.
                        </div>
                    </div>
                @endif

                <div class="grid">
                    <div class="grid-item col-12 col-md-6">
                        <div class="form-control">
                            <select id="employee_id" name="employee_id" class="input @error('employee_id') input-error @enderror">
                                <option value="">Pilih karyawan</option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}"
                                        @selected((string) old('employee_id') === (string) $employee->id)>
                                        {{ $employee->name }}
                                    </option>
                                @endforeach
                            </select>
                            <label class="form-label-float" for="employee_id">Karyawan</label>
                        </div>
                    </div>

                    <div class="grid-item col-12 col-md-6">
                        <div class="form-control">
                            <select id="leave_type_id" name="leave_type_id" class="input @error('leave_type_id') input-error @enderror">
                                <option value="">Pilih jenis izin</option>
                                @foreach ($leaveTypes as $leaveType)
                                    <option value="{{ $leaveType->id }}"
                                        @selected((string) old('leave_type_id') === (string) $leaveType->id)>
                                        {{ $leaveType->name }}
                                    </option>
                                @endforeach
                            </select>
                            <label class="form-label-float" for="leave_type_id">Jenis Izin</label>
                        </div>
                    </div>

                    <div class="grid-item col-12 col-md-6">
                        <div class="form-control">
                            <input id="start_date" name="start_date" type="date"
                                   value="{{ old('start_date') }}"
                                   class="input @error('start_date') input-error @enderror">
                            <label class="form-label-float" for="start_date">Mulai</label>
                        </div>
                    </div>

                    <div class="grid-item col-12 col-md-6">
                        <div class="form-control">
                            <input id="end_date" name="end_date" type="date"
                                   value="{{ old('end_date') }}"
                                   class="input @error('end_date') input-error @enderror">
                            <label class="form-label-float" for="end_date">Selesai</label>
                        </div>
                    </div>

                    <div class="grid-item col-12">
                        <div class="form-control">
                            <textarea id="note" name="note" rows="2" placeholder=" "
                                      class="input @error('note') input-error @enderror">{{ old('note') }}</textarea>
                            <label class="form-label-float" for="note">Keterangan (opsional)</label>
                        </div>
                        <p class="form-helper">Contoh: cuti menikah, sakit, atau acara keluarga.</p>
                    </div>
                </div>

                <p class="form-helper" style="margin-left: 0;">Jumlah hari kerja dihitung otomatis; hari libur mingguan dan tanggal libur tidak ikut dihitung.</p>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn btn-text" data-modal-close>Batal</button>
                <button type="submit" class="btn btn-contained" @disabled($employees->isEmpty() || $leaveTypes->isEmpty())>
                    Catat Izin
                </button>
            </div>
        </form>
    </x-crud-modal>
@endsection
