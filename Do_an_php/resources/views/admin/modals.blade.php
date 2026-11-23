<!-- MODAL: THÊM SỰ KIỆN MỚI -->
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
        <form id="add-event-form" method="POST" action="{{ route('admin.event.store') }}" enctype="multipart/form-data">
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
                @foreach($allCategories ?? [] as $cat)
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
              <input type="datetime-local" name="thoi_gian_bat_dau" id="new-ev-start" required min="{{ now()->format('Y-m-d\TH:i') }}" class="form-control form-control-sm py-2" />
            </div>
            <div class="col-sm-6">
              <label for="new-ev-end" class="form-label small fw-medium text-dark mb-1">Thời gian kết thúc <span class="text-danger">*</span></label>
              <input type="datetime-local" name="thoi_gian_ket_thuc" id="new-ev-end" required min="{{ now()->format('Y-m-d\TH:i') }}" class="form-control form-control-sm py-2" />
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
            <label for="new-ev-image" class="form-label small fw-medium text-dark mb-1">Chọn ảnh thumbnail có sẵn</label>
            <select name="hinh_anh" id="new-ev-image" class="form-select form-select-sm py-2" onchange="previewPublicImage('new-ev-image', 'new-ev-image-preview')">
              <option value="">Dùng ảnh mặc định</option>
              @foreach($eventImages ?? [] as $image)
                <option value="{{ $image }}">{{ $image }}</option>
              @endforeach
            </select>
            <img id="new-ev-image-preview" class="d-none mt-2 rounded-3 border object-fit-cover" alt="Xem trước ảnh thumbnail" style="width: 112px; height: 72px;" />
          </div>

          <div class="mb-3">
            <label for="new-ev-image-upload" class="form-label small fw-medium text-dark mb-1">Hoặc tải ảnh mới từ máy tính</label>
            <input type="file" name="hinh_anh_tai_len" id="new-ev-image-upload" accept="image/jpeg,image/png,image/jpg,image/webp" class="form-control form-control-sm py-2" onchange="previewUploadedImage(this, 'new-ev-image-preview')" />
            <div class="form-text">Chấp nhận JPG, PNG, WEBP; tối đa 2MB. Ảnh tải lên sẽ được ưu tiên hơn ảnh đã chọn.</div>
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

<!-- MODAL: CHỈNH SỬA SỰ KIỆN -->
<div class="modal fade" id="edit-event-modal" tabindex="-1" aria-labelledby="editEventModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content rounded-4 border-0 shadow position-relative p-2 p-sm-3">
      <div class="modal-header border-0 pb-0">
        <div>
          <h2 class="fs-5 fw-bold tracking-tight text-dark mb-0" id="editEventModalLabel">Chỉnh sửa sự kiện</h2>
          <p class="small text-muted mb-0">Cập nhật thông tin chi tiết của sự kiện.</p>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-3 p-sm-4">
        <form id="edit-event-form" method="POST" action="#" enctype="multipart/form-data">
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
                @foreach($allCategories ?? [] as $cat)
                  <option value="{{ $cat->id }}">{{ $cat->ten_danh_muc }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-sm-6">
              <label for="edit-ev-capacity" class="form-label small fw-medium text-dark mb-1">Quy mô chỗ ngồi (Số vé tối đa) <span class="text-danger">*</span></label>
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

          <div class="mb-3">
            <label for="edit-ev-image" class="form-label small fw-medium text-dark mb-1">Chọn ảnh thumbnail có sẵn</label>
            <select name="hinh_anh" id="edit-ev-image" class="form-select form-select-sm py-2" onchange="previewPublicImage('edit-ev-image', 'edit-ev-image-preview')">
              <option value="">Dùng ảnh mặc định</option>
              @foreach($eventImages ?? [] as $image)
                <option value="{{ $image }}">{{ $image }}</option>
              @endforeach
            </select>
            <img id="edit-ev-image-preview" class="d-none mt-2 rounded-3 border object-fit-cover" alt="Xem trước ảnh thumbnail" style="width: 112px; height: 72px;" />
          </div>

          <div class="mb-3">
            <label for="edit-ev-image-upload" class="form-label small fw-medium text-dark mb-1">Hoặc tải ảnh mới từ máy tính</label>
            <input type="file" name="hinh_anh_tai_len" id="edit-ev-image-upload" accept="image/jpeg,image/png,image/jpg,image/webp" class="form-control form-control-sm py-2" onchange="previewUploadedImage(this, 'edit-ev-image-preview')" />
            <div class="form-text">Để trống nếu không muốn thay đổi ảnh sự kiện.</div>
          </div>

          <div class="row g-3 mb-3">
            <div class="col-12">
              <label for="edit-ev-status" class="form-label small fw-medium text-dark mb-1">Trạng thái sự kiện</label>
              <select name="trang_thai" id="edit-ev-status" class="form-select form-select-sm py-2">
                <option value="nhap">Bản nháp (Chưa công khai)</option>
                <option value="cong_khai">Công khai (Cho phép xem và đăng ký)</option>
                <option value="da_huy">Đã hủy (Khóa đăng ký vé)</option>
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
              <i class="fa-regular fa-floppy-disk me-1"></i> Lưu thay đổi
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>


<!-- MODAL: THÊM DANH MỤC MỚI -->
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
        <form id="add-category-form" method="POST" action="{{ route('admin.categories.store') }}">
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
              <option value="hoat_dong">Đang hoạt động (Hiển thị công khai)</option>
              <option value="tam_dung">Tạm dừng (Ẩn khỏi bộ lọc)</option>
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

<!-- MODAL: CHỈNH SỬA DANH MỤC -->
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
              <option value="hoat_dong">Đang hoạt động (Hiển thị công khai)</option>
              <option value="tam_dung">Tạm dừng (Ẩn khỏi bộ lọc)</option>
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
