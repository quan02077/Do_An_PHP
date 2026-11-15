<div id="adm-view-registrations" class="d-none">

    <!-- Card: Quét mã QR Soát vé Check-in -->
    <div class="bg-light rounded-4 border p-3 p-md-4 mb-4">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-3">
            <div>
                <h3 class="fs-6 fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                    <i class="fa-solid fa-qrcode text-primary"></i>
                    <span>Soát vé & Check-in QR tại sự kiện</span>
                </h3>
                <p class="text-secondary small mb-0">Quét mã QR trên điện thoại của khách hoặc nhập mã vé để xác nhận tham gia.</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button type="button" id="btn-toggle-camera" onclick="toggleCameraScanner()" class="btn btn-sm btn-dark rounded-3 px-3 py-2 fw-medium d-flex align-items-center gap-1.5 shadow-xs">
                    <i class="fa-solid fa-camera"></i>
                    <span id="btn-camera-text">Bật Camera Quét QR</span>
                </button>
            </div>
        </div>

        <div class="row g-3 align-items-start">
            <!-- Cột Camera Scanner -->
            <div class="col-12 col-md-5">
                <div id="qr-camera-container" class="rounded-3 border bg-white p-2 d-none text-center">
                    <div id="qr-reader" style="width: 100%;" class="rounded-2 overflow-hidden"></div>
                    <div class="small text-muted mt-2"><i class="fa-solid fa-video me-1"></i>Đang hướng camera để quét mã QR...</div>
                </div>
                <div id="qr-camera-placeholder" class="rounded-3 border bg-white p-4 text-center text-muted small">
                    <i class="fa-solid fa-camera-retro fs-3 mb-2 d-block text-secondary"></i>
                    Nhấn "<strong>Bật Camera Quét QR</strong>" để dùng Webcam laptop hoặc Camera điện thoại điểm danh.
                </div>
            </div>

            <!-- Cột Nhập tay & Kết quả Check-in -->
            <div class="col-12 col-md-7">
                <!-- Form nhập mã nhanh -->
                <div class="input-group mb-3">
                    <span class="input-group-text bg-white border-end-0 text-muted">
                        <i class="fa-solid fa-ticket"></i>
                    </span>
                    <input type="text" id="manual-ticket-code" class="form-control border-start-0 font-monospace" placeholder="Nhập mã vé (VD: QQQ-2026-ABC123 hoặc VE-XXXX)">
                    <button type="button" onclick="performCheckIn(document.getElementById('manual-ticket-code').value)" class="btn btn-dark px-3 fw-medium">
                        Check-in ngay
                    </button>
                </div>

                <!-- Box thông báo kết quả thời gian thực -->
                <div id="checkin-feedback" class="d-none rounded-3 border p-3 small">
                    <!-- Sẽ được fill bằng JavaScript -->
                </div>
            </div>
        </div>
    </div>

    <!-- Bảng danh sách đăng ký vé -->
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
                <tr id="reg-row-{{ $reg->id }}" data-ticket-code="{{ $reg->ma_ve }}">
                    <td class="py-3 px-3 font-monospace fw-medium text-secondary">{{ $reg->ma_ve }}</td>
                    <td class="py-3 px-3">
                        <div class="fw-semibold text-dark">{{ $reg->nguoiDung->ho_ten ?? 'Khách' }}</div>
                        <div class="text-muted font-monospace small">{{ $reg->nguoiDung->email ?? 'N/A' }}</div>
                    </td>
                    <td class="py-3 px-3 fw-medium text-dark text-truncate" style="max-width: 240px;">{{ $reg->suKien->ten_su_kien ?? ('Sự kiện #' . $reg->su_kien_id) }}</td>
                    <td class="py-3 px-3 text-secondary">{{ $reg->thoi_gian_dang_ky ? \Carbon\Carbon::parse($reg->thoi_gian_dang_ky)->format('d/m/Y H:i') : '' }}</td>
                    <td class="py-3 px-3 text-muted fst-italic">{{ $reg->ghi_chu ?: 'Không có' }}</td>
                    <td class="py-3 px-3" id="reg-status-cell-{{ $reg->id }}">
                        @if($reg->trang_thai === 'da_tham_gia' || $reg->trang_thai === 'da_check_in')
                        <span class="badge rounded-pill bg-info text-white border border-info">
                            <i class="fa-solid fa-circle-check me-1"></i>Đã tham gia
                        </span>
                        @elseif($reg->trang_thai === 'da_xac_nhan')
                        <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle">Đã duyệt</span>
                        @elseif($reg->trang_thai === 'da_huy')
                        <span class="badge rounded-pill bg-danger-subtle text-danger border border-danger-subtle">Đã hủy</span>
                        @else
                        <span class="badge rounded-pill bg-warning-subtle text-warning border border-warning-subtle">Chờ duyệt</span>
                        @endif
                    </td>
                    <td class="py-3 px-3 text-end">
                        @if($reg->trang_thai !== 'da_tham_gia' && $reg->trang_thai !== 'da_check_in' && $reg->trang_thai !== 'da_huy')
                        <button type="button" onclick="performCheckIn('{{ $reg->ma_ve }}')" class="btn btn-sm btn-outline-primary py-1 px-2.5 rounded-2 me-1" title="Xác nhận tham gia">
                            Check-in
                        </button>
                        @endif
                        @if($reg->trang_thai !== 'da_xac_nhan' && $reg->trang_thai !== 'da_tham_gia')
                        <form method="POST" action="{{ route('admin.registrations.status', $reg->id) }}" class="d-inline">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="trang_thai" value="da_xac_nhan">
                            <button type="submit" class="btn btn-sm btn-outline-success py-1 px-2.5 rounded-2 me-1">Duyệt</button>
                        </form>
                        @endif
                        @if($reg->trang_thai !== 'da_huy')
                        <form method="POST" action="{{ route('admin.registrations.status', $reg->id) }}" class="d-inline" onsubmit="return confirm('Xác nhận hủy đăng ký vé này?')">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="trang_thai" value="da_huy">
                            <button type="submit" class="btn btn-sm btn-outline-danger py-1 px-2.5 rounded-2">Hủy</button>
                        </form>
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

<!-- Thư viện Quét mã QR bằng Camera chuẩn HTML5 -->
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

<script>
let html5QrScanner = null;
let isCameraRunning = false;
let isScanningPaused = false;

function toggleCameraScanner() {
    const container = document.getElementById('qr-camera-container');
    const placeholder = document.getElementById('qr-camera-placeholder');
    const btnText = document.getElementById('btn-camera-text');

    if (!isCameraRunning) {
        container.classList.remove('d-none');
        placeholder.classList.add('d-none');
        btnText.textContent = 'Tắt Camera';

        html5QrScanner = new Html5Qrcode("qr-reader");
        html5QrScanner.start(
            { facingMode: "environment" },
            {
                fps: 10,
                qrbox: { width: 220, height: 220 }
            },
            (decodedText) => {
                if (!isScanningPaused) {
                    isScanningPaused = true;
                    performCheckIn(decodedText);
                    // Dừng 2.5 giây tránh quét lặp liên tục
                    setTimeout(() => { isScanningPaused = false; }, 2500);
                }
            },
            (errorMessage) => {
                // Ignore parse failures
            }
        ).catch(err => {
            alert('Không thể mở Camera: ' + err);
            stopCameraScanner();
        });
        isCameraRunning = true;
    } else {
        stopCameraScanner();
    }
}

function stopCameraScanner() {
    if (html5QrScanner && isCameraRunning) {
        html5QrScanner.stop().then(() => {
            html5QrScanner.clear();
            document.getElementById('qr-camera-container').classList.add('d-none');
            document.getElementById('qr-camera-placeholder').classList.remove('d-none');
            document.getElementById('btn-camera-text').textContent = 'Bật Camera Quét QR';
            isCameraRunning = false;
        }).catch(err => console.error(err));
    }
}

// Hàm gửi request Check-in qua RESTful API
function performCheckIn(ticketCode) {
    ticketCode = (ticketCode || '').trim();
    if (!ticketCode) {
        alert('Vui lòng nhập mã vé hợp lệ.');
        return;
    }

    const feedbackBox = document.getElementById('checkin-feedback');
    feedbackBox.classList.remove('d-none', 'alert-success', 'alert-warning', 'alert-danger');
    feedbackBox.className = 'rounded-3 border p-3 small bg-light text-secondary';
    feedbackBox.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i>Đang kiểm tra mã vé: <strong>' + ticketCode + '</strong>...';

    fetch('{{ route('admin.checkin') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ ma_ve: ticketCode })
    })
    .then(async response => {
        const data = await response.json();
        if (response.ok && data.success) {
            // Thành công
            feedbackBox.className = 'rounded-3 border border-success bg-success-subtle text-success p-3 small';
            feedbackBox.innerHTML = `
                <div class="d-flex align-items-center gap-2 fw-bold fs-6 mb-1">
                    <i class="fa-solid fa-circle-check fs-5"></i>
                    <span>Check-in Thành Công!</span>
                </div>
                <div><strong>Người tham dự:</strong> ${data.ticket.ho_ten} (${data.ticket.email})</div>
                <div><strong>Sự kiện:</strong> ${data.ticket.ten_su_kien}</div>
                <div><strong>Mã vé:</strong> <code class="text-dark">${data.ticket.ma_ve}</code> | <strong>Thời gian:</strong> ${data.ticket.thoi_gian}</div>
            `;

            // Cập nhật dòng tương ứng trong bảng
            if (data.ticket.id) {
                const cell = document.getElementById('reg-status-cell-' + data.ticket.id);
                if (cell) {
                    cell.innerHTML = '<span class="badge rounded-pill bg-info text-white border border-info"><i class="fa-solid fa-circle-check me-1"></i>Đã tham gia</span>';
                }
            }
            document.getElementById('manual-ticket-code').value = '';
        } else if (data.status === 'already_checked_in') {
            // Đã check-in trước đó
            feedbackBox.className = 'rounded-3 border border-warning bg-warning-subtle text-dark p-3 small';
            feedbackBox.innerHTML = `
                <div class="d-flex align-items-center gap-2 fw-bold fs-6 mb-1 text-warning-emphasis">
                    <i class="fa-solid fa-triangle-exclamation fs-5 text-warning"></i>
                    <span>CẢNH BÁO: VÉ ĐÃ ĐƯỢC SỬ DỤNG!</span>
                </div>
                <div>Vé <code>${data.ticket?.ma_ve || ticketCode}</code> của <strong>${data.ticket?.ho_ten || 'Khách'}</strong> đã được check-in vào lúc: <strong>${data.ticket?.thoi_gian || 'trước đó'}</strong>.</div>
            `;
        } else {
            // Lỗi hoặc không tìm thấy
            feedbackBox.className = 'rounded-3 border border-danger bg-danger-subtle text-danger p-3 small';
            feedbackBox.innerHTML = `
                <div class="d-flex align-items-center gap-2 fw-bold mb-1">
                    <i class="fa-solid fa-circle-xmark fs-5"></i>
                    <span>Không thể Check-in</span>
                </div>
                <div>${data.message || 'Mã vé không hợp lệ hoặc không tìm thấy trong hệ thống.'}</div>
            `;
        }
    })
    .catch(err => {
        feedbackBox.className = 'rounded-3 border border-danger bg-danger-subtle text-danger p-3 small';
        feedbackBox.innerHTML = '<i class="fa-solid fa-circle-xmark me-2"></i>Lỗi kết nối tới máy chủ khi check-in.';
    });
}
</script>
