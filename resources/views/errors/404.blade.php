@extends('layouts.blank')
@section('title', 'Page Not Found')
@section('content')
    @include('partials.error-page', [
        'code' => '404',
        'heading' => 'Page Not Found ⚠️',
        'description' => 'We couldn’t find the page you are looking for.',
        'illustration' => 'images/pages/404.png',
        'tree' => 'images/pages/tree.png',
    ])
@endsection
