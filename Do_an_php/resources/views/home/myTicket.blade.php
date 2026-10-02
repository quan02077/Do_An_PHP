@extends('layouts.app')

@section('title', 'Vé của tôi — QQQ')

@section('content')
<div class="container-xl py-4">

  <!-- Alert thông báo nếu có -->
  @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4 shadow-sm" role="alert">
      <div class="d-flex align-items-center gap-2">
        <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        <span>{{ session('success') }}</span>
      </div>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  @if (session('warning'))
    <div class="alert alert-warning alert-dismissible fade show rounded-3 mb-4 shadow-sm" role="alert">
      <div class="d-flex align-items-center gap-2">
        <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        <span>{{ session('warning') }}</span>
      </div>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  <!-- Banner thông tin tài khoản đang thao tác CSDL -->
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
            @if(!empty($user->so_dien_thoai))
              <span>•</span>
              <span>{{ $user->so_dien_thoai }}</span>
            @endif
          </div>
        </div>
      </div>

      <div class="d-flex align-items-center gap-2">
        <a href="{{ route('Dashboard.favorite') }}" class="btn btn-sm btn-outline-secondary rounded-3 px-3 py-2 fw-medium small d-flex align-items-center gap-1.5">
          <svg class="text-danger" style="width: 14px; height: 14px;" fill="currentColor" viewBox="0 0 24 24"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
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
      <p class="text-muted small mb-0">Dữ liệu được tải trực tiếp từ cơ sở dữ liệu theo tài khoản hiện tại</p>
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
            <div class="d-flex align-items-center gap-2 mb-1.5 flex-wrap">
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

        <div class="d-flex align-items-center gap-2 w-100 w-md-auto pt-2 pt-md-0 border-top border-md-top-0">
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
                  id="btn-cancel-{{ $ticket->id }}"
                >
                  <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
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
          <svg style="width: 28px; height: 28px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
          </svg>
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
@endsection