@extends('layouts.app')

@section('title', 'Vé của tôi — QQQ')

@section('content')
<div class="container-xl py-4">

  <!-- Banner thông tin tài khoản -->
  @if(isset($user) && $user)
  <div class="bg-white rounded-4 border shadow-sm p-4 mb-4 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
    <div class="d-flex align-items-center gap-3">
      <div class="rounded-3 bg-dark text-white fw-bold d-flex align-items-center justify-content-center shadow-sm flex-shrink-0 fs-3" style="width: 56px; height: 56px;">
        {{ mb_strtoupper(mb_substr($user->ho_ten ?? 'U', 0, 1)) }}
      </div>
      <div>
        <div class="d-flex align-items-center gap-2 mb-1">
          <h1 class="fs-5 fw-bold tracking-tight text-dark mb-0">{{ $user->ho_ten }}</h1>
          <span class="badge rounded-pill {{ $user->vai_tro === 'admin' ? 'bg-danger' : 'bg-primary-subtle text-primary border border-primary-subtle' }}">
            {{ $user->vai_tro === 'admin' ? 'Quản trị viên' : 'Thành viên' }}
          </span>
        </div>
        <div class="d-flex flex-wrap gap-2 small text-muted">
          <span>{{ $user->email }}</span>
        </div>
      </div>
    </div>

    <div class="d-flex align-items-center gap-2">
      <a href="{{ route('Dashboard.favorite') }}" class="btn btn-sm btn-outline-secondary rounded-3 px-3 py-2 fw-medium small d-flex align-items-center gap-2">
        <i class="fa-solid fa-heart text-danger"></i>
        <span>Xem yêu thích</span>
      </a>
      <a href="{{ route('Home.index') }}" class="btn btn-sm btn-dark rounded-3 px-3 py-2 fw-medium small">
        + Đặt thêm vé
      </a>
    </div>
  </div>
  @endif

  <!-- Header Tiêu đề & Đếm số vé -->
  <div class="d-flex align-items-center justify-content-between mb-3">
    <div>
      <h2 class="fs-4 fw-bold text-dark mb-0">Vé đã đăng ký của bạn</h2>
    </div>
    <span class="badge rounded-pill bg-white text-dark border px-3 py-2 fw-semibold">
      Tổng cộng: <span id="tickets-count">{{ count($tickets) }}</span> vé
    </span>
  </div>

  <!-- Danh sách vé từ CSDL -->
  <div class="d-flex flex-column gap-3" id="tickets-list">
    @forelse ($tickets as $ticket)
    @php
    $ev = $ticket->suKien;
    $img = $ev ? $ev->url_hinh_anh : asset('uploads/su_kien/001.jpg');
    $code = $ticket->ma_ve ?: ('QQQ-2026-' . str_pad($ticket->id, 4, '0', STR_PAD_LEFT));
    $date = ($ev && $ev->thoi_gian_bat_dau) ? date('H:i - d/m/Y', strtotime($ev->thoi_gian_bat_dau)) : 'Đang cập nhật';
    $isCancelled = ($ticket->trang_thai === 'da_huy');
    @endphp

    <div class="bg-white rounded-4 border shadow-sm p-3 p-sm-4 d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3 ticket-card {{ $isCancelled ? 'opacity-75 bg-light-subtle' : '' }}" id="ticket-item-{{ $ticket->id }}">
      <div class="d-flex align-items-center gap-3">
        <img src="{{ $img }}" alt="{{ $ev->ten_su_kien ?? 'Sự kiện' }}" class="rounded-3 object-fit-cover flex-shrink-0" style="width: 80px; height: 80px;" />
        <div>
          <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
            <span class="badge rounded-pill bg-light text-dark border font-monospace" style="font-size: 12px; letter-spacing: 0.5px;">{{ $code }}</span>
            <span id="badge-status-{{ $ticket->id }}">
              @if ($ticket->trang_thai === 'da_check_in')
              <span class="badge bg-success text-white rounded-pill">Đã check-in</span>
              @elseif ($ticket->trang_thai === 'cho_duyet')
              <span class="badge bg-warning text-dark rounded-pill">Chờ duyệt</span>
              @elseif ($isCancelled)
              <span class="badge bg-danger text-white rounded-pill">Đã hủy</span>
              @else
              <span class="badge bg-primary text-white rounded-pill">Đã xác nhận</span>
              @endif
            </span>
          </div>

          <h3 class="fs-6 fw-bold text-dark mb-1 text-truncate" style="max-width: 480px;">
            <a href="{{ $ev ? route('Event.show', $ev->id) : '#' }}" class="text-decoration-none text-dark hover-underline">
              {{ $ev->ten_su_kien ?? 'Sự kiện không còn tồn tại' }}
            </a>
          </h3>

          <div class="d-flex flex-wrap gap-2 small text-muted">
            <span>{{ $date }}</span>
            @if(!empty($ev->dia_diem))
            <span>•</span>
            <span>{{ $ev->dia_diem }}</span>
            @endif
            @if(!empty($ticket->thoi_gian_dang_ky))
            <span>•</span>
            <span class="text-secondary">Đăng ký lúc: {{ date('d/m/Y H:i', strtotime($ticket->thoi_gian_dang_ky)) }}</span>
            @endif
          </div>
        </div>
      </div>

      <div class="d-flex align-items-center gap-2 w-100 w-md-auto pt-2 pt-md-0 border-top border-md-top-0 flex-wrap flex-md-nowrap">
        @if(!$isCancelled)
        <button
          type="button"
          class="btn btn-sm btn-dark rounded-3 px-3 py-2 fw-medium d-flex align-items-center gap-1.5 flex-grow-1 flex-md-grow-0 btn-show-ticket"
          data-ticket-code="{{ $code }}"
          onclick="openTicketModal('{{ $ticket->id }}', '{{ $code }}', '{{ addslashes($ev->ten_su_kien ?? 'Sự kiện') }}', '{{ addslashes($date) }}', '{{ addslashes($ev->dia_diem ?? 'Địa điểm cập nhật') }}', '{{ addslashes($user->ho_ten ?? 'Khách') }}', '{{ addslashes($user->email ?? '') }}', '{{ $img }}', '{{ $ticket->trang_thai }}')">
          <i class="fa-solid fa-qrcode"></i>
          <span>Xem vé & QR</span>
        </button>
        @endif

        @if($ev)
        <a href="{{ route('Event.show', $ev->id) }}" class="btn btn-sm btn-outline-secondary rounded-3 px-3 py-2 flex-grow-1 flex-md-grow-0 fw-medium">
          Xem sự kiện
        </a>
        @endif

        <div id="cancel-btn-wrapper-{{ $ticket->id }}">
          @if (!$isCancelled)
          <form action="{{ route('Tickets.cancel', $ticket->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn hủy vé này không?');" class="d-inline">
            @csrf
            <button
              type="submit"
              class="btn btn-sm btn-outline-danger rounded-3 px-3 py-2 flex-grow-1 flex-md-grow-0 fw-medium d-flex align-items-center gap-1"
              id="btn-cancel-{{ $ticket->id }}">
              <i class="fa-solid fa-xmark"></i>
              <span>Hủy vé</span>
            </button>
          </form>
          @else
          <span class="text-muted small fst-italic px-2">Vé đã hủy</span>
          @endif
        </div>
      </div>
    </div>
    @empty
    <div class="text-center py-5 bg-white rounded-4 border shadow-sm p-4">
      <div class="rounded-circle bg-light text-muted d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 56px; height: 56px;">
        <i class="fa-solid fa-ticket fs-3"></i>
      </div>
      <h3 class="fs-6 fw-bold text-dark mb-1">Tài khoản này chưa có vé đăng ký nào</h3>
      <p class="small text-muted mb-3">Hãy khám phá các sự kiện hấp dẫn và đăng ký tham gia ngay hôm nay.</p>
      <a href="{{ route('Home.index') }}" class="btn btn-dark btn-sm rounded-3 px-3 py-2 fw-medium">
        Khám phá sự kiện ngay
      </a>
    </div>
    @endforelse
  </div>

</div>

<!-- Modal Vé Điện Tử Phong Cách MoMo / Ticketbox -->
<div class="modal fade" id="ticketModal" tabindex="-1" aria-labelledby="ticketModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
    <div class="modal-content border-0 bg-transparent shadow-none">
      <div class="modal-body p-0">
        <!-- Ticket Container -->
        <div id="momo-ticket-card" class="bg-white rounded-4 shadow-lg border overflow-hidden position-relative" style="background: #ffffff;">
          
          <!-- Top Header: Brand & Status -->
          <div class="bg-dark text-white p-4 pb-3">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <span class="small text-uppercase tracking-wider text-light opacity-75 font-monospace" style="font-size: 11px;">
                <i class="fa-solid fa-ticket me-1"></i> EVENTVN • VÉ ĐIỆN TỬ
              </span>
              <span id="modal-ticket-badge" class="badge rounded-pill bg-success text-white px-2.5 py-1 small">
                ĐÃ XÁC NHẬN
              </span>
            </div>
            <h3 id="modal-ticket-event-name" class="fs-5 fw-bold text-white mb-0 text-truncate-2" style="line-height: 1.35;">
              Tên sự kiện
            </h3>
          </div>

          <!-- Body Info -->
          <div class="p-4 pt-3 pb-3">
            <div class="d-flex align-items-center gap-3 mb-3 p-2.5 rounded-3 bg-light border">
              <img id="modal-ticket-img" src="" alt="Poster" class="rounded-2 object-fit-cover flex-shrink-0" style="width: 54px; height: 54px;">
              <div class="small">
                <div class="text-muted" style="font-size: 11px;">NGƯỜI THAM DỰ</div>
                <div id="modal-ticket-user-name" class="fw-bold text-dark text-truncate" style="max-width: 260px;">Họ tên</div>
                <div id="modal-ticket-user-email" class="text-secondary text-truncate" style="font-size: 12px; max-width: 260px;">Email</div>
              </div>
            </div>

            <div class="d-flex flex-column gap-2 small text-secondary">
              <div class="d-flex align-items-center gap-2">
                <i class="fa-regular fa-calendar-check text-muted" style="width: 16px;"></i>
                <span id="modal-ticket-date" class="text-dark fw-medium">Thời gian</span>
              </div>
              <div class="d-flex align-items-start gap-2">
                <i class="fa-solid fa-location-dot text-danger mt-0.5" style="width: 16px;"></i>
                <span id="modal-ticket-location" class="text-dark">Địa điểm</span>
              </div>
            </div>
          </div>

          <!-- Notch Divider (Đường răng cưa vé kiểu MoMo) -->
          <div class="position-relative py-1">
            <div style="border-top: 2px dashed #e2e8f0; width: 100%;"></div>
            <div class="position-absolute rounded-circle bg-light border" style="width: 26px; height: 26px; left: -14px; top: -12px;"></div>
            <div class="position-absolute rounded-circle bg-light border" style="width: 26px; height: 26px; right: -14px; top: -12px;"></div>
          </div>

          <!-- Bottom: Dynamic QR Code -->
          <div class="p-4 pt-3 text-center bg-white">
            <div class="d-inline-block p-2.5 bg-white border rounded-3 shadow-xs mb-2">
              <div id="ticket-qr-code" class="d-flex justify-content-center align-items-center" style="width: 150px; height: 150px;"></div>
            </div>
            
            <div class="font-monospace fw-bold text-dark fs-5 tracking-wide mb-1" id="modal-ticket-code">
              QQQ-2026-XXXXXX
            </div>
            <p class="text-muted small mb-0" style="font-size: 11.5px;">
              Xuất trình mã QR này tại cổng sự kiện để Ban tổ chức quét check-in.
            </p>
          </div>

          <!-- Action Buttons inside Ticket -->
          <div class="p-3 bg-light border-top d-flex gap-2 btn-print-hide">
            <button type="button" class="btn btn-dark w-100 py-2 fw-medium rounded-3 small d-flex align-items-center justify-content-center gap-1.5" onclick="printTicket()">
              <i class="fa-solid fa-print"></i>
              <span>In / Lưu PDF</span>
            </button>
            <button type="button" class="btn btn-outline-secondary w-50 py-2 fw-medium rounded-3 small" data-bs-dismiss="modal">
              Đóng
            </button>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>

<!-- Print Stylesheet -->
<style>
@media print {
  body * {
    visibility: hidden !important;
  }
  #momo-ticket-card, #momo-ticket-card * {
    visibility: visible !important;
  }
  #momo-ticket-card {
    position: absolute !important;
    left: 50% !important;
    top: 30px !important;
    transform: translateX(-50%) !important;
    width: 380px !important;
    border: 1px solid #111 !important;
    box-shadow: none !important;
  }
  .btn-print-hide, .modal-backdrop {
    display: none !important;
  }
}
</style>

<!-- QRCode.js Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

<script>
let ticketModalInstance = null;

function openTicketModal(id, code, title, date, location, name, email, img, status) {
  document.getElementById('modal-ticket-event-name').textContent = title;
  document.getElementById('modal-ticket-user-name').textContent = name;
  document.getElementById('modal-ticket-user-email').textContent = email;
  document.getElementById('modal-ticket-date').textContent = date;
  document.getElementById('modal-ticket-location').textContent = location;
  document.getElementById('modal-ticket-code').textContent = code;
  document.getElementById('modal-ticket-img').src = img;

  // Status Badge
  const badgeEl = document.getElementById('modal-ticket-badge');
  if (status === 'da_tham_gia' || status === 'da_check_in') {
    badgeEl.className = 'badge rounded-pill bg-info text-white px-2.5 py-1 small';
    badgeEl.textContent = 'ĐÃ CHECK-IN';
  } else if (status === 'da_huy') {
    badgeEl.className = 'badge rounded-pill bg-danger text-white px-2.5 py-1 small';
    badgeEl.textContent = 'ĐÃ HỦY';
  } else {
    badgeEl.className = 'badge rounded-pill bg-success text-white px-2.5 py-1 small';
    badgeEl.textContent = 'ĐÃ XÁC NHẬN';
  }

  // Generate QR Code
  const qrContainer = document.getElementById('ticket-qr-code');
  qrContainer.innerHTML = '';
  new QRCode(qrContainer, {
    text: code,
    width: 150,
    height: 150,
    colorDark: "#111827",
    colorLight: "#ffffff",
    correctLevel: QRCode.CorrectLevel.H
  });

  if (!ticketModalInstance) {
    ticketModalInstance = new bootstrap.Modal(document.getElementById('ticketModal'));
  }
  ticketModalInstance.show();
}

function printTicket() {
  window.print();
}

// Tự động mở vé khi vừa đặt vé thành công
@if(session('new_ticket_code'))
document.addEventListener('DOMContentLoaded', function() {
  const targetCode = '{{ session('new_ticket_code') }}';
  const btn = document.querySelector(`.btn-show-ticket[data-ticket-code="${targetCode}"]`);
  if (btn) {
    setTimeout(() => {
      btn.click();
    }, 400);
  }
});
@endif
</script>
@endsection