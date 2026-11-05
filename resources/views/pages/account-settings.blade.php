@extends('layouts.app')

@section('title', 'Account Settings')

@push('styles')
    <style>
        .account-avatar { width: 120px; height: 120px; margin-right: 1.5625rem; border-radius: var(--radius); object-fit: cover; }
        .account-avatar-actions .btn-outlined { margin-left: 1.125rem; }
        .account-avatar-hint { margin-top: 1.25rem; }
        @media (max-width: 599px) {
            .account-avatar-actions .btn { width: 100%; text-align: center; }
            .account-avatar-actions .btn-outlined { margin-left: 0; margin-top: 1rem; }
        }

        .security-section { padding-bottom: 0 !important; }
        .security-illustration img { width: 183px; height: 256px; }
        .two-factor-head { margin-top: 0.4375rem; }
        .two-factor-wrap { margin-top: 1.4375rem; display: flex; justify-content: center; }
        .two-factor-box { max-width: 368px; display: flex; text-align: center; align-items: center; flex-direction: column; }
        .two-factor-box .avatar { width: 48px; height: 48px; border-radius: 5px; color: #fff; background-color: var(--primary-main); }
        .two-factor-box .avatar .mdi { font-size: 1.75rem; }
        .two-factor-box strong { display: block; font-weight: 600; margin-top: 0.875rem; margin-bottom: 0.875rem; }
        .security-actions { margin-top: 2.75rem; }

        .info-bio { min-height: 4.5rem !important; }
    </style>
@endpush

@section('content')
    <div class="grid">
        <div class="grid-item col-12">
            <div class="card">
                <div class="tabs" data-tabs role="tablist" aria-label="account-settings tabs">
                    <button type="button" class="tab active" data-tab="account" role="tab" aria-selected="true">
                        <x-icon name="AccountOutline" />
                        <span class="tab-name">Account</span>
                    </button>
                    <button type="button" class="tab" data-tab="security" role="tab" aria-selected="false">
                        <x-icon name="LockOpenOutline" />
                        <span class="tab-name">Security</span>
                    </button>
                    <button type="button" class="tab" data-tab="info" role="tab" aria-selected="false">
                        <x-icon name="InformationOutline" />
                        <span class="tab-name">Info</span>
                    </button>
                </div>

                {{-- Account tab --}}
                <div class="tab-panel active" data-panel="account">
                    <div class="card-content">
                        <form onsubmit="event.preventDefault();">
                            <div class="grid">
                                <div class="grid-item col-12" style="margin-top: 1.2rem; margin-bottom: 0.75rem;">
                                    <div class="d-flex align-center">
                                        <img id="account-settings-avatar" class="account-avatar" src="/images/avatars/1.png" alt="Profile Pic">
                                        <div class="account-avatar-actions">
                                            <label class="btn btn-contained" for="account-settings-upload-image">
                                                Upload New Photo
                                                <input hidden type="file" id="account-settings-upload-image" accept="image/png, image/jpeg">
                                            </label>
                                            <button type="button" class="btn btn-outlined btn-error" data-avatar-reset="/images/avatars/1.png">Reset</button>
                                            <p class="text-body2 account-avatar-hint">Allowed PNG or JPEG. Max size of 800K.</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="grid-item col-12 col-sm-6">
                                    <div class="form-control">
                                        <input id="account-username" class="input" type="text" placeholder="johnDoe" value="johnDoe">
                                        <label class="form-label-float" for="account-username">Username</label>
                                    </div>
                                </div>
                                <div class="grid-item col-12 col-sm-6">
                                    <div class="form-control">
                                        <input id="account-name" class="input" type="text" placeholder="John Doe" value="John Doe">
                                        <label class="form-label-float" for="account-name">Name</label>
                                    </div>
                                </div>
                                <div class="grid-item col-12 col-sm-6">
                                    <div class="form-control">
                                        <input id="account-email" class="input" type="email" placeholder="johnDoe@example.com" value="johnDoe@example.com">
                                        <label class="form-label-float" for="account-email">Email</label>
                                    </div>
                                </div>
                                <div class="grid-item col-12 col-sm-6">
                                    <div class="form-control">
                                        <select id="account-role" class="input">
                                            <option value="admin" selected>Admin</option>
                                            <option value="author">Author</option>
                                            <option value="editor">Editor</option>
                                            <option value="maintainer">Maintainer</option>
                                            <option value="subscriber">Subscriber</option>
                                        </select>
                                        <label class="form-label-float" for="account-role">Role</label>
                                    </div>
                                </div>
                                <div class="grid-item col-12 col-sm-6">
                                    <div class="form-control">
                                        <select id="account-status" class="input">
                                            <option value="active" selected>Active</option>
                                            <option value="inactive">Inactive</option>
                                            <option value="pending">Pending</option>
                                        </select>
                                        <label class="form-label-float" for="account-status">Status</label>
                                    </div>
                                </div>
                                <div class="grid-item col-12 col-sm-6">
                                    <div class="form-control">
                                        <input id="account-company" class="input" type="text" placeholder="ABC Pvt. Ltd." value="ABC Pvt. Ltd.">
                                        <label class="form-label-float" for="account-company">Company</label>
                                    </div>
                                </div>

                                <div class="grid-item col-12" style="margin-bottom: 0.75rem;">
                                    <div class="alert alert-warning" role="alert">
                                        <div class="alert-icon"><x-icon name="AlertOutline" class="alert-warning-icon" /></div>
                                        <div class="alert-message">
                                            <div class="alert-title">Your email is not confirmed. Please check your inbox.</div>
                                            <a href="/" onclick="event.preventDefault();">Resend Confirmation</a>
                                        </div>
                                        <div class="alert-action">
                                            <button type="button" class="icon-btn icon-btn-sm" data-alert-close aria-label="close">
                                                <x-icon name="Close" />
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="grid-item col-12">
                                    <button type="submit" class="btn btn-contained" style="margin-right: 0.875rem;">Save Changes</button>
                                    <button type="reset" class="btn btn-outlined btn-secondary">Reset</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Security tab --}}
                <div class="tab-panel" data-panel="security">
                    <form onsubmit="event.preventDefault();">
                        <div class="card-content security-section">
                            <div class="grid">
                                <div class="grid-item col-12 col-sm-6">
                                    <div class="grid">
                                        <div class="grid-item col-12" style="margin-top: 1.1875rem;">
                                            <div class="form-control">
                                                <div class="input-group has-end">
                                                    <input id="account-settings-current-password" class="input" type="password" placeholder=" ">
                                                    <span class="input-end">
                                                        <button type="button" class="icon-btn" data-toggle-password="account-settings-current-password" aria-label="toggle password visibility">
                                                            <x-icon name="EyeOffOutline" class="eye-off" />
                                                            <x-icon name="EyeOutline" class="eye-on d-none" />
                                                        </button>
                                                    </span>
                                                </div>
                                                <label class="form-label-float" for="account-settings-current-password">Current Password</label>
                                            </div>
                                        </div>
                                        <div class="grid-item col-12" style="margin-top: 1.5rem;">
                                            <div class="form-control">
                                                <div class="input-group has-end">
                                                    <input id="account-settings-new-password" class="input" type="password" placeholder=" ">
                                                    <span class="input-end">
                                                        <button type="button" class="icon-btn" data-toggle-password="account-settings-new-password" aria-label="toggle password visibility">
                                                            <x-icon name="EyeOffOutline" class="eye-off" />
                                                            <x-icon name="EyeOutline" class="eye-on d-none" />
                                                        </button>
                                                    </span>
                                                </div>
                                                <label class="form-label-float" for="account-settings-new-password">New Password</label>
                                            </div>
                                        </div>
                                        <div class="grid-item col-12">
                                            <div class="form-control">
                                                <div class="input-group has-end">
                                                    <input id="account-settings-confirm-new-password" class="input" type="password" placeholder=" ">
                                                    <span class="input-end">
                                                        <button type="button" class="icon-btn" data-toggle-password="account-settings-confirm-new-password" aria-label="toggle password visibility">
                                                            <x-icon name="EyeOffOutline" class="eye-off" />
                                                            <x-icon name="EyeOutline" class="eye-on d-none" />
                                                        </button>
                                                    </span>
                                                </div>
                                                <label class="form-label-float" for="account-settings-confirm-new-password">Confirm New Password</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="grid-item col-12 col-sm-6 d-flex align-center justify-center security-illustration" style="margin-top: 1.875rem;">
                                    <img alt="avatar" width="183" height="256" src="/images/pages/pose-m-1.png">
                                </div>
                            </div>
                        </div>

                        <div class="divider" style="margin: 0;"></div>

                        <div class="card-content">
                            <div class="d-flex align-center two-factor-head">
                                <x-icon name="KeyOutline" class="mr-3" />
                                <h6>Two-factor authentication</h6>
                            </div>

                            <div class="two-factor-wrap">
                                <div class="two-factor-box">
                                    <span class="avatar"><x-icon name="LockOpenOutline" /></span>
                                    <strong>Two factor authentication is not enabled yet.</strong>
                                    <p class="text-body2">
                                        Two-factor authentication adds an additional layer of security to your account by requiring more than just
                                        a password to log in. Learn more.
                                    </p>
                                </div>
                            </div>

                            <div class="security-actions">
                                <button type="submit" class="btn btn-contained" style="margin-right: 0.875rem;">Save Changes</button>
                                <button type="reset" class="btn btn-outlined btn-secondary">Reset</button>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- Info tab --}}
                <div class="tab-panel" data-panel="info">
                    <div class="card-content">
                        <form onsubmit="event.preventDefault();">
                            <div class="grid">
                                <div class="grid-item col-12" style="margin-top: 1.2rem;">
                                    <div class="form-control">
                                        <textarea id="account-settings-bio" class="input info-bio" placeholder="Bio">The name&rsquo;s John Deo. I am a tireless seeker of knowledge, occasional purveyor of wisdom and also, coincidentally, a graphic designer. Algolia helps businesses across industries quickly create relevant &#128526;, scalable &#128512;, and lightning &#128525; fast search and discovery experiences.</textarea>
                                        <label class="form-label-float" for="account-settings-bio">Bio</label>
                                    </div>
                                </div>
                                <div class="grid-item col-12 col-sm-6">
                                    <div class="form-control">
                                        <input id="account-settings-date" class="input" type="date">
                                        <label class="form-label-float" for="account-settings-date">Birth Date</label>
                                    </div>
                                </div>
                                <div class="grid-item col-12 col-sm-6">
                                    <div class="form-control">
                                        <input id="account-settings-phone" class="input" type="number" placeholder="(123) 456-7890">
                                        <label class="form-label-float" for="account-settings-phone">Phone</label>
                                    </div>
                                </div>
                                <div class="grid-item col-12 col-sm-6">
                                    <div class="form-control">
                                        <input id="account-settings-website" class="input" type="text" placeholder="https://example.com/" value="https://themeselection.com/">
                                        <label class="form-label-float" for="account-settings-website">Website</label>
                                    </div>
                                </div>
                                <div class="grid-item col-12 col-sm-6">
                                    <div class="form-control">
                                        <select id="account-settings-country" class="input">
                                            <option value="USA" selected>USA</option>
                                            <option value="UK">UK</option>
                                            <option value="Australia">Australia</option>
                                            <option value="Germany">Germany</option>
                                        </select>
                                        <label class="form-label-float" for="account-settings-country">Country</label>
                                    </div>
                                </div>
                                <div class="grid-item col-12 col-sm-6">
                                    <div class="form-control">
                                        <select id="account-settings-multiple-select" class="input" multiple size="4">
                                            <option value="English" selected>English</option>
                                            <option value="French">French</option>
                                            <option value="Spanish">Spanish</option>
                                            <option value="Portuguese">Portuguese</option>
                                            <option value="Italian">Italian</option>
                                            <option value="German">German</option>
                                            <option value="Arabic">Arabic</option>
                                        </select>
                                        <label class="form-label-float" for="account-settings-multiple-select">Languages</label>
                                    </div>
                                </div>
                                <div class="grid-item col-12 col-sm-6">
                                    <span class="form-label-legend">Gender</span>
                                    <div class="radio-group">
                                        <label class="radio-label"><input type="radio" name="account-settings-info-radio" value="male" checked> Male</label>
                                        <label class="radio-label"><input type="radio" name="account-settings-info-radio" value="female"> Female</label>
                                        <label class="radio-label"><input type="radio" name="account-settings-info-radio" value="other"> Other</label>
                                    </div>
                                </div>
                                <div class="grid-item col-12">
                                    <button type="submit" class="btn btn-contained" style="margin-right: 0.875rem;">Save Changes</button>
                                    <button type="reset" class="btn btn-outlined btn-secondary">Reset</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
