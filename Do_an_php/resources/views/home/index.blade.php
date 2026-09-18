@extends('layouts.app')

@section('title', 'QQQ — Khám phá & Đặt vé sự kiện Dễ dàng, Nhanh chóng')

@section('content')
@php
  $categories = $categories ?? collect();
  $events = $events ?? collect();
@endphp

  <section class="bg-white border-bottom py-5">
    <div class="container-xl">
      <div class="mx-auto text-center" style="max-width: 720px;">
        <h1 class="display-6 fw-bold tracking-tight text-dark mb-3">
          Khám phá &amp; Đặt vé sự kiện <br class="d-none d-sm-inline" />
          <span class="text-secondary fw-normal">Dễ dàng, Nhanh chóng</span>
        </h1>
        <div class="mx-auto position-relative" style="max-width: 560px;">
          <form method="GET" action="{{ route('trang-chu') }}">
            <div class="input-group input-group-lg shadow-sm">
              <span class="input-group-text bg-white border-end-0 text-muted ps-3">
                <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <circle cx="11" cy="11" r="8"></circle>
                  <path d="m21 21-4.3-4.3"></path>
                </svg>
              </span>
              <input 
                type="text" 
                name="search" 
                id="search-input" 
                value="{{ request('search') }}"
                aria-label="Tìm kiếm sự kiện"
                placeholder="Tìm theo tên sự kiện, địa điểm, chủ đề..." 
                class="form-control border-start-0 fs-6 py-2.5 ps-1"
              />
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>

  <section class="container-xl py-5">
    <div class="mb-4">
      <div class="d-flex align-items-center gap-2 overflow-x-auto pb-2" id="category-pills-container">
        <a 
          href="{{ route('trang-chu') }}" 
          class="btn btn-sm rounded-pill px-3 py-1.5 fw-medium {{ !request('category') ? 'btn-dark shadow-xs' : 'btn-outline-secondary' }}"
        >
          Tất cả
        </a>

        @foreach ($categories as $cat)
          <a 
            href="{{ route('trang-chu', ['category' => $cat->id]) }}" 
            class="btn btn-sm rounded-pill px-3 py-1.5 fw-medium {{ request('category') == $cat->id ? 'btn-dark shadow-xs' : 'btn-outline-secondary' }}"
          >
            {{ $cat->ten_danh_muc }}
          </a>
        @endforeach
      </div>

      <div class="d-flex flex-column flex-sm-row sm:align-items-center justify-content-between gap-3 pt-3 border-top mt-2">
        <div class="btn-group btn-group-sm rounded-pill p-1 bg-white border shadow-sm w-auto align-self-start align-self-sm-auto" role="group">
          <a 
            href="{{ route('trang-chu') }}" 
            class="status-filter-tab btn btn-sm rounded-pill px-3 {{ !request('status') ? 'btn-dark active' : 'btn-outline-secondary border-0' }}"
          >
            Tất cả
          </a>
          <a 
            href="{{ route('trang-chu', ['status' => 'sap_dien_ra']) }}" 
            class="status-filter-tab btn btn-sm rounded-pill px-3 {{ request('status') === 'sap_dien_ra' ? 'btn-dark active' : 'btn-outline-secondary border-0' }}"
          >
            Sắp diễn ra
          </a>
          <a 
            href="{{ route('trang-chu', ['status' => 'dang_dien_ra']) }}" 
            class="status-filter-tab btn btn-sm rounded-pill px-3 {{ request('status') === 'dang_dien_ra' ? 'btn-dark active' : 'btn-outline-secondary border-0' }}"
          >
            Đang diễn ra
          </a>
          <a 
            href="{{ route('trang-chu', ['status' => 'da_ket_thuc']) }}" 
            class="status-filter-tab btn btn-sm rounded-pill px-3 {{ request('status') === 'da_ket_thuc' ? 'btn-dark active' : 'btn-outline-secondary border-0' }}"
          >
            Đã kết thúc
          </a>
          <a 
            href="{{ route('trang-chu', ['status' => 'yeu_thich']) }}" 
            class="status-filter-tab btn btn-sm rounded-pill px-3 {{ request('status') === 'yeu_thich' ? 'btn-dark active' : 'btn-outline-secondary border-0' }} d-flex align-items-center gap-1"
          >Yêu thích
          </a>
        </div>

        <div id="events-count-label" class="small fw-semibold text-secondary align-self-center">
          Hiển thị {{ count($events) }} sự kiện
        </div>
      </div>
    </div>

    <div id="events-grid-container" class="row g-4">
      @forelse ($events as $event)
        @php
          $id = $event->id;
          $title = $event->ten_su_kien;
          $image = $event->url_hinh_anh;
          $location = $event->dia_diem ?? 'Chưa cập nhật';
          $capacity = $event->so_luong_toi_da ?? 100;
          $registered = $event->so_luong_da_dang_ky ?? ($event->dangKys ? $event->dangKys->count() : 0);
          $status = $event->trang_thai ?? 'sap_dien_ra';
          $catName = $event->danhMuc->ten_danh_muc ?? 'Chung';

          $rawDate = $event->thoi_gian_bat_dau;
          $dateFormatted = $rawDate ? date('d/m/Y - H:i',  strtotime($rawDate)) : 'Đang cập nhật';

          $percent = min(100, round(($registered / max(1, $capacity)) * 100));
        @endphp

        <div class="col-12 col-sm-6 col-lg-4">
          <div class="card h-100 border shadow-sm card-hover-scale overflow-hidden">
            <div class="position-relative overflow-hidden bg-light" style="height: 200px;">
              <img src="{{ $image }}" alt="{{ $title }}" class="w-100 h-100 object-fit-cover" loading="lazy" />
              
              <div class="position-absolute top-0 start-0 m-3 d-flex flex-wrap gap-1 align-items-center">
                <span class="badge bg-white text-dark border shadow-sm rounded-pill py-1 px-2.5">
                  {{ $catName }}
                </span>

                @if ($status === 'sap_dien_ra')
                  <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill py-1 px-2.5">
                    Sắp diễn ra
                  </span>
                @elseif ($status === 'dang_dien_ra')
                  <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill py-1 px-2.5">
                    Đang diễn ra
                  </span>
                @elseif ($status === 'da_ket_thuc')
                  <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill py-1 px-2.5">
                    Đã kết thúc
                  </span>
                @endif
              </div>

              <button 
                type="button"
                data-id="{{ $id }}"
                onclick="if(window.handleBookmarkClick) handleBookmarkClick(event, Number(this.dataset.id))"
                class="bookmark-btn-{{ $id }} position-absolute top-0 end-0 m-3 btn btn-light rounded-circle shadow-sm p-0 d-flex align-items-center justify-content-center border text-secondary"
                style="width: 36px; height: 36px; z-index: 5;"
                title="Lưu vào yêu thích"
                aria-label="Lưu vào yêu thích"
              >
                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                </svg>
              </button>
            </div>

            <div class="card-body d-flex flex-column justify-content-between p-4">
              <div>
                <div class="d-flex align-items-center gap-2 text-muted small mb-2">
                  <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"></rect><line x1="16" x2="16" y1="2" y2="6"></line><line x1="8" x2="8" y1="2" y2="6"></line><line x1="3" x2="21" y1="10" y2="10"></line></svg>
                  <span>{{ $dateFormatted }}</span>
                </div>

                <h3 class="fs-5 fw-bold text-dark mb-2 line-clamp-2">
                  <a href="{{ url('/events/' . $id) }}" class="text-decoration-none text-dark">
                    {{ $title }}
                  </a>
                </h3>

                <div class="d-flex align-items-center gap-1 text-muted small mb-3 line-clamp-1">
                  <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                  <span>{{ $location }}</span>
                </div>
              </div>

              <div class="mt-3">
                <div class="mb-3">
                  <div class="d-flex justify-content-between small text-secondary mb-1">
                    <span>Số chỗ đã đăng ký</span>
                    <span class="fw-semibold text-dark tabular-nums">{{ $registered }}/{{ $capacity }} ({{ $percent }}%)</span>
                  </div>
                  <div class="progress" style="height: 6px;">
                    <div 
                      class="progress-bar {{ $percent >= 100 ? 'bg-danger' : ($percent >= 80 ? 'bg-warning' : 'bg-dark') }}" 
                      role="progressbar" 
                      @style(["width: {$percent}%"]) 
                      aria-valuenow="{{ $percent }}" 
                      aria-valuemin="0" 
                      aria-valuemax="100"
                    ></div>
                  </div>
                </div>

                <div class="d-flex gap-2 pt-2 border-top">
                  <a href="{{ url('/events/' . $id) }}" class="btn btn-sm btn-outline-secondary flex-fill py-2 fw-medium">
                    Xem chi tiết
                  </a>
                  <a href="{{ url('/events/' . $id . '?register=1') }}" class="btn btn-sm btn-dark flex-fill py-2 fw-medium">
                    Đăng ký ngay
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      @empty
        <div class="col-12 text-center py-5 px-3 bg-white rounded-4 border shadow-sm my-3">
          <div class="mx-auto mb-3 bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
            <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
          </div>
          <h3 class="fs-5 fw-bold text-dark mb-1">Không tìm thấy sự kiện nào</h3>
          <p class="small text-secondary mb-3">Hãy thử tìm kiếm với từ khóa khác hoặc xóa bớt bộ lọc.</p>
          <div>
            <a href="{{ url('/') }}" class="btn btn-sm btn-dark px-3 py-2 fw-medium rounded-pill">
              Đặt lại bộ lọc
            </a>
          </div>
        </div>
      @endforelse
    </div>
  </section>
@endsection