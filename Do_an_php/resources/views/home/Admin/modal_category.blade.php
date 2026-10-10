<!-- ─── MODAL 1: THÊM DANH MỤC MỚI ──────────────────────────────────────── -->
<div class="modal fade" id="add-category-modal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true" style="z-index: 1060;">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 border-0 shadow position-relative p-2 p-sm-3">
      <div class="modal-header border-0 pb-0">
        <div>
          <h2 class="fs-5 fw-bold tracking-tight text-dark mb-1" id="addCategoryModalLabel">Thêm danh mục mới</h2>
          <p class="small text-muted mb-0">Tạo nhóm danh mục mới để phân loại sự kiện hiệu quả hơn.</p>
        </div>
        <button type="button" class="btn-close align-self-start" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-3 p-sm-4">
        <form id="add-category-form" method="POST" action="#">
          @csrf
          <div class="mb-3">
            <label for="new-cat-name" class="form-label small fw-medium text-dark mb-1">Tên danh mục <span class="text-danger">*</span></label>
            <input type="text" name="ten_danh_muc" id="new-cat-name" required class="form-control form-control-sm py-2" placeholder="Ví dụ: Triển lãm, Ẩm thực, Công nghệ..." />
          </div>

          <div class="mb-3">
            <label for="new-cat-desc" class="form-label small fw-medium text-dark mb-1">Mô tả danh mục</label>
            <textarea name="mo_ta" id="new-cat-desc" rows="3" class="form-control form-control-sm py-2" placeholder="Mô tả tóm tắt về loại hình sự kiện này..."></textarea>
          </div>

          <div class="mb-4">
            <label for="new-cat-status" class="form-label small fw-medium text-dark mb-1">Trạng thái hiển thị</label>
            <select name="trang_thai" id="new-cat-status" class="form-select form-select-sm py-2">
              <option value="1">Đang hoạt động (Hiển thị công khai)</option>
              <option value="0">Tạm dừng (Ẩn khỏi bộ lọc)</option>
            </select>
          </div>

          <div class="d-flex gap-2 pt-2 border-top">
            <button type="button" class="btn btn-outline-secondary flex-grow-1 py-2 small fw-medium" data-bs-dismiss="modal">Hủy</button>
            <button type="submit" class="btn btn-dark flex-grow-1 py-2 small fw-medium">
              <i class="fa-solid fa-plus me-1"></i> Lưu danh mục
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- ─── MODAL 2: CHỈNH SỬA DANH MỤC ────────────────────────────────────── -->
<div class="modal fade" id="edit-category-modal" tabindex="-1" aria-labelledby="editCategoryModalLabel" aria-hidden="true" style="z-index: 1060;">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 border-0 shadow position-relative p-2 p-sm-3">
      <div class="modal-header border-0 pb-0">
        <div>
          <h2 class="fs-5 fw-bold tracking-tight text-dark mb-1" id="editCategoryModalLabel">Chỉnh sửa danh mục</h2>
          <p class="small text-muted mb-0">Cập nhật thông tin chi tiết của danh mục sự kiện.</p>
        </div>
        <button type="button" class="btn-close align-self-start" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-3 p-sm-4">
        <form id="edit-category-form" method="POST" action="#">
          @csrf
          @method('PUT')
          <input type="hidden" id="edit-cat-id" name="id" />

          <div class="mb-3">
            <label for="edit-cat-name" class="form-label small fw-medium text-dark mb-1">Tên danh mục <span class="text-danger">*</span></label>
            <input type="text" name="ten_danh_muc" id="edit-cat-name" required class="form-control form-control-sm py-2" />
          </div>

          <div class="mb-3">
            <label for="edit-cat-desc" class="form-label small fw-medium text-dark mb-1">Mô tả danh mục</label>
            <textarea name="mo_ta" id="edit-cat-desc" rows="3" class="form-control form-control-sm py-2"></textarea>
          </div>

          <div class="mb-4">
            <label for="edit-cat-status" class="form-label small fw-medium text-dark mb-1">Trạng thái hiển thị</label>
            <select name="trang_thai" id="edit-cat-status" class="form-select form-select-sm py-2">
              <option value="1">Đang hoạt động (Hiển thị công khai)</option>
              <option value="0">Tạm dừng (Ẩn khỏi bộ lọc)</option>
            </select>
          </div>

          <div class="d-flex gap-2 pt-2 border-top">
            <button type="button" class="btn btn-outline-secondary flex-grow-1 py-2 small fw-medium" data-bs-dismiss="modal">Hủy</button>
            <button type="submit" class="btn btn-primary flex-grow-1 py-2 small fw-medium">
              <i class="fa-regular fa-floppy-disk me-1"></i> Cập nhật danh mục
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
