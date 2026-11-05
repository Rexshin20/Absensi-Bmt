<header class="layout-navbar">
    <div class="navbar-content-container">
        <div class="navbar-inner">
            <div class="navbar-right">
                <button type="button" class="icon-btn d-none d-sm-inline-flex" data-theme-toggle aria-label="toggle dark mode">
                    <x-icon name="WeatherNight" class="theme-icon-light" />
                    <x-icon name="WeatherSunny" class="theme-icon-dark d-none" />
                </button>

                <div class="position-relative">
                    <button type="button" class="icon-btn" data-menu-trigger="notification-menu"
                            aria-label="notifications" aria-haspopup="true">
                        <x-icon name="BellOutline" />
                    </button>
                    <div class="menu-popover notification-popover" id="notification-menu">
                        <div class="notification-header">
                            <span class="fw-600">Notifications</span>
                            <span class="chip chip-primary chip-sm">8 New</span>
                        </div>
                        <div class="notification-body">
                            <button type="button" class="notification-item">
                                <span class="avatar"><img alt="Flora" src="{{ asset('images/avatars/4.png') }}"></span>
                                <span class="notification-content">
                                    <span class="notification-title">Congratulation Flora! 🎉</span>
                                    <span class="notification-subtitle">Won the monthly best seller badge</span>
                                </span>
                                <span class="notification-time">Today</span>
                            </button>
                            <button type="button" class="notification-item">
                                <span class="avatar" style="background-color: var(--primary-main); color: #fff;">VU</span>
                                <span class="notification-content">
                                    <span class="notification-title">New user registered.</span>
                                    <span class="notification-subtitle">5 hours ago</span>
                                </span>
                                <span class="notification-time">Yesterday</span>
                            </button>
                            <button type="button" class="notification-item">
                                <span class="avatar"><img alt="message" src="{{ asset('images/avatars/5.png') }}"></span>
                                <span class="notification-content">
                                    <span class="notification-title">New message received 👋🏻</span>
                                    <span class="notification-subtitle">You have 10 unread messages</span>
                                </span>
                                <span class="notification-time">11 Aug</span>
                            </button>
                            <button type="button" class="notification-item">
                                <img width="38" height="38" alt="paypal" src="{{ asset('images/misc/paypal.png') }}">
                                <span class="notification-content">
                                    <span class="notification-title">Paypal</span>
                                    <span class="notification-subtitle">Received Payment</span>
                                </span>
                                <span class="notification-time">25 May</span>
                            </button>
                            <button type="button" class="notification-item">
                                <span class="avatar"><img alt="order" src="{{ asset('images/avatars/3.png') }}"></span>
                                <span class="notification-content">
                                    <span class="notification-title">Revised Order 📦</span>
                                    <span class="notification-subtitle">New order revised from john</span>
                                </span>
                                <span class="notification-time">19 Mar</span>
                            </button>
                            <button type="button" class="notification-item">
                                <img width="38" height="38" alt="chart" src="{{ asset('images/misc/chart.png') }}">
                                <span class="notification-content">
                                    <span class="notification-title">Finance report has been generated</span>
                                    <span class="notification-subtitle">25 hrs ago</span>
                                </span>
                                <span class="notification-time">27 Dec</span>
                            </button>
                        </div>
                        <div class="notification-footer">
                            <button type="button" class="btn btn-contained btn-full">Read All Notifications</button>
                        </div>
                    </div>
                </div>

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
