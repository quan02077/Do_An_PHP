@extends('layouts.app')

@section('title', 'Bảng điều khiển Quản trị — QQQ')

@section('content')
<div class="row g-3 mb-4" id="admin-stats-grid">
    <!-- Rendered by JS -->
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
            <button type="button" onclick="openAddCategoryModal()" class="btn btn-sm btn-outline-secondary rounded-3 px-3 py-2 fw-medium small d-flex align-items-center gap-1.5">
                <svg style="width: 15px; height: 15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Thêm danh mục</span>
            </button>
            <button type="button" onclick="openAddEventModal()" class="btn btn-sm btn-dark rounded-3 px-3 py-2 fw-medium small d-flex align-items-center gap-1.5 shadow-xs">
                <svg style="width: 15px; height: 15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
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
                    <!-- Render dynamically -->
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
                    <!-- Render dynamically -->
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
                    <!-- Render dynamically -->
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection