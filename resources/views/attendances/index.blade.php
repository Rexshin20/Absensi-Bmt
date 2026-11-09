@extends('layouts.app')

@section('title', 'Absensi')

@include('partials.datatables')


@push('styles')
    <style>
        .attendance-name { display: block; color: var(--text-primary); font-weight: 500; }
        .attendance-detail-photo { display: block; width: 100%; max-height: 420px; border-radius: var(--radius); object-fit: contain; background: var(--bg-default); }
        .attendance-detail-placeholder { padding: 3rem 1rem; border: 1px dashed var(--divider); border-radius: var(--radius); color: var(--text-secondary); text-align: center; }
        .attendance-detail-photo[hidden], .attendance-detail-placeholder[hidden] { display: none; }
        .attendance-detail-layout { display: grid; grid-template-columns: minmax(0, 1fr) minmax(0, 1.2fr); gap: 1.5rem; align-items: start; }
        .attendance-detail-list { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 1rem; margin: 0; }
        .attendance-detail-list dt { margin-bottom: 0.25rem; color: var(--text-secondary); font-size: 0.75rem; }
        .attendance-detail-list dd { margin: 0; color: var(--text-primary); font-size: 0.875rem; overflow-wrap: anywhere; }
        .attendance-detail-list .attendance-detail-wide { grid-column: 1 / -1; }
        .attendance-time { font-variant-numeric: tabular-nums; font-weight: 500; color: var(--text-primary); }
        .date-row { display: flex; justify-content: flex-start; margin-top: 1rem; }
        .date-picker { display: flex; align-items: flex-end; gap: 0.625rem; }
        .date-picker-field { display: flex; flex-direction: column; gap: 0.375rem; }
        .date-picker-label { color: var(--text-secondary); font-size: 0.75rem; font-weight: 500; letter-spacing: 0.3px; }
        .date-input-wrap { position: relative; display: flex; align-items: center; width: clamp(9rem, 55vw, 14rem); }
        .date-input-icon { position: absolute; left: 0.75rem; z-index: 1; color: var(--primary-main); pointer-events: none; }
        .date-picker .date-input { width: 100%; min-height: 2.75rem; padding: 0.5rem 0.75rem 0.5rem 2.5rem; border-color: var(--divider); border-radius: 8px; background-color: var(--bg-paper); font-size: 0.875rem; cursor: pointer; }
        .date-picker .date-input:hover { border-color: var(--primary-main); }
        .date-picker .date-input:focus { border-color: var(--primary-main); box-shadow: 0 0 0 3px rgba(8, 127, 75, 0.12); }
        .date-picker .date-input::-webkit-calendar-picker-indicator { cursor: pointer; opacity: 0.72; }
        .date-nav-button { width: 2.75rem; height: 2.75rem; border: 1px solid var(--divider); border-radius: 8px; color: var(--text-secondary); background-color: var(--bg-paper); transition: color 150ms ease, border-color 150ms ease, background-color 150ms ease; }
        .date-nav-button:hover { color: var(--primary-main); border-color: var(--primary-main); background-color: var(--action-hover); }
        .date-nav-button:focus-visible { outline: 2px solid var(--primary-main); outline-offset: 2px; }
        .summary-chips { display: flex; align-items: center; flex-wrap: wrap; gap: 0.5rem; }
        .attendance-detail-trigger { color: var(--primary-main); }
        .attendance-detail-trigger:hover { color: var(--primary-dark); background-color: rgba(8, 127, 75, 0.08); }
        .attendance-detail-trigger:focus-visible { outline: 2px solid var(--primary-main); outline-offset: 2px; }
        .geo-status { margin: 0.5rem 0 0; font-size: 0.8125rem; color: var(--text-secondary); }
        .geo-status.is-error { color: var(--error-main); }

        @media (max-width: 700px) {
            .attendance-detail-layout { grid-template-columns: 1fr; }
            .attendance-detail-photo { max-height: 320px; }
        }
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
                       class="icon-btn date-nav-button" aria-label="hari sebelumnya">
                        <x-icon name="ChevronLeft" />
                    </a>
                    <label class="date-picker-field" for="date">
                        <span class="date-picker-label">Tanggal absensi</span>
                        <span class="date-input-wrap">
                            <x-icon name="CalendarMonth" size="20" class="date-input-icon" />
                            <input type="date" name="date" id="date" value="{{ $date }}" class="input date-input">
                        </span>
                    </label>
                    <a href="{{ route('attendances.index', ['date' => $nextDate]) }}"
                       class="icon-btn date-nav-button" aria-label="hari berikutnya">
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
                            <th class="align-right" data-no-search data-no-export data-no-sort>Aksi</th>
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
                                <td class="align-right">
                                    <button type="button" class="icon-btn icon-btn-sm attendance-detail-trigger"
                                            data-modal-open="attendance-detail-modal"
                                            data-attendance-detail
                                            title="Lihat detail absensi {{ $attendance->employee->name }}"
                                            aria-label="Lihat detail absensi {{ $attendance->employee->name }}"
                                            data-employee="{{ $attendance->employee->name }}"
                                            data-position="{{ $attendance->employee->position?->name ?? 'Tanpa jabatan' }}"
                                            data-date="{{ $attendance->date->format('d/m/Y') }}"
                                            data-time="{{ $attendance->check_in_time->format('H:i') }} WIB"
                                            data-status="{{ $attendance->statusLabel() }}"
                                            data-status-class="{{ $attendance->status === 'late' ? 'chip-error' : 'chip-success' }}"
                                            data-location="{{ $attendance->officeLocation?->name ?? '—' }}"
                                            data-distance="{{ number_format($attendance->distance_m, 0, ',', '.') }} m dari titik kantor"
                                            data-source="{{ $attendance->sourceLabel() }}"
                                            data-coordinates="{{ number_format($attendance->latitude, 7, '.', ',') }}, {{ number_format($attendance->longitude, 7, '.', ',') }}"
                                            data-photo="{{ $attendance->photo_path ? asset('storage/' . $attendance->photo_path) : '' }}">
                                        <x-icon name="EyeOutline" size="20" />
                                    </button>
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

    <x-crud-modal id="attendance-detail-modal"
                  title="Detail Absensi"
                  subheader="Informasi kehadiran dan foto bukti absensi."
                  :wide="true">
        <div class="modal-content">
            <div class="attendance-detail-layout">
                <div>
                    <img id="attendance-detail-photo" class="attendance-detail-photo" alt="" hidden>
                    <div id="attendance-detail-no-photo" class="attendance-detail-placeholder" hidden>
                        Foto tidak tersedia untuk absensi ini.
                    </div>
                </div>
                <dl class="attendance-detail-list">
                    <div class="attendance-detail-wide">
                        <dt>Karyawan</dt>
                        <dd id="attendance-detail-employee">—</dd>
                    </div>
                    <div>
                        <dt>Jabatan</dt>
                        <dd id="attendance-detail-position">—</dd>
                    </div>
                    <div>
                        <dt>Status</dt>
                        <dd><span id="attendance-detail-status" class="chip chip-sm">—</span></dd>
                    </div>
                    <div>
                        <dt>Tanggal</dt>
                        <dd id="attendance-detail-date">—</dd>
                    </div>
                    <div>
                        <dt>Jam Absen</dt>
                        <dd id="attendance-detail-time">—</dd>
                    </div>
                    <div>
                        <dt>Lokasi Kantor</dt>
                        <dd id="attendance-detail-location">—</dd>
                    </div>
                    <div>
                        <dt>Jarak dari kantor</dt>
                        <dd id="attendance-detail-distance">—</dd>
                    </div>
                    <div>
                        <dt>Sumber Absensi</dt>
                        <dd id="attendance-detail-source">—</dd>
                    </div>
                    <div class="attendance-detail-wide">
                        <dt>Koordinat GPS</dt>
                        <dd id="attendance-detail-coordinates">—</dd>
                    </div>
                </dl>
            </div>
        </div>
        <div class="modal-actions">
            <button type="button" class="btn btn-text" data-modal-close>Tutup</button>
        </div>
    </x-crud-modal>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.addEventListener('click', function (event) {
                var trigger = event.target.closest('[data-attendance-detail]');
                if (!trigger) return;

                [
                    'employee',
                    'position',
                    'date',
                    'time',
                    'location',
                    'distance',
                    'source',
                    'coordinates'
                ].forEach(function (key) {
                    document.getElementById('attendance-detail-' + key).textContent = trigger.dataset[key];
                });

                var status = document.getElementById('attendance-detail-status');
                status.textContent = trigger.dataset.status;
                status.className = 'chip chip-sm ' + trigger.dataset.statusClass;

                var photo = document.getElementById('attendance-detail-photo');
                var noPhoto = document.getElementById('attendance-detail-no-photo');
                photo.hidden = true;
                noPhoto.hidden = Boolean(trigger.dataset.photo);

                if (trigger.dataset.photo) {
                    photo.alt = 'Foto absensi ' + trigger.dataset.employee;
                    photo.onload = function () {
                        photo.hidden = false;
                        noPhoto.hidden = true;
                    };
                    photo.onerror = function () {
                        photo.hidden = true;
                        noPhoto.hidden = false;
                    };
                    photo.src = trigger.dataset.photo;
                } else {
                    photo.removeAttribute('src');
                    photo.alt = '';
                }
            }, true);

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
