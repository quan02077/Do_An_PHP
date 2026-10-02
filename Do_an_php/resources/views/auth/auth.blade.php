<!doctype html>
<html lang="vi">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Đăng nhập & Đăng ký — EventVN</title>
    <!-- Bootstrap 5.3.3 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/style.css') }}" />
  </head>
  <body class="bg-body-tertiary text-dark min-vh-100 d-flex flex-column">
    <!-- Header đơn giản -->
    <header class="bg-white border-bottom py-3">
      <div class="container d-flex align-items-center justify-content-between">
        <a href="{{ route('Home.index') }}" class="navbar-brand d-flex align-items-center gap-2 m-0 p-0 text-decoration-none">
          <div class="rounded-3 bg-dark text-white d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 32px; height: 32px; font-size: 1rem;">
            E
          </div>
          <span class="fs-5 fw-bold tracking-tight text-dark m-0">Event<span class="text-secondary fw-normal">VN</span></span>
        </a>
        <a href="{{ route('Home.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 d-flex align-items-center gap-1.5 small">
          <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
          <span>Quay lại Trang chủ</span>
        </a>
      </div>
    </header>

    <!-- Main Content Form -->
    <main class="container grow d-flex align-items-center justify-content-center py-5">
      <div class="w-100 bg-white rounded-4 border shadow-sm p-4 p-sm-5" style="max-width: 440px;">
        
        <!-- Tab Chuyển Đăng nhập / Đăng ký thuần Blade -->
        <div class="btn-group w-100 p-1 bg-light rounded-pill mb-4" role="group">
          <a 
            href="{{ route('Auth.index') }}" 
            class="btn btn-sm rounded-pill fw-medium {{ request('mode') !== 'register' ? 'btn-dark shadow-xs' : 'text-secondary' }}"
          >
            Đăng nhập
          </a>
          <a 
            href="{{ route('Auth.index', ['mode' => 'register']) }}" 
            class="btn btn-sm rounded-pill fw-medium {{ request('mode') === 'register' ? 'btn-dark shadow-xs' : 'text-secondary' }}"
          >
            Đăng ký
          </a>
        </div>

        @if ($errors->any())
          <div class="alert alert-danger py-2 px-3 small rounded-3 mb-3">
            <ul class="mb-0 ps-3">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        @if (session('success'))
          <div class="alert alert-success py-2 px-3 small rounded-3 mb-3">
            {{ session('success') }}
          </div>
        @endif

        @if (session('warning'))
          <div class="alert alert-warning py-2 px-3 small rounded-3 mb-3">
            {{ session('warning') }}
          </div>
        @endif

        @if (request('mode') !== 'register')
        <!-- ─── FORM ĐĂNG NHẬP ──────────────────────────────────────────────── -->
        <form id="login-form" method="POST" action="{{ route('Auth.login') }}">
          @csrf
          <div class="mb-3">
            <label for="login-email" class="form-label small fw-medium text-dark mb-1">Địa chỉ Email <span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-text bg-light text-muted border-end-0">
                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
              </span>
              <input type="email" id="login-email" name="email" value="{{ old('email') }}" required placeholder="name@example.com" class="form-control border-start-0" />
            </div>
          </div>

          <div class="mb-3">
            <div class="d-flex align-items-center justify-content-between mb-1">
              <label for="login-password" class="form-label small fw-medium text-dark mb-0">Mật khẩu <span class="text-danger">*</span></label>
              <a href="#" onclick="alert('Vui lòng liên hệ quản trị viên hoặc dùng chức năng Đăng nhập nhanh bên dưới!')" class="small text-secondary text-decoration-none">Quên mật khẩu?</a>
            </div>
            <div class="input-group">
              <span class="input-group-text bg-light text-muted border-end-0">
                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
              </span>
              <input type="password" id="login-password" name="password" required placeholder="••••••••" class="form-control border-start-0" />
            </div>
          </div>

          <button type="submit" class="btn btn-dark w-100 py-2.5 mt-2 fw-medium shadow-sm rounded-3">
            Đăng nhập vào tài khoản
          </button>

          <!-- Đăng nhập 1-chạm Demo -->
          <div class="mt-4 pt-3 border-top text-center">
            <div class="small text-muted mb-2" style="font-size: 12px;">Đăng nhập nhanh 1-chạm (Test CSDL):</div>
            <div class="d-flex gap-2">
              <a href="{{ route('Auth.switch', 2) }}" class="btn btn-sm btn-outline-primary flex-grow-1 rounded-3 py-1.5 small">
                Thành viên (ID: 2)
              </a>
              <a href="{{ route('Auth.switch', 1) }}" class="btn btn-sm btn-outline-dark flex-grow-1 rounded-3 py-1.5 small">
                Quản trị viên (ID: 1)
              </a>
            </div>
          </div>
        </form>
        @else
        <!-- ─── FORM ĐĂNG KÝ ──────────────────────────────────────────────── -->
        <form id="register-form" method="POST" action="{{ route('Auth.register') }}">
          @csrf
          <div class="mb-3">
            <label for="reg-name" class="form-label small fw-medium text-dark mb-1">Họ và tên <span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-text bg-light text-muted border-end-0">
                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
              </span>
              <input type="text" id="reg-name" name="name" value="{{ old('name') }}" required placeholder="Nguyễn Văn A" class="form-control border-start-0" />
            </div>
          </div>

          <div class="mb-3">
            <label for="reg-email" class="form-label small fw-medium text-dark mb-1">Địa chỉ Email <span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-text bg-light text-muted border-end-0">
                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
              </span>
              <input type="email" id="reg-email" name="email" value="{{ old('email') }}" required placeholder="vana@example.com" class="form-control border-start-0" />
            </div>
          </div>

          <div class="mb-3">
            <label for="reg-phone" class="form-label small fw-medium text-dark mb-1">Số điện thoại</label>
            <div class="input-group">
              <span class="input-group-text bg-light text-muted border-end-0">
                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
              </span>
              <input type="tel" id="reg-phone" name="phone" value="{{ old('phone') }}" placeholder="0912 345 678" class="form-control border-start-0" />
            </div>
          </div>

          <div class="mb-3">
            <label for="reg-password" class="form-label small fw-medium text-dark mb-1">Mật khẩu <span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-text bg-light text-muted border-end-0">
                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
              </span>
              <input type="password" id="reg-password" name="password" required placeholder="Tối thiểu 6 ký tự" class="form-control border-start-0" />
            </div>
          </div>

          <div class="form-check mb-4">
            <input type="checkbox" id="reg-agree" required class="form-check-input" checked />
            <label for="reg-agree" class="form-check-label small text-secondary">
              Tôi đồng ý với <a href="#" class="text-dark fw-medium text-decoration-underline">Điều khoản dịch vụ</a> và Chính sách bảo mật của EventVN.
            </label>
          </div>

          <button type="submit" class="btn btn-dark w-100 py-2.5 fw-medium shadow-sm rounded-3">
            Đăng ký tài khoản Thành viên
          </button>
        </form>
        @endif
      </div>
    </main>

    <!-- Bootstrap 5.3.3 JS Bundle CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>
