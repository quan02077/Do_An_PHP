<!doctype html>
<html lang="vi">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>@yield('title', 'QQQ — Nền tảng Đặt vé & Quản lý Sự kiện')</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
  <link rel="stylesheet" href="{{ asset('css/style.css') }}" />
  @stack('styles')
</head>

<body class="bg-light text-dark min-vh-100 d-flex flex-column overflow-x-hidden">
  @include('partials.nav')

  <main class="grow">
    @if(session('success') || session('warning') || session('error') || session('status') || (isset($errors) && $errors->any()))
      <div class="container pt-3">
        @include('partials.alerts')
      </div>
    @endif
    @yield('content')
  </main>

  @include('partials.footer')

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  @include('partials.ajax_actions')
  <script>
    window.QQQRealtime = {
      enabled: {{ Illuminate\Support\Js::from(config('broadcasting.default') === 'reverb') }},
      key: {{ Illuminate\Support\Js::from(config('broadcasting.connections.reverb.key')) }},
      port: {{ Illuminate\Support\Js::from((int) config('broadcasting.connections.reverb.options.port', 8080)) }},
      secure: {{ Illuminate\Support\Js::from(config('broadcasting.connections.reverb.options.scheme') === 'https') }}
    };
  </script>
  @if(file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
    @vite('resources/js/app.js')
  @endif
  @stack('scripts')
</body>

</html>
