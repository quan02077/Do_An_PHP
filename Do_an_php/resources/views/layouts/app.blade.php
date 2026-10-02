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
  
  <!-- Container chứa Toast thông báo trong trang -->
  <div id="toast-container" class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1090;"></div>

  <!-- Modal Xác nhận trong trang (In-page Confirm Modal) -->
  <div class="modal fade" id="appConfirmModal" tabindex="-1" aria-labelledby="appConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 380px;">
      <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
        <div class="modal-body p-4 text-center">
          <div class="rounded-circle bg-danger-subtle text-danger d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 54px; height: 54px;">
            <svg style="width: 26px; height: 26px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
          </div>
          <h5 class="modal-title fw-bold text-dark mb-2 fs-5" id="appConfirmModalTitle">Xác nhận thao tác</h5>
          <p class="text-secondary small mb-4 fs-6" id="appConfirmModalMessage">Bạn có chắc chắn Không!</p>
          <div class="d-flex gap-2">
            <button type="button" class="btn btn-light border flex-grow-1 py-2 rounded-3 fw-medium small" data-bs-dismiss="modal">
              Hủy bỏ
            </button>
            <button type="button" class="btn btn-danger flex-grow-1 py-2 rounded-3 fw-semibold small shadow-sm" id="appConfirmModalBtn">
              Đồng ý
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Script tiện ích toàn cục: In-page Confirm & In-page Toast -->
  <script>
    // Hàm hiển thị Modal xác nhận ngay trong trang thay cho confirm() của trình duyệt
    window.showConfirmModal = function(message, onConfirm, title = 'Xác nhận thao tác', confirmBtnText = 'Đồng ý', confirmBtnClass = 'btn-danger') {
      const modalEl = document.getElementById('appConfirmModal');
      const msgEl = document.getElementById('appConfirmModalMessage');
      const titleEl = document.getElementById('appConfirmModalTitle');
      const btnEl = document.getElementById('appConfirmModalBtn');

      if (msgEl) msgEl.textContent = message || 'Bạn có chắc chắn Không!';
      if (titleEl) titleEl.textContent = title;
      if (btnEl) {
        btnEl.textContent = confirmBtnText;
        btnEl.className = 'btn ' + confirmBtnClass + ' flex-grow-1 py-2 rounded-3 fw-semibold small shadow-sm';
        
        // Gán sự kiện click mới
        const newBtn = btnEl.cloneNode(true);
        btnEl.parentNode.replaceChild(newBtn, btnEl);
        newBtn.addEventListener('click', () => {
          const bsModal = bootstrap.Modal.getInstance(modalEl);
          if (bsModal) bsModal.hide();
          if (typeof onConfirm === 'function') onConfirm();
        });
      }

      const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
      modal.show();
    };

    // Hàm hiển thị Toast thông báo ngay trong trang thay cho alert() của trình duyệt
    window.showToast = function(message, type = 'success') {
      const container = document.getElementById('toast-container');
      if (!container) return;

      const bgClass = type === 'success' ? 'bg-dark text-white' : (type === 'danger' || type === 'error' ? 'bg-danger text-white' : 'bg-primary text-white');
      const icon = type === 'success' 
        ? '<svg style="width: 18px; height: 18px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>'
        : '<svg style="width: 18px; height: 18px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';

      const toastEl = document.createElement('div');
      toastEl.className = `toast align-items-center ${bgClass} border-0 shadow-lg rounded-3 mb-2 show`;
      toastEl.setAttribute('role', 'alert');
      toastEl.setAttribute('aria-live', 'assertive');
      toastEl.setAttribute('aria-atomic', 'true');
      toastEl.innerHTML = `
        <div class="d-flex align-items-center justify-content-between">
          <div class="toast-body d-flex align-items-center gap-2 small py-2.5 px-3">
            ${icon}
            <span class="fw-medium">${message}</span>
          </div>
          <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
      `;

      container.appendChild(toastEl);
      setTimeout(() => {
        toastEl.classList.remove('show');
        setTimeout(() => toastEl.remove(), 300);
      }, 3500);
    };
  </script>

  @stack('scripts')
</body>

</html>