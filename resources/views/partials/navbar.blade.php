@php
    $pendingLeaveRequests = \App\Models\LeaveRequest::with(['employee', 'leaveType'])
        ->where('status', 'pending')
        ->orderBy('start_date')
        ->take(5)
        ->get();
    $pendingLeaveTotal = \App\Models\LeaveRequest::where('status', 'pending')->count();
@endphp

<header class="layout-navbar">
    <div class="navbar-content-container">
        <div class="navbar-inner">
            <button type="button" class="icon-btn navbar-menu-toggle" data-nav-toggle
                    aria-label="buka menu navigasi" aria-controls="app-navigation">
                <x-icon name="Menu" />
            </button>
            <div class="navbar-right">
                <button type="button" class="icon-btn d-none d-sm-inline-flex" data-theme-toggle aria-label="toggle dark mode">
                    <x-icon name="WeatherNight" class="theme-icon-light" />
                    <x-icon name="WeatherSunny" class="theme-icon-dark d-none" />
                </button>

                @if ($pendingLeaveTotal > 0)
                    <div class="position-relative">
                        <button type="button" class="icon-btn notification-trigger" data-menu-trigger="notification-menu"
                                aria-label="{{ $pendingLeaveTotal }} pengajuan izin menunggu verifikasi" aria-haspopup="true">
                            <x-icon name="BellOutline" />
                            <span class="notification-badge">{{ $pendingLeaveTotal > 9 ? '9+' : $pendingLeaveTotal }}</span>
                        </button>
                        <div class="menu-popover notification-popover" id="notification-menu">
                            <div class="notification-header">
                                <span class="fw-600">Pengajuan Izin</span>
                                <span class="chip chip-warning chip-sm">{{ $pendingLeaveTotal }} menunggu</span>
                            </div>
                            <div class="notification-body">
                                @foreach ($pendingLeaveRequests as $leaveRequest)
                                    @php
                                        $leaveName = $leaveRequest->employee?->name ?? 'Karyawan';
                                        $leaveWords = preg_split('/\s+/', trim($leaveName));
                                        $leaveInitials = strtoupper(
                                            mb_substr($leaveWords[0] ?? '', 0, 1) . mb_substr($leaveWords[1] ?? '', 0, 1)
                                        );
                                        if ($leaveInitials === '') {
                                            $leaveInitials = '?';
                                        }
                                        $leaveRange = $leaveRequest->start_date->format('d/m/Y');
                                        if (! $leaveRequest->end_date->equalTo($leaveRequest->start_date)) {
                                            $leaveRange .= ' – ' . $leaveRequest->end_date->format('d/m/Y');
                                        }
                                    @endphp
                                    <a href="{{ route('leave-requests.show', $leaveRequest) }}" class="notification-item">
                                        <span class="avatar notification-avatar">{{ $leaveInitials }}</span>
                                        <span class="notification-content">
                                            <span class="notification-title">{{ $leaveName }}</span>
                                            <span class="notification-subtitle">{{ $leaveRequest->leaveType?->name ?? 'Izin' }} · {{ $leaveRange }}</span>
                                        </span>
                                    </a>
                                @endforeach
                            </div>
                            <div class="notification-footer">
                                <a class="btn btn-contained btn-full" href="{{ route('leave-requests.index', ['status' => 'pending']) }}">
                                    Lihat semua pengajuan
                                </a>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="position-relative">
                    <span class="avatar-badge" data-menu-trigger="user-menu" role="button" tabindex="0" aria-label="user menu">
                        <span class="avatar"><img alt="{{ auth()->user()?->name ?? 'John Doe' }}" src="{{ asset('images/avatars/1.png') }}"></span>
                        <span class="dot"></span>
                    </span>
                    <div class="menu-popover user-menu-popover" id="user-menu">
                        <div class="user-menu-header">
                            <div class="d-flex align-center">
                                <span class="avatar" style="width: 2.5rem; height: 2.5rem;">
                                    <img alt="{{ auth()->user()?->name ?? 'John Doe' }}" src="{{ asset('images/avatars/1.png') }}">
                                </span>
                                <div class="d-flex flex-column ml-3 align-start">
                                    <span class="user-name">{{ auth()->user()?->name ?? 'John Doe' }}</span>
                                    <span class="user-role">Admin</span>
                                </div>
                            </div>
                        </div>
                        <div class="menu-divider" style="margin-top: 0; margin-bottom: 0.25rem;"></div>
                        <a class="menu-item user-menu-item" href="{{ route('accounts.index') }}">
                            <x-icon name="AccountOutline" /> Akun
                        </a>
                        <div class="menu-divider"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="menu-item" style="padding: 0.5rem 1rem;">
                                <x-icon name="Login" class="mr-2" /> Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
