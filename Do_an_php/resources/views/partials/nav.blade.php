<!-- ─── Navbar Header Dùng Chung Cho Mọi Trang ─────────────────────── -->
<header class="sticky-top bg-white border-bottom shadow-sm">
  <nav class="navbar navbar-expand-md navbar-light bg-white py-2.5">
    <div class="container-xl">
      <!-- Logo -->
      <a href="{{ url('/') }}" class="navbar-brand d-flex align-items-center gap-2 text-decoration-none">
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
            <a href="{{ route('Home.index') }}" class="nav-link {{ request()->routeIs('Home.index') ? 'active fw-semibold text-dark' : 'text-secondary' }} px-3 py-2 rounded">Trang chủ</a>
          </li>
          <li class="nav-item">
            <a href="{{ route('Dashboard.myTicket') }}" class="nav-link {{ request()->routeIs('Dashboard.myTicket') ? 'active fw-semibold text-dark' : 'text-secondary' }} px-3 py-2 rounded">Vé của tôi</a>
          </li>
          <li class="nav-item">
            <a href="{{ route('Dashboard.favorite') }}" class="nav-link {{ request()->routeIs('Dashboard.favorite') ? 'active fw-semibold text-dark' : 'text-secondary' }} px-3 py-2 rounded d-flex align-items-center gap-1.5">Yêu thích</a>
          </li>
        </ul>

        <!-- Phân quyền & Tài khoản người dùng (đồng bộ từ website_) -->
        <div class="d-flex align-items-center gap-2 pt-2 pt-md-0">
          <div class="navbar-role-switcher d-none"></div>
          <div class="navbar-auth-container">
            <div class="d-flex align-items-center gap-2">
              <a href="{{ route('Auth.index') }}" class="btn btn-sm btn-outline-secondary">Đăng nhập</a>
              <a href="{{ route('Auth.index') }}?mode=register" class="btn btn-sm btn-dark">Đăng ký</a>
            </div>
          </div>  
        </div>
      </div>
    </div>
  </nav>
</header>
