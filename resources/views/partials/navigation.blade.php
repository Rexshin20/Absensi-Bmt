@php
    $pendingLeaveCount = \App\Models\LeaveRequest::where('status', 'pending')->count();

    $navigation = [
        [
            'title' => 'Dashboard',
            'icon' => 'HomeOutline',
            'path' => '/',
            'route' => 'dashboard',
            'active' => request()->routeIs('dashboard'),
        ],
        ['section' => 'Absensi'],
        [
            'title' => 'Absensi',
            'icon' => 'Fingerprint',
            'path' => '/absensi',
            'route' => 'attendances.index',
            'active' => request()->routeIs('attendances.*'),
        ],
        [
            'title' => 'Izin & Cuti',
            'icon' => 'CalendarTextOutline',
            'path' => '/absensi/izin',
            'route' => 'leave-requests.index',
            'active' => request()->routeIs('leave-requests.*'),
            'badge' => $pendingLeaveCount,
        ],
        ['section' => 'Master Data'],
        [
            'title' => 'Karyawan',
            'icon' => 'AccountMultipleOutline',
            'path' => '/master/karyawan',
            'route' => 'employees.index',
            'active' => request()->routeIs('employees.*'),
        ],
        [
            'title' => 'Akun',
            'icon' => 'AccountOutline',
            'path' => '/master/akun',
            'route' => 'accounts.index',
            'active' => request()->routeIs('accounts.*'),
        ],
        [
            'title' => 'Divisi',
            'icon' => 'AccountGroupOutline',
            'path' => '/master/divisi',
            'route' => 'divisions.index',
            'active' => request()->routeIs('divisions.*'),
        ],
        [
            'title' => 'Jabatan',
            'icon' => 'BadgeAccountOutline',
            'path' => '/master/jabatan',
            'route' => 'positions.index',
            'active' => request()->routeIs('positions.*'),
        ],
        [
            'title' => 'Lokasi Kantor',
            'icon' => 'MapMarkerRadiusOutline',
            'path' => '/master/lokasi-kantor',
            'route' => 'office-locations.index',
            'active' => request()->routeIs('office-locations.*'),
        ],
        [
            'title' => 'Hari Kerja',
            'icon' => 'CalendarClockOutline',
            'path' => '/pengaturan/hari-kerja',
            'route' => 'work-settings.edit',
            'active' => request()->routeIs('work-settings.*'),
        ],
        [
            'title' => 'Hari Libur',
            'icon' => 'CalendarRemoveOutline',
            'path' => '/master/hari-libur',
            'route' => 'holidays.index',
            'active' => request()->routeIs('holidays.*'),
        ],
        [
            'title' => 'Jenis Izin',
            'icon' => 'FileDocumentOutline',
            'path' => '/master/jenis-izin',
            'route' => 'leave-types.index',
            'active' => request()->routeIs('leave-types.*'),
        ],
        ['section' => 'Akun'],
        [
            'title' => 'Keluar',
            'icon' => 'Login',
            'logout' => true,
        ],
    ];
@endphp

<aside class="layout-vertical-nav">
    <div class="nav-paper">
        <div class="nav-header">
            <a class="brand" href="{{ route('dashboard') }}">
                <x-logo :size="32" />
                <span class="brand-text">
                    <span class="brand-title">BMT Cinde Barokah</span>
                    <span class="brand-subtitle">Baitul Maal wat Tamwil</span>
                </span>
            </a>
            <button type="button" class="icon-btn icon-btn-sm menu-toggle-btn" data-nav-close aria-label="close navigation">
                <x-icon name="Close" />
            </button>
        </div>

        <div class="nav-scroll">
            <ul class="nav-list">
                @foreach ($navigation as $item)
                    @if (isset($item['section']))
                        <li class="nav-section-title">
                            <div class="divider-text"><span>{{ $item['section'] }}</span></div>
                        </li>
                    @else
                        <li class="nav-link-item">
                            @if (! empty($item['logout']))
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="nav-link">
                                        <span class="nav-link-icon"><x-icon :name="$item['icon']" /></span>
                                        <span class="nav-link-text">{{ $item['title'] }}</span>
                                    </button>
                                </form>
                            @else
                                <a class="nav-link {{ ! empty($item['active']) ? 'active' : '' }}"
                                   href="{{ $item['path'] }}"
                                   @if (! empty($item['newTab'])) target="_blank" rel="noopener noreferrer" @endif>
                                    <span class="nav-link-icon"><x-icon :name="$item['icon']" /></span>
                                    <span class="nav-link-text">
                                        {{ $item['title'] }}
                                        @if (array_key_exists('badge', $item))
                                            <span class="nav-badge" data-leave-badge
                                                  @if (! $item['badge']) style="display: none;" @endif>{{ $item['badge'] }}</span>
                                        @endif
                                    </span>
                                </a>
                            @endif
                        </li>
                    @endif
                @endforeach
            </ul>
        </div>
    </div>
</aside>
<div class="nav-backdrop" data-nav-close></div>

@push('scripts')
    <script>
        (function () {
            var badge = document.querySelector('[data-leave-badge]');
            if (!badge) return;

            function refresh() {
                fetch('{{ route('leave-requests.pending-count') }}', {
                    headers: { 'Accept': 'application/json' },
                    cache: 'no-store',
                    credentials: 'same-origin'
                }).then(function (response) {
                    return response.ok ? response.json() : null;
                }).then(function (data) {
                    if (!data) return;
                    badge.textContent = data.count;
                    badge.style.display = data.count > 0 ? 'inline-flex' : 'none';
                }).catch(function () {});
            }

            setInterval(refresh, 30000);
            document.addEventListener('visibilitychange', function () {
                if (!document.hidden) refresh();
            });
            window.addEventListener('pageshow', refresh);
        })();
    </script>
@endpush
