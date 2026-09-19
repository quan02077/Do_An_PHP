@extends('layouts.app')

@section('title', 'Vé của tôi — QQQ')

@section('content')
<div class="container-xl py-4">
  <div class="d-flex align-items-center justify-content-between mb-4">
    <h1 class="fs-4 fw-bold text-dark mb-0">Vé đã đăng ký ({{ count($tickets) }})</h1>
    <a href="{{ route('trang-chu') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
      + Khám phá thêm sự kiện
    </a>
  </div>

  <!-- ─── DANH SÁCH VÉ ĐÃ ĐĂNG KÝ ─────────────────────────────────────── -->
  <div class="d-flex flex-column gap-3">
      @forelse ($tickets as $ticket)
        @php
          $ev = $ticket->suKien;
          $img = $ev->url_hinh_anh ?? 'https://images.unsplash.com/photo-1501281668745-f7f57925c3b4?w=500&fit=crop';
          $code = $ticket->ma_ve ?: ('QQQ-2026-' . str_pad($ticket->id, 4, '0', STR_PAD_LEFT));
          $date = $ev && $ev->thoi_gian_bat_dau ? date('d/m/Y - H:i', strtotime($ev->thoi_gian_bat_dau)) : 'Đang cập nhật';
        @endphp

        <div class="bg-white rounded-4 border shadow-sm p-3 p-sm-4 d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
          <div class="d-flex align-items-center gap-3">
            <img src="{{ $img }}" alt="{{ $ev->ten_su_kien ?? 'Sự kiện' }}" class="rounded-3 object-fit-cover shrink-0" style="width: 76px; height: 76px;" />
            <div>
              <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge rounded-pill bg-light text-dark border font-monospace" style="font-size: 12px;">{{ $code }}</span>
                @if ($ticket->trang_thai === 'da_check_in')
                  <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">Đã check-in</span>
                @elseif ($ticket->trang_thai === 'cho_duyet')
                  <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill">Chờ duyệt</span>
                @elseif ($ticket->trang_thai === 'da_huy')
                  <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill">Đã hủy</span>
                @else
                  <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill">Đã xác nhận</span>
                @endif
              </div>
              <h3 class="fs-6 fw-bold text-dark mb-1 text-truncate" style="max-width: 480px;">
                <a href="{{ url('/events/' . ($ev->id ?? 1)) }}" class="text-decoration-none text-dark">
                  {{ $ev->ten_su_kien ?? 'Sự kiện' }}
                </a>
              </h3>
              <div class="d-flex flex-wrap gap-2 small text-muted">
                <span>{{ $date }}</span>
                <span>•</span>
                <span>{{ $ev->dia_diem ?? 'Chưa cập nhật' }}</span>
              </div>
            </div>
          </div>

          <div class="d-flex align-items-center gap-2 w-100 w-md-auto pt-2 pt-md-0 border-top border-md-top-0">
            <a href="{{ url('/events/' . ($ev->id ?? 1)) }}" class="btn btn-sm btn-outline-secondary rounded-3 px-3 py-2 grow flex-md-grow-0 fw-medium">
              Xem sự kiện
            </a>
            @if ($ticket->trang_thai !== 'da_huy')
              <button type="button" onclick="alert('Tính năng hủy vé đang được kết nối hệ thống!')" class="btn btn-sm btn-outline-danger rounded-3 px-3 py-2 grow flex-md-grow-0 fw-medium">
                Hủy vé
              </button>
            @endif
          </div>
        </div>
      @empty
        <div class="text-center py-5 bg-white rounded-4 border shadow-sm p-4">
          <div class="rounded-circle bg-light text-muted d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 52px; height: 52px;">
            <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
            </svg>
          </div>
          <h3 class="fs-6 fw-bold text-dark mb-1">Bạn chưa đăng ký sự kiện nào</h3>
          <p class="small text-muted mb-3">Hãy khám phá các sự kiện hấp dẫn và đăng ký tham gia ngay hôm nay.</p>
          <a href="{{ route('trang-chu') }}" class="btn btn-dark btn-sm rounded-3 px-3 py-2 fw-medium">
            Khám phá sự kiện
          </a>
        </div>
      @endforelse
    </div>
</div>
@endsection