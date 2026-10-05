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
            <a href="{{ route('Dashboard.favorite') }}" class="nav-link {{ request()->routeIs('Dashboard.favorite') ? 'active fw-semibold text-dark bg-light' : 'text-secondary' }} px-3 py-2 rounded-pill small d-flex align-items-center gap-1.5">
              <svg class="text-danger" style="width: 15px; height: 15px; fill: currentColor;" viewBox="0 0 24 24">
                <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z" />
              </svg>
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
            <button class="btn btn-sm btn-light border rounded-pill d-flex align-items-center gap-2 px-3 py-1.5 shadow-xs" type="button" data-bs-toggle="dropdown" aria-expanded="false">
              <span class="rounded-circle bg-dark text-white fw-bold d-flex align-items-center justify-content-center" style="width: 24px; height: 24px; font-size: 11px;">
                {{ mb_strtoupper(mb_substr($currentUser->ho_ten ?? 'U', 0, 1)) }}
              </span>
              <span class="small fw-semibold text-dark text-truncate" style="max-width: 130px;">{{ $currentUser->ho_ten }}</span>
              <span class="badge {{ $currentUser->vai_tro === 'admin' ? 'bg-danger' : 'bg-primary-subtle text-primary' }} rounded-pill" style="font-size: 10px;">
                {{ $currentUser->vai_tro === 'admin' ? 'Admin' : 'Thành viên' }}
              </span>
              <svg style="width: 12px; height: 12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
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
                  <svg class="me-1.5" style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                  </svg>
                  Hồ sơ cá nhân
                </a>
              </li>
              <li>
                <a class="dropdown-item small rounded-2 py-1.5" href="{{ route('Dashboard.myTicket') }}">
                  <svg class="me-1.5" style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                  </svg>
                  Vé của tôi
                </a>
              </li>
              <li>
                <a class="dropdown-item small rounded-2 py-1.5" href="{{ route('Dashboard.favorite') }}">
                  <svg class="me-1.5 text-danger" style="width: 14px; height: 14px;" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z" />
                  </svg>
                  Sự kiện yêu thích
                </a>
              </li>
              <li>
                <hr class="dropdown-divider my-2">
              </li>
              <li>
                <a class="dropdown-item small rounded-2 py-1.5 text-danger" href="{{ route('Auth.logout') }}">
                  <svg class="me-1.5" style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                  </svg>
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