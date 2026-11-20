@extends('layouts.app')

@section('title', 'Vé của tôi — QQQ')

@section('content')
<div class="container-xl py-4">

  <!-- Banner thông tin tài khoản -->
  @if(isset($user) && $user)
  <div class="bg-white rounded-4 border shadow-sm p-3 p-sm-4 mb-4 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
    <div class="d-flex align-items-center gap-3 min-w-0">
      <div class="rounded-3 bg-dark text-white fw-bold d-flex align-items-center justify-content-center shadow-sm flex-shrink-0 fs-3" style="width: 56px; height: 56px;">
        {{ mb_strtoupper(mb_substr($user->ho_ten ?? 'U', 0, 1)) }}
      </div>
      <div class="min-w-0">
        <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
          <h1 class="fs-5 fw-bold tracking-tight text-dark mb-0 text-break">{{ $user->ho_ten }}</h1>
          <span class="badge rounded-pill {{ $user->vai_tro === 'admin' ? 'bg-danger' : 'bg-primary-subtle text-primary border border-primary-subtle' }}">
            {{ $user->vai_tro === 'admin' ? 'Quản trị viên' : 'Thành viên' }}
          </span>
        </div>
        <div class="d-flex flex-wrap gap-2 small text-muted">
          <span class="text-break">{{ $user->email }}</span>
        </div>
      </div>
    </div>

    <div class="d-flex align-items-center gap-2 flex-wrap">
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
  <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
    <div>
      <h2 class="fs-4 fw-bold text-dark mb-0">Vé đã đăng ký của bạn</h2>
    </div>
    <span class="badge rounded-pill bg-white text-dark border px-3 py-2 fw-semibold flex-shrink-0">
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
    $ticketTitle = addslashes($ev->ten_su_kien ?? 'Sự kiện');
    $ticketLocation = addslashes($ev->dia_diem ?? 'Địa điểm cập nhật');
    $userName = addslashes($user->ho_ten ?? 'Khách');
    @endphp

    <div class="bg-white rounded-4 border shadow-sm p-3 p-sm-4 d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3 ticket-card {{ $isCancelled ? 'opacity-75 bg-light-subtle' : '' }}" id="ticket-item-{{ $ticket->id }}">
      <div class="d-flex align-items-start align-items-sm-center gap-3 w-100 min-w-0">
        <img src="{{ $img }}" alt="{{ $ev->ten_su_kien ?? 'Sự kiện' }}" class="rounded-3 object-fit-cover flex-shrink-0 ticket-thumb" />
        <div class="flex-grow-1 min-w-0">
          <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
            <span class="badge rounded-pill bg-light text-dark border font-monospace text-break" style="font-size: 12px; letter-spacing: 0.5px;">{{ $code }}</span>
            <span id="badge-status-{{ $ticket->id }}">
              @if ($ticket->trang_thai === 'da_check_in' || $ticket->trang_thai === 'da_tham_gia')
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

          <h3 class="fs-6 fw-bold text-dark mb-1 line-clamp-2 text-break">
            <a href="{{ $ev ? route('Event.show', $ev->id) : '#' }}" class="text-decoration-none text-dark hover-underline">
              {{ $ev->ten_su_kien ?? 'Sự kiện không còn tồn tại' }}
            </a>
          </h3>

          <div class="d-flex flex-wrap gap-2 small text-muted">
            <span>{{ $date }}</span>
            @if(!empty($ev->dia_diem))
            <span>•</span>
            <span class="text-break">{{ $ev->dia_diem }}</span>
            @endif
            @if(!empty($ticket->thoi_gian_dang_ky))
            <span>•</span>
            <span class="text-secondary">Đăng ký lúc: {{ date('d/m/Y H:i', strtotime($ticket->thoi_gian_dang_ky)) }}</span>
            @endif
          </div>
        </div>
      </div>

      <div class="d-flex align-items-center gap-2 flex-wrap flex-md-nowrap flex-md-shrink-0 ticket-actions">
        @if(!$isCancelled)
        <button
          type="button"
          id="btn-qr-{{ $ticket->id }}"
          class="btn btn-sm btn-dark rounded-3 px-3 py-2 fw-medium d-flex align-items-center gap-1"
          onclick="openTicketModal('{{ $ticket->id }}', '{{ $code }}', '{{ $ticketTitle }}', '{{ $date }}', '{{ $ticketLocation }}', '{{ $userName }}')">
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
          @if ($ticket->trang_thai === 'da_tham_gia')
          <span class="text-muted small fst-italic px-2">Đã check-in</span>
          @elseif (!$isCancelled)
          <form
            action="{{ route('Tickets.cancel', $ticket->id) }}"
            method="POST"
            data-ajax-cancel
            data-ticket-id="{{ $ticket->id }}"
            data-confirm-message="Bạn có chắc chắn muốn hủy vé này không?"
            class="d-inline">
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

<!-- Modal Vé Đơn Giản (Vẫn dùng Thư viện QRCode.js) -->
<div class="modal fade" id="ticketModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="max-width: 380px;">
    <div class="modal-content text-center p-4 rounded-4 shadow border-0">
      
      <!-- 1. Tiêu đề vé -->
      <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1 mb-2 align-self-center small">
        VÉ ĐIỆN TỬ CHÍNH THỨC
      </span>
      <h5 class="fw-bold text-dark mb-1 text-break" id="modal-ticket-event-name">Tên sự kiện</h5>
      <p class="text-muted small mb-3" id="modal-ticket-date">Ngày giờ</p>

      <!-- 2. Mã QR động (Vẽ bằng thư viện QRCode.js) -->
      <div class="d-inline-block p-2 bg-light border rounded-3 mx-auto mb-2">
        <div id="ticket-qr-code" class="d-flex justify-content-center align-items-center" style="width: 150px; height: 150px;"></div>
      </div>
      <div class="font-monospace fw-bold fs-5 text-dark text-break" id="modal-ticket-code">QQQ-2026-ID-XXXXXXXXXXXX</div>
      <div class="text-muted small" style="font-size: 12px;">Mã QR dùng để quét check-in tại cổng</div>
      
      <hr class="my-3" style="border-style: dashed;">

      <!-- 3. Thông tin người tham dự -->
      <div class="small text-secondary text-start px-2">
        <div class="mb-1 text-break"><strong>Người tham dự:</strong> <span id="modal-ticket-user-name" class="text-dark"></span></div>
        <div class="text-break"><strong>Địa điểm:</strong> <span id="modal-ticket-location" class="text-dark"></span></div>
      </div>

      <!-- 4. Nút đóng -->
      <button type="button" class="btn btn-dark w-100 mt-3 rounded-3 py-2 fw-medium" data-bs-dismiss="modal">Đóng vé</button>
    </div>
  </div>
</div>

<!-- Nạp Thư viện QRCode.js từ CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

<script>
let ticketModalInstance = null;

function openTicketModal(id, code, title, date, location, name) {
  document.getElementById('modal-ticket-event-name').textContent = title;
  document.getElementById('modal-ticket-date').textContent = date;
  document.getElementById('modal-ticket-user-name').textContent = name;
  document.getElementById('modal-ticket-location').textContent = location;
  document.getElementById('modal-ticket-code').textContent = code;

  const qrContainer = document.getElementById('ticket-qr-code');
  qrContainer.innerHTML = '';
  new QRCode(qrContainer, {
    text: code,
    width: 150,
    height: 150,
    colorDark: "#111827",
    colorLight: "#ffffff"
  });

  if (!ticketModalInstance) {
    ticketModalInstance = new bootstrap.Modal(document.getElementById('ticketModal'));
  }
  ticketModalInstance.show();
}
</script>
@endsection
