<!doctype html>
<html lang="vi">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>@yield('title', 'QQQ — Nền tảng Đặt vé & Quản lý Sự kiện')</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="{{ asset('css/style.css') }}" />
  @stack('styles')
</head>

<body class="bg-light text-dark min-vh-100 d-flex flex-column">
  @include('partials.nav')

  <main class="grow">
    @yield('content')
  </main>
  @include('partials.footer')
  <div id="toast-container" class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1080;"></div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  @stack('scripts')
</body>

</html>