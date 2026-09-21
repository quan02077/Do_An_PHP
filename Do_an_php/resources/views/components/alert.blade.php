@props([
'type' => 'info',
'dismissible' => false,
'icon' => true
])

@php
$icons = [
'success' => '<svg class="shrink-0 me-2" style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
</svg>',
'danger' => '<svg class="shrink-0 me-2" style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <circle cx="12" cy="12" r="10" stroke-width="2" />
    <line x1="15" y1="9" x2="9" y2="15" stroke-width="2" stroke-linecap="round" />
    <line x1="9" y1="9" x2="15" y2="15" stroke-width="2" stroke-linecap="round" />
</svg>',
'warning' => '<svg class="shrink-0 me-2" style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
</svg>',
'info' => '<svg class="shrink-0 me-2" style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <circle cx="12" cy="12" r="10" stroke-width="2" />
    <line x1="12" y1="16" x2="12" y2="12" stroke-width="2" stroke-linecap="round" />
    <line x1="12" y1="8" x2="12.01" y2="8" stroke-width="2" stroke-linecap="round" />
</svg>',
];
@endphp

<div {{ $attributes->merge(['class' => "alert alert-{$type} d-flex align-items-center " . ($dismissible ? 'alert-dismissible fade show' : '') . ' rounded-3 shadow-sm mb-3']) }} role="alert">
    @if($icon && isset($icons[$type]))
    {!! $icons[$type] !!}
    @endif
    <div class="grow small">
        {{ $slot }}
    </div>
    @if($dismissible)
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
    @endif
</div>