@props([
    'type' => 'info', // success, danger, warning, info, primary, secondary
    'dismissible' => true,
    'icon' => true,
    'message' => null,
])

@php
    // Chuẩn hóa loại alert sang class chuẩn của Bootstrap
    $type = match ($type) {
        'error' => 'danger',
        default => $type,
    };

    // Bộ icon FontAwesome tương ứng với từng trạng thái
    $icons = [
        'success'   => 'fa-solid fa-circle-check',
        'danger'    => 'fa-solid fa-circle-xmark',
        'warning'   => 'fa-solid fa-triangle-exclamation',
        'info'      => 'fa-solid fa-circle-info',
        'primary'   => 'fa-solid fa-bell',
        'secondary' => 'fa-solid fa-circle-info',
    ];

    $iconClass = $icons[$type] ?? 'fa-solid fa-circle-info';
@endphp

<div {{ $attributes->merge(['class' => "alert alert-{$type} " . ($dismissible ? 'alert-dismissible fade show' : '') . " rounded-3 shadow-sm mb-4 d-flex align-items-center gap-2"]) }} role="alert">
    @if ($icon)
        <i class="{{ $iconClass }} fs-6 flex-shrink-0"></i>
    @endif
    <div class="flex-grow-1 small">
        {{ $message ?? $slot }}
    </div>
    @if ($dismissible)
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
    @endif
</div>

