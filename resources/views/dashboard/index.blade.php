@extends('layouts.app')

@section('title', 'Beranda')

@push('styles')
    <style>
        .dashboard-welcome {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1.5rem;
        }

        .dashboard-welcome-logo {
            width: 76px;
            height: 76px;
            flex: 0 0 auto;
        }

        .dashboard-welcome > .card-content {
            flex: 1 1 auto;
            min-width: 0;
        }

        .dashboard-stat-card,
        .dashboard-panel {
            height: 100%;
        }

        .dashboard-stat-link {
            display: block;
            height: 100%;
            color: inherit;
            text-decoration: none;
            transition: transform 180ms ease, box-shadow 180ms ease;
        }

        .dashboard-stat-link:hover {
            color: inherit;
            transform: translateY(-2px);
            box-shadow: var(--shadow-8);
        }

        .dashboard-stat-icon {
            width: 44px;
            height: 44px;
            color: #fff;
        }

        .dashboard-stat-icon--primary { background-color: var(--primary-main); }
        .dashboard-stat-icon--success { background-color: var(--success-main); }
        .dashboard-stat-icon--warning { background-color: var(--warning-main); }
        .dashboard-stat-icon--info { background-color: var(--info-main); }

        .dashboard-summary-chips {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .dashboard-shortcuts {
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        .dashboard-shortcut-icon {
            color: var(--primary-main);
            background-color: var(--action-selected);
            transition: background-color 150ms ease, color 150ms ease;
        }

        .dashboard-shortcut {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.5rem;
            border-radius: var(--radius);
            color: inherit;
            text-decoration: none;
            transition: background-color 150ms ease;
        }

        .dashboard-shortcut:hover {
            background-color: var(--action-hover);
            color: inherit;
        }

        .dashboard-stat-link:focus-visible,
        .dashboard-shortcut:focus-visible {
            outline: 2px solid var(--primary-main);
            outline-offset: -2px;
        }

        @media (max-width: 599px) {
            .dashboard-welcome-logo {
                width: 56px;
                height: 56px;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            window.MaterioCharts.weekly(document.querySelector('#weekly-overview-chart'));
        });
    </script>
@endpush

@section('content')
    <div class="grid">
        <div class="grid-item col-12">
            <section class="card dashboard-welcome">
                <div class="card-content">
                    <div>
                        <p class="text-caption mb-1">{{ $today->format('d/m/Y') }} · Ringkasan operasional</p>
                        <h5 class="mb-1">Selamat datang, {{ auth()->user()->name }}</h5>
                        <p class="text-body2 mb-3">Pantau kehadiran karyawan BMT Cinde Barokah.</p>
                        <a class="btn btn-contained btn-sm" href="{{ route('attendances.index') }}">
                            <x-icon name="Fingerprint" class="mr-1" /> Buka Absensi
                        </a>
                    </div>
                </div>
                <x-logo :size="76" class="dashboard-welcome-logo mr-5" />
            </section>
        </div>

        <div class="grid-item col-12">
            <div class="grid">
                @foreach ([
                    [
                        'title' => 'Karyawan Aktif',
                        'value' => $activeEmployees,
                        'icon' => 'AccountMultipleOutline',
                        'color' => 'primary',
                        'hint' => 'Data karyawan aktif',
                        'route' => 'employees.index',
                    ],
                    [
                        'title' => 'Hadir Hari Ini',
                        'value' => $presentCount,
                        'icon' => 'CheckCircleOutline',
                        'color' => 'success',
                        'hint' => 'Tercatat hari ini',
                        'route' => 'attendances.index',
                    ],
                    [
                        'title' => 'Terlambat',
                        'value' => $lateCount,
                        'icon' => 'ClockAlertOutline',
                        'color' => 'warning',
                        'hint' => 'Perlu dipantau',
                        'route' => 'attendances.index',
                    ],
                ] as $stat)
                    <div class="grid-item col-12 col-sm-6 col-lg-4">
                        <a class="card dashboard-stat-card dashboard-stat-link" href="{{ route($stat['route']) }}">
                            <div class="card-content">
                                <div class="d-flex align-center justify-between mb-3">
                                    <span class="avatar avatar-rounded dashboard-stat-icon dashboard-stat-icon--{{ $stat['color'] }}">
                                        <x-icon :name="$stat['icon']" size="24" />
                                    </span>
                                    <x-icon name="ArrowTopRight" class="text-secondary" />
                                </div>
                                <span class="text-caption">{{ $stat['title'] }}</span>
                                <h5 class="my-1">{{ number_format($stat['value'], 0, ',', '.') }}</h5>
                                <span class="text-caption">{{ $stat['hint'] }}</span>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="grid-item col-12 col-lg-5">
            <section class="card dashboard-panel">
                <div class="card-header">
                    <div>
                        <div class="card-header-title">Kehadiran 7 Hari Terakhir</div>
                        <p class="card-header-subheader">Jumlah karyawan aktif yang melakukan absensi pada hari kerja.</p>
                    </div>
                </div>
                <div class="card-content">
                    <div id="weekly-overview-chart"
                         data-labels='@json($weeklyLabels)'
                         data-values='@json($weeklyCounts)'
                         data-current-index="{{ $weeklyCurrentIndex }}"></div>
                    <div class="d-flex align-center justify-between mt-3">
                        <div>
                            <h5>{{ number_format($weeklyTotal, 0, ',', '.') }}</h5>
                            <span class="text-caption">Absensi tercatat</span>
                        </div>
                        <a class="btn btn-outlined btn-sm" href="{{ route('attendances.index') }}">Lihat Rekap</a>
                    </div>
                </div>
            </section>
        </div>

        <div class="grid-item col-12 col-lg-7">
            <section class="card dashboard-panel">
                <div class="card-header">
                    <div>
                        <div class="card-header-title">Ringkasan Hari Ini</div>
                        <p class="card-header-subheader">Kehadiran karyawan aktif per {{ $today->format('d/m/Y') }}.</p>
                    </div>
                </div>
                <div class="card-content">
                    <div class="d-flex align-center justify-between flex-wrap gap-3 mb-3">
                        <div>
                            <h4>{{ $attendanceRate }}%</h4>
                            <span class="text-caption">Kehadiran tercatat</span>
                        </div>
                        <div class="dashboard-summary-chips">
                            <span class="chip chip-sm chip-success">{{ $presentCount }} Hadir</span>
                            <span class="chip chip-sm chip-warning">{{ $lateCount }} Terlambat</span>
                            <span class="chip chip-sm">{{ $missingCount }} Belum absen</span>
                        </div>
                    </div>
                    <div class="linear-progress progress-primary" role="progressbar"
                         aria-label="Persentase kehadiran hari ini"
                         aria-valuenow="{{ $attendanceRate }}" aria-valuemin="0" aria-valuemax="100">
                        <div class="bar" style="width: {{ $attendanceRate }}%;"></div>
                    </div>
                    <p class="text-caption mt-2 mb-0">
                        {{ $checkedInCount }} dari {{ $activeEmployees }} karyawan aktif sudah tercatat.
                    </p>
                </div>
            </section>
        </div>

        <div class="grid-item col-12 col-lg-8">
            <section class="card dashboard-panel">
                <div class="card-header">
                    <div>
                        <div class="card-header-title">Absensi Terbaru Hari Ini</div>
                        <p class="card-header-subheader">Karyawan yang terakhir melakukan absensi masuk.</p>
                    </div>
                </div>
                <div class="table-wrapper">
                    <table class="table" style="min-width: 560px;" aria-label="absensi terbaru hari ini">
                        <thead>
                            <tr>
                                <th>Karyawan</th>
                                <th>Jabatan</th>
                                <th>Jam</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($latestAttendances as $attendance)
                                <tr>
                                    <td class="fw-500">{{ $attendance->employee->name }}</td>
                                    <td>{{ $attendance->employee->position?->name ?? '—' }}</td>
                                    <td>{{ $attendance->check_in_time->format('H:i') }}</td>
                                    <td>
                                        <span class="chip chip-sm {{ $attendance->status === 'late' ? 'chip-warning' : 'chip-success' }}">
                                            {{ $attendance->statusLabel() }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-secondary">Belum ada absensi masuk hari ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <div class="grid-item col-12 col-lg-4">
            <section class="card dashboard-panel">
                <div class="card-header">
                    <div>
                        <div class="card-header-title">Akses Cepat</div>
                        <p class="card-header-subheader">Menu yang sering digunakan.</p>
                    </div>
                </div>
                <div class="card-content dashboard-shortcuts">
                    @foreach ([
                        [
                            'title' => 'Data Karyawan',
                            'subtitle' => 'Kelola data dan status karyawan',
                            'icon' => 'AccountMultipleOutline',
                            'route' => 'employees.index',
                        ],
                        [
                            'title' => 'Pengaturan Hari Kerja',
                            'subtitle' => 'Atur jadwal operasional',
                            'icon' => 'CalendarClock',
                            'route' => 'work-settings.edit',
                        ],
                    ] as $link)
                        <a href="{{ route($link['route']) }}" class="dashboard-shortcut">
                            <span class="d-flex align-center">
                                <span class="avatar avatar-rounded dashboard-shortcut-icon mr-3">
                                    <x-icon :name="$link['icon']" />
                                </span>
                                <span class="d-flex flex-column">
                                    <span class="text-body2 fw-600">{{ $link['title'] }}</span>
                                    <span class="text-caption">{{ $link['subtitle'] }}</span>
                                </span>
                            </span>
                            <x-icon name="ChevronRight" />
                        </a>
                    @endforeach
                </div>
            </section>
        </div>
    </div>
@endsection
