@extends('layouts.blank')

@section('title', 'Login')

@push('styles')
    <style>
        .auth-intro { text-align: center; }
        .auth-remember { display: flex; align-items: center; gap: 0.5rem; margin: 0.25rem 0 1.5rem; font-size: 0.875rem; color: var(--text-secondary); }
        .auth-footnote { margin: 0; text-align: center; font-size: 0.8125rem; color: var(--text-secondary); }
    </style>
@endpush

@section('content')
    <div class="content-center">
        <div class="card auth-card">
            <div class="auth-card-content">
                <div class="auth-brand">
                    <x-logo :size="38" />
                    <span class="brand-text">
                        <span class="brand-title">BMT Cinde Barokah</span>
                        <span class="brand-subtitle">Baitul Maal wat Tamwil</span>
                    </span>
                </div>

                <div class="auth-intro mb-6">
                    <h5 class="fw-600" style="margin-bottom: 0.375rem;">Sistem Absensi &amp; Perizinan Karyawan</h5>
                    <p class="text-body2 mb-0">Masuk untuk mengelola absensi, izin, dan cuti karyawan.</p>
                </div>

                <form method="POST" action="{{ route('login.store') }}" class="auth-form" novalidate>
                    @csrf
                    <div class="form-control">
                        <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="username" autofocus
                               placeholder=" " class="input @error('email') input-error @enderror">
                        <label class="form-label-float" for="email">Email</label>
                    </div>
                    @error('email')
                        <p class="form-helper form-error" style="margin-bottom: 1rem;">{{ $message }}</p>
                    @enderror

                    <div class="form-control">
                        <div class="input-group has-end">
                            <input id="auth-login-password" name="password" type="password" autocomplete="current-password"
                                   placeholder=" " class="input @error('password') input-error @enderror">
                            <span class="input-end">
                                <button type="button" class="icon-btn" data-toggle-password="auth-login-password" aria-label="tampilkan password">
                                    <x-icon name="EyeOffOutline" class="eye-off" />
                                    <x-icon name="EyeOutline" class="eye-on d-none" />
                                </button>
                            </span>
                        </div>
                        <label class="form-label-float" for="auth-login-password">Password</label>
                    </div>
                    @error('password')
                        <p class="form-helper form-error" style="margin-bottom: 1rem;">{{ $message }}</p>
                    @enderror

                    <label class="checkbox-label auth-remember">
                        <input type="checkbox" name="remember" value="1" @checked(old('remember'))>
                        Ingat saya
                    </label>

                    <button type="submit" class="btn btn-contained btn-lg btn-full" style="margin-bottom: 1.5rem;">Masuk</button>

                    <p class="auth-footnote">Kendala akses? Hubungi administrator BMT Cinde Barokah.</p>
                </form>
            </div>
        </div>

        <img class="illustration-tree-1" alt="tree" src="{{ asset('images/pages/auth-v1-tree.png') }}">
        <img class="illustration-mask theme-img-light" alt="mask" src="{{ asset('images/pages/auth-v1-mask-light.png') }}">
        <img class="illustration-mask theme-img-dark" alt="mask" src="{{ asset('images/pages/auth-v1-mask-dark.png') }}">
        <img class="illustration-tree-2" alt="tree-2" src="{{ asset('images/pages/auth-v1-tree-2.png') }}">
    </div>
@endsection
