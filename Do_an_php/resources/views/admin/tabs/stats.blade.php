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

<!-- Biểu đồ phân tích trực quan (Chart.js) -->
<div class="row g-3 mb-4">
    <!-- Chart 1: Phân bổ sự kiện theo danh mục -->
    <div class="col-12 col-lg-5">
        <div class="bg-white p-4 rounded-4 border shadow-sm h-100 d-flex flex-column">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h3 class="fs-6 fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                    <i class="fa-solid fa-chart-pie text-primary"></i>
                    <span>Sự kiện theo danh mục</span>
                </h3>
                <span class="badge bg-light text-secondary border font-monospace small">Tỷ lệ %</span>
            </div>
            <div class="flex-grow-1 d-flex align-items-center justify-content-center position-relative" style="min-height: 240px; max-height: 260px;">
                <canvas id="categoryChartCanvas"></canvas>
            </div>
        </div>
    </div>

    <!-- Chart 2: Top sự kiện nhiều lượt đăng ký nhất -->
    <div class="col-12 col-lg-7">
        <div class="bg-white p-4 rounded-4 border shadow-sm h-100 d-flex flex-column">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h3 class="fs-6 fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                    <i class="fa-solid fa-chart-column text-success"></i>
                    <span>Top sự kiện có nhiều lượt đăng ký</span>
                </h3>
                <span class="badge bg-light text-secondary border font-monospace small">Số lượng vé</span>
            </div>
            <div class="flex-grow-1 d-flex align-items-center justify-content-center position-relative" style="min-height: 240px; max-height: 260px;">
                <canvas id="eventBookingChartCanvas"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Tích hợp Thư viện Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Dữ liệu Danh mục
    const catLabels = {{ Illuminate\Support\Js::from($categoryChart['labels'] ?? []) }};
    const catCounts = {{ Illuminate\Support\Js::from($categoryChart['counts'] ?? []) }};

    const ctxCat = document.getElementById('categoryChartCanvas');
    if (ctxCat) {
        new Chart(ctxCat, {
            type: 'doughnut',
            data: {
                labels: catLabels.length ? catLabels : ['Chưa có danh mục'],
                datasets: [{
                    data: catCounts.length ? catCounts : [1],
                    backgroundColor: [
                        '#3b82f6', '#10b981', '#f59e0b', '#ef4444', 
                        '#8b5cf6', '#06b6d4', '#ec4899', '#64748b'
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 12,
                            padding: 12,
                            font: { size: 11.5, family: 'sans-serif' }
                        }
                    }
                },
                cutout: '62%'
            }
        });
    }

    // 2. Dữ liệu Top sự kiện đăng ký vé
    const eventLabels = {{ Illuminate\Support\Js::from($eventBookingChart['labels'] ?? []) }};
    const eventCounts = {{ Illuminate\Support\Js::from($eventBookingChart['counts'] ?? []) }};

    const ctxEvent = document.getElementById('eventBookingChartCanvas');
    if (ctxEvent) {
        new Chart(ctxEvent, {
            type: 'bar',
            data: {
                labels: eventLabels.length ? eventLabels : ['Chưa có sự kiện'],
                datasets: [{
                    label: 'Lượt vé',
                    data: eventCounts.length ? eventCounts : [0],
                    backgroundColor: '#111827',
                    borderRadius: 6,
                    maxBarThickness: 36
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, precision: 0 }
                    },
                    x: {
                        grid: { display: false },
                        ticks: {
                            font: { size: 11 }
                        }
                    }
                }
            }
        });
    }
});
</script>
