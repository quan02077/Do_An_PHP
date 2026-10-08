@props([
'type' => 'info',
'dismissible' => false,
'icon' => true
])

@php
$icons = [
    'success' => '<i class="fa-solid fa-circle-check me-2 flex-shrink-0"></i>',
    'danger'  => '<i class="fa-solid fa-circle-xmark me-2 flex-shrink-0"></i>',
    'warning' => '<i class="fa-solid fa-triangle-exclamation me-2 flex-shrink-0"></i>',
    'info'    => '<i class="fa-solid fa-circle-info me-2 flex-shrink-0"></i>',
];
@endphp

<div {{ $attributes->merge(['class' => "alert alert-{$type} d-flex align-items-center " . ($dismissible ? 'alert-dismissible fade show' : '') . ' rounded-3 shadow-sm mb-3']) }} role="alert">
    @if($icon && isset($icons[$type]))
    {!! $icons[$type] !!}
    @endif
    <div class="flex-grow-1 small">
        {{ $slot }}
    </div>
    @if($dismissible)
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
    @endif
</div>