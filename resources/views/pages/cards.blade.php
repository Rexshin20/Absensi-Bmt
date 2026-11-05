@extends('layouts.app')

@section('title', 'Cards')

@push('styles')
    <style>
        .card-user { position: relative; }
        .card-user-avatar {
            position: absolute;
            left: 1.313rem;
            top: 10.28125rem;
            width: 75px;
            height: 75px;
            border: 0.25rem solid #fff;
        }
        .card-user-body { margin-top: 1.4375rem; margin-bottom: 2.1875rem; }
        .card-user-foot { gap: 0.5rem; }

        .card-media-bg { background-size: cover; background-repeat: no-repeat; background-position: center; }

        .card-split-media,
        .card-split-body { display: flex; flex-direction: column; }
        .card-split-media { align-items: center; justify-content: center; border-bottom: 1px solid var(--divider); }
        .card-split-body { padding-top: 0 !important; padding-left: 1.5rem !important; }
        @media (min-width: 900px) {
            .card-split-media { border-bottom: 0; border-right: 1px solid var(--divider); }
            .card-split-body { padding-top: 1.5rem !important; padding-left: 0 !important; }
        }

        .card-horizontal-info { display: flex; flex-direction: column; align-items: flex-start; }
        .card-horizontal-info .card-content { padding: 0.75rem 1.1875rem; }
        .card-horizontal-img { display: flex; align-items: center; justify-content: center; order: -1; }
        .card-horizontal-img img { height: 11rem; border-radius: var(--radius); }
        @media (min-width: 900px) {
            .card-horizontal-info { padding-top: 0 !important; }
            .card-horizontal-img { order: 0; padding-left: 0 !important; }
        }
        @media (max-width: 899px) {
            .card-horizontal-info .card-content { padding-top: 0; }
        }

        .card-watch .card-content { padding: 0.75rem 1.3125rem 1rem !important; }
        .card-watch .btn { padding-top: 0.625rem; padding-bottom: 0.625rem; width: 100%; border-top-left-radius: 0; border-top-right-radius: 0; }

        .card-membership-left .card-content { padding: 0.8125rem 1.4375rem 1.5625rem !important; }
        .card-membership-price { background-color: var(--action-hover); padding: 4.5rem 1.25rem 4rem !important; }
        .card-membership-price .amount { line-height: 1; font-weight: 600; font-size: 3.75rem !important; }
        .card-membership-feature { display: flex; align-items: center; margin-bottom: 1.6875rem; }
        .card-membership-feature:last-child { margin-bottom: 0; }
        .card-membership-feature .mdi { color: var(--primary-main); margin-right: 0.6875rem; font-size: 1.25rem; }
        .card-membership-box { border-right: 1px solid var(--divider); }

        .card-support .card-content {
            display: flex;
            text-align: center;
            align-items: center;
            flex-direction: column;
            padding: 2.4375rem 1.25rem 2.3125rem !important;
        }
        .card-support .avatar { width: 50px; height: 50px; margin-bottom: 0.5625rem; color: #fff; background-color: var(--primary-main); }
        .card-support .avatar .mdi { font-size: 2rem; }
        .card-support .btn { padding: 0.4375rem 1.375rem; }

        .card-solid { border: 0; box-shadow: none; color: #fff; }
        .card-solid .card-content { padding: 0.8125rem 1.25rem 1.125rem !important; }
        .card-solid .card-title { display: flex; align-items: center; margin-bottom: 0.6875rem; color: #fff; }
        .card-solid .card-title .mdi { margin-right: 0.625rem; }
        .card-solid .text-body2 { color: #fff; }

        .card-navigation .card-content { padding: 1.25rem; }
        .card-section-title { margin-bottom: 1.5rem; }
        .card-section-gap { padding-top: 4.375rem !important; padding-bottom: 1rem !important; }
        .card-media-bg-cover { height: 14.5625rem; }
        .card-media-user { height: 12.625rem; }
        .card-media-watch { height: 9.375rem; }
    </style>
@endpush

@section('content')
    <div class="grid">
        <div class="grid-item col-12 card-section-title" style="padding-bottom: 1rem;">
            <h5>Basic Cards</h5>
        </div>

        <div class="grid-item col-12 col-sm-6 col-md-4">
            <div class="card">
                <div class="card-media card-media-bg card-media-bg-cover" style="background-image: url('/images/cards/glass-house.png');"></div>
                <div class="card-content">
                    <h6 class="mb-2">Influencing The Influencer</h6>
                    <p class="text-body2">
                        Cancun is back, better than ever! Over a hundred Mexico resorts have reopened and the state tourism minister
                        predicts Cancun will draw as many visitors in 2006 as it did two years ago.
                    </p>
                </div>
            </div>
        </div>

        <div class="grid-item col-12 col-sm-6 col-md-4">
            <div class="card card-user">
                <div class="card-media card-media-bg card-media-user" style="background-image: url('/images/cards/background-user.png');"></div>
                <img class="avatar card-user-avatar" src="/images/avatars/1.png" alt="Robert Meyer">
                <div class="card-content">
                    <div class="d-flex flex-wrap align-center justify-between card-user-body">
                        <div class="d-flex flex-column mr-2 mb-1">
                            <h6>Robert Meyer</h6>
                            <span class="text-caption">London, UK</span>
                        </div>
                        <button type="button" class="btn btn-contained">Send Request</button>
                    </div>
                    <div class="d-flex flex-wrap align-center justify-between card-user-foot">
                        <span class="text-subtitle2 text-primary-color" style="white-space: nowrap;">18 mutual friends</span>
                        <div class="avatar-group">
                            <span class="avatar"><img src="/images/avatars/8.png" alt="Alice Cobb"></span>
                            <span class="avatar"><img src="/images/avatars/7.png" alt="Jeffery Warner"></span>
                            <span class="avatar"><img src="/images/avatars/3.png" alt="Howard Lloyd"></span>
                            <span class="avatar avatar-more">+4</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid-item col-12 col-sm-6 col-md-4">
            <div class="card">
                <div class="card-media card-media-bg card-media-bg-cover" style="background-image: url('/images/cards/paper-boat.png');"></div>
                <div class="card-content">
                    <h6 class="mb-2">Popular Uses Of The Internet</h6>
                    <p class="text-body2">Although cards can support multiple actions, UI controls, and an overflow menu.</p>
                </div>
                <div class="card-actions card-action-dense">
                    <div class="d-flex align-center justify-between w-100">
                        <button type="button" class="btn btn-text" data-collapse-toggle="card-collapse-content">Details</button>
                        <button type="button" class="icon-btn icon-btn-sm" data-collapse-toggle="card-collapse-content" aria-label="toggle details">
                            <x-icon name="ChevronDown" class="chevron-down" style="font-size: 1.875rem;" />
                            <x-icon name="ChevronUp" class="chevron-up d-none" style="font-size: 1.875rem;" />
                        </button>
                    </div>
                </div>
                <div class="collapse" id="card-collapse-content">
                    <div class="divider" style="margin: 0;"></div>
                    <div class="card-content">
                        <p class="text-body2">
                            I&prime;m a thing. But, like most politicians, he promised more than he could deliver. You won&prime;t have
                            time for sleeping, soldier, not with all the bed making you&prime;ll be doing. Then we&prime;ll go with that
                            data file! Hey, you add a one and two zeros to that or we walk! You&prime;re going to do his laundry?
                            I&prime;ve got to find a way to escape.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid-item col-12 col-sm-6">
            <div class="card">
                <div class="grid">
                    <div class="grid-item col-12 col-md-5 card-split-media">
                        <div class="card-content d-flex align-center justify-center">
                            <img width="137" height="176" alt="Apple iPhone 11 Pro" src="/images/cards/iPhone-11-pro.png">
                        </div>
                    </div>
                    <div class="grid-item col-12 col-md-7 card-split-body">
                        <div class="card-content">
                            <h6 class="mb-2">Apple iPhone 11 Pro</h6>
                            <p class="text-body2" style="margin-bottom: 0.875rem;">
                                Apple iPhone 11 Pro smartphone. Announced Sep 2019. Features 5.8&Prime; display Apple A13 Bionic
                            </p>
                            <p class="fw-500" style="margin-bottom: 0.75rem;">
                                Price: <span class="fw-bold">$899</span>
                            </p>
                        </div>
                        <div class="card-actions card-action-dense">
                            <div class="d-flex align-center justify-between w-100" style="position: relative;">
                                <button type="button" class="btn btn-text">
                                    <x-icon name="CartPlus" size="20" class="mr-2" />
                                    Add to Card
                                </button>
                                <button type="button" class="icon-btn icon-btn-sm" data-menu-trigger="card-share-menu" aria-label="share">
                                    <x-icon name="ShareVariant" size="20" />
                                </button>
                                <div class="menu-popover" id="card-share-menu">
                                    <button type="button" class="menu-item"><x-icon name="Facebook" /></button>
                                    <button type="button" class="menu-item"><x-icon name="Twitter" /></button>
                                    <button type="button" class="menu-item"><x-icon name="Linkedin" /></button>
                                    <button type="button" class="menu-item"><x-icon name="GooglePlus" /></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid-item col-12 col-sm-6">
            <div class="card">
                <div class="grid">
                    <div class="grid-item col-12 col-md-6 col-lg-7 card-horizontal-info">
                        <div class="card-content">
                            <h6 class="mb-2">Stumptown Roasters</h6>
                            <div class="d-flex flex-wrap align-center" style="margin-bottom: 1.1875rem;">
                                <span class="rating mr-2" aria-label="5 Stars">
                                    @for ($i = 0; $i < 5; $i++)
                                        <x-icon name="Star" />
                                    @endfor
                                </span>
                                <p class="text-body2">5 Star | 98 reviews</p>
                            </div>
                            <p class="text-body2" style="margin-bottom: 1rem;">
                                Before there was a United States of America, there were coffee houses, because how are you supposed to
                                build.
                            </p>
                        </div>
                        <div class="card-actions card-action-dense w-100">
                            <button type="button" class="btn btn-text">Location</button>
                            <button type="button" class="btn btn-text">Reviews</button>
                        </div>
                    </div>
                    <div class="grid-item col-12 col-md-6 col-lg-5 card-horizontal-img">
                        <div class="card-content d-flex align-center justify-center">
                            <img alt="Stumptown Roasters" src="/images/cards/analog-clock.jpg">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid-item col-12 col-sm-6 col-md-4">
            <div class="card card-watch">
                <div class="card-media card-media-bg card-media-watch" style="background-image: url('/images/cards/watch-on-hand.jpg');"></div>
                <div class="card-content">
                    <h6 class="mb-2">Apple Watch</h6>
                    <p class="mb-2">$249.40</p>
                    <p class="text-body2">3.1GHz 6-core 10th-generation Intel Core i5 processor, Turbo Boost up to 4.5GHz</p>
                </div>
                <button type="button" class="btn btn-contained">Add To Cart</button>
            </div>
        </div>

        <div class="grid-item col-12 col-md-8">
            <div class="card">
                <div class="grid">
                    <div class="grid-item col-12 col-sm-7 card-membership-left">
                        <div class="card-content">
                            <h6 style="margin-bottom: 0.875rem;">Lifetime Membership</h6>
                            <p class="text-body2">
                                Here, I focus on a range of items and features that we use in life without giving them a second thought
                                such as Coca Cola, body muscles and holding ones own breath. Though, most of these notes are not
                                fundamentally necessary, they are such that you can use them for a good laugh, at a drinks party or for
                                picking up women or men.
                            </p>
                            <div class="divider" style="margin-top: 1.625rem; margin-bottom: 1.6875rem;"></div>
                            <div class="grid">
                                <div class="grid-item col-12 col-sm-5">
                                    <div class="card-membership-box">
                                        <div class="card-membership-feature">
                                            <x-icon name="LockOpenOutline" />
                                            <span class="text-body2">Full Access</span>
                                        </div>
                                        <div class="card-membership-feature">
                                            <x-icon name="AccountOutline" />
                                            <span class="text-body2">15 Members</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="grid-item col-12 col-sm-7">
                                    <div class="card-membership-feature">
                                        <x-icon name="StarOutline" />
                                        <span class="text-body2">Access all Features</span>
                                    </div>
                                    <div class="card-membership-feature">
                                        <x-icon name="TrendingUp" />
                                        <span class="text-body2">Lifetime Free Update</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="grid-item col-12 col-sm-5" style="padding-top: 0 !important;">
                        <div class="card-content h-100 d-flex align-center justify-center text-center card-membership-price">
                            <div>
                                <div class="d-flex align-end justify-center" style="margin-bottom: 0.875rem;">
                                    <h6>$</h6>
                                    <h6 class="amount">899</h6>
                                    <h6>USD</h6>
                                </div>
                                <p class="text-body2" style="margin-bottom: 3.4375rem;">
                                    <span>5 Tips For Offshore</span>
                                    <span>Software Development</span>
                                </p>
                                <button type="button" class="btn btn-contained">Contact Now</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid-item col-12 col-sm-6 col-md-4">
            <div class="card">
                <div class="card-header"><div class="card-header-title" style="font-size: 1.25rem;">Influencing The Influencer</div></div>
                <div class="card-content">
                    <p class="text-body2" style="margin-bottom: 0.8125rem;">
                        Computers have become ubiquitous in almost every facet of our lives. At work, desk jockeys spend hours in
                        front of their desktops, while delivery people scan bar codes with handhelds and workers in the field stay in
                        touch.
                    </p>
                    <p class="text-body2">
                        If you&rsquo;re in the market for new desktops, notebooks, or PDAs, there are a myriad of choices. Here&rsquo;s a rundown
                        of some of the best systems available.
                    </p>
                </div>
                <div class="card-actions card-action-dense">
                    <button type="button" class="btn btn-text">Read More</button>
                </div>
            </div>
        </div>

        <div class="grid-item col-12 col-sm-6 col-md-4">
            <div class="card">
                <div class="card-header"><div class="card-header-title" style="font-size: 1.25rem;">The Best Answers</div></div>
                <div class="card-content">
                    <div class="d-flex flex-wrap align-center" style="margin-bottom: 1.25rem;">
                        <span class="rating mr-2" aria-label="5 Stars">
                            @for ($i = 0; $i < 5; $i++)
                                <x-icon name="Star" />
                            @endfor
                        </span>
                        <p class="text-body2">5 Star | 98 reviews</p>
                    </div>
                    <p class="text-body2" style="margin-bottom: 0.8125rem;">
                        If you are looking for a new way to promote your business that won&rsquo;t cost you more money, maybe printing is
                        one of the options you won&rsquo;t resist.
                    </p>
                    <p class="text-body2">
                        Printing is a widely use process in making printed materials that are used for advertising. It become fast,
                        easy and simple. If you want your promotional material to be an eye-catching.
                    </p>
                </div>
                <div class="card-actions card-action-dense">
                    <button type="button" class="btn btn-text">Location</button>
                    <button type="button" class="btn btn-text">Reviews</button>
                </div>
            </div>
        </div>

        <div class="grid-item col-12 col-sm-6 col-md-4">
            <div class="card card-support">
                <div class="card-content">
                    <span class="avatar"><x-icon name="HelpCircleOutline" /></span>
                    <h6 style="margin-bottom: 0.6875rem;">Support</h6>
                    <p class="text-body2" style="margin-bottom: 1.5rem;">
                        According to us blisters are a very common thing and we come across them very often in our daily lives. It is
                        a very common occurrence like cold or fever depending upon your lifestyle.
                    </p>
                    <button type="button" class="btn btn-contained">Contact Now</button>
                </div>
            </div>
        </div>

        <div class="grid-item col-12 card-section-gap">
            <h5>Navigation Cards</h5>
        </div>

        <div class="grid-item col-12 col-md-6">
            <div class="card card-navigation">
                <div class="tabs" data-tabs>
                    <button type="button" class="tab active" data-tab="1" aria-selected="true">Item One</button>
                    <button type="button" class="tab" data-tab="2" aria-selected="false">Item Two</button>
                    <button type="button" class="tab" data-tab="3" aria-selected="false">Item Three</button>
                </div>
                <div class="card-content">
                    <div class="tab-panel active" data-panel="1">
                        <h6 class="mb-2">Header One</h6>
                        <p class="text-body2 mb-4">Pudding tiramisu caramels. Gingerbread gummies danish chocolate bar toffee marzipan. Wafer wafer cake powder danish oat cake.</p>
                        <button type="button" class="btn btn-contained">Button One</button>
                    </div>
                    <div class="tab-panel" data-panel="2">
                        <h6 class="mb-2">Header Two</h6>
                        <p class="text-body2 mb-4">Drag&eacute;e chupa chups souffl&eacute; cheesecake jelly tootsie roll cupcake marzipan. Carrot cake sweet roll gummi bears caramels jelly beans.</p>
                        <button type="button" class="btn btn-contained">Button Two</button>
                    </div>
                    <div class="tab-panel" data-panel="3">
                        <h6 class="mb-2">Header Three</h6>
                        <p class="text-body2 mb-4">Icing cake macaroon macaroon jelly chocolate bar. Chupa chups dessert dessert souffl&eacute; chocolate bar jujubes gummi bears lollipop.</p>
                        <button type="button" class="btn btn-contained">Button Three</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid-item col-12 col-md-6">
            <div class="card card-navigation">
                <div class="tabs tabs-centered" data-tabs>
                    <button type="button" class="tab active" data-tab="c1" aria-selected="true">Item One</button>
                    <button type="button" class="tab" data-tab="c2" aria-selected="false">Item Two</button>
                    <button type="button" class="tab" data-tab="c3" aria-selected="false">Item Three</button>
                </div>
                <div class="card-content text-center">
                    <div class="tab-panel active" data-panel="c1">
                        <h6 class="mb-2">Header One</h6>
                        <p class="text-body2 mb-4">Pudding tiramisu caramels. Gingerbread gummies danish chocolate bar toffee marzipan. Wafer wafer cake powder danish oat cake.</p>
                        <button type="button" class="btn btn-contained">Button One</button>
                    </div>
                    <div class="tab-panel" data-panel="c2">
                        <h6 class="mb-2">Header Two</h6>
                        <p class="text-body2 mb-4">Drag&eacute;e chupa chups souffl&eacute; cheesecake jelly tootsie roll cupcake marzipan. Carrot cake sweet roll gummi bears caramels jelly beans.</p>
                        <button type="button" class="btn btn-contained">Button Two</button>
                    </div>
                    <div class="tab-panel" data-panel="c3">
                        <h6 class="mb-2">Header Three</h6>
                        <p class="text-body2 mb-4">Icing cake macaroon macaroon jelly chocolate bar. Chupa chups dessert dessert souffl&eacute; chocolate bar jujubes gummi bears lollipop.</p>
                        <button type="button" class="btn btn-contained">Button Three</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid-item col-12 card-section-gap">
            <h5>Solid Cards</h5>
        </div>

        <div class="grid-item col-12 col-sm-6 col-md-4">
            <div class="card card-solid" style="background-color: var(--info-main);">
                <div class="card-content">
                    <h6 class="card-title"><x-icon name="Twitter" /> Twitter Card</h6>
                    <p class="text-body2" style="margin-bottom: 0.75rem;">
                        Turns out semicolon-less style is easier and safer in TS because most gotcha edge cases are type invalid as
                        well.
                    </p>
                    <div class="d-flex flex-wrap align-center justify-between">
                        <div class="d-flex align-center mr-2">
                            <span class="avatar mr-3" style="width: 34px; height: 34px;"><img src="/images/avatars/4.png" alt="Mary Vaughn"></span>
                            <span class="text-body2">Mary Vaughn</span>
                        </div>
                        <div class="d-flex align-center">
                            <div class="d-flex align-center mr-4">
                                <x-icon name="Heart" class="mr-1" />
                                <span class="text-body2">1.2k</span>
                            </div>
                            <div class="d-flex align-center">
                                <x-icon name="ShareVariant" class="mr-1" />
                                <span class="text-body2">80</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid-item col-12 col-sm-6 col-md-4">
            <div class="card card-solid" style="background-color: var(--primary-main);">
                <div class="card-content">
                    <h6 class="card-title"><x-icon name="Facebook" /> Facebook Card</h6>
                    <p class="text-body2" style="margin-bottom: 0.75rem;">
                        You&rsquo;ve read about the importance of being courageous, rebellious and imaginative. These are all vital
                        ingredients in an effective.
                    </p>
                    <div class="d-flex flex-wrap align-center justify-between">
                        <div class="d-flex align-center mr-2">
                            <span class="avatar mr-3" style="width: 34px; height: 34px;"><img src="/images/avatars/1.png" alt="Eugene Clarke"></span>
                            <span class="text-body2">Eugene Clarke</span>
                        </div>
                        <div class="d-flex align-center">
                            <div class="d-flex align-center mr-4">
                                <x-icon name="Heart" class="mr-1" />
                                <span class="text-body2">3.2k</span>
                            </div>
                            <div class="d-flex align-center">
                                <x-icon name="ShareVariant" class="mr-1" />
                                <span class="text-body2">49</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid-item col-12 col-sm-6 col-md-4">
            <div class="card card-solid" style="background-color: var(--success-main);">
                <div class="card-content">
                    <h6 class="card-title"><x-icon name="Linkedin" /> LinkedIn Card</h6>
                    <p class="text-body2" style="margin-bottom: 0.75rem;">
                        With the Internet spreading like wildfire and reaching every part of our daily life, more and more traffic is
                        directed.
                    </p>
                    <div class="d-flex flex-wrap align-center justify-between">
                        <div class="d-flex align-center mr-2">
                            <span class="avatar mr-3" style="width: 34px; height: 34px;"><img src="/images/avatars/8.png" alt="Anne Burke"></span>
                            <span class="text-body2">Anne Burke</span>
                        </div>
                        <div class="d-flex align-center">
                            <div class="d-flex align-center mr-4">
                                <x-icon name="Heart" class="mr-1" />
                                <span class="text-body2">1.1k</span>
                            </div>
                            <div class="d-flex align-center">
                                <x-icon name="ShareVariant" class="mr-1" />
                                <span class="text-body2">67</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
