@extends('layouts.app')

@section('title', $event->ten_su_kien . ' — QQQ')
@section('content')

<div class="container-xl py-4">
    <!-- Breadcrumb & Back button -->
    @include('partials.breadcrumb', [
    'backUrl' => route('Home.index'),
    'backText' => 'Quay lại trang chủ',
    'items' => [
    'Trang chủ' => route('Home.index'),
    $event->ten_su_kien => ''
    ]
    ])

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="position-relative rounded-4 overflow-hidden bg-dark shadow-sm border mb-4">
                <img id="event-image" src="{{ $event->url_hinh_anh }}" alt="{{ $event->ten_su_kien }}" class="w-100 object-fit-cover" style="height: 360px;" />
                <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(to top, rgba(15, 23, 42, 0.85) 0%, rgba(15, 23, 42, 0.25) 50%, transparent 100%); pointer-events: none;"></div>

                <div class="position-absolute top-0 start-0 p-3 d-flex gap-2" id="event-badges-container">
                    <span class="badge text-bg-light fw-medium">{{ $event->danhMuc->ten_danh_muc ?? 'Chung' }}</span>
                    @if($event->trang_thai === 'sap_dien_ra')
                    <span class="badge bg-primary fw-medium">Sắp diễn ra</span>
                    @elseif($event->trang_thai === 'dang_dien_ra')
                    <span class="badge bg-success fw-medium">Đang diễn ra</span>
                    @else
                    <span class="badge bg-secondary fw-medium">Đã kết thúc</span>
                    @endif
                </div>

                <!-- Nút Bookmark Trái tim (Kết nối CSDL bảng yeu_thich) -->
                <button
                    id="event-bookmark-btn"
                    type="button"
                    onclick="toggleDetailFavorite({{ $event->id }})"
                    class="position-absolute top-0 end-0 m-3 btn btn-light rounded-circle p-0 d-flex align-items-center justify-content-center shadow-sm border z-2"
                    style="width: 44px; height: 44px;"
                    title="{{ $isFavorited ? 'Bỏ lưu khỏi yêu thích' : 'Lưu vào yêu thích' }}">
                    <svg id="detail-heart-icon" class="{{ $isFavorited ? 'text-danger' : 'text-muted' }}" style="width: 22px; height: 22px; fill: {{ $isFavorited ? 'currentColor' : 'none' }}; stroke: currentColor;" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z" />
                    </svg>
                </button>

                <div class="position-absolute bottom-0 start-0 end-0 p-4 text-white">
                    <h1 id="event-title" class="fw-bold tracking-tight mb-2 display-6">{{ $event->ten_su_kien }}</h1>
                    <div class="d-flex flex-wrap align-items-center gap-3 small text-white-50">
                        <div class="d-flex align-items-center gap-2" id="event-time-badge">
                            <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <rect width="18" height="18" x="3" y="4" rx="2" />
                                <line x1="16" x2="16" y1="2" y2="6" />
                                <line x1="8" x2="8" y1="2" y2="6" />
                                <line x1="3" x2="21" y1="10" y2="10" />
                            </svg>
                            {{ \Carbon\Carbon::parse($event->thoi_gian_bat_dau)->format('H:i - d/m/Y') }}
                        </div>
                        <div class="d-flex align-items-center gap-2" id="event-location-badge">
                            <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0" />
                                <circle cx="12" cy="10" r="3" />
                            </svg>
                            {{ $event->dia_diem }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Description Section -->
            <div class="bg-white border shadow-sm rounded-4 p-4 p-md-5 mb-4">
                <h2 class="fw-bold tracking-tight fs-4 text-dark border-bottom pb-3 mb-3">Giới thiệu sự kiện</h2>
                <div id="event-description" class="text-secondary small leading-relaxed" style="white-space: pre-line; line-height: 1.7;">{{ $event->mo_ta }}</div>
            </div>

            <!-- Organizer Section -->
            <div class="bg-white border shadow-sm rounded-4 p-4 p-md-5 mb-4">
                <h2 class="fw-bold tracking-tight fs-4 text-dark border-bottom pb-3 mb-4">Đơn vị tổ chức</h2>
                <div class="d-flex align-items-center gap-3">
                    <div>
                        <h3 class="fs-6 fw-bold text-dark mb-1">{{ $event->ban_to_chuc ?? ($event->nguoiTao->ho_ten ?? 'Ban Tổ Chức Sự Kiện') }}</h3>
                        <p class="small text-muted mb-2">Đơn vị tổ chức sự kiện chuyên nghiệp</p>
                        <div class="d-flex flex-wrap gap-3 small text-secondary">
                            @if(!empty($event->nguoiTao->email))
                            <span class="d-flex align-items-center gap-2">
                                <svg style="width: 14px; height: 14px;" class="text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <rect width="20" height="16" x="2" y="4" rx="2" />
                                    <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" />
                                </svg>
                                {{ $event->nguoiTao->email }}
                            </span>
                            @endif
                            @if(!empty($event->nguoiTao->so_dien_thoai))
                            <span class="d-flex align-items-center gap-2">
                                <svg style="width: 14px; height: 14px;" class="text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                {{ $event->nguoiTao->so_dien_thoai }}
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right 1 Col: Ticket Registration Card -->
        <div class="col-lg-4">
            <div class="bg-white border shadow-sm rounded-4 p-4 sticky-top" style="top: 84px;">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="small fw-semibold text-muted text-uppercase tracking-wider">Vé tham dự</span>
                    @if($event->gia_ve > 0)
                    <span class="badge rounded-pill bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1">{{ number_format($event->gia_ve, 0, ',', '.') }} VNĐ</span>
                    @else
                    <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle px-2.5 py-1">MIỄN PHÍ</span>
                    @endif
                </div>

                <!-- Capacity Progress -->
                @php
                $daDangKy = $event->so_luong_da_dang_ky ?? 0;
                $tongSoVe = $event->so_luong_toi_da ?? 100;
                $phanTram = ($tongSoVe > 0) ? min(100, round(($daDangKy / $tongSoVe) * 100)) : 0;
                $veConLai = max(0, $tongSoVe - $daDangKy);
                @endphp
                <div class="mb-4">
                    <div class="d-flex justify-content-between small fw-medium mb-1">
                        <span class="text-muted">Tình trạng chỗ ngồi</span>
                        <span id="capacity-text" class="fw-semibold text-dark">{{ $daDangKy }} / {{ $tongSoVe }} ({{ $phanTram }}%)</span>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div
                            class="progress-bar {{ $phanTram >= 100 ? 'bg-danger' : ($phanTram >= 80 ? 'bg-warning' : 'bg-dark') }}"
                            role="progressbar"
                            @style(["width: {$phanTram}%"])
                            aria-valuenow="{{ $phanTram }}"
                            aria-valuemin="0"
                            aria-valuemax="100"></div>
                    </div>
                    <div class="text-muted text-end mt-1 small" id="remaining-text">Còn {{ $veConLai }} vé</div>
                </div>

                <!-- Quick Specs -->
                <div class="py-3 border-top border-bottom mb-4 d-flex flex-column gap-3 small">
                    <div class="d-flex align-items-start gap-3">
                        <div class="text-muted mt-1">
                            <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <rect width="18" height="18" x="3" y="4" rx="2" />
                                <line x1="16" x2="16" y1="2" y2="6" />
                                <line x1="8" x2="8" y1="2" y2="6" />
                                <line x1="3" x2="21" y1="10" y2="10" />
                            </svg>
                        </div>
                        <div>
                            <div class="fw-medium text-dark">Ngày diễn ra</div>
                            <div id="sidebar-date" class="text-secondary">{{ \Carbon\Carbon::parse($event->thoi_gian_bat_dau)->format('d/m/Y') }}</div>
                        </div>
                    </div>

                    <div class="d-flex align-items-start gap-3">
                        <div class="text-muted mt-1">
                            <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" />
                                <polyline points="12 6 12 12 16 14" />
                            </svg>
                        </div>
                        <div>
                            <div class="fw-medium text-dark">Thời gian</div>
                            <div id="sidebar-time" class="text-secondary">
                                {{ \Carbon\Carbon::parse($event->thoi_gian_bat_dau)->format('H:i') }} -
                                {{ \Carbon\Carbon::parse($event->thoi_gian_ket_thuc)->format('H:i') }}
                            </div>
                        </div>
                    </div>

                    <div class="d-flex align-items-start gap-3">
                        <div class="text-muted mt-1">
                            <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0" />
                                <circle cx="12" cy="10" r="3" />
                            </svg>
                        </div>
                        <div>
                            <div class="fw-medium text-dark">Địa điểm</div>
                            <div id="sidebar-location" class="text-secondary">{{ $event->dia_diem }}</div>
                        </div>
                    </div>
                </div>

                <!-- Action Button (Kết nối CSDL bảng dang_ky) -->
                @if($userTicket && $userTicket->trang_thai !== 'da_huy')
                <div class="alert alert-success d-flex align-items-center gap-2 mb-2 p-3 small rounded-3 border">
                    <svg style="width: 20px; height: 20px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <div>
                        <div class="fw-bold">Bạn đã có vé sự kiện này!</div>
                        <div class="font-monospace text-muted mt-0.5">Mã vé: {{ $userTicket->ma_ve }}</div>
                    </div>
                </div>
                <a href="{{ route('Dashboard.myTicket') }}" class="btn btn-outline-dark w-100 py-2.5 fw-medium shadow-sm rounded-3">
                    Xem vé trong "Vé của tôi"
                </a>
                @elseif($veConLai > 0)
                <form method="POST" action="{{ route('Event.book', $event->id) }}">
                    @csrf
                    <button
                        type="submit"
                        id="register-action-btn"
                        class="btn btn-dark w-100 py-2.5 fw-semibold shadow-sm rounded-3 d-flex align-items-center justify-content-center gap-2">
                        <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                        <span>{{ ($userTicket && $userTicket->trang_thai === 'da_huy') ? 'Đăng ký lại vé tham dự' : 'Đăng ký tham gia ngay' }}</span>
                    </button>
                </form>
                <p class="text-center text-muted mt-2 mb-0 small">Vé điện tử sẽ được cấp ngay vào CSDL sau khi đăng ký</p>
                @else
                <button
                    type="button"
                    class="btn btn-secondary w-100 py-2.5 fw-medium shadow-sm rounded-3"
                    disabled>
                    Đã hết vé
                </button>
                @endif
            </div>
        </div>
    </div>
</div>

<meta name="csrf-token" content="{{ csrf_token() }}">

@push('scripts')
<script>
  function toggleDetailFavorite(eventId) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const icon = document.getElementById('detail-heart-icon');
    const btn = document.getElementById('event-bookmark-btn');

    fetch('{{ url("/favorite/toggle") }}/' + eventId, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json'
      }
    })
    .then(res => res.json())
    .then(data => {
      if (data.login_required) {
        showConfirmModal('Vui lòng đăng nhập để lưu sự kiện yêu thích. Bạn có muốn chuyển đến trang Đăng nhập không?', () => {
          window.location.href = '{{ route("Auth.index") }}';
        }, 'Yêu cầu đăng nhập', 'Đến trang đăng nhập', 'btn-dark');
        return;
      }
      if (data.success) {
        if (data.is_favorited) {
          icon.classList.remove('text-muted');
          icon.classList.add('text-danger');
          icon.style.fill = 'currentColor';
          btn.title = 'Bỏ lưu khỏi yêu thích';
        } else {
          icon.classList.remove('text-danger');
          icon.classList.add('text-muted');
          icon.style.fill = 'none';
          btn.title = 'Lưu vào yêu thích';
        }

        const navBadge = document.getElementById('nav-fav-badge');
        if (navBadge) {
          navBadge.textContent = data.count;
          if (data.count > 0) navBadge.classList.remove('d-none');
          else navBadge.classList.add('d-none');
        }

        showToast(data.message, data.is_favorited ? 'success' : 'dark');
      }
    })
    .catch(err => {
      console.error(err);
      showToast('Không thể kết nối máy chủ để cập nhật CSDL.', 'danger');
    });
  }
</script>
@endpush
@endsection