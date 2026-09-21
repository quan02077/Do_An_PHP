@props([
'items' => [],
'backUrl' => null,
'backText' => 'Quay lại trang chủ'
])

<div class="mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
    @if(!empty($backUrl))
    <a href="{{ $backUrl }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 d-inline-flex align-items-center gap-2 text-decoration-none">
        <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        <span>{{ $backText }}</span>
    </a>
    @endif

    @if(!empty($items))
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0 small">
            @foreach($items as $label => $url)
            @if(!$loop->last && !empty($url))
            <li class="breadcrumb-item">
                <a href="{{ $url }}" class="text-decoration-none text-secondary">{{ $label }}</a>
            </li>
            @else
            <li class="breadcrumb-item active text-dark fw-medium" aria-current="page">{{ $label }}</li>
            @endif
            @endforeach
        </ol>
    </nav>
    @endif
</div>