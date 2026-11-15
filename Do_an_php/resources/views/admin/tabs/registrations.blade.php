<div id="adm-view-registrations" class="d-none">

    <!-- Card: Quét mã QR Soát vé Check-in -->
    <div class="bg-light p-3 p-md-4 rounded-4 border mb-4">
        <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
            <i class="fa-solid fa-qrcode text-primary"></i>
            <span>Soát vé & Check-in QR</span>
        </h5>
        
        <div class="row g-3">
            <!-- Khung Camera -->
            <div class="col-12 col-md-5 text-center">
                <div id="reader" style="width: 100%; max-width: 280px; min-height: 180px;" class="mx-auto border rounded-3 overflow-hidden bg-white mb-2 d-flex align-items-center justify-content-center text-muted small">
                    <span>Camera sẽ hiển thị tại đây</span>
                </div>
                <button type="button" id="btn-camera" class="btn btn-sm btn-dark" onclick="batCamera()">
                    <i class="fa-solid fa-camera me-1"></i> Bật Camera Quét QR
                </button>
            </div>

            <!-- Ô Nhập mã vé & Nơi hiện kết quả -->
            <div class="col-12 col-md-7">
                <div class="input-group mb-2">
                    <input type="text" id="inputMaVe" class="form-control font-monospace" placeholder="Nhập mã vé (VD: QQQ-2026-XXXXXX)">
                    <button type="button" class="btn btn-dark" onclick="guiCheckIn(document.getElementById('inputMaVe').value)">
                        Check-in
                    </button>
                </div>

                <!-- Hộp hiện kết quả: Xanh (Thành công) hoặc Đỏ (Lỗi) -->
                <div id="hopKetQua" class="alert d-none mt-2 p-3 small rounded-3"></div>
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

<!-- Thư viện Quét mã QR bằng Camera chuẩn HTML5 (CDN Cloudflare) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"></script>

<script>
let scanner = null;
let isScanning = false;
let isThrottled = false;

// HÀM 1: Bật / Tắt camera của Laptop hoặc Điện thoại
function batCamera() {
    const btn = document.getElementById('btn-camera');

    if (!isScanning) {
        if (!scanner) {
            scanner = new Html5Qrcode("reader");
        }

        scanner.start(
            { facingMode: "environment" },
            { fps: 10, qrbox: { width: 220, height: 220 } },
            (maVeQuetDuoc) => {
                if (!isThrottled) {
                    isThrottled = true;
                    guiCheckIn(maVeQuetDuoc);
                    setTimeout(() => { isThrottled = false; }, 3000); // Ngưng 3s tránh quét lặp liên tục
                }
            }
        ).then(() => {
            isScanning = true;
            btn.className = "btn btn-sm btn-outline-danger";
            btn.innerHTML = '<i class="fa-solid fa-stop me-1"></i> Tắt Camera';
        }).catch(err => {
            alert('Không thể mở Camera: ' + err + '\n\nLưu ý: Bạn hãy kiểm tra xem trình duyệt Chrome có đang chặn quyền Camera không nhé!');
        });
    } else {
        scanner.stop().then(() => {
            isScanning = false;
            btn.className = "btn btn-sm btn-dark";
            btn.innerHTML = '<i class="fa-solid fa-camera me-1"></i> Bật Camera Quét QR';
            document.getElementById('reader').innerHTML = '<span class="text-muted small">Camera đã tắt</span>';
        });
    }
}

// HÀM 2: Gửi mã vé lên Server Laravel bằng hàm fetch()
function guiCheckIn(maVe) {
    maVe = (maVe || '').trim();
    if (!maVe) return alert('Vui lòng nhập mã vé!');

    const hopKetQua = document.getElementById('hopKetQua');
    hopKetQua.className = 'alert alert-info mt-2 p-3 small rounded-3';
    hopKetQua.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i>Đang kiểm tra mã vé <strong>' + maVe + '</strong>...';
    hopKetQua.classList.remove('d-none');

    fetch('/admin/check-in', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ ma_ve: maVe })
    })
    .then(response => response.json())
    .then(data => {
        hopKetQua.classList.remove('d-none', 'alert-info', 'alert-success', 'alert-danger');

        if (data.success) {
            // Check-in thành công: Hiện hộp màu xanh
            hopKetQua.classList.add('alert-success');
            hopKetQua.innerHTML = '<i class="fa-solid fa-circle-check me-2 fs-6"></i><strong>' + data.message + '</strong>';

            // Cập nhật trạng thái dòng vé trong bảng phía dưới thành "Đã tham gia"
            if (data.ticket && data.ticket.id) {
                const cell = document.getElementById('reg-status-cell-' + data.ticket.id);
                if (cell) {
                    cell.innerHTML = '<span class="badge rounded-pill bg-info text-white border border-info"><i class="fa-solid fa-circle-check me-1"></i>Đã tham gia</span>';
                }
            }
            document.getElementById('inputMaVe').value = '';
        } else {
            // Có lỗi hoặc vé đã dùng: Hiện hộp màu đỏ
            hopKetQua.classList.add('alert-danger');
            hopKetQua.innerHTML = '<i class="fa-solid fa-circle-xmark me-2 fs-6"></i><strong>' + data.message + '</strong>';
        }
    })
    .catch(err => {
        hopKetQua.className = 'alert alert-danger mt-2 p-3 small rounded-3';
        hopKetQua.innerHTML = 'Lỗi kết nối khi gửi check-in!';
    });
}
</script>
