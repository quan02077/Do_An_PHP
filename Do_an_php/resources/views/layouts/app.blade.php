<!doctype html>
<html lang="vi">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>@yield('title', 'QQQ — Nền tảng Đặt vé & Quản lý Sự kiện')</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
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
            <i class="fa-solid fa-triangle-exclamation fs-4"></i>
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
        ? '<i class="fa-solid fa-check fs-6 me-1"></i>' 
        : '<i class="fa-solid fa-circle-exclamation fs-6 me-1"></i>';

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