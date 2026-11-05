@extends('layouts.blank')
@section('title', 'Server Error')
@section('content')
    @include('partials.error-page', [
        'code' => '500',
        'heading' => 'Internal server error 👨🏻‍💻',
        'description' => 'Oops, something went wrong!',
        'illustration' => 'images/pages/500.png',
        'tree' => 'images/pages/tree-3.png',
    ])
@endsection
