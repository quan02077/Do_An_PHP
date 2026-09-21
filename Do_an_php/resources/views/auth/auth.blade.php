<!doctype html>
<html lang="vi">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Đăng nhập & Đăng ký — QQQ</title>
    <!-- Bootstrap 5.3.3 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/style.css') }}" />
  </head>
  <body class="bg-body-tertiary text-dark min-vh-100 d-flex flex-column">
    <!-- Header đơn giản -->
    <header class="bg-white border-bottom py-3">
      <div class="container d-flex align-items-center justify-content-between">
        <a href="{{ route('Home.index') }}" class="navbar-brand d-flex align-items-center gap-2 m-0 p-0 text-decoration-none">

          <span class="fs-5 fw-bold tracking-tight text-dark m-0">QQQ</span>
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
        
        <!-- Tab Chuyển Đăng nhập / Đăng ký thuần Blade (Không cần JS) -->
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

        @if (request('mode') !== 'register')
        <!-- ─── FORM ĐĂNG NHẬP ──────────────────────────────────────────────── -->
        <form id="login-form">
          <div class="mb-3">
            <label for="login-email" class="form-label small fw-medium text-dark mb-1">Địa chỉ Email</label>
            <div class="input-group">
              <span class="input-group-text bg-white border-end-0 text-muted ps-3 pe-2">
                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
              </span>
              <input 
                type="email" 
                id="login-email" 
                name="email"
                required 
                placeholder="name@example.com" 
                class="form-control border-start-0 ps-1 py-2"
              />
            </div>
          </div>

          <div class="mb-3">
            <div class="d-flex align-items-center justify-content-between mb-1">
              <label for="login-password" class="form-label small fw-medium text-dark mb-0">Mật khẩu</label>
              <a href="#" onclick="alert('Tính năng khôi phục mật khẩu đang được kết nối hệ thống!')" class="small text-secondary text-decoration-none">Quên mật khẩu?</a>
            </div>
            <div class="input-group">
              <span class="input-group-text bg-white border-end-0 text-muted ps-3 pe-2">
                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
              </span>
              <input 
                type="password" 
                id="login-password" 
                name="password" 
                required 
                placeholder="••••••••" 
                class="form-control border-start-0 ps-1 py-2"
              />
            </div>
          </div>
          <button 
            type="submit" 
            class="btn btn-dark w-100 py-2.5 fw-medium shadow-sm rounded-3"
          >
            Đăng nhập vào tài khoản
          </button>
        </form>
        @else
        <!-- ─── FORM ĐĂNG KÝ ──────────────────────────────────────────────── -->
        <form id="register-form">
          <div class="mb-3">
            <label for="reg-name" class="form-label small fw-medium text-dark mb-1">Họ và tên</label>
            <div class="input-group">
              <span class="input-group-text bg-white border-end-0 text-muted ps-3 pe-2">
                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
              </span>
              <input 
                type="text" 
                id="reg-name" 
                name="name"
                required 
                placeholder="Nguyễn Văn A" 
                class="form-control border-start-0 ps-1 py-2"
              />
            </div>
          </div>

          <div class="mb-3">
            <label for="reg-email" class="form-label small fw-medium text-dark mb-1">Địa chỉ Email</label>
            <div class="input-group">
              <span class="input-group-text bg-white border-end-0 text-muted ps-3 pe-2">
                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
              </span>
              <input 
                type="email" 
                id="reg-email" 
                name="email"
                required 
                placeholder="vana@example.com" 
                class="form-control border-start-0 ps-1 py-2"
              />
            </div>
          </div>

          <div class="mb-3">
            <label for="reg-phone" class="form-label small fw-medium text-dark mb-1">Số điện thoại</label>
            <div class="input-group">
              <span class="input-group-text bg-white border-end-0 text-muted ps-3 pe-2">
                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
              </span>
              <input 
                type="tel" 
                id="reg-phone" 
                name="phone"
                required 
                placeholder="0912 345 678" 
                class="form-control border-start-0 ps-1 py-2"
              />
            </div>
          </div>

          <div class="mb-3">
            <label for="reg-password" class="form-label small fw-medium text-dark mb-1">Mật khẩu</label>
            <div class="input-group">
              <span class="input-group-text bg-white border-end-0 text-muted ps-3 pe-2">
                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
              </span>
              <input 
                type="password" 
                id="reg-password" 
                name="password" 
                required 
                placeholder="Tối thiểu 6 ký tự" 
                class="form-control border-start-0 ps-1 py-2"
              />
            </div>
          </div>

          <div class="form-check mb-4">
            <input type="checkbox" id="reg-agree" required class="form-check-input" />
            <label for="reg-agree" class="form-check-label small text-secondary">
              Tôi đồng ý với <a href="#" class="text-dark fw-medium text-decoration-underline">Điều khoản dịch vụ</a> và Chính sách bảo mật của QQQ.
            </label>
          </div>

          <button 
            type="submit" 
            class="btn btn-dark w-100 py-2.5 fw-medium shadow-sm rounded-3"
          >
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
