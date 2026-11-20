<div id="adm-view-events">
    <form method="GET" action="{{ route('admin.index') }}" class="d-flex gap-2 mb-3">
        <input type="hidden" name="tab" value="events">
        <input type="search" name="event_search" value="{{ request('event_search') }}" class="form-control" placeholder="Tìm tên, mô tả, địa điểm, danh mục" aria-label="Tìm sự kiện">
        <button class="btn btn-dark" type="submit">Tìm</button>
    </form>
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
                $registered = $event->so_luong_da_dang_ky;
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
                                data-id="{{ $event->id }}"
                                data-title="{{ $event->ten_su_kien }}"
                                data-category="{{ $event->danh_muc_id }}"
                                data-capacity="{{ $event->so_luong_toi_da }}"
                                data-start="{{ optional($event->thoi_gian_bat_dau)->format('Y-m-d\\TH:i') }}"
                                data-end="{{ optional($event->thoi_gian_ket_thuc)->format('Y-m-d\\TH:i') }}"
                                data-location="{{ $event->dia_diem }}"
                                data-price="{{ $event->gia_ve }}"
                                data-image="{{ $event->hinh_anh }}"
                                data-status="{{ $event->trang_thai }}"
                                data-description="{{ $event->mo_ta }}"
                                onclick="openEditEventModal(this)"
                                title="Chỉnh sửa sự kiện">
                                <i class="fa-regular fa-pen-to-square" style="font-size: 14px;"></i>
                            </button>
                            <!-- Nút Xóa sự kiện -->
                            <form method="POST" action="{{ route('admin.event.destroy', $event->id) }}" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa sự kiện này?')">
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
    @if($events->hasPages())
        <div class="mt-3">{{ $events->links('pagination::bootstrap-5') }}</div>
    @endif
</div>
