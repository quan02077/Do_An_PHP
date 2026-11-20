{{-- Container hiển thị thông báo ngắn (Bootstrap Toast) cho thao tác AJAX --}}
<div
  id="qqq-toast-container"
  class="toast-container position-fixed bottom-0 end-0 p-3"
  style="z-index: 1080;"
  aria-live="polite"
  aria-atomic="true"></div>

<script>
(() => {
  const loginUrl = @json(route('Auth.index'));

  // Lấy CSRF token ưu tiên từ input của form, dự phòng từ thẻ meta
  function getCsrfToken(form) {
    return (
      form?.querySelector('input[name="_token"]')?.value ||
      document.querySelector('meta[name="csrf-token"]')?.content ||
      ''
    );
  }

  // Hiển thị thông báo ngắn bằng Bootstrap Toast (có fallback sang alert nhỏ)
  function showToast(message, variant = 'success') {
    const container = document.getElementById('qqq-toast-container');
    if (!container || !message) return;

    const colorClass =
      variant === 'danger'
        ? 'text-bg-danger'
        : variant === 'warning'
          ? 'text-bg-warning'
          : 'text-bg-dark';

    if (window.bootstrap && window.bootstrap.Toast) {
      const toastEl = document.createElement('div');
      toastEl.className = `toast align-items-center ${colorClass} border-0 shadow-sm`;
      toastEl.setAttribute('role', 'status');
      toastEl.setAttribute('aria-live', 'polite');
      toastEl.setAttribute('aria-atomic', 'true');

      const bodyWrap = document.createElement('div');
      bodyWrap.className = 'd-flex';

      const body = document.createElement('div');
      body.className = 'toast-body small fw-medium';
      body.textContent = message;

      const closeBtn = document.createElement('button');
      closeBtn.type = 'button';
      closeBtn.className = `btn-close ${variant === 'warning' ? '' : 'btn-close-white'} me-2 m-auto`;
      closeBtn.setAttribute('data-bs-dismiss', 'toast');
      closeBtn.setAttribute('aria-label', 'Đóng');

      bodyWrap.appendChild(body);
      bodyWrap.appendChild(closeBtn);
      toastEl.appendChild(bodyWrap);
      container.appendChild(toastEl);

      const toast = window.bootstrap.Toast.getOrCreateInstance(toastEl, { delay: 3000 });
      toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
      toast.show();
      return;
    }

    const alertEl = document.createElement('div');
    alertEl.className = `alert alert-${variant} py-2 px-3 small shadow-sm mb-2`;
    alertEl.setAttribute('role', 'status');
    alertEl.textContent = message;
    container.appendChild(alertEl);
    setTimeout(() => alertEl.remove(), 3500);
  }

  // Xử lý chuyển hướng đăng nhập nếu gặp 401 hoặc bị middleware redirect về trang auth
  async function handleAuthRedirect(response) {
    if (response.status === 401) {
      let redirectTo = loginUrl;
      try {
        const data = await response.json();
        if (data && data.redirect) redirectTo = data.redirect;
      } catch (_) {}
      window.location.href = redirectTo;
      return true;
    }

    const contentType = response.headers.get('content-type') || '';
    if (response.redirected && !contentType.includes('application/json')) {
      window.location.href = response.url || loginUrl;
      return true;
    }

    return false;
  }

  // Gắn sự kiện submit theo cơ chế event delegation để không vỡ sau khi AJAX tìm kiếm thay DOM
  document.addEventListener('submit', async (event) => {
    const form = event.target;
    if (!(form instanceof HTMLFormElement)) return;

    // 1. AJAX Yêu thích (Toggle Favorite)
    if (form.matches('form[data-ajax-favorite]')) {
      event.preventDefault();
      if (form.dataset.submitting === '1') return;

      const submitBtn = form.querySelector('button[type="submit"]');
      form.dataset.submitting = '1';
      if (submitBtn) submitBtn.disabled = true;

      try {
        const response = await fetch(form.action, {
          method: 'POST',
          credentials: 'same-origin',
          headers: {
            'X-CSRF-TOKEN': getCsrfToken(form),
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
          },
        });

        if (await handleAuthRedirect(response)) return;

        let data = null;
        try {
          data = await response.json();
        } catch (_) {}

        if (!response.ok || !data || data.success !== true) {
          showToast(data?.message || 'Không thể cập nhật yêu thích. Vui lòng thử lại.', 'danger');
          return;
        }

        const isFav = Boolean(data.favorited);
        const label = isFav ? 'Bỏ lưu khỏi yêu thích' : 'Lưu vào yêu thích';

        if (submitBtn) {
          submitBtn.classList.toggle('text-danger', isFav);
          submitBtn.classList.toggle('text-secondary', !isFav);
          submitBtn.title = label;
          submitBtn.setAttribute('aria-label', label);
          submitBtn.setAttribute('aria-pressed', isFav ? 'true' : 'false');

          const icon = submitBtn.querySelector('i');
          if (icon) {
            icon.classList.toggle('fa-solid', isFav);
            icon.classList.toggle('fa-regular', !isFav);
            icon.classList.toggle('text-danger', isFav);
            icon.classList.toggle('text-secondary', !isFav);
            icon.classList.remove('animate-heart-pop');
            void icon.offsetWidth;
            icon.classList.add('animate-heart-pop');
          }
        }

        if (typeof data.fav_count === 'number') {
          const navBadge = document.getElementById('nav-fav-badge');
          if (navBadge) {
            navBadge.textContent = String(data.fav_count);
            navBadge.classList.toggle('d-none', data.fav_count <= 0);
          }
          const favsCount = document.getElementById('favs-count');
          if (favsCount) {
            favsCount.textContent = String(data.fav_count);
          }
        }

        showToast(data.message || (isFav ? 'Đã thêm sự kiện vào danh sách yêu thích!' : 'Đã xóa sự kiện khỏi danh sách yêu thích!'), 'success');
      } catch (_) {
        showToast('Lỗi kết nối mạng. Vui lòng thử lại sau.', 'danger');
      } finally {
        delete form.dataset.submitting;
        if (submitBtn) submitBtn.disabled = false;
      }
      return;
    }

    // 2. AJAX Hủy vé (Cancel Ticket)
    if (form.matches('form[data-ajax-cancel]')) {
      event.preventDefault();
      if (form.dataset.submitting === '1') return;

      const confirmText = form.dataset.confirmMessage || 'Bạn có chắc chắn muốn hủy vé này không?';
      if (!window.confirm(confirmText)) return;

      const submitBtn = form.querySelector('button[type="submit"]');
      form.dataset.submitting = '1';
      if (submitBtn) submitBtn.disabled = true;

      try {
        const response = await fetch(form.action, {
          method: 'POST',
          credentials: 'same-origin',
          headers: {
            'X-CSRF-TOKEN': getCsrfToken(form),
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
          },
        });

        if (await handleAuthRedirect(response)) return;

        let data = null;
        try {
          data = await response.json();
        } catch (_) {}

        if (!response.ok || !data || data.success !== true) {
          showToast(data?.message || 'Không thể hủy vé. Vui lòng thử lại.', 'danger');
          return;
        }

        const ticketId = form.dataset.ticketId || data.ticket_id;
        if (ticketId) {
          const badgeWrapper = document.getElementById(`badge-status-${ticketId}`);
          if (badgeWrapper) {
            badgeWrapper.innerHTML = '<span class="badge bg-danger text-white rounded-pill">Đã hủy</span>';
          }

          const cancelWrapper = document.getElementById(`cancel-btn-wrapper-${ticketId}`);
          if (cancelWrapper) {
            cancelWrapper.innerHTML = '<span class="text-muted small fst-italic px-2">Vé đã hủy</span>';
          }

          const qrBtn = document.getElementById(`btn-qr-${ticketId}`);
          if (qrBtn) {
            qrBtn.remove();
          }

          const card = document.getElementById(`ticket-item-${ticketId}`);
          if (card) {
            card.classList.add('opacity-75', 'bg-light-subtle');
          }
        }

        if (typeof data.ticket_count === 'number') {
          const navTicketBadge = document.getElementById('nav-ticket-badge');
          if (navTicketBadge) {
            navTicketBadge.textContent = String(data.ticket_count);
            navTicketBadge.classList.toggle('d-none', data.ticket_count <= 0);
          }
        }

        showToast(data.message || 'Đã hủy vé thành công!', 'success');
      } catch (_) {
        showToast('Lỗi kết nối mạng. Vui lòng thử lại sau.', 'danger');
      } finally {
        delete form.dataset.submitting;
        if (submitBtn && document.body.contains(submitBtn)) {
          submitBtn.disabled = false;
        }
      }
    }
  });
})();
</script>
