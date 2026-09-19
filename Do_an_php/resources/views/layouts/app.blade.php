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
    <!-- ─── Navbar Header Dùng Chung Cho Mọi Trang ─────────────────────── -->
    <header class="sticky-top bg-white border-bottom shadow-sm">
      <nav class="navbar navbar-expand-md navbar-light bg-white py-2.5">
        <div class="container-xl">
          <!-- Logo -->
          <a href="{{ url('/') }}" class="navbar-brand d-flex align-items-center gap-2 text-decoration-none">
            <span class="d-inline-flex align-items-center justify-content-center bg-dark text-white rounded fw-bold shadow-sm" style="width: 36px; height: 36px; font-size: 18px;">
              Q
            </span>
            <div class="lh-sm">
              <div class="fw-bold text-dark fs-5 tracking-tight">QQQ</div>
              <div class="d-none d-sm-block text-muted small" style="font-size: 12px;">Sự kiện &amp; Hội thảo</div>
            </div>
          </a>

          <!-- Mobile Hamburger Toggle -->
          <button class="navbar-toggler border-0 p-2 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>

          <!-- Nav items -->
          <div class="collapse navbar-collapse" id="navbarMain">
            <ul class="navbar-nav me-auto mb-2 mb-md-0 gap-1 pt-2 pt-md-0">
              <li class="nav-item">
                <a href="{{ route('trang-chu') }}" class="nav-link {{ request()->routeIs('trang-chu') ? 'active fw-semibold text-dark' : 'text-secondary' }} px-3 py-2 rounded">Trang chủ</a>
              </li>
              <li class="nav-item">
                <a href="{{ route('dashboard') }}" class="nav-link {{ (request()->routeIs('dashboard') && request('tab') !== 'bookmarks') ? 'active fw-semibold text-dark' : 'text-secondary' }} px-3 py-2 rounded">Vé của tôi</a>
              </li>
              <li class="nav-item">
                <a href="{{ route('dashboard', ['tab' => 'bookmarks']) }}" class="nav-link {{ (request()->routeIs('dashboard') && request('tab') === 'bookmarks') ? 'active fw-semibold text-dark' : 'text-secondary' }} px-3 py-2 rounded d-flex align-items-center gap-1.5">Yêu thích</a>
              </li>
            </ul>

            <!-- Phân quyền & Tài khoản người dùng (đồng bộ từ website_) -->
            <div class="d-flex align-items-center gap-2 pt-2 pt-md-0">
              <div class="navbar-role-switcher d-none"></div>
              <div class="navbar-auth-container">
                <div class="d-flex align-items-center gap-2">
                  <a href="{{ route('auth') }}" class="btn btn-sm btn-outline-secondary">Đăng nhập</a>
                  <a href="{{ route('auth') }}?mode=register" class="btn btn-sm btn-dark">Đăng ký</a>
                </div>
              </div>  
            </div>
          </div>
        </div>
      </nav>
    </header>

    <!-- ─── Nội Dung Chính Từng Trang Sẽ Nhúng Vào Đây ───────────────────── -->
    <main class="grow">
      @yield('content')
    </main>

    <!-- ─── Footer Chân Trang Dùng Chung ───────────────────────────────── -->
    <footer class="bg-white border-top py-4 text-center text-muted small mt-auto">
      <div class="container-xl d-flex flex-column flex-sm-row align-items-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-2">
          <span class="d-inline-flex align-items-center justify-content-center bg-dark text-white rounded fw-bold" style="width: 24px; height: 24px; font-size: 12px;">Q</span>
          <span class="fw-bold text-dark">Nhóm QQQ</span>
          <span>2026.</span>
        </div>
      </div>
    </footer>

    <!-- Toast Notification Container (Bootstrap 5) -->
    <div id="toast-container" class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1080;"></div>

    <!-- Bootstrap 5 Bundle JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')
  </body>
</html>