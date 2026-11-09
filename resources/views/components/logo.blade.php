@props(['size' => 30])
<img src="{{ asset('images/logos/bmt.png') }}" width="{{ $size }}" height="{{ $size }}" alt="Logo BMT Cinde Barokah" {{ $attributes->merge(['style' => 'object-fit: contain;']) }}>
