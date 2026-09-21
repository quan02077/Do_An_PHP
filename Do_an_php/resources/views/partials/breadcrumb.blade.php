@props([
'items' => [],
'backUrl' => null,
'backText' => 'Quay lại trang chủ'
])

<div class="mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
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