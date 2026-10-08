<!doctype html>
<html lang="vi">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>
    @if(request('mode') === 'register')
    Đăng ký tài khoản — QQQ
    @elseif(request('mode') === 'forgot')
    Quên mật khẩu — QQQ
    @else
    Đăng nhập — QQQ
    @endif
  </title>
  <!-- Bootstrap 5.3.3 & Font Awesome 6.5.2 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
  <link rel="stylesheet" href="{{ asset('css/style.css') }}" />
</head>

<body class="bg-body-tertiary text-dark min-vh-100 d-flex flex-column">
  <!-- Header đơn giản -->
  <header class="bg-white border-bottom py-3">
    <div class="container d-flex align-items-center justify-content-between">
      <a href="{{ route('Home.index') }}" class="navbar-brand d-flex align-items-center gap-2 m-0 p-0 text-decoration-none">
        <div class="rounded-3 bg-dark text-white d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 32px; height: 32px; font-size: 1rem;">
          Q
        </div>
        <span class="fs-5 fw-bold tracking-tight text-dark m-0">QQQ</span>
      </a>
      <a href="{{ route('Home.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 d-flex align-items-center gap-2 small">
        <i class="fa-solid fa-arrow-left"></i>
        <span>Quay lại Trang chủ</span>
      </a>
    </div>
  </header>

  <!-- Main Content Form -->
  <main class="container grow d-flex align-items-center justify-content-center py-5">
    <div class="w-100 bg-white rounded-4 border shadow-sm p-4 p-sm-5" style="max-width: 440px;">

      <!-- Tab Chuyển Đăng nhập / Đăng ký hoặc Quay lại nếu Quên mật khẩu -->
      @if (request('mode') === 'forgot')
      <div class="mb-4">
        <a href="{{ route('Auth.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 d-inline-flex align-items-center gap-2 small text-decoration-none">
          <i class="fa-solid fa-arrow-left"></i>
          <span>Quay lại Đăng nhập</span>
        </a>
      </div>
      @else
      <div class="btn-group w-100 p-1 bg-light rounded-pill mb-4" role="group">
        <a
          href="{{ route('Auth.index') }}"
          class="btn btn-sm rounded-pill fw-medium {{ request('mode') !== 'register' ? 'btn-dark shadow-sm' : 'text-secondary' }}">
          Đăng nhập
        </a>
        <a
          href="{{ route('Auth.index', ['mode' => 'register']) }}"
          class="btn btn-sm rounded-pill fw-medium {{ request('mode') === 'register' ? 'btn-dark shadow-sm' : 'text-secondary' }}">
          Đăng ký
        </a>
      </div>
      @endif

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

      @if (request('mode') === 'forgot')
      <!-- ─── FORM QUÊN MẬT KHẨU ────────────────────────────────────────── -->
      <div class="text-center mb-4">
        <div class="d-inline-flex align-items-center justify-content-center bg-light text-dark rounded-circle mb-3 shadow-sm" style="width: 54px; height: 54px;">
          <i class="fa-solid fa-key fs-4"></i>
        </div>
        <h4 class="fw-bold text-dark mb-1 fs-5">Khôi phục mật khẩu</h4>
        <p class="text-secondary small mb-0">Xác minh thông tin tài khoản đã đăng ký để thiết lập lại mật khẩu mới.</p>
      </div>

      <form id="forgot-form" method="POST" action="{{ route('Auth.resetPassword') }}">
        @csrf
        <div class="mb-3">
          <label for="forgot-email" class="form-label small fw-medium text-dark mb-1">Địa chỉ Email đã đăng ký <span class="text-danger">*</span></label>
          <div class="input-group">
            <span class="input-group-text bg-light text-muted border-end-0">
              <i class="fa-regular fa-envelope"></i>
            </span>
            <input type="email" id="forgot-email" name="email" value="{{ old('email') }}" required placeholder="name@example.com" class="form-control border-start-0" />
          </div>
        </div>

        <div class="mb-3">
          <label for="forgot-phone" class="form-label small fw-medium text-dark mb-1">Số điện thoại xác minh <span class="text-danger">*</span></label>
          <div class="input-group">
            <span class="input-group-text bg-light text-muted border-end-0">
              <i class="fa-solid fa-phone"></i>
            </span>
            <input type="tel" id="forgot-phone" name="phone" value="{{ old('phone') }}" required placeholder="0912 345 678" class="form-control border-start-0" />
          </div>
        </div>

        <div class="mb-3">
          <label for="forgot-password" class="form-label small fw-medium text-dark mb-1">Mật khẩu mới <span class="text-danger">*</span></label>
          <div class="input-group">
            <span class="input-group-text bg-light text-muted border-end-0">
              <i class="fa-solid fa-lock"></i>
            </span>
            <input type="password" id="forgot-password" name="password" required placeholder="Tối thiểu 6 ký tự" class="form-control border-start-0" />
          </div>
        </div>

        <div class="mb-3">
          <label for="forgot-password-confirmation" class="form-label small fw-medium text-dark mb-1">Xác nhận mật khẩu mới <span class="text-danger">*</span></label>
          <div class="input-group">
            <span class="input-group-text bg-light text-muted border-end-0">
              <i class="fa-solid fa-shield-halved"></i>
            </span>
            <input type="password" id="forgot-password-confirmation" name="password_confirmation" required placeholder="Nhập lại mật khẩu mới" class="form-control border-start-0" />
          </div>
        </div>

        <div class="form-check form-switch d-flex align-items-center mb-3">
          <input class="form-check-input" type="checkbox" id="forgot-show-pass" onchange="show_hide_pass(this)">
          <label class="form-check-label ms-3" for="forgot-show-pass">
            Hiện mật khẩu
          </label>
        </div>

        <button type="submit" class="btn btn-dark w-100 py-2.5 mt-2 fw-medium shadow-sm rounded-3">
          Đặt lại mật khẩu
        </button>

        <div class="text-center mt-3">
          <a href="{{ route('Auth.index') }}" class="small text-secondary text-decoration-none d-inline-flex align-items-center gap-2">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Quay lại Đăng nhập</span>
          </a>
        </div>
      </form>
      @elseif (request('mode') !== 'register')
      <!-- ─── FORM ĐĂNG NHẬP ──────────────────────────────────────────────── -->
      <form id="login-form" method="POST" action="{{ route('Auth.login') }}">
        @csrf
        <div class="mb-3">
          <label for="login-email" class="form-label small fw-medium text-dark mb-1">Địa chỉ Email <span class="text-danger">*</span></label>
          <div class="input-group">
            <span class="input-group-text bg-light text-muted border-end-0">
              <i class="fa-regular fa-envelope"></i>
            </span>
            <input type="email" id="login-email" name="email" value="{{ old('email') }}" required placeholder="name@example.com" class="form-control border-start-0" />
          </div>
        </div>

        <div class="mb-3">
          <div class="d-flex align-items-center justify-content-between mb-1">
            <label for="login-password" class="form-label small fw-medium text-dark mb-0">Mật khẩu <span class="text-danger">*</span></label>
            <a href="{{ route('Auth.index', ['mode' => 'forgot']) }}" class="small text-secondary text-decoration-none">Quên mật khẩu?</a>
          </div>
          <div class="input-group">
            <span class="input-group-text bg-light text-muted border-end-0">
              <i class="fa-solid fa-lock"></i>
            </span>
            <input type="password" id="login-password" name="password" required placeholder="••••••••" class="form-control border-start-0" />
          </div>
        </div>

        <div class="form-check form-switch d-flex align-items-center mb-3">
          <input class="form-check-input" type="checkbox" id="login-show-pass" onchange="show_hide_pass(this)">
          <label class="form-check-label ms-3" for="login-show-pass">
            Hiện mật khẩu
          </label>
        </div>

        <button type="submit" class="btn btn-dark w-100 py-2.5 mt-2 fw-medium shadow-sm rounded-3">
          Đăng nhập vào tài khoản
        </button>
      </form>
      @else
      <!-- ─── FORM ĐĂNG KÝ ──────────────────────────────────────────────── -->
      <form id="register-form" method="POST" action="{{ route('Auth.register') }}">
        @csrf
        <div class="mb-3">
          <label for="reg-name" class="form-label small fw-medium text-dark mb-1">Họ và tên <span class="text-danger">*</span></label>
          <div class="input-group">
            <span class="input-group-text bg-light text-muted border-end-0">
              <i class="fa-regular fa-user"></i>
            </span>
            <input type="text" id="reg-name" name="name" value="{{ old('name') }}" required placeholder="Nguyễn Văn A" class="form-control border-start-0" />
          </div>
        </div>

        <div class="mb-3">
          <label for="reg-email" class="form-label small fw-medium text-dark mb-1">Địa chỉ Email <span class="text-danger">*</span></label>
          <div class="input-group">
            <span class="input-group-text bg-light text-muted border-end-0">
              <i class="fa-regular fa-envelope"></i>
            </span>
            <input type="email" id="reg-email" name="email" value="{{ old('email') }}" required placeholder="vana@example.com" class="form-control border-start-0" />
          </div>
        </div>

        <div class="mb-3">
          <label for="reg-phone" class="form-label small fw-medium text-dark mb-1">Số điện thoại</label>
          <div class="input-group">
            <span class="input-group-text bg-light text-muted border-end-0">
              <i class="fa-solid fa-phone"></i>
            </span>
            <input type="tel" id="reg-phone" name="phone" value="{{ old('phone') }}" placeholder="0912 345 678" class="form-control border-start-0" />
          </div>
        </div>

        <div class="mb-3">
          <label for="reg-password" class="form-label small fw-medium text-dark mb-1">Mật khẩu <span class="text-danger">*</span></label>
          <div class="input-group">
            <span class="input-group-text bg-light text-muted border-end-0">
              <i class="fa-solid fa-lock"></i>
            </span>
            <input type="password" id="reg-password" name="password" required placeholder="Tối thiểu 6 ký tự" class="form-control border-start-0" />
          </div>
        </div>

        <div class="mb-3">
          <label for="reg-password-confirmation" class="form-label small fw-medium text-dark mb-1">Xác nhận mật khẩu <span class="text-danger">*</span></label>
          <div class="input-group">
            <span class="input-group-text bg-light text-muted border-end-0">
              <i class="fa-solid fa-shield-halved"></i>
            </span>
            <input type="password" id="reg-password-confirmation" name="password_confirmation" required placeholder="Nhập lại mật khẩu" class="form-control border-start-0" />
          </div>
        </div>

        <div class="form-check form-switch d-flex align-items-center mb-3">
          <input class="form-check-input" type="checkbox" id="reg-show-pass" onchange="show_hide_pass(this)">
          <label class="form-check-label ms-3" for="reg-show-pass">
            Hiện mật khẩu
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
  <script>
    function show_hide_pass(checkbox) {
      const isChecked = checkbox ? checkbox.checked : false;
      const form = checkbox ? checkbox.closest('form') : document;
      const passwordInputs = form.querySelectorAll("input[name*='password']");

      passwordInputs.forEach(pass => {
        pass.type = isChecked ? "text" : "password";
      });
    }
  </script>
</body>

</html>