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
