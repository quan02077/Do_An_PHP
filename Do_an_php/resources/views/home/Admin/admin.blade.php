@extends('layouts.app')

@section('title', 'Bảng Quản trị — EventVN Admin Portal')

@section('content')
<div class="container-fluid px-3 px-lg-4 py-4">

    <!-- Top Overview Stats -->
    <div class="row g-3 mb-4" id="admin-stats-grid">
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="bg-white p-3 p-sm-4 rounded-4 border shadow-sm h-100">
                <div class="small fw-semibold text-muted text-uppercase tracking-wider mb-1">Tổng sự kiện</div>
                <div class="fs-3 fw-bold tracking-tight text-dark tabular-nums">{{ $totalEvents ?? 0 }}</div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="bg-white p-3 p-sm-4 rounded-4 border shadow-sm h-100">
                <div class="small fw-semibold text-muted text-uppercase tracking-wider mb-1">Sắp diễn ra</div>
                <div class="fs-3 fw-bold tracking-tight text-dark tabular-nums">{{ $upcomingEvents ?? 0 }}</div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="bg-white p-3 p-sm-4 rounded-4 border shadow-sm h-100">
                <div class="small fw-semibold text-muted text-uppercase tracking-wider mb-1">Vé đã đăng ký</div>
                <div class="fs-3 fw-bold tracking-tight text-dark tabular-nums">{{ $totalBookings ?? 0 }}</div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="bg-white p-3 p-sm-4 rounded-4 border shadow-sm h-100">
                <div class="small fw-semibold text-muted text-uppercase tracking-wider mb-1">Thành viên</div>
                <div class="fs-3 fw-bold tracking-tight text-dark tabular-nums">{{ $totalMembers ?? 0 }}</div>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs & Actions & Tables -->
    <div class="bg-white rounded-4 border shadow-sm p-3 p-sm-4 p-md-5">
        <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 border-bottom pb-4 mb-4">
            <!-- Admin Tabs -->
            <div class="btn-group p-1 bg-light rounded-pill align-self-start" role="group" aria-label="Admin navigation tabs">
                <button type="button" onclick="switchAdminTab('events')" id="adm-tab-events" class="btn btn-sm rounded-pill fw-semibold btn-dark shadow-xs px-3 py-2">
                    Quản lý sự kiện
                </button>
                <button type="button" onclick="switchAdminTab('registrations')" id="adm-tab-registrations" class="btn btn-sm rounded-pill fw-medium text-secondary px-3 py-2">
                    Duyệt đăng ký vé
                </button>
                <button type="button" onclick="switchAdminTab('categories')" id="adm-tab-categories" class="btn btn-sm rounded-pill fw-medium text-secondary px-3 py-2">
                    Danh mục sự kiện
                </button>
            </div>

            <!-- Action Buttons -->
            <div id="admin-action-btn-container" class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-sm btn-outline-secondary rounded-3 px-3 py-2 fw-medium small d-flex align-items-center gap-1.5">
                    <i class="fa-solid fa-plus me-1"></i>
                    <span>Thêm danh mục</span>
                </button>
                <button type="button" class="btn btn-sm btn-dark rounded-3 px-3 py-2 fw-medium small d-flex align-items-center gap-1.5 shadow-xs">
                    <i class="fa-solid fa-plus me-1"></i>
                    <span>Thêm sự kiện mới</span>
                </button>
            </div>
        </div>

        <!-- ─── TAB 1: QUẢN LÝ SỰ KIỆN ─────────────────────────────────────── -->
        <div id="adm-view-events">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 small">
                    <thead class="table-light text-secondary text-uppercase small">
                        <tr>
                            <th class="py-3 px-3">Sự kiện</th>
                            <th class="py-3 px-3">Danh mục</th>
                            <th class="py-3 px-3">Thời gian</th>
                            <th class="py-3 px-3">Địa điểm</th>
                            <th class="py-3 px-3">Đăng ký</th>
                            <th class="py-3 px-3">Trạng thái</th>
                            <th class="py-3 px-3 text-end">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody id="admin-events-tbody">
                        @forelse($events ?? [] as $event)
                        @php
                        $img = $event->url_hinh_anh;
                        $name_event = $event->ten_su_kien;
                        $name_category = $event->danhMuc->ten_danh_muc ?? 'Chung';
                        $date = $event->thoi_gian_bat_dau ? $event->thoi_gian_bat_dau->format('d/m/Y H:i') : 'N/A';
                        $location = $event->dia_diem ?? 'N/A';
                        $registered = $event->dangKys->count();
                        $capacity = $event->so_luong_toi_da;
                        $status = $event->trang_thai_dien_ra;
                        @endphp
                        <tr>
                            <td class="py-3 px-3">
                                <div class="d-flex align-items-center gap-3">
                                    <img src="{{ $img }}" alt="{{ $name_event }}" class="rounded-3 object-fit-cover flex-shrink-0" style="width: 44px; height: 44px;" />
                                    <span class="fw-semibold text-dark text-truncate" style="max-width: 240px;">{{ $name_event }}</span>
                                </div>
                            </td>
                            <td class="py-3 px-3 fw-medium text-secondary">{{ $name_category }}</td>
                            <td class="py-3 px-3 text-secondary">{{ $date }}</td>
                            <td class="py-3 px-3 text-secondary text-truncate" style="max-width: 200px;">{{ $location }}</td>
                            <td class="py-3 px-3 fw-medium text-dark">{{ $registered }}/{{ $capacity }}</td>
                            <td class="py-3 px-3">
                                @if($status === 'sap_dien_ra')
                                <span class="badge rounded-pill bg-warning-subtle text-warning border border-warning-subtle">Sắp diễn ra</span>
                                @elseif($status === 'dang_dien_ra')
                                <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle">Đang diễn ra</span>
                                @elseif($status === 'da_ket_thuc')
                                <span class="badge rounded-pill bg-secondary-subtle text-secondary border border-secondary-subtle">Đã kết thúc</span>
                                @elseif($status === 'da_huy')
                                <span class="badge rounded-pill bg-danger-subtle text-danger border border-danger-subtle">Đã hủy</span>
                                @else
                                <span class="badge rounded-pill bg-light text-secondary border">Bản nháp</span>
                                @endif
                            </td>
                            <td class="py-3 px-3 text-end">
                                <div class="d-flex align-items-center justify-content-end gap-1">
                                    <!-- Nút Sửa sự kiện -->
                                    <button type="button"
                                        class="btn btn-sm btn-outline-primary p-1 rounded-2"
                                        title="Chỉnh sửa sự kiện">
                                        <i class="fa-regular fa-pen-to-square" style="font-size: 14px;"></i>
                                    </button>
                                    <!-- Nút Xóa sự kiện -->
                                    <form method="POST" action="{{ route('Event.destroy', $event->id) }}" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa sự kiện này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger p-1 rounded-2" title="Xóa sự kiện">
                                            <i class="fa-regular fa-trash-can" style="font-size: 14px;"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                Chưa có sự kiện nào trong hệ thống.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ─── TAB 2: DUYỆT ĐĂNG KÝ VÉ ───────────────────────────────────── -->
        <div id="adm-view-registrations" class="d-none">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 small">
                    <thead class="table-light text-secondary text-uppercase small">
                        <tr>
                            <th class="py-3 px-3">Mã vé</th>
                            <th class="py-3 px-3">Người tham dự</th>
                            <th class="py-3 px-3">Sự kiện</th>
                            <th class="py-3 px-3">Ngày đăng ký</th>
                            <th class="py-3 px-3">Ghi chú</th>
                            <th class="py-3 px-3">Trạng thái</th>
                            <th class="py-3 px-3 text-end">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody id="admin-regs-tbody">
                        @forelse($registrations ?? [] as $reg)
                        <tr>
                            <td class="py-3 px-3 font-monospace fw-medium text-secondary">{{ $reg->ma_ve }}</td>
                            <td class="py-3 px-3">
                                <div class="fw-semibold text-dark">{{ $reg->nguoiDung->ho_ten ?? 'Khách' }}</div>
                                <div class="text-muted font-monospace small">{{ $reg->nguoiDung->email ?? 'N/A' }}</div>
                            </td>
                            <td class="py-3 px-3 fw-medium text-dark text-truncate" style="max-width: 240px;">{{ $reg->suKien->ten_su_kien ?? ('Sự kiện #' . $reg->su_kien_id) }}</td>
                            <td class="py-3 px-3 text-secondary">{{ $reg->thoi_gian_dang_ky ? \Carbon\Carbon::parse($reg->thoi_gian_dang_ky)->format('d/m/Y H:i') : '' }}</td>
                            <td class="py-3 px-3 text-muted fst-italic">{{ $reg->ghi_chu ?: 'Không có' }}</td>
                            <td class="py-3 px-3">
                                @if($reg->trang_thai === 'da_xac_nhan')
                                <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle">Đã duyệt</span>
                                @elseif($reg->trang_thai === 'da_huy')
                                <span class="badge rounded-pill bg-danger-subtle text-danger border border-danger-subtle">Đã hủy</span>
                                @else
                                <span class="badge rounded-pill bg-warning-subtle text-warning border border-warning-subtle">Chờ duyệt</span>
                                @endif
                            </td>
                            <td class="py-3 px-3 text-end">
                                @if($reg->trang_thai !== 'da_xac_nhan')
                                <button type="button" class="btn btn-sm btn-outline-success py-1 px-2.5 rounded-2 me-1">Duyệt</button>
                                @endif
                                @if($reg->trang_thai !== 'da_huy')
                                <button type="button" class="btn btn-sm btn-outline-danger py-1 px-2.5 rounded-2">Hủy</button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                Chưa có lượt đăng ký vé nào.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ─── TAB 3: DANH MỤC SỰ KIỆN ──────────────────────────────────── -->
        <div id="adm-view-categories" class="d-none">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 small">
                    <thead class="table-light text-secondary text-uppercase small">
                        <tr>
                            <th class="py-3 px-3">ID</th>
                            <th class="py-3 px-3">Tên danh mục</th>
                            <th class="py-3 px-3">Mô tả</th>
                            <th class="py-3 px-3">Số lượng sự kiện</th>
                            <th class="py-3 px-3">Trạng thái</th>
                            <th class="py-3 px-3 text-end">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody id="admin-cats-tbody">
                        @forelse($categories ?? [] as $cat)
                        <tr>
                            <td class="py-3 px-3 font-monospace text-muted">#{{ $cat->id }}</td>
                            <td class="py-3 px-3">
                                <div class="d-flex align-items-center gap-2 fw-semibold text-dark">
                                    <span class="p-1 rounded bg-light border text-secondary flex-shrink-0 d-inline-flex align-items-center justify-content-center" style="width: 26px; height: 26px;">
                                        <i class="fa-solid fa-folder text-secondary" style="font-size: 13px;"></i>
                                    </span>
                                    <span>{{ $cat->ten_danh_muc }}</span>
                                </div>
                            </td>
                            <td class="py-3 px-3 text-muted text-truncate" style="max-width: 240px;">{{ $cat->mo_ta ?: '—' }}</td>
                            <td class="py-3 px-3 fw-medium text-secondary">{{ $cat->su_kiens_count ?? 0 }} sự kiện</td>
                            <td class="py-3 px-3">
                                <span class="badge rounded-pill border bg-success-subtle text-success border-success-subtle">
                                    Hoạt động
                                </span>
                            </td>
                            <td class="py-3 px-3 text-end">
                                <div class="d-flex align-items-center justify-content-end gap-1">
                                    <!-- Nút Sửa danh mục -->
                                    <button type="button"
                                        class="btn btn-sm btn-outline-primary p-1 rounded-2"
                                        title="Chỉnh sửa danh mục">
                                        <i class="fa-regular fa-pen-to-square" style="font-size: 14px;"></i>
                                    </button>
                                    <!-- Nút Xóa danh mục -->
                                    <button type="button" class="btn btn-sm btn-outline-danger p-1 rounded-2" title="Xóa danh mục">
                                        <i class="fa-regular fa-trash-can" style="font-size: 14px;"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                Chưa có danh mục nào.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>

<!-- ─── TÁCH MODALS RA FILE RIÊNG (THEO YÊU CẦU) ───────────────────────── -->
@include('home.Admin.modal_event')
@include('home.Admin.modal_category')

@endsection

@push('scripts')
<script>
    function switchAdminTab(tab) {
        const tabs = ["events", "registrations", "categories"];
        tabs.forEach((t) => {
            const btn = document.getElementById("adm-tab-" + t);
            const view = document.getElementById("adm-view-" + t);
            if (t === tab) {
                btn.className = "btn btn-sm rounded-pill fw-semibold btn-dark shadow-xs px-3 py-2";
                view.classList.remove("d-none");
            } else {
                btn.className = "btn btn-sm rounded-pill fw-medium text-secondary px-3 py-2";
                view.classList.add("d-none");
            }
        });
    }
</script>
@endpush