{{-- Hiển thị thông báo Flash Messages và Lỗi Validation đồng nhất toàn ứng dụng --}}
@if (session('success'))
    <x-alert type="success" :dismissible="true">
        {{ session('success') }}
    </x-alert>
@endif

@if (session('warning'))
    <x-alert type="warning" :dismissible="true">
        {{ session('warning') }}
    </x-alert>
@endif

@if (session('error'))
    <x-alert type="danger" :dismissible="true">
        {{ session('error') }}
    </x-alert>
@endif

@if (session('status'))
    <x-alert type="info" :dismissible="true">
        {{ session('status') }}
    </x-alert>
@endif

@if (isset($errors) && $errors->any())
    <x-alert type="danger" :dismissible="true">
        <ul class="mb-0 ps-3">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </x-alert>
@endif

