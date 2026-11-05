@php
    $code = $code ?? '404';
    $heading = $heading ?? 'Page Not Found ⚠️';
    $description = $description ?? 'We couldn\u2019t find the page you are looking for.';
    $illustration = $illustration ?? 'images/pages/404.png';
    $tree = $tree ?? 'images/pages/tree.png';
@endphp
<div class="content-center">
    <div class="error-page-content">
        <div class="error-box">
            <h1>{{ $code }}</h1>
            <h5 class="error-title">{{ $heading }}</h5>
            <p class="text-body2">{{ $description }}</p>
        </div>
        <img class="error-illustration" height="487" alt="error-illustration" src="{{ asset($illustration) }}">
        <a href="{{ url('/') }}" class="btn btn-contained" style="padding-left: 1.375rem; padding-right: 1.375rem;">Back to Home</a>
    </div>
    <img class="error-tree" alt="tree" src="{{ asset($tree) }}">
    <img class="illustration-mask theme-img-light" alt="mask" src="{{ asset('images/pages/misc-mask-light.png') }}">
    <img class="illustration-mask theme-img-dark" alt="mask" src="{{ asset('images/pages/misc-mask-dark.png') }}">
</div>
