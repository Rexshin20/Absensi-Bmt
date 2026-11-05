@extends('layouts.blank')
@section('title', 'Unauthorized')
@section('content')
    @include('partials.error-page', [
        'code' => '403',
        'heading' => 'You are not authorized! 🔐',
        'description' => 'You don’t have permission to access this page. Go Home!',
        'illustration' => 'images/pages/401.png',
        'tree' => 'images/pages/tree-2.png',
    ])
@endsection
