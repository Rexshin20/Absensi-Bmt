@extends('layouts.app')

@section('title', 'Icons')

@push('styles')
    <style>
        .icon-grid { display: flex; flex-wrap: wrap; margin: -12px; }
        .icon-grid-item { padding: 12px; }
        .icon-grid-item .card-content { display: flex; padding: 1.25rem; }
        .icon-grid-item .tooltip { display: inline-flex; }
        .icons-heading a { color: var(--primary-main); text-decoration: none; }
        .icons-heading a:hover { text-decoration: underline; }
    </style>
@endpush

@section('content')
    @php
        $icons = [
            'Abacus', 'Account', 'AbTesting', 'AccountBox', 'AccountCog', 'AbjadArabic', 'AbjadHebrew',
            'AbugidaThai', 'AccessPoint', 'AccountCash', 'AccountEdit', 'AccountAlert', 'AccountCheck',
            'AccountChild', 'AccountClock', 'AccountGroup', 'AccountCancel', 'AccountCircle', 'AccessPointOff',
            'AccountConvert', 'AccountDetails', 'AccessPointPlus', 'AccessPointCheck', 'AccessPointMinus',
            'AccountArrowLeft', 'AccountCowboyHat', 'AbugidaDevanagari', 'AccessPointRemove', 'AccountArrowRight',
            'AccountBoxOutline', 'AccountCogOutline', 'AccessPointNetwork', 'AccountBoxMultiple', 'AccountCashOutline',
            'AccountChildCircle', 'AccountEditOutline', 'AccountAlertOutline', 'AccountCheckOutline',
            'AccountChildOutline', 'AccountClockOutline', 'AccountCancelOutline', 'AccountCircleOutline',
            'AccessPointNetworkOff', 'AccountConvertOutline', 'AccountDetailsOutline', 'AccountArrowLeftOutline',
            'AccountArrowRightOutline', 'AccountBoxMultipleOutline',
        ];
    @endphp

    <div class="grid">
        <div class="grid-item col-12 icons-heading">
            <h5><a href="https://materialdesignicons.com/" target="_blank" rel="noreferrer">Material Design Icons</a></h5>
            <p class="text-body2">Material Design Icons from the Community</p>
        </div>

        <div class="grid-item col-12">
            <div class="icon-grid">
                @foreach ($icons as $icon)
                    <div class="icon-grid-item">
                        <span class="tooltip" data-tooltip="{{ $icon }}">
                            <div class="card">
                                <div class="card-content">
                                    <x-icon :name="$icon" />
                                </div>
                            </div>
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="grid-item col-12 text-center">
            <a class="btn btn-contained" href="https://materialdesignicons.com/" target="_blank" rel="noreferrer">
                View All Material Design Icons
            </a>
        </div>
    </div>
@endsection
