@props([
'variant' => 'dark',
'size' => '',
'type' => 'button',
'href' => null
])

@php
$sizeClass = $size ? "btn-{$size}" : '';
$classes = "btn btn-{$variant} {$sizeClass} rounded-3 fw-medium shadow-sm d-inline-flex align-items-center justify-content-center gap-2 text-decoration-none";
@endphp

@if($href)
<a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
@else
<button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</button>
@endif