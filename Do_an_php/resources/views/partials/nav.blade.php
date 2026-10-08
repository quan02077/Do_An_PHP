<!-- ─── Navbar Header Dùng Chung Cho Mọi Trang ─────────────────────── -->
<header class="sticky-top bg-white border-bottom shadow-sm">
  <nav class="navbar navbar-expand-md navbar-light bg-white py-2.5">
    <div class="container-xl">
      <!-- Logo -->
      <a href="{{ route('Home.index') }}" class="navbar-brand d-flex align-items-center gap-2 text-decoration-none">
        <div class="rounded-3 bg-dark text-white d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 36px; height: 36px; font-size: 1.15rem;">
          Q
        </div>
        <div class="lh-sm">
          <div class="fw-bold text-dark fs-5 tracking-tight">QQQ</div>
          <div class="d-none d-sm-block text-muted small" style="font-size: 11px;">Sự kiện &amp; Hội thảo</div>
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
            <a href="{{ route('Home.index') }}" class="nav-link {{ request()->routeIs('Home.index') ? 'active fw-semibold text-dark bg-light' : 'text-secondary' }} px-3 py-2 rounded-pill small">Trang chủ</a>
          </li>
          <li class="nav-item">
            <a href="{{ route('Dashboard.myTicket') }}" class="nav-link {{ request()->routeIs('Dashboard.myTicket') ? 'active fw-semibold text-dark bg-light' : 'text-secondary' }} px-3 py-2 rounded-pill small d-flex align-items-center gap-1.5">
              <span>Vé của tôi</span>
              @if(isset($ticketCount) && $ticketCount > 0)
              <span id="nav-ticket-badge" class="badge rounded-pill bg-dark text-white ms-1" style="font-size: 11px;">{{ $ticketCount }}</span>
              @endif
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ route('Dashboard.favorite') }}" class="nav-link {{ request()->routeIs('Dashboard.favorite') ? 'active fw-semibold text-dark bg-light' : 'text-secondary' }} px-3 py-2 rounded-pill small d-flex align-items-center gap-1">
              <i class="fa-solid fa-heart text-danger me-1"></i>
              <span>Yêu thích</span>
              <span id="nav-fav-badge" class="badge rounded-pill bg-danger text-white ms-1 {{ (isset($favCount) && $favCount > 0) ? '' : 'd-none' }}" style="font-size: 11px;">{{ $favCount ?? 0 }}</span>
            </a>
          </li>
        </ul>

        <!-- Tài khoản người dùng & Phân quyền -->
        <div class="d-flex align-items-center gap-2 pt-2 pt-md-0">
          @if(isset($currentUser) && $currentUser)
          <!-- Dropdown người dùng -->
          <div class="dropdown">
            <button class="btn btn-sm btn-light border rounded-pill d-flex align-items-center gap-2 px-3 py-1.5 shadow-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
              <span class="rounded-circle bg-dark text-white fw-bold d-flex align-items-center justify-content-center" style="width: 24px; height: 24px; font-size: 11px;">
                {{ mb_strtoupper(mb_substr($currentUser->ho_ten ?? 'U', 0, 1)) }}
              </span>
              <span class="small fw-semibold text-dark text-truncate" style="max-width: 130px;">{{ $currentUser->ho_ten }}</span>
              <span class="badge {{ $currentUser->vai_tro === 'admin' ? 'bg-danger' : 'bg-primary-subtle text-primary' }} rounded-pill" style="font-size: 10px;">
                {{ $currentUser->vai_tro === 'admin' ? 'Admin' : 'Thành viên' }}
              </span>
              <i class="fa-solid fa-chevron-down text-secondary" style="font-size: 11px;"></i>
            </button>

            <ul class="dropdown-menu dropdown-menu-end shadow-sm border rounded-3 p-2" style="min-width: 240px;">
              <li class="px-2 py-1.5 border-bottom mb-2">
                <a href="{{ route('Dashboard.profile') }}" class="cursor-pointer text-decoration-none d-block">
                  <div class="fw-semibold text-dark small">{{ $currentUser->ho_ten }}</div>
                  <div class="text-muted small text-truncate" style="font-size: 12px;">{{ $currentUser->email }}</div>
                </a>
              </li>

              <li>
                <a class="dropdown-item small rounded-2 py-1.5 {{ request()->routeIs('Dashboard.profile') ? 'active fw-semibold' : '' }}" href="{{ route('Dashboard.profile') }}">
                  <i class="fa-regular fa-user me-2 text-secondary"></i>
                  Hồ sơ cá nhân
                </a>
              </li>
              <li>
                <a class="dropdown-item small rounded-2 py-1.5" href="{{ route('Dashboard.myTicket') }}">
                  <i class="fa-solid fa-ticket me-2 text-secondary"></i>
                  Vé của tôi
                </a>
              </li>
              <li>
                <a class="dropdown-item small rounded-2 py-1.5" href="{{ route('Dashboard.favorite') }}">
                  <i class="fa-solid fa-heart me-2 text-danger"></i>
                  Sự kiện yêu thích
                </a>
              </li>
              <li>
                <hr class="dropdown-divider my-2">
              </li>
              <li>
                <a class="dropdown-item small rounded-2 py-1.5 text-danger" href="{{ route('Auth.logout') }}">
                  <i class="fa-solid fa-arrow-right-from-bracket me-2 text-danger"></i>
                  Đăng xuất
                </a>
              </li>
            </ul>
          </div>
          @else
          <div class="d-flex align-items-center gap-2">
            <a href="{{ route('Auth.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">Đăng nhập</a>
            <a href="{{ route('Auth.index') }}?mode=register" class="btn btn-sm btn-dark rounded-pill px-3">Đăng ký</a>
          </div>
          @endif
        </div>
      </div>
    </div>
  </nav>
</header>