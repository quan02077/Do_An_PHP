@extends('layouts.app')

@section('title', 'Sự kiện yêu thích — QQQ')

@section('content')
<div class="container-xl py-4">
  <div class="d-flex align-items-center justify-content-between mb-4">
    <h1 class="fs-4 fw-bold text-dark mb-0">Sự kiện yêu thích ({{ count($favorites) }})</h1>
    <a href="{{ route('trang-chu') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
      + Khám phá thêm sự kiện
    </a>
  </div>

  <div class="row g-4">
      @forelse ($favorites as $fav)
        @php
          $ev = $fav->suKien;
          $img = $ev->url_hinh_anh ?? 'https://images.unsplash.com/photo-1501281668745-f7f57925c3b4?w=500&fit=crop';
          $date = $ev && $ev->thoi_gian_bat_dau ? date('d/m/Y - H:i', strtotime($ev->thoi_gian_bat_dau)) : 'Đang cập nhật';
        @endphp

        <div class="col-12 col-sm-6 col-lg-4">
          <div class="card h-100 bg-white rounded-4 border shadow-sm overflow-hidden d-flex flex-column card-hover-scale">
            <div class="position-relative" style="height: 180px;">
              <img src="{{ $img }}" alt="{{ $ev->ten_su_kien ?? 'Sự kiện' }}" class="w-100 h-100 object-fit-cover" />
              <span class="position-absolute top-0 start-0 m-2.5 badge bg-white text-dark border shadow-sm rounded-pill py-1 px-2.5">
                {{ $ev->danhMuc->ten_danh_muc ?? 'Chung' }}
              </span>
              <button 
                type="button"
                onclick="alert('Đã xóa khỏi danh sách yêu thích!')"
                class="position-absolute top-0 end-0 m-2.5 btn btn-light rounded-circle p-0 d-flex align-items-center justify-content-center shadow-sm text-danger border"
                style="width: 36px; height: 36px;"
                title="Bỏ lưu"
              >
                <svg style="width: 16px; height: 16px; fill: currentColor;" viewBox="0 0 24 24">
                  <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/>
                </svg>
              </button>
            </div>
            <div class="p-3 grow d-flex flex-column justify-content-between">
              <div>
                <div class="small fw-medium text-secondary mb-1">{{ $date }}</div>
                <h4 class="fs-6 fw-bold text-dark mb-1 text-truncate">
                  <a href="{{ url('/events/' . ($ev->id ?? 1)) }}" class="text-decoration-none text-dark">
                    {{ $ev->ten_su_kien ?? 'Sự kiện' }}
                  </a>
                </h4>
                <div class="small text-muted mb-3 text-truncate">{{ $ev->dia_diem ?? 'Chưa cập nhật' }}</div>
              </div>
              <a href="{{ url('/events/' . ($ev->id ?? 1)) }}" class="btn btn-dark btn-sm w-100 py-2 rounded-3 fw-medium">
                Xem chi tiết &amp; Đặt vé
              </a>
            </div>
          </div>
        </div>
      @empty
        <div class="col-12 text-center py-5 bg-white rounded-4 border shadow-sm p-4">
          <div class="rounded-circle bg-danger-subtle text-danger d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 52px; height: 52px;">
            <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
            </svg>
          </div>
          <h3 class="fs-6 fw-bold text-dark mb-1">Chưa có sự kiện yêu thích nào</h3>
          <p class="small text-muted mb-3">Bấm vào biểu tượng Trái tim trên bất kỳ sự kiện nào để lưu lại tại đây.</p>
          <a href="{{ route('trang-chu') }}" class="btn btn-dark btn-sm rounded-3 px-3 py-2 fw-medium">
            Khám phá ngay
          </a>
        </div>
      @endforelse
    </div>  
</div>
@endsection