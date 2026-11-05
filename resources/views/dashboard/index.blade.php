@extends('layouts.app')

@section('title', 'Dashboard')

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            window.MaterioCharts.weekly(document.querySelector('#weekly-overview-chart'));
        });
    </script>
@endpush

@section('content')
    <div class="grid">
        {{-- Trophy --}}
        <div class="grid-item col-12 col-md-4">
            <div class="card position-relative">
                <div class="card-content">
                    <h6>Congratulations John! 🥳</h6>
                    <p class="text-body2" style="letter-spacing: 0.25px;">Best seller of the month</p>
                    <h5 class="text-main" style="margin: 1rem 0;">$42.8k</h5>
                    <button type="button" class="btn btn-contained btn-sm">View Sales</button>
                    <img class="position-absolute theme-img-light" alt="triangle background"
                         src="{{ asset('images/misc/triangle-light.png') }}"
                         style="right: 0; bottom: 0; height: 170px;">
                    <img class="position-absolute theme-img-dark" alt="triangle background"
                         src="{{ asset('images/misc/triangle-dark.png') }}"
                         style="right: 0; bottom: 0; height: 170px;">
                    <img class="position-absolute" alt="trophy" src="{{ asset('images/misc/trophy.png') }}"
                         style="right: 36px; bottom: 20px; height: 98px;">
                </div>
            </div>
        </div>

        {{-- Statistics Card --}}
        <div class="grid-item col-12 col-md-8">
            <div class="card">
                <div class="card-header">
                    <div>
                        <div class="card-header-title" style="margin-bottom: 0.625rem;">Statistics Card</div>
                        <p class="card-header-subheader">
                            <span class="fw-600 text-primary-color">Total 48.5% growth</span> 😎 this month
                        </p>
                    </div>
                    <button type="button" class="icon-btn icon-btn-sm card-more-options" aria-label="settings">
                        <x-icon name="DotsVertical" size="20" />
                    </button>
                </div>
                <div class="card-content" style="padding-top: 0.75rem;">
                    <div class="grid">
                        @foreach ([
                            ['stats' => '245k', 'title' => 'Sales', 'color' => 'primary', 'icon' => 'TrendingUp'],
                            ['stats' => '12.5k', 'title' => 'Customers', 'color' => 'success', 'icon' => 'AccountOutline'],
                            ['stats' => '1.54k', 'title' => 'Products', 'color' => 'warning', 'icon' => 'CellphoneLink'],
                            ['stats' => '$88k', 'title' => 'Revenue', 'color' => 'info', 'icon' => 'CurrencyUsd'],
                        ] as $stat)
                            <div class="grid-item col-12 col-sm-3" style="padding-top: 0; padding-bottom: 0;">
                                <div class="d-flex align-center">
                                    <span class="avatar avatar-rounded mr-3"
                                          style="width: 44px; height: 44px; box-shadow: var(--shadow-3); color: #fff; background-color: var(--{{ $stat['color'] }}-main);">
                                        <x-icon :name="$stat['icon']" size="28" />
                                    </span>
                                    <div class="d-flex flex-column">
                                        <span class="text-caption">{{ $stat['title'] }}</span>
                                        <h6>{{ $stat['stats'] }}</h6>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Weekly Overview --}}
        <div class="grid-item col-12 col-md-6 col-lg-4">
            <div class="card">
                <div class="card-header">
                    <div class="card-header-title" style="line-height: 2rem; letter-spacing: 0.15px;">Weekly Overview</div>
                    <button type="button" class="icon-btn icon-btn-sm card-more-options" aria-label="settings">
                        <x-icon name="DotsVertical" size="20" />
                    </button>
                </div>
                <div class="card-content">
                    <div id="weekly-overview-chart"></div>
                    <div class="d-flex align-center" style="margin-bottom: 1.75rem;">
                        <h5 class="mr-4">45%</h5>
                        <p class="text-body2">Your sales performance is 45% 😎 better compared to last month</p>
                    </div>
                    <button type="button" class="btn btn-contained btn-full">Details</button>
                </div>
            </div>
        </div>

        {{-- Total Earning --}}
        <div class="grid-item col-12 col-md-6 col-lg-4">
            <div class="card">
                <div class="card-header">
                    <div class="card-header-title" style="line-height: 1.6; letter-spacing: 0.15px;">Total Earning</div>
                    <button type="button" class="icon-btn icon-btn-sm card-more-options" aria-label="settings">
                        <x-icon name="DotsVertical" size="20" />
                    </button>
                </div>
                <div class="card-content" style="padding-top: 0.5625rem;">
                    <div class="d-flex align-center" style="margin-bottom: 0.375rem;">
                        <h4 class="fw-600" style="font-size: 2.125rem; margin-right: 0.5rem;">$24,895</h4>
                        <div class="d-flex align-center text-success">
                            <x-icon name="MenuUp" style="font-size: 1.875rem;" />
                            <span class="text-body2 fw-600 text-success">10%</span>
                        </div>
                    </div>
                    <p class="text-caption" style="margin-bottom: 2.5rem;">Compared to $84,325 last year</p>

                    @foreach ([
                        ['title' => 'Zipcar', 'subtitle' => 'Vuejs, React & HTML', 'amount' => '$24,895.65', 'progress' => 75, 'color' => 'primary', 'img' => 'logo-zipcar.png', 'height' => 20],
                        ['title' => 'Bitbank', 'subtitle' => 'Sketch, Figma & XD', 'amount' => '$8,650.20', 'progress' => 50, 'color' => 'info', 'img' => 'logo-bitbank.png', 'height' => 27],
                        ['title' => 'Aviato', 'subtitle' => 'HTML & Angular', 'amount' => '$1,245.80', 'progress' => 20, 'color' => 'secondary', 'img' => 'logo-aviato.png', 'height' => 20],
                    ] as $item)
                        <div class="d-flex align-center" style="{{ $loop->last ? '' : 'margin-bottom: 2.125rem;' }}">
                            <span class="avatar avatar-rounded mr-3" style="width: 40px; height: 40px; background-color: var(--action-hover);">
                                <img src="{{ asset('images/cards/' . $item['img']) }}" alt="{{ $item['title'] }}" height="{{ $item['height'] }}" style="width: auto;">
                            </span>
                            <div class="d-flex flex-wrap align-center justify-between w-100">
                                <div class="d-flex flex-column mr-2">
                                    <span class="text-body2 fw-600 text-primary-color" style="margin-bottom: 0.125rem;">{{ $item['title'] }}</span>
                                    <span class="text-caption">{{ $item['subtitle'] }}</span>
                                </div>
                                <div class="d-flex flex-column" style="min-width: 85px;">
                                    <span class="text-body2 fw-600 text-primary-color" style="margin-bottom: 0.5rem;">{{ $item['amount'] }}</span>
                                    <div class="linear-progress progress-{{ $item['color'] }}">
                                        <div class="bar" style="width: {{ $item['progress'] }}%;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Vertical statistics cards --}}
        <div class="grid-item col-12 col-md-6 col-lg-4">
            <div class="grid">
                @foreach ([
                    ['stats' => '$25.6k', 'icon' => 'Poll', 'color' => 'success', 'trend' => 'positive', 'trendNumber' => '+42%', 'title' => 'Total Profit', 'subtitle' => 'Weekly Profit'],
                    ['stats' => '$78', 'icon' => 'CurrencyUsd', 'color' => 'secondary', 'trend' => 'negative', 'trendNumber' => '-15%', 'title' => 'Refunds', 'subtitle' => 'Past Month'],
                    ['stats' => '862', 'icon' => 'BriefcaseVariantOutline', 'color' => 'primary', 'trend' => 'negative', 'trendNumber' => '-18%', 'title' => 'New Project', 'subtitle' => 'Yearly Project'],
                    ['stats' => '15', 'icon' => 'HelpCircleOutline', 'color' => 'warning', 'trend' => 'negative', 'trendNumber' => '-18%', 'title' => 'Sales Queries', 'subtitle' => 'Last Week'],
                ] as $stat)
                    <div class="grid-item col-6">
                        <div class="card">
                            <div class="card-content">
                                <div class="d-flex align-start justify-between" style="margin-bottom: 1.375rem;">
                                    <span class="avatar mr-4"
                                          style="box-shadow: var(--shadow-3); color: #fff; background-color: var(--{{ $stat['color'] }}-main);">
                                        <x-icon :name="$stat['icon']" />
                                    </span>
                                    <button type="button" class="icon-btn icon-btn-sm card-more-options" aria-label="settings">
                                        <x-icon name="DotsVertical" size="20" />
                                    </button>
                                </div>
                                <span class="fw-600" style="font-size: 0.875rem;">{{ $stat['title'] }}</span>
                                <div class="d-flex flex-wrap align-start" style="margin-top: 0.375rem; margin-bottom: 0.375rem;">
                                    <h6 class="mr-2">{{ $stat['stats'] }}</h6>
                                    <sup class="text-caption {{ $stat['trend'] === 'positive' ? 'text-success' : 'text-error' }}">{{ $stat['trendNumber'] }}</sup>
                                </div>
                                <span class="text-caption">{{ $stat['subtitle'] }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Sales by Countries --}}
        <div class="grid-item col-12 col-md-6 col-lg-4">
            <div class="card">
                <div class="card-header">
                    <div class="card-header-title" style="line-height: 1.2; letter-spacing: 0.31px;">Sales by Countries</div>
                    <button type="button" class="icon-btn icon-btn-sm card-more-options" aria-label="settings">
                        <x-icon name="DotsVertical" size="20" />
                    </button>
                </div>
                <div class="card-content" style="padding-top: 0.5rem;">
                    @foreach ([
                        ['sales' => '894k', 'subtitle' => 'USA', 'title' => '$8,656k', 'trendDir' => 'up', 'trendNumber' => '25.8%', 'avatarColor' => 'success', 'avatarText' => 'US'],
                        ['sales' => '645k', 'subtitle' => 'UK', 'title' => '$2,415k', 'trendDir' => 'down', 'trendNumber' => '6.2%', 'avatarColor' => 'error', 'avatarText' => 'UK'],
                        ['sales' => '148k', 'subtitle' => 'India', 'title' => '$865k', 'trendDir' => 'up', 'trendNumber' => '12.4%', 'avatarColor' => 'warning', 'avatarText' => 'IN'],
                        ['sales' => '86k', 'subtitle' => 'Japan', 'title' => '$745k', 'trendDir' => 'down', 'trendNumber' => '11.9%', 'avatarColor' => 'secondary', 'avatarText' => 'JA'],
                        ['sales' => '42k', 'subtitle' => 'Korea', 'title' => '$45k', 'trendDir' => 'up', 'trendNumber' => '16.2%', 'avatarColor' => 'error', 'avatarText' => 'KO'],
                    ] as $item)
                        <div class="d-flex align-center" style="{{ $loop->last ? '' : 'margin-bottom: 1.46875rem;' }}">
                            <span class="avatar mr-3"
                                  style="width: 38px; height: 38px; font-size: 1rem; color: #fff; background-color: var(--{{ $item['avatarColor'] }}-main);">{{ $item['avatarText'] }}</span>
                            <div class="d-flex flex-wrap align-center justify-between w-100">
                                <div class="d-flex flex-column mr-2">
                                    <div class="d-flex">
                                        <span class="fw-600 mr-1" style="letter-spacing: 0.25px;">{{ $item['title'] }}</span>
                                        <span class="d-flex align-center">
                                            <x-icon :name="$item['trendDir'] === 'up' ? 'ChevronUp' : 'ChevronDown'"
                                                    class="{{ $item['trendDir'] === 'up' ? 'text-success' : 'text-error' }}" />
                                            <span class="text-caption fw-600 {{ $item['trendDir'] === 'down' ? 'text-error' : 'text-success' }}">{{ $item['trendNumber'] }}</span>
                                        </span>
                                    </div>
                                    <span class="text-caption" style="line-height: 1.5;">{{ $item['subtitle'] }}</span>
                                </div>
                                <div class="d-flex flex-column text-end">
                                    <span class="fw-600" style="font-size: 0.875rem; line-height: 1.72; letter-spacing: 0.22px;">{{ $item['sales'] }}</span>
                                    <span class="text-caption" style="line-height: 1.5;">Sales</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Deposit / Withdraw --}}
        <div class="grid-item col-12 col-md-12 col-lg-8">
            <div class="card" style="display: flex; justify-content: space-between; flex-direction: row; flex-wrap: wrap;">
                <div style="width: 100%; flex: 1 1 45%;">
                    <div class="card-header" style="padding-top: 1.375rem; align-items: center;">
                        <div class="card-header-title" style="font-size: 1.25rem; line-height: 1.6; letter-spacing: 0.15px;">Deposit</div>
                        <span class="text-caption">View All</span>
                    </div>
                    <div class="card-content" style="padding-bottom: 1.375rem;">
                        @foreach ([
                            ['amount' => '+$4,650', 'subtitle' => 'Sell UI Kit', 'title' => 'Gumroad Account', 'logo' => 'gumroad.png', 'w' => 28, 'h' => 29],
                            ['amount' => '+$92,705', 'subtitle' => 'Wallet deposit', 'title' => 'Mastercard', 'logo' => 'mastercard-label.png', 'w' => 38, 'h' => 38],
                            ['amount' => '+$957', 'subtitle' => 'iOS Application', 'title' => 'Stripe Account', 'logo' => 'stripe.png', 'w' => 20, 'h' => 28],
                            ['amount' => '+$6,837', 'subtitle' => 'Bank Transfer', 'title' => 'American Bank', 'logo' => 'american-bank.png', 'w' => 34, 'h' => 32],
                            ['amount' => '+$446', 'subtitle' => 'Wallet deposit', 'title' => 'Bank Account', 'logo' => 'citi-bank.png', 'w' => 33, 'h' => 22],
                        ] as $item)
                            <div class="d-flex align-center" style="{{ $loop->last ? '' : 'margin-bottom: 1.5rem;' }}">
                                <div class="d-flex justify-center" style="min-width: 38px;">
                                    <img src="{{ asset('images/logos/' . $item['logo']) }}" alt="{{ $item['title'] }}" width="{{ $item['w'] }}" height="{{ $item['h'] }}">
                                </div>
                                <div class="d-flex flex-wrap align-center justify-between w-100 ml-4">
                                    <div class="d-flex flex-column mr-2">
                                        <span class="fw-600" style="font-size: 0.875rem;">{{ $item['title'] }}</span>
                                        <span class="text-caption">{{ $item['subtitle'] }}</span>
                                    </div>
                                    <span class="text-subtitle2 text-success fw-600">{{ $item['amount'] }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <hr class="divider-vertical" style="margin: 1.25rem 0;">

                <div style="width: 100%; flex: 1 1 45%;">
                    <div class="card-header" style="padding-top: 1.375rem; align-items: center;">
                        <div class="card-header-title" style="font-size: 1.25rem; line-height: 1.6; letter-spacing: 0.15px;">Withdraw</div>
                        <span class="text-caption">View All</span>
                    </div>
                    <div class="card-content" style="padding-bottom: 1.375rem;">
                        @foreach ([
                            ['amount' => '-$145', 'subtitle' => 'Paypal deposit', 'title' => 'Google Adsense', 'logo' => 'google.png', 'w' => 29, 'h' => 30],
                            ['amount' => '-$1870', 'subtitle' => 'Security & compliance', 'title' => 'Github Enterprise', 'logo' => 'github.png', 'w' => 34, 'h' => 34],
                            ['amount' => '-$450', 'subtitle' => 'Debit card deposit', 'title' => 'Upgrade Slack Plan', 'logo' => 'slack.png', 'w' => 30, 'h' => 30],
                            ['amount' => '-$540', 'subtitle' => 'Cloud Hosting', 'title' => 'Digital Ocean', 'logo' => 'digital-ocean.png', 'w' => 30, 'h' => 30],
                            ['amount' => '-$21', 'subtitle' => 'Choosing a Cloud Platform', 'title' => 'AWS Account', 'logo' => 'aws.png', 'w' => 36, 'h' => 21],
                        ] as $item)
                            <div class="d-flex align-center" style="{{ $loop->last ? '' : 'margin-bottom: 1.5rem;' }}">
                                <div class="d-flex justify-center" style="min-width: 36px;">
                                    <img src="{{ asset('images/logos/' . $item['logo']) }}" alt="{{ $item['title'] }}" width="{{ $item['w'] }}" height="{{ $item['h'] }}">
                                </div>
                                <div class="d-flex flex-wrap align-center justify-between w-100 ml-4">
                                    <div class="d-flex flex-column mr-2">
                                        <span class="fw-600" style="font-size: 0.875rem;">{{ $item['title'] }}</span>
                                        <span class="text-caption">{{ $item['subtitle'] }}</span>
                                    </div>
                                    <span class="text-subtitle2 text-error fw-600">{{ $item['amount'] }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Dashboard table --}}
        <div class="grid-item col-12">
            <div class="card">
                <div class="table-wrapper">
                    <table class="table" style="min-width: 800px;" aria-label="table in dashboard">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Date</th>
                                <th>Salary</th>
                                <th>Age</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ([
                                ['age' => 27, 'status' => 'current', 'date' => '09/27/2018', 'name' => 'Sally Quinn', 'salary' => '$19586.23', 'email' => 'eebsworth2m@sbwire.com', 'designation' => 'Human Resources Assistant'],
                                ['age' => 61, 'date' => '09/23/2016', 'salary' => '$23896.35', 'status' => 'professional', 'name' => 'Margaret Bowers', 'email' => 'kocrevy0@thetimes.co.uk', 'designation' => 'Nuclear Power Engineer'],
                                ['age' => 59, 'date' => '10/15/2017', 'name' => 'Minnie Roy', 'status' => 'rejected', 'salary' => '$18991.67', 'email' => 'ediehn6@163.com', 'designation' => 'Environmental Specialist'],
                                ['age' => 30, 'date' => '06/12/2018', 'status' => 'resigned', 'salary' => '$19252.12', 'name' => 'Ralph Leonard', 'email' => 'dfalloona@ifeng.com', 'designation' => 'Sales Representative'],
                                ['age' => 66, 'status' => 'applied', 'date' => '03/24/2018', 'salary' => '$13076.28', 'name' => 'Annie Martin', 'designation' => 'Operator', 'email' => 'sganderton2@tuttocitta.it'],
                                ['age' => 33, 'date' => '08/25/2017', 'salary' => '$10909.52', 'name' => 'Adeline Day', 'status' => 'professional', 'email' => 'hnisius4@gnu.org', 'designation' => 'Senior Cost Accountant'],
                                ['age' => 61, 'status' => 'current', 'date' => '06/01/2017', 'salary' => '$17803.80', 'name' => 'Lora Jackson', 'designation' => 'Geologist', 'email' => 'ghoneywood5@narod.ru'],
                                ['age' => 22, 'date' => '12/03/2017', 'salary' => '$12336.17', 'name' => 'Rodney Sharp', 'status' => 'professional', 'designation' => 'Cost Accountant', 'email' => 'dcrossman3@google.co.jp'],
                            ] as $row)
                                @php
                                    $statusColor = [
                                        'applied' => 'info',
                                        'rejected' => 'error',
                                        'current' => 'primary',
                                        'resigned' => 'warning',
                                        'professional' => 'success',
                                    ][$row['status']];
                                @endphp
                                <tr>
                                    <td style="padding-top: 0.125rem; padding-bottom: 0.125rem;">
                                        <div class="d-flex flex-column">
                                            <span class="fw-500" style="font-size: 0.875rem;">{{ $row['name'] }}</span>
                                            <span class="text-caption">{{ $row['designation'] }}</span>
                                        </div>
                                    </td>
                                    <td>{{ $row['email'] }}</td>
                                    <td>{{ $row['date'] }}</td>
                                    <td>{{ $row['salary'] }}</td>
                                    <td>{{ $row['age'] }}</td>
                                    <td>
                                        <span class="chip chip-sm chip-{{ $statusColor }}" style="height: 24px; font-size: 0.75rem;">{{ $row['status'] }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
