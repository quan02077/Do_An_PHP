<!-- ─── MODAL 1: THÊM SỰ KIỆN MỚI ──────────────────────────────────────── -->
<div class="modal fade" id="add-event-modal" tabindex="-1" aria-labelledby="addEventModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content rounded-4 border-0 shadow position-relative p-2 p-sm-3">
      <div class="modal-header border-0 pb-0">
        <div>
          <h2 class="fs-5 fw-bold tracking-tight text-dark mb-0" id="addEventModalLabel">Thêm sự kiện mới</h2>
          <p class="small text-muted mb-0">Điền đầy đủ thông tin để tạo và xuất bản sự kiện mới.</p>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-3 p-sm-4">
        <form method="POST" action="{{ route('Event.store') }}">
          @csrf
          <div class="mb-3">
            <label for="new-ev-title" class="form-label small fw-medium text-dark mb-1">Tên sự kiện <span class="text-danger">*</span></label>
            <input type="text" name="ten_su_kien" id="new-ev-title" required class="form-control form-control-sm py-2" placeholder="Ví dụ: Hội thảo Công nghệ AI Summit 2026..." />
          </div>

          <div class="row g-3 mb-3">
            <div class="col-sm-6">
              <div class="d-flex align-items-center justify-content-between mb-1">
                <label for="new-ev-category" class="form-label small fw-medium text-dark mb-0">Danh mục <span class="text-danger">*</span></label>
                <button type="button" onclick="openAddCategoryModal()" class="btn btn-link btn-sm p-0 text-decoration-none small text-primary fw-medium">
                  + Thêm mới
                </button>
              </div>
              <select name="danh_muc_id" id="new-ev-category" class="form-select form-select-sm py-2" required>
                @foreach($categories ?? [] as $cat)
                  <option value="{{ $cat->id }}">{{ $cat->ten_danh_muc }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-sm-6">
              <label for="new-ev-capacity" class="form-label small fw-medium text-dark mb-1">Quy mô chỗ ngồi (Số vé tối đa) <span class="text-danger">*</span></label>
              <input type="number" name="so_luong_toi_da" id="new-ev-capacity" required min="1" value="200" class="form-control form-control-sm py-2" />
            </div>
          </div>

          <div class="row g-3 mb-3">
            <div class="col-sm-6">
              <label for="new-ev-start" class="form-label small fw-medium text-dark mb-1">Thời gian bắt đầu <span class="text-danger">*</span></label>
              <input type="datetime-local" name="thoi_gian_bat_dau" id="new-ev-start" required class="form-control form-control-sm py-2" />
            </div>
            <div class="col-sm-6">
              <label for="new-ev-end" class="form-label small fw-medium text-dark mb-1">Thời gian kết thúc <span class="text-danger">*</span></label>
              <input type="datetime-local" name="thoi_gian_ket_thuc" id="new-ev-end" required class="form-control form-control-sm py-2" />
            </div>
          </div>

          <div class="row g-3 mb-3">
            <div class="col-sm-8">
              <label for="new-ev-location" class="form-label small fw-medium text-dark mb-1">Địa điểm tổ chức <span class="text-danger">*</span></label>
              <input type="text" name="dia_diem" id="new-ev-location" required class="form-control form-control-sm py-2" placeholder="Ví dụ: Trung tâm Hội nghị Quốc gia..." />
            </div>
            <div class="col-sm-4">
              <label for="new-ev-price" class="form-label small fw-medium text-dark mb-1">Giá vé (VNĐ, 0 = Miễn phí)</label>
              <input type="number" name="gia_ve" id="new-ev-price" min="0" step="1000" value="0" class="form-control form-control-sm py-2" placeholder="0" />
            </div>
          </div>

          <div class="mb-3">
            <label for="new-ev-image" class="form-label small fw-medium text-dark mb-1">Link ảnh thumbnail</label>
            <input type="url" name="hinh_anh" id="new-ev-image" value="https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=800" class="form-control form-control-sm py-2" />
          </div>

          <div class="mb-4">
            <label for="new-ev-desc" class="form-label small fw-medium text-dark mb-1">Mô tả chi tiết sự kiện</label>
            <textarea name="mo_ta" id="new-ev-desc" rows="3" required class="form-control form-control-sm py-2" placeholder="Nội dung tóm tắt và thông tin chương trình..."></textarea>
          </div>

          <div class="d-flex gap-2 pt-2 border-top">
            <button type="button" class="btn btn-outline-secondary flex-grow-1 py-2 small fw-medium" data-bs-dismiss="modal">Hủy</button>
            <button type="submit" class="btn btn-dark flex-grow-1 py-2 small fw-medium">
              <i class="fa-solid fa-plus me-1"></i> Tạo sự kiện
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- ─── MODAL 2: CHỈNH SỬA SỰ KIỆN ─────────────────────────────────────── -->
<div class="modal fade" id="edit-event-modal" tabindex="-1" aria-labelledby="editEventModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content rounded-4 border-0 shadow position-relative p-2 p-sm-3">
      <div class="modal-header border-0 pb-0">
        <div>
          <h2 class="fs-5 fw-bold tracking-tight text-dark mb-0" id="editEventModalLabel">Chỉnh sửa sự kiện</h2>
          <p class="small text-muted mb-0">Cập nhật thông tin chi tiết của sự kiện đã chọn.</p>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-3 p-sm-4">
        <form id="edit-event-form" method="POST" action="">
          @csrf
          @method('PUT')
          <input type="hidden" id="edit-ev-id" name="id" />

          <div class="mb-3">
            <label for="edit-ev-title" class="form-label small fw-medium text-dark mb-1">Tên sự kiện <span class="text-danger">*</span></label>
            <input type="text" name="ten_su_kien" id="edit-ev-title" required class="form-control form-control-sm py-2" />
          </div>

          <div class="row g-3 mb-3">
            <div class="col-sm-6">
              <label for="edit-ev-category" class="form-label small fw-medium text-dark mb-1">Danh mục <span class="text-danger">*</span></label>
              <select name="danh_muc_id" id="edit-ev-category" class="form-select form-select-sm py-2" required>
                @foreach($categories ?? [] as $cat)
                  <option value="{{ $cat->id }}">{{ $cat->ten_danh_muc }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-sm-6">
              <label for="edit-ev-capacity" class="form-label small fw-medium text-dark mb-1">Quy mô chỗ ngồi <span class="text-danger">*</span></label>
              <input type="number" name="so_luong_toi_da" id="edit-ev-capacity" required min="1" class="form-control form-control-sm py-2" />
            </div>
          </div>

          <div class="row g-3 mb-3">
            <div class="col-sm-6">
              <label for="edit-ev-start" class="form-label small fw-medium text-dark mb-1">Thời gian bắt đầu <span class="text-danger">*</span></label>
              <input type="datetime-local" name="thoi_gian_bat_dau" id="edit-ev-start" required class="form-control form-control-sm py-2" />
            </div>
            <div class="col-sm-6">
              <label for="edit-ev-end" class="form-label small fw-medium text-dark mb-1">Thời gian kết thúc <span class="text-danger">*</span></label>
              <input type="datetime-local" name="thoi_gian_ket_thuc" id="edit-ev-end" required class="form-control form-control-sm py-2" />
            </div>
          </div>

          <div class="row g-3 mb-3">
            <div class="col-sm-8">
              <label for="edit-ev-location" class="form-label small fw-medium text-dark mb-1">Địa điểm tổ chức <span class="text-danger">*</span></label>
              <input type="text" name="dia_diem" id="edit-ev-location" required class="form-control form-control-sm py-2" />
            </div>
            <div class="col-sm-4">
              <label for="edit-ev-price" class="form-label small fw-medium text-dark mb-1">Giá vé (VNĐ)</label>
              <input type="number" name="gia_ve" id="edit-ev-price" min="0" step="1000" class="form-control form-control-sm py-2" />
            </div>
          </div>

          <div class="row g-3 mb-3">
            <div class="col-sm-8">
              <label for="edit-ev-image" class="form-label small fw-medium text-dark mb-1">Link ảnh thumbnail</label>
              <input type="url" name="hinh_anh" id="edit-ev-image" class="form-control form-control-sm py-2" />
            </div>
            <div class="col-sm-4">
              <label for="edit-ev-status" class="form-label small fw-medium text-dark mb-1">Trạng thái sự kiện</label>
              <select name="trang_thai" id="edit-ev-status" class="form-select form-select-sm py-2">
                <option value="cong_khai">Công khai</option>
                <option value="nhap">Bản nháp</option>
                <option value="da_huy">Đã hủy</option>
              </select>
            </div>
          </div>

          <div class="mb-4">
            <label for="edit-ev-desc" class="form-label small fw-medium text-dark mb-1">Mô tả chi tiết sự kiện</label>
            <textarea name="mo_ta" id="edit-ev-desc" rows="3" required class="form-control form-control-sm py-2"></textarea>
          </div>

          <div class="d-flex gap-2 pt-2 border-top">
            <button type="button" class="btn btn-outline-secondary flex-grow-1 py-2 small fw-medium" data-bs-dismiss="modal">Hủy</button>
            <button type="submit" class="btn btn-primary flex-grow-1 py-2 small fw-medium">
              <i class="fa-regular fa-floppy-disk me-1"></i> Cập nhật sự kiện
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
