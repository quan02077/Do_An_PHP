@php
    $id = $event->id;
    $title = $event->ten_su_kien;
    $image = $event->url_hinh_anh ?: asset('uploads/su_kien/001.jpg');
    $location = $event->dia_diem ?? 'Chưa cập nhật';
    $capacity = $event->so_luong_toi_da ?? 100;
    $registered = $event->so_luong_da_dang_ky ?? ($event->dangKys ? $event->dangKys->count() : 0);
    $status = $event->trang_thai_dien_ra;
    $catName = $event->danhMuc->ten_danh_muc ?? 'Chung';
    $rawDate = $event->thoi_gian_bat_dau;
    $dateFormatted = $rawDate ? date('d/m/Y - H:i', strtotime($rawDate)) : 'Đang cập nhật';
    $percent = min(100, round(($registered / max(1, $capacity)) * 100));
    $isFav = $isFav ?? false;
@endphp

<div class="col-12 col-sm-6 col-lg-4">
  <div class="card h-100 border shadow-sm card-hover-scale overflow-hidden rounded-4 bg-white">
    <div class="position-relative overflow-hidden bg-light" style="height: 200px;">
      <img src="{{ $image }}" alt="{{ $title }}" class="w-100 h-100 object-fit-cover" loading="lazy" />

      <div class="position-absolute top-0 start-0 m-3 d-flex flex-wrap gap-1 align-items-center card-badges-overlay">
        <span class="badge bg-white text-dark border shadow-sm rounded-pill py-1 px-2.5">
          {{ $catName }}
        </span>

        @if ($event->trang_thai === 'da_huy')
        <span class="badge bg-danger text-white rounded-pill py-1 px-2.5">Đã hủy</span>
        @elseif ($status === 'sap_dien_ra')
        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill py-1 px-2.5">Sắp diễn ra</span>
        @elseif ($status === 'dang_dien_ra')
        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill py-1 px-2.5">Đang diễn ra</span>
        @elseif ($status === 'da_ket_thuc')
        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill py-1 px-2.5">Đã kết thúc</span>
        @endif
      </div>

      <!-- Nút Bookmark Trái tim -->
      <form action="{{ route('Favorite.toggle', $id) }}" method="POST" data-ajax-favorite data-event-id="{{ $id }}" class="position-absolute top-0 end-0 m-3" style="z-index: 5;">
        @csrf
        <button
          type="submit"
          class="btn btn-light rounded-circle shadow-sm p-0 d-flex align-items-center justify-content-center border {{ $isFav ? 'text-danger' : 'text-secondary' }}"
          style="width: 36px; height: 36px;"
          title="{{ $isFav ? 'Bỏ lưu khỏi yêu thích' : 'Lưu vào yêu thích' }}"
          aria-label="{{ $isFav ? 'Bỏ lưu khỏi yêu thích' : 'Lưu vào yêu thích' }}"
          aria-pressed="{{ $isFav ? 'true' : 'false' }}">
          <i class="{{ $isFav ? 'fa-solid fa-heart text-danger' : 'fa-regular fa-heart text-secondary' }} fs-6"></i>
        </button>
      </form>
    </div>

    <div class="card-body d-flex flex-column justify-content-between p-3 p-sm-4">
      <div class="min-w-0">
        <div class="d-flex align-items-center gap-2 text-muted small mb-2">
          <i class="fa-regular fa-calendar flex-shrink-0"></i>
          <span>{{ $dateFormatted }}</span>
        </div>

        <h3 class="fs-5 fw-bold text-dark mb-2 line-clamp-2 text-break">
          <a href="{{ route('Event.show', $id) }}" class="text-decoration-none text-dark">
            {{ $title }}
          </a>
        </h3>

        <div class="d-flex align-items-center gap-2 text-muted small mb-3 min-w-0">
          <i class="fa-solid fa-location-dot flex-shrink-0"></i>
          <span class="text-truncate">{{ $location }}</span>
        </div>
      </div>

      <div class="mt-3">
        <div class="mb-3">
          <div class="d-flex justify-content-between gap-2 flex-wrap small text-secondary mb-1">
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
            Xem chi tiết &amp; Đặt vé
          </a>
        </div>
      </div>
    </div>
  </div>
</div>
