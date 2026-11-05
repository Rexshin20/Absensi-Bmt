@extends('layouts.app')

@section('title', 'Typography')

@section('content')
    <div class="grid">
        <div class="grid-item col-12">
            <div class="card">
                <div class="card-header"><div class="card-header-title" style="font-size: 1.25rem;">Headings</div></div>
                <div class="card-content">
                    <div class="grid">
                        @foreach ([1, 2, 3, 4, 5, 6] as $level)
                            <div class="grid-item col-12 col-sm-2 d-flex align-center"><span>H{{ $level }}</span></div>
                            <div class="grid-item col-12 col-sm-10">
                                <h{{ $level }} style="margin-bottom: 0.5rem;">Heading {{ $level }}</h{{ $level }}>
                                <p class="text-body2">
                                    @switch($level)
                                        @case(1) font-size: 96px / line-height: 112px / font-weight: 500 @break
                                        @case(2) font-size: 60px / line-height: 72px / font-weight: 500 @break
                                        @case(3) font-size: 48px / line-height: 56px / font-weight: 500 @break
                                        @case(4) font-size: 32px / line-height: 40px / font-weight: 500 @break
                                        @case(5) font-size: 24px / line-height: 32px / font-weight: 500 @break
                                        @default font-size: 20px / line-height: 32px / font-weight: 500
                                    @endswitch
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="grid-item col-12">
            <div class="card">
                <div class="card-header"><div class="card-header-title" style="font-size: 1.25rem;">Texts</div></div>
                <div class="card-content">
                    <div class="grid">
                        <div class="grid-item col-12 col-sm-2 d-flex align-center"><span>subtitle1</span></div>
                        <div class="grid-item col-12 col-sm-10">
                            <p class="text-subtitle1" style="margin-bottom: 0.5rem;">Cupcake ipsum dolor sit amet chocolate bar pastry sesame snaps.</p>
                            <p class="text-body2">font-size: 16px / line-height: 28px / font-weight: 400</p>
                        </div>

                        <div class="grid-item col-12 col-sm-2 d-flex align-center"><span>subtitle2</span></div>
                        <div class="grid-item col-12 col-sm-10">
                            <p class="text-subtitle2" style="margin-bottom: 0.5rem;">Cupcake ipsum dolor sit amet chocolate bar pastry sesame snaps.</p>
                            <p class="text-body2">font-size: 14px / line-height: 21px / font-weight: 500</p>
                        </div>

                        <div class="grid-item col-12 col-sm-2 d-flex align-center"><span>body1</span></div>
                        <div class="grid-item col-12 col-sm-10">
                            <p style="margin-bottom: 0.5rem;">Cupcake ipsum dolor sit amet chocolate bar pastry sesame snaps.</p>
                            <p class="text-body2">font-size: 16px / line-height: 24px / font-weight: 400</p>
                        </div>

                        <div class="grid-item col-12 col-sm-2 d-flex align-center"><span>body2</span></div>
                        <div class="grid-item col-12 col-sm-10">
                            <p class="text-body2" style="margin-bottom: 0.5rem;">Cupcake ipsum dolor sit amet chocolate bar pastry sesame snaps.</p>
                            <p class="text-body2">font-size: 14px / line-height: 21px / font-weight: 400</p>
                        </div>

                        <div class="grid-item col-12 col-sm-2 d-flex align-center"><span>button</span></div>
                        <div class="grid-item col-12 col-sm-10">
                            <span class="text-button">Button Text</span>
                            <p class="text-body2" style="margin-top: 0.5rem;">font-size: 14px / line-height: 17px / font-weight: 500 / text-transform: uppercase</p>
                        </div>

                        <div class="grid-item col-12 col-sm-2 d-flex align-center"><span>caption</span></div>
                        <div class="grid-item col-12 col-sm-10">
                            <span class="text-caption">Cupcake ipsum dolor sit amet chocolate bar pastry sesame snaps.</span>
                            <p class="text-body2" style="margin-top: 0.5rem;">font-size: 12px / line-height: 15px / font-weight: 400</p>
                        </div>

                        <div class="grid-item col-12 col-sm-2 d-flex align-center"><span>overline</span></div>
                        <div class="grid-item col-12 col-sm-10">
                            <span class="text-overline">Cupcake ipsum dolor sit amet chocolate bar pastry sesame snaps.</span>
                            <p class="text-body2" style="margin-top: 0.25rem;">font-size: 12px / line-height: 15px / font-weight: 400 / text-transform: uppercase</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
