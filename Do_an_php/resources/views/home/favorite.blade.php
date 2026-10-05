@extends('layouts.app')

@section('title', 'Sự kiện yêu thích — QQQ')

@section('content')
<div class="container-xl py-4">

  <!-- Alert thông báo -->
  @if (session('success'))
  <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4 shadow-sm" role="alert">
    <div class="d-flex align-items-center gap-2">
      <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
      </svg>
      <span>{{ session('success') }}</span>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
  @endif

  @if (session('warning'))
  <div class="alert alert-warning alert-dismissible fade show rounded-3 mb-4 shadow-sm" role="alert">
    <div class="d-flex align-items-center gap-2">
      <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
      </svg>
      <span>{{ session('warning') }}</span>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
  @endif

  <!-- Banner thông tin tài khoản đang thao tác CSDL -->
  @if(isset($user) && $user)
  <div class="bg-white rounded-4 border shadow-sm p-4 mb-4 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
    <div class="d-flex align-items-center gap-3">
      <div class="rounded-3 bg-danger-subtle text-danger fw-bold d-flex align-items-center justify-content-center shadow-sm flex-shrink-0 fs-3" style="width: 56px; height: 56px;">
        <svg style="width: 28px; height: 28px;" fill="currentColor" viewBox="0 0 24 24">
          <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z" />
        </svg>
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
      <a href="{{ route('Dashboard.myTicket') }}" class="btn btn-sm btn-outline-secondary rounded-3 px-3 py-2 fw-medium small">
        Xem vé của tôi
      </a>
      <a href="{{ route('Home.index') }}" class="btn btn-sm btn-dark rounded-3 px-3 py-2 fw-medium small">
        Khám phá thêm
      </a>
    </div>
  </div>
  @endif

  <!-- Header & Đếm số lượng -->
  <div class="d-flex align-items-center justify-content-between mb-4">
    <div>
      <h2 class="fs-4 fw-bold text-dark mb-0">Sự kiện yêu thích</h2>
    </div>
    <span class="badge rounded-pill bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 fw-semibold">
      Đã lưu: <span id="favs-count">{{ count($favorites) }}</span> sự kiện
    </span>
  </div>

  <div class="row g-4" id="favs-grid">
    @forelse ($favorites as $fav)
    @php
    $ev = $fav->suKien;
    if (!$ev) continue;
    $img = $ev->url_hinh_anh ?: asset('uploads/su_kien/001.jpg');
    $date = $ev->thoi_gian_bat_dau ? date('H:i - d/m/Y', strtotime($ev->thoi_gian_bat_dau)) : 'Đang cập nhật';
    @endphp

    <div class="col-12 col-sm-6 col-lg-4 fav-item-col" id="fav-card-{{ $ev->id }}">
      <div class="card h-100 bg-white rounded-4 border shadow-sm overflow-hidden d-flex flex-column card-hover-scale">
        <div class="position-relative" style="height: 190px;">
          <img src="{{ $img }}" alt="{{ $ev->ten_su_kien }}" class="w-100 h-100 object-fit-cover" />
          <span class="position-absolute top-0 start-0 m-3 badge bg-white text-dark border shadow-sm rounded-pill py-1 px-2.5">
            {{ $ev->danhMuc->ten_danh_muc ?? 'Chung' }}
          </span>

          <!-- Nút Bỏ lưu: Dùng chung route Favorite.toggle -->
          <form action="{{ route('Favorite.toggle', $ev->id) }}" method="POST" class="position-absolute top-0 end-0 m-3" style="z-index: 5;" onsubmit="return confirm('Bạn có chắc chắn muốn bỏ lưu sự kiện này khỏi danh sách yêu thích?');">
            @csrf
            <button
              type="submit"
              class="btn btn-light rounded-circle p-0 d-flex align-items-center justify-content-center shadow-sm text-danger border"
              style="width: 38px; height: 38px;"
              title="Bỏ lưu khỏi Yêu thích">
              <svg style="width: 18px; height: 18px; fill: currentColor;" viewBox="0 0 24 24">
                <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z" />
              </svg>
            </button>
          </form>
        </div>

        <div class="p-4 flex-grow-1 d-flex flex-column justify-content-between">
          <div>
            <div class="small fw-medium text-secondary mb-1.5 d-flex align-items-center gap-1.5">
              <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <rect width="18" height="18" x="3" y="4" rx="2" ry="2"></rect>
                <line x1="16" x2="16" y1="2" y2="6"></line>
                <line x1="8" x2="8" y1="2" y2="6"></line>
                <line x1="3" x2="21" y1="10" y2="10"></line>
              </svg>
              <span>{{ $date }}</span>
            </div>
            <h4 class="fs-6 fw-bold text-dark mb-1 text-truncate">
              <a href="{{ route('Event.show', $ev->id) }}" class="text-decoration-none text-dark hover-underline">
                {{ $ev->ten_su_kien }}
              </a>
            </h4>
            <div class="small text-muted mb-3 text-truncate d-flex align-items-center gap-1">
              <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"></path>
                <circle cx="12" cy="10" r="3"></circle>
              </svg>
              <span>{{ $ev->dia_diem ?? 'Chưa cập nhật' }}</span>
            </div>
          </div>

          <div class="d-flex gap-2 pt-2 border-top">
            <a href="{{ route('Event.show', $ev->id) }}" class="btn btn-dark btn-sm w-100 py-2 rounded-3 fw-medium">
              Xem chi tiết &amp; Đặt vé
            </a>
          </div>
        </div>
      </div>
    </div>
    @empty
    <div class="col-12 text-center py-5 bg-white rounded-4 border shadow-sm p-4" id="empty-state">
      <div class="rounded-circle bg-danger-subtle text-danger d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 56px; height: 56px;">
        <svg style="width: 28px; height: 28px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
        </svg>
      </div>
      <h3 class="fs-6 fw-bold text-dark mb-1">Tài khoản này chưa lưu sự kiện nào</h3>
      <p class="small text-muted mb-3">Bấm vào biểu tượng Trái tim trên bất kỳ sự kiện nào để lưu lại danh sách yêu thích trong CSDL.</p>
      <a href="{{ route('Home.index') }}" class="btn btn-dark btn-sm rounded-3 px-3 py-2 fw-medium">
        Khám phá sự kiện ngay
      </a>
    </div>
    @endforelse
  </div>
</div>
@endsection