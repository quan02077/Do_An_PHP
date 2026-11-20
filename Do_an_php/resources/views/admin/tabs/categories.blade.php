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
                        <span class="badge rounded-pill border {{ $cat->trang_thai === 'hoat_dong' ? 'bg-success-subtle text-success border-success-subtle' : 'bg-secondary-subtle text-secondary border-secondary-subtle' }}">
                            {{ $cat->trang_thai === 'hoat_dong' ? 'Hoạt động' : 'Tạm dừng' }}
                        </span>
                    </td>
                    <td class="py-3 px-3 text-end">
                        <div class="d-flex align-items-center justify-content-end gap-1">
                            <!-- Nút Sửa danh mục -->
                            <button type="button"
                                class="btn btn-sm btn-outline-primary p-1 rounded-2"
                                data-id="{{ $cat->id }}"
                                data-name="{{ $cat->ten_danh_muc }}"
                                data-description="{{ $cat->mo_ta }}"
                                data-status="{{ $cat->trang_thai }}"
                                onclick="openEditCategoryModal(this)"
                                title="Chỉnh sửa danh mục">
                                <i class="fa-regular fa-pen-to-square" style="font-size: 14px;"></i>
                            </button>
                            <!-- Nút Xóa danh mục -->
                            <form method="POST" action="{{ route('admin.categories.destroy', $cat->id) }}" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa danh mục này?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger p-1 rounded-2" title="Xóa danh mục">
                                    <i class="fa-regular fa-trash-can" style="font-size: 14px;"></i>
                                </button>
                            </form>
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
    @if($categories->hasPages())
        <div class="mt-3">{{ $categories->links('pagination::bootstrap-5') }}</div>
    @endif
</div>
