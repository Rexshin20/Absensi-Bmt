@props(['name', 'size' => null, 'color' => null])
@php
    $kebab = strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', $name));
    $classes = 'mdi mdi-' . $kebab;
    if ($size) {
        $classes .= ' mdi-' . $size . 'px';
    }
    if ($color) {
        $classes .= ' ' . $color;
    }
@endphp
<i {{ $attributes->merge(['class' => $classes]) }} aria-hidden="true"></i>
