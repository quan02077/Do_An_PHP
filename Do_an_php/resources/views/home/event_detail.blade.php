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

    <!-- Flash Alert -->
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

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="position-relative rounded-4 overflow-hidden bg-dark shadow-sm border mb-4">
                <img id="event-image" src="{{ $event->url_hinh_anh }}" alt="{{ $event->ten_su_kien }}" class="w-100 object-fit-cover" style="height: 360px;" />
                <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(to top, rgba(15, 23, 42, 0.85) 0%, rgba(15, 23, 42, 0.25) 50%, transparent 100%); pointer-events: none;"></div>

                <div class="position-absolute top-0 start-0 p-3 d-flex gap-2" id="event-badges-container">
                    <span class="badge text-bg-light fw-medium">{{ $event->danhMuc->ten_danh_muc ?? 'Chung' }}</span>
                    @if($event->trang_thai === 'da_huy')
                    <span class="badge bg-danger fw-medium">Đã hủy</span>
                    @elseif($event->trang_thai_dien_ra === 'sap_dien_ra')
                    <span class="badge bg-primary fw-medium">Sắp diễn ra</span>
                    @elseif($event->trang_thai_dien_ra === 'dang_dien_ra')
                    <span class="badge bg-success fw-medium">Đang diễn ra</span>
                    @else
                    <span class="badge bg-secondary fw-medium">Đã kết thúc</span>
                    @endif
                </div>

                <!-- Nút Bookmark Trái tim -->
                <form action="{{ route('Favorite.toggle', $event->id) }}" method="POST" class="position-absolute top-0 end-0 m-3 z-2">
                    @csrf
                    <button
                        type="submit"
                        class="btn btn-light rounded-circle p-0 d-flex align-items-center justify-content-center shadow-sm border"
                        style="width: 44px; height: 44px;"
                        title="{{ $isFavorited ? 'Bỏ lưu khỏi yêu thích' : 'Lưu vào yêu thích' }}">
                        <i class="{{ $isFavorited ? 'fa-solid fa-heart text-danger' : 'fa-regular fa-heart text-secondary' }} fs-5"></i>
                    </button>
                </form>

                <div class="position-absolute bottom-0 start-0 end-0 p-4 text-white">
                    <h1 id="event-title" class="fw-bold tracking-tight mb-2 display-6">{{ $event->ten_su_kien }}</h1>
                    <div class="d-flex flex-wrap align-items-center gap-3 small text-white-50">
                        <div class="d-flex align-items-center gap-2" id="event-time-badge">
                            <i class="fa-regular fa-calendar"></i>
                            <span>{{ \Carbon\Carbon::parse($event->thoi_gian_bat_dau)->format('H:i - d/m/Y') }}</span>
                        </div>
                        <div class="d-flex align-items-center gap-2" id="event-location-badge">
                            <i class="fa-solid fa-location-dot"></i>
                            <span>{{ $event->dia_diem }}</span>
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
                                <i class="fa-regular fa-envelope text-muted"></i>
                                <span>{{ $event->nguoiTao->email }}</span>
                            </span>
                            @endif
                            @if(!empty($event->nguoiTao->so_dien_thoai))
                            <span class="d-flex align-items-center gap-2">
                                <i class="fa-solid fa-phone text-muted"></i>
                                <span>{{ $event->nguoiTao->so_dien_thoai }}</span>
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
                            <i class="fa-regular fa-calendar"></i>
                        </div>
                        <div>
                            <div class="fw-medium text-dark">Ngày diễn ra</div>
                            <div id="sidebar-date" class="text-secondary">{{ \Carbon\Carbon::parse($event->thoi_gian_bat_dau)->format('d/m/Y') }}</div>
                        </div>
                    </div>

                    <div class="d-flex align-items-start gap-3">
                        <div class="text-muted mt-1">
                            <i class="fa-regular fa-clock"></i>
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
                            <i class="fa-solid fa-location-dot"></i>
                        </div>
                        <div>
                            <div class="fw-medium text-dark">Địa điểm</div>
                            <div id="sidebar-location" class="text-secondary">{{ $event->dia_diem }}</div>
                        </div>
                    </div>
                </div>

                <!-- Action Button (Kết nối CSDL bảng dang_ky) -->
                @if($event->trang_thai === 'da_huy')
                <div class="alert alert-danger d-flex align-items-center gap-2 mb-2 p-3 small rounded-3 border">
                    <i class="fa-solid fa-circle-xmark fs-5 text-danger flex-shrink-0"></i>
                    <div>
                        <div class="fw-bold">Sự kiện đã bị hủy!</div>
                        <div class="text-muted mt-0.5">Ban tổ chức đã thông báo hủy sự kiện này. Không thể đặt vé.</div>
                    </div>
                </div>
                <button type="button" class="btn btn-secondary w-100 py-2.5 fw-medium shadow-sm rounded-3" disabled>
                    Sự kiện đã bị hủy
                </button>
                @elseif($userTicket && $userTicket->trang_thai !== 'da_huy')
                <div class="alert alert-success d-flex align-items-center gap-2 mb-2 p-3 small rounded-3 border">
                    <i class="fa-solid fa-circle-check fs-5 text-success flex-shrink-0"></i>
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
                        <i class="fa-solid fa-ticket"></i>
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

@endsection