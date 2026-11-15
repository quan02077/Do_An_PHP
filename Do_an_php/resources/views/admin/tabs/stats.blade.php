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
