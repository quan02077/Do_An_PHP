<!doctype html>
<html lang="vi">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'QQQ — Nền tảng Đặt vé & Quản lý Sự kiện')</title>

    <!-- Bootstrap 5.3.3 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />

    <!-- File CSS dùng chung (Copy từ website_/css/style.css sang public/css/) -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}" />

    @stack('styles')
  </head>
  <body class="bg-light text-dark min-vh-100 d-flex flex-column">
    <!-- Navbar Header Dùng Chung -->
    @include('partials.nav')

    <!-- ─── Nội Dung Chính Từng Trang Sẽ Nhúng Vào Đây ───────────────────── -->
    <main class="grow">
      @yield('content')
    </main>

    <!-- Footer Chân Trang Dùng Chung -->
    @include('partials.footer')

    <!-- Toast Notification Container (Bootstrap 5) -->
    <div id="toast-container" class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1080;"></div>

    <!-- Bootstrap 5 Bundle JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')
  </body>
</html>