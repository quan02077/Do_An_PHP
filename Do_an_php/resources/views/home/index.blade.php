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
        <form method="GET" action="{{ route('Home.index') }}">
          <div class="shadow-sm rounded-3">
            <x-input type="text" name="search" id="search-input" :value="request('search')" aria-label="Tìm kiếm sự kiện" placeholder="Tìm theo tên sự kiện, địa điểm, chủ đề..." class="fs-6 py-2.5 ps-1">
              <x-slot:icon>
                <i class="fa-solid fa-magnifying-glass text-secondary"></i>
              </x-slot:icon>
            </x-input>
          </div>
        </form>
      </div>
    </div>
  </div>
</section>

<section class="container-xl py-5">
  @if (session('success'))
  <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4 shadow-sm" role="alert">
    <div class="d-flex align-items-center gap-2">
      <i class="fa-solid fa-circle-check fs-6"></i>
      <span>{{ session('success') }}</span>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
  @endif

  @if (session('warning'))
  <div class="alert alert-warning alert-dismissible fade show rounded-3 mb-4 shadow-sm" role="alert">
    <div class="d-flex align-items-center gap-2">
      <i class="fa-solid fa-triangle-exclamation fs-6"></i>
      <span>{{ session('warning') }}</span>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
  @endif

  <div class="mb-4">
    <div class="d-flex align-items-center gap-2 overflow-x-auto pb-2" id="category-pills-container">
      <a
        href="{{ route('Home.index') }}"
        class="btn btn-sm rounded-pill px-3 py-1.5 fw-medium {{ !request('category') ? 'btn-dark shadow-sm' : 'btn-outline-secondary' }}">
        Tất cả
      </a>

      @foreach ($categories as $cat)
      <a
        href="{{ route('Home.index', ['category' => $cat->id]) }}"
        class="btn btn-sm rounded-pill px-3 py-1.5 fw-medium {{ request('category') == $cat->id ? 'btn-dark shadow-sm' : 'btn-outline-secondary' }}">
        {{ $cat->ten_danh_muc }}
      </a>
      @endforeach
    </div>

    <div class="d-flex flex-column flex-sm-row justify-content-between gap-3 pt-3 border-top mt-2">
      <div class="btn-group btn-group-sm rounded-pill p-1 bg-white border shadow-sm w-auto align-self-start" role="group">
        <a
          href="{{ route('Home.index') }}"
          class="status-filter-tab btn btn-sm rounded-pill px-3 {{ !request('status') ? 'btn-dark active' : 'btn-outline-secondary border-0' }}">
          Tất cả
        </a>
        <a
          href="{{ route('Home.index', ['status' => 'sap_dien_ra']) }}"
          class="status-filter-tab btn btn-sm rounded-pill px-3 {{ request('status') === 'sap_dien_ra' ? 'btn-dark active' : 'btn-outline-secondary border-0' }}">
          Sắp diễn ra
        </a>
        <a
          href="{{ route('Home.index', ['status' => 'dang_dien_ra']) }}"
          class="status-filter-tab btn btn-sm rounded-pill px-3 {{ request('status') === 'dang_dien_ra' ? 'btn-dark active' : 'btn-outline-secondary border-0' }}">
          Đang diễn ra
        </a>
        <a
          href="{{ route('Home.index', ['status' => 'da_ket_thuc']) }}"
          class="status-filter-tab btn btn-sm rounded-pill px-3 {{ request('status') === 'da_ket_thuc' ? 'btn-dark active' : 'btn-outline-secondary border-0' }}">
          Đã kết thúc
        </a>
        <a
          href="{{ route('Home.index', ['status' => 'yeu_thich']) }}"
          class="status-filter-tab btn btn-sm rounded-pill px-3 {{ request('status') === 'yeu_thich' ? 'btn-dark active' : 'btn-outline-secondary border-0' }} d-flex align-items-center gap-1">
          <i class="fa-solid fa-heart text-danger"></i>
          <span>Yêu thích</span>
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
    $status = $event->trang_thai_dien_ra;
    $catName = $event->danhMuc->ten_danh_muc ?? 'Chung';

    $rawDate = $event->thoi_gian_bat_dau;
    $dateFormatted = $rawDate ? date('d/m/Y - H:i', strtotime($rawDate)) : 'Đang cập nhật';

    $percent = min(100, round(($registered / max(1, $capacity)) * 100));
    $isFav = in_array($id, $userFavIds ?? []);
    @endphp

    <div class="col-12 col-sm-6 col-lg-4">
      <div class="card h-100 border shadow-sm card-hover-scale overflow-hidden">
        <div class="position-relative overflow-hidden bg-light" style="height: 200px;">
          <img src="{{ $image }}" alt="{{ $title }}" class="w-100 h-100 object-fit-cover" loading="lazy" />

          <div class="position-absolute top-0 start-0 m-3 d-flex flex-wrap gap-1 align-items-center">
            <span class="badge bg-white text-dark border shadow-sm rounded-pill py-1 px-2.5">
              {{ $catName }}
            </span>

            @if ($event->trang_thai === 'da_huy')
            <span class="badge bg-danger text-white rounded-pill py-1 px-2.5">
              Đã hủy
            </span>
            @elseif ($status === 'sap_dien_ra')
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

          <!-- Nút Bookmark Trái tim -->
          <form action="{{ route('Favorite.toggle', $id) }}" method="POST" class="position-absolute top-0 end-0 m-3" style="z-index: 5;">
            @csrf
            <button
              type="submit"
              class="btn btn-light rounded-circle shadow-sm p-0 d-flex align-items-center justify-content-center border {{ $isFav ? 'text-danger' : 'text-secondary' }}"
              style="width: 36px; height: 36px;"
              title="{{ $isFav ? 'Bỏ lưu khỏi yêu thích' : 'Lưu vào yêu thích' }}"
              aria-label="Lưu vào yêu thích">
              <i class="{{ $isFav ? 'fa-solid fa-heart' : 'fa-regular fa-heart' }} fs-6"></i>
            </button>
          </form>
        </div>

        <div class="card-body d-flex flex-column justify-content-between p-4">
          <div>
            <div class="d-flex align-items-center gap-2 text-muted small mb-2">
              <i class="fa-regular fa-calendar"></i>
              <span>{{ $dateFormatted }}</span>
            </div>

            <h3 class="fs-5 fw-bold text-dark mb-2 line-clamp-2">
              <a href="{{ route('Event.show', $id) }}" class="text-decoration-none text-dark">
                {{ $title }}
              </a>
            </h3>

            <div class="d-flex align-items-center gap-2 text-muted small mb-3 line-clamp-1">
              <i class="fa-solid fa-location-dot"></i>
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
                  aria-valuemax="100"></div>
              </div>
            </div>

            <div class="d-flex gap-2 pt-2 border-top">
              <a href="{{ route('Event.show', $id) }}" class="btn btn-sm btn-outline-secondary flex-fill py-2 fw-medium">
                Xem chi tiết
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
    @empty
    <div class="col-12 text-center py-5 px-3 bg-white rounded-4 border shadow-sm my-3">
      <div class="mx-auto mb-3 bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
        <i class="fa-solid fa-magnifying-glass fs-4 text-muted"></i>
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