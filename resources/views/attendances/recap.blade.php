@extends('layouts.app')

@section('title', 'Rekap Bulanan')

@push('styles')
    <style>
        .recap-filter { display: flex; align-items: flex-end; flex-wrap: wrap; gap: 0.625rem; margin-top: 1rem; }
        .recap-filter-field { display: flex; flex-direction: column; gap: 0.375rem; }
        .recap-filter-label { color: var(--text-secondary); font-size: 0.75rem; font-weight: 500; letter-spacing: 0.3px; }
        .recap-filter .input { min-height: 2.75rem; padding: 0.5rem 0.75rem; border-color: var(--divider); border-radius: 8px; background-color: var(--bg-paper); font-size: 0.875rem; }
        .recap-filter .input:hover { border-color: var(--primary-main); }
        .recap-filter .input:focus { border-color: var(--primary-main); box-shadow: 0 0 0 3px rgba(8, 127, 75, 0.12); }
        .recap-filter-month { width: 12rem; }
        .recap-filter-location { min-width: 13rem; }
        .recap-actions { display: flex; flex-wrap: wrap; gap: 0.5rem; }
        .recap-office { color: var(--text-primary); font-size: 0.9375rem; font-weight: 600; text-align: center; margin: 1.25rem 0 0.5rem; }
        .recap-office:first-child { margin-top: 0; }
        .recap-table { min-width: 760px; }
        .recap-table thead th { text-align: center; vertical-align: middle; }
        .recap-table thead th.recap-name { text-align: left; }
        .recap-table td { vertical-align: middle; }
        .recap-table td.recap-number { text-align: center; font-variant-numeric: tabular-nums; }
        .recap-table td.recap-name { font-weight: 500; }
        .recap-table td.recap-absent.is-filled { color: var(--error-main); font-weight: 600; }
        .recap-table td.recap-note { color: var(--text-secondary); font-size: 0.8125rem; }
    </style>
@endpush

@section('content')
    <div class="grid">
        <div class="grid-item col-12">
            <div class="d-flex align-center justify-between flex-wrap gap-3">
                <div>
                    <h5 class="mb-1">Rekap Bulanan</h5>
                    <p class="text-body2 mb-0">Rekap kehadiran per karyawan beserta keterangan tidak masuk.</p>
                </div>
                <div class="recap-actions">
                    <a href="{{ route('attendances.recap.export', ['format' => 'pdf', 'month' => $month, 'location_id' => $locationId]) }}"
                       class="btn btn-outlined btn-sm" title="Unduh PDF">
                        <x-icon name="FilePdfBox" size="18" class="mr-1" /> PDF
                    </a>
                    <a href="{{ route('attendances.recap.export', ['format' => 'xlsx', 'month' => $month, 'location_id' => $locationId]) }}"
                       class="btn btn-outlined btn-sm" title="Unduh Excel">
                        <x-icon name="FileExcelOutline" size="18" class="mr-1" /> Excel
                    </a>
                    <a href="{{ route('attendances.recap.export', ['format' => 'csv', 'month' => $month, 'location_id' => $locationId]) }}"
                       class="btn btn-outlined btn-sm" title="Unduh CSV">
                        <x-icon name="FileDelimitedOutline" size="18" class="mr-1" /> CSV
                    </a>
                </div>
            </div>

            <form method="GET" action="{{ route('attendances.recap') }}" class="recap-filter">
                <label class="recap-filter-field" for="month">
                    <span class="recap-filter-label">Bulan</span>
                    <input type="month" name="month" id="month" value="{{ $month }}"
                           class="input recap-filter-month">
                </label>
                <label class="recap-filter-field" for="location_id">
                    <span class="recap-filter-label">Lokasi kantor</span>
                    <select name="location_id" id="location_id" class="input recap-filter-location">
                        <option value="">Semua lokasi kantor</option>
                        @foreach ($officeLocations as $officeLocation)
                            <option value="{{ $officeLocation->id }}" @selected((string) $locationId === (string) $officeLocation->id)>
                                {{ $officeLocation->name }}
                            </option>
                        @endforeach
                    </select>
                </label>
                <noscript><button type="submit" class="btn btn-outlined btn-sm">Lihat</button></noscript>
            </form>
        </div>

        <div class="grid-item col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-center justify-between flex-wrap gap-3">
                        <div>
                            <div class="card-header-title" style="font-size: 1.25rem;">
                                Rekap Absensi — {{ $periodLabel }}
                            </div>
                            <p class="card-header-subheader">
                                {{ $officeLabel }}{{ $cutoffLabel ? ' · '.$cutoffLabel : '' }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @forelse ($groups as $group)
                        <div class="recap-office">{{ $group['label'] }}</div>
                        <div style="overflow-x: auto;">
                            <table class="table recap-table" aria-label="rekap absensi {{ $group['label'] }}">
                                <thead>
                                    <tr>
                                        <th rowspan="2" style="width: 3.5rem;">No.</th>
                                        <th rowspan="2" class="recap-name">Nama Karyawan</th>
                                        <th colspan="2">Masuk</th>
                                        <th rowspan="2">Tidak Masuk</th>
                                        <th rowspan="2" class="recap-name">Keterangan</th>
                                    </tr>
                                    <tr>
                                        <th>Tepat Waktu</th>
                                        <th>Terlambat</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($group['rows'] as $row)
                                        <tr>
                                            <td class="recap-number">{{ $row['no'] }}</td>
                                            <td class="recap-name">{{ $row['name'] }}</td>
                                            <td class="recap-number">{{ $row['present'] }}</td>
                                            <td class="recap-number">{{ $row['late'] }}</td>
                                            <td class="recap-number recap-absent {{ $row['absent'] > 0 ? 'is-filled' : '' }}">{{ $row['absent'] }}</td>
                                            <td class="recap-note">{{ $row['note'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @empty
                        <p class="text-body2 mb-0">
                            Belum ada karyawan aktif{{ $locationId ? ' di lokasi ini' : '' }}.
                        </p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            var monthInput = document.getElementById('month');
            var locationSelect = document.getElementById('location_id');

            monthInput.addEventListener('change', function () {
                if (monthInput.value) monthInput.closest('form').submit();
            });

            locationSelect.addEventListener('change', function () {
                this.form.submit();
            });
        })();
    </script>
@endpush
