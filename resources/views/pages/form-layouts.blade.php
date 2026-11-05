@extends('layouts.app')

@section('title', 'Form Layouts')

@push('styles')
    <style>
        .inline-check { display: inline-flex; align-items: center; gap: 0.5rem; }
        .stack > * + * { margin-top: 1.25rem; }
        .separator-form .divider { margin: 0; }
        .payment-option { display: flex; align-items: center; gap: 0.75rem; }
        .payment-option .mdi { color: var(--text-secondary); }
    </style>
@endpush

@section('content')
    <div class="grid">
        {{-- Basic --}}
        <div class="grid-item col-12 col-md-6">
            <div class="card">
                <div class="card-header"><div class="card-header-title" style="font-size: 1.25rem;">Basic</div></div>
                <div class="card-content">
                    <form onsubmit="event.preventDefault();" class="stack">
                        <div class="form-control">
                            <input id="basic-name" class="input" type="text" placeholder="Leonard Carter">
                            <label class="form-label-float" for="basic-name">Name</label>
                        </div>
                        <div>
                            <div class="form-control">
                                <input id="basic-email" class="input" type="email" placeholder="carterleonard@gmail.com">
                                <label class="form-label-float" for="basic-email">Email</label>
                            </div>
                            <p class="form-helper">You can use letters, numbers &amp; periods</p>
                        </div>
                        <div>
                            <div class="form-control">
                                <div class="input-group has-end">
                                    <input id="form-layouts-basic-password" class="input" type="password" placeholder=" ">
                                    <span class="input-end">
                                        <button type="button" class="icon-btn" data-toggle-password="form-layouts-basic-password" aria-label="show password">
                                            <x-icon name="EyeOffOutline" class="eye-off" />
                                            <x-icon name="EyeOutline" class="eye-on d-none" />
                                        </button>
                                    </span>
                                </div>
                                <label class="form-label-float" for="form-layouts-basic-password">Password</label>
                            </div>
                            <p class="form-helper">Use 8 or more characters with a mix of letters, numbers &amp; symbols</p>
                        </div>
                        <div>
                            <div class="form-control">
                                <div class="input-group has-end">
                                    <input id="form-layouts-confirm-password" class="input" type="password" placeholder=" ">
                                    <span class="input-end">
                                        <button type="button" class="icon-btn" data-toggle-password="form-layouts-confirm-password" aria-label="show password">
                                            <x-icon name="EyeOffOutline" class="eye-off" />
                                            <x-icon name="EyeOutline" class="eye-on d-none" />
                                        </button>
                                    </span>
                                </div>
                                <label class="form-label-float" for="form-layouts-confirm-password">Confirm Password</label>
                            </div>
                            <p class="form-helper">Make sure to type the same password as above</p>
                        </div>
                        <div class="d-flex flex-wrap align-center justify-between gap-4">
                            <button type="submit" class="btn btn-contained btn-lg">Get Started!</button>
                            <span class="d-flex align-center">
                                <span class="mr-2">Already have an account?</span>
                                <a href="{{ route('login') }}" target="_blank">Log in</a>
                            </span>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Basic with icons --}}
        <div class="grid-item col-12 col-md-6">
            <div class="card">
                <div class="card-header"><div class="card-header-title" style="font-size: 1.25rem;">Basic with Icons</div></div>
                <div class="card-content">
                    <form onsubmit="event.preventDefault();" class="stack">
                        <div class="form-control">
                            <div class="input-group">
                                <span class="input-icon"><x-icon name="AccountOutline" /></span>
                                <input id="icons-name" class="input" type="text" placeholder="Leonard Carter">
                            </div>
                            <label class="form-label-float" for="icons-name">Full Name</label>
                        </div>
                        <div>
                            <div class="form-control">
                                <div class="input-group">
                                    <span class="input-icon"><x-icon name="EmailOutline" /></span>
                                    <input id="icons-email" class="input" type="email" placeholder="carterleonard@gmail.com">
                                </div>
                                <label class="form-label-float" for="icons-email">Email</label>
                            </div>
                            <p class="form-helper">You can use letters, numbers &amp; periods</p>
                        </div>
                        <div class="form-control">
                            <div class="input-group">
                                <span class="input-icon"><x-icon name="Phone" /></span>
                                <input id="icons-phone" class="input" type="number" placeholder="+1-123-456-8790">
                            </div>
                            <label class="form-label-float" for="icons-phone">Phone No.</label>
                        </div>
                        <div class="form-control">
                            <div class="input-group" style="align-items: flex-start;">
                                <span class="input-icon" style="top: 0.625rem;"><x-icon name="MessageOutline" /></span>
                                <textarea id="icons-message" class="input" rows="3" placeholder="Bio..." style="min-height: 6rem;"></textarea>
                            </div>
                            <label class="form-label-float" for="icons-message">Message</label>
                        </div>
                        <button type="submit" class="btn btn-contained btn-lg">Submit</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Multi column with separator --}}
        <div class="grid-item col-12">
            <div class="card separator-form">
                <div class="card-header"><div class="card-header-title" style="font-size: 1.25rem;">Multi Column with Form Separator</div></div>
                <hr class="divider">
                <form onsubmit="event.preventDefault();">
                    <div class="card-content">
                        <div class="grid">
                            <div class="grid-item col-12"><span class="section-label">1. Account Details</span></div>
                            <div class="grid-item col-12 col-sm-6">
                                <div class="form-control">
                                    <input class="input" type="text" placeholder="carterLeonard" aria-label="Username">
                                    <label class="form-label-float">Username</label>
                                </div>
                            </div>
                            <div class="grid-item col-12 col-sm-6">
                                <div class="form-control">
                                    <input class="input" type="email" placeholder="carterleonard@gmail.com" aria-label="Email">
                                    <label class="form-label-float">Email</label>
                                </div>
                            </div>
                            <div class="grid-item col-12 col-sm-6">
                                <div class="form-control">
                                    <div class="input-group has-end">
                                        <input id="form-layouts-separator-password" class="input" type="password" placeholder=" ">
                                        <span class="input-end">
                                            <button type="button" class="icon-btn" data-toggle-password="form-layouts-separator-password" aria-label="show password">
                                                <x-icon name="EyeOffOutline" class="eye-off" />
                                                <x-icon name="EyeOutline" class="eye-on d-none" />
                                            </button>
                                        </span>
                                    </div>
                                    <label class="form-label-float" for="form-layouts-separator-password">Password</label>
                                </div>
                            </div>
                            <div class="grid-item col-12 col-sm-6">
                                <div class="form-control">
                                    <div class="input-group has-end">
                                        <input id="form-layouts-separator-password-2" class="input" type="password" placeholder=" ">
                                        <span class="input-end">
                                            <button type="button" class="icon-btn" data-toggle-password="form-layouts-separator-password-2" aria-label="show password">
                                                <x-icon name="EyeOffOutline" class="eye-off" />
                                                <x-icon name="EyeOutline" class="eye-on d-none" />
                                            </button>
                                        </span>
                                    </div>
                                    <label class="form-label-float" for="form-layouts-separator-password-2">Confirm Password</label>
                                </div>
                            </div>
                            <div class="grid-item col-12"><hr class="divider" style="margin: 0;"></div>
                            <div class="grid-item col-12"><span class="section-label">2. Personal Info</span></div>
                            <div class="grid-item col-12 col-sm-6">
                                <div class="form-control">
                                    <input class="input" type="text" placeholder="Leonard" aria-label="First Name">
                                    <label class="form-label-float">First Name</label>
                                </div>
                            </div>
                            <div class="grid-item col-12 col-sm-6">
                                <div class="form-control">
                                    <input class="input" type="text" placeholder="Carter" aria-label="Last Name">
                                    <label class="form-label-float">Last Name</label>
                                </div>
                            </div>
                            <div class="grid-item col-12 col-sm-6">
                                <div class="form-control">
                                    <select class="input" aria-label="Country">
                                        <option value="" disabled selected></option>
                                        <option>UK</option>
                                        <option>USA</option>
                                        <option>Australia</option>
                                        <option>Germany</option>
                                    </select>
                                    <label class="form-label-float">Country</label>
                                </div>
                            </div>
                            <div class="grid-item col-12 col-sm-6">
                                <div class="form-control">
                                    <select class="input" multiple size="3" aria-label="Language">
                                        <option selected>English</option>
                                        <option>French</option>
                                        <option>Spanish</option>
                                        <option>Portuguese</option>
                                        <option>Italian</option>
                                        <option>German</option>
                                        <option>Arabic</option>
                                    </select>
                                    <label class="form-label-float">Languages</label>
                                </div>
                            </div>
                            <div class="grid-item col-12 col-sm-6">
                                <div class="form-control">
                                    <input class="input" type="date" aria-label="Birth Date">
                                    <label class="form-label-float">Birth Date</label>
                                </div>
                            </div>
                            <div class="grid-item col-12 col-sm-6">
                                <div class="form-control">
                                    <input class="input" type="tel" placeholder="+1-123-456-8790" aria-label="Phone No.">
                                    <label class="form-label-float">Phone No.</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr class="divider">
                    <div class="card-actions">
                        <button type="submit" class="btn btn-contained btn-lg mr-2">Submit</button>
                        <button type="button" class="btn btn-outlined btn-lg btn-secondary">Cancel</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Form alignment --}}
        <div class="grid-item col-12">
            <div class="card">
                <div class="card-header"><div class="card-header-title" style="font-size: 1.25rem;">Form Alignment</div></div>
                <div class="card-content" style="min-height: 500px; display: flex; align-items: center; justify-content: center;">
                    <form onsubmit="event.preventDefault();"
                          style="max-width: 400px; width: 100%; padding: 3rem; border-radius: var(--radius); border: 1px solid var(--divider);">
                        <div class="grid">
                            <div class="grid-item col-12" style="margin-bottom: 1.25rem;"><h5>Sign In</h5></div>
                            <div class="grid-item col-12">
                                <div class="form-control">
                                    <input class="input" type="text" placeholder="carterLeonard" aria-label="Username">
                                    <label class="form-label-float">Username</label>
                                </div>
                            </div>
                            <div class="grid-item col-12">
                                <div class="form-control">
                                    <div class="input-group has-end">
                                        <input id="form-layouts-alignment-password" class="input" type="password" placeholder=" ">
                                        <span class="input-end">
                                            <button type="button" class="icon-btn" data-toggle-password="form-layouts-alignment-password" aria-label="show password">
                                                <x-icon name="EyeOffOutline" class="eye-off" />
                                                <x-icon name="EyeOutline" class="eye-on d-none" />
                                            </button>
                                        </span>
                                    </div>
                                    <label class="form-label-float" for="form-layouts-alignment-password">Password</label>
                                </div>
                            </div>
                            <div class="grid-item col-12">
                                <label class="checkbox-label"><input type="checkbox"> Remember me</label>
                            </div>
                            <div class="grid-item col-12">
                                <button type="submit" class="btn btn-contained btn-lg btn-full">Login</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
