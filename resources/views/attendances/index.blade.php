@extends('layouts.app')

@section('title', 'Absensi')

@include('partials.datatables')


@push('styles')
    <style>
        .attendance-name { display: block; color: var(--text-primary); font-weight: 500; }
        .attendance-time { font-variant-numeric: tabular-nums; font-weight: 500; color: var(--text-primary); }
        .date-row { display: flex; justify-content: flex-start; margin-top: 1rem; }
        .date-picker { display: flex; align-items: center; gap: 0.5rem; }
        .date-picker .form-control { width: 190px; }
        .date-picker .input { text-align: center; text-align-last: center; }
        .summary-chips { display: flex; align-items: center; flex-wrap: wrap; gap: 0.5rem; }
        .geo-status { margin: 0.5rem 0 0; font-size: 0.8125rem; color: var(--text-secondary); }
        .geo-status.is-error { color: var(--error-main); }
    </style>
@endpush

@section('content')
    <div class="grid">
        <div class="grid-item col-12">
            <div class="d-flex align-center justify-between flex-wrap gap-3">
                <div>
                    <h5 class="mb-1">Absensi</h5>
                    <p class="text-body2 mb-0">Rekap absen masuk karyawan. Absen di luar radius kantor ditolak.</p>
                </div>
                <div class="page-toolbar">
                    <button type="button" class="btn btn-contained" data-modal-open="checkin-modal">
                        <x-icon name="CrosshairsGps" size="18" class="mr-1" /> Absen Sekarang
                    </button>
                </div>
            </div>
            <div class="date-row">
                <form method="GET" action="{{ route('attendances.index') }}" class="date-picker">
                    <a href="{{ route('attendances.index', ['date' => $previousDate]) }}"
                       class="icon-btn icon-btn-sm" aria-label="hari sebelumnya">
                        <x-icon name="ChevronLeft" />
                    </a>
                    <div class="form-control">
                        <input type="date" name="date" id="date" value="{{ $date }}" placeholder=" " class="input">
                    </div>
                    <a href="{{ route('attendances.index', ['date' => $nextDate]) }}"
                       class="icon-btn icon-btn-sm" aria-label="hari berikutnya">
                        <x-icon name="ChevronRight" />
                    </a>
                    <noscript><button type="submit" class="btn btn-outlined btn-sm">Lihat</button></noscript>
                </form>
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
                    <div class="d-flex align-center justify-between flex-wrap gap-3">
                        <div>
                            <div class="card-header-title" style="font-size: 1.25rem;">
                                Daftar Absen — {{ $dateLabel }}
                            </div>
                            <p class="card-header-subheader">Batas jam masuk {{ $workStart }} WIB.</p>
                        </div>
                        <div class="summary-chips">
                            <span class="chip chip-sm chip-success">{{ $presentCount }} Hadir</span>
                            <span class="chip chip-sm chip-error">{{ $lateCount }} Terlambat</span>
                            <span class="chip chip-sm">{{ $missingCount }} Belum absen</span>
                        </div>
                    </div>
                </div>
                <table class="table" data-table
                       data-export
                       data-export-title="Rekap Absensi Harian"
                       data-export-message="Tanggal {{ $dateLabel }} — batas jam masuk {{ $workStart }} WIB"
                       data-export-filename="absensi-{{ $date }}"
                       data-empty="Belum ada absen pada tanggal ini. Klik tombol Absen Sekarang untuk mencatat absen."
                       style="min-width: 760px;" aria-label="daftar absen">
                    <thead>
                        <tr>
                            <th>Karyawan</th>
                            <th>Jam Absen</th>
                            <th class="align-center">Status</th>
                            <th>Lokasi</th>
                            <th class="align-right" data-no-search>Sumber</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($attendances as $attendance)
                            <tr>
                                <td>
                                    <span class="attendance-name">{{ $attendance->employee->name }}</span>
                                    <span class="text-caption">{{ $attendance->employee->position?->name ?? 'Tanpa jabatan' }}</span>
                                </td>
                                <td><span class="attendance-time">{{ $attendance->check_in_time->format('H:i') }} WIB</span></td>
                                <td class="align-center">
                                    <span class="chip chip-sm {{ $attendance->status === 'late' ? 'chip-error' : 'chip-success' }}">
                                        {{ $attendance->statusLabel() }}
                                    </span>
                                </td>
                                <td>
                                    <span class="text-body2">{{ $attendance->officeLocation?->name ?? '—' }}</span>
                                    <span class="text-caption d-block">
                                        {{ number_format($attendance->distance_m, 0, ',', '.') }} m dari titik kantor
                                    </span>
                                </td>
                                <td class="align-right">
                                    <span class="chip chip-sm chip-secondary">{{ $attendance->sourceLabel() }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Modal absen --}}
    <x-crud-modal id="checkin-modal"
                  title="Absen Sekarang"
                  subheader="Lokasi perangkat dipakai untuk memeriksa radius kantor."
                  :open="$errors->any()">
        <form method="POST" action="{{ route('attendances.check-in') }}" id="checkin-form">
            @csrf
            <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude') }}">
            <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude') }}">

            <div class="modal-content">
                <div class="form-control">
                    <select id="employee_id" name="employee_id" class="input @error('employee_id') input-error @enderror">
                        <option value="">Pilih karyawan</option>
                        @foreach ($employees as $employee)
                            <option value="{{ $employee->id }}"
                                @selected((string) old('employee_id') === (string) $employee->id)>
                                {{ $employee->name }}
                                @if ($employee->officeLocation)
                                    — {{ $employee->officeLocation->name }} (max {{ $employee->officeLocation->radius_m }} m)
                                @else
                                    — tanpa lokasi kantor
                                @endif
                            </option>
                        @endforeach
                    </select>
                    <label class="form-label-float" for="employee_id">Karyawan</label>
                </div>

                <p class="geo-status" id="geo-status">
                    Jam absen mengikuti waktu server saat tombol ditekan.
                </p>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn btn-text" data-modal-close>Batal</button>
                <button type="submit" class="btn btn-contained" id="checkin-submit">Ambil Lokasi & Absen</button>
            </div>
        </form>
    </x-crud-modal>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var dateInput = document.getElementById('date');
            dateInput.addEventListener('change', function () {
                if (dateInput.value) dateInput.closest('form').submit();
            });

            var form = document.getElementById('checkin-form');
            var latitude = document.getElementById('latitude');
            var longitude = document.getElementById('longitude');
            var status = document.getElementById('geo-status');
            var submitButton = document.getElementById('checkin-submit');

            form.addEventListener('submit', function (event) {
                event.preventDefault();

                if (!navigator.geolocation) {
                    status.textContent = 'Browser ini tidak mendukung pengambilan lokasi. Buka lewat aplikasi Android.';
                    status.classList.add('is-error');
                    return;
                }

                submitButton.disabled = true;
                status.classList.remove('is-error');
                status.textContent = 'Mengambil lokasi perangkat...';

                navigator.geolocation.getCurrentPosition(function (position) {
                    latitude.value = position.coords.latitude.toFixed(7);
                    longitude.value = position.coords.longitude.toFixed(7);
                    form.submit();
                }, function (error) {
                    submitButton.disabled = false;
                    status.classList.add('is-error');
                    status.textContent = error.code === error.PERMISSION_DENIED
                        ? 'Izin lokasi ditolak. Aktifkan izin lokasi pada browser lalu coba lagi.'
                        : 'Lokasi tidak terbaca: ' + (error.message || 'coba lagi.');
                }, { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 });
            });
        });
    </script>
@endpush
