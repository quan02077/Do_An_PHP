@extends('layouts.app')

@section('title', 'Quản trị hệ thống — EventVN')

@section('content')
<div class="container py-4">
    <!-- Header Page -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <h1 class="fs-4 fw-bold tracking-tight text-dark mb-1">Bảng điều khiển Quản trị</h1>
            <p class="text-secondary small mb-0">Quản lý sự kiện, duyệt đăng ký vé và thiết lập danh mục hệ thống.</p>
        </div>
    </div>

    <!-- 1. Stats Overview -->
    @include('admin.tabs.stats')

    <!-- 2. Navigation Tabs & Content Tables -->
    <div class="bg-white rounded-4 border shadow-sm p-3 p-sm-4 p-md-5">
        <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 border-bottom pb-4 mb-4">
            <!-- Admin Tabs -->
            <div class="overflow-x-auto w-100 pb-1 scrollbar-none" style="-webkit-overflow-scrolling: touch;">
                <div class="btn-group p-1 bg-light rounded-pill align-self-start flex-nowrap d-inline-flex" role="group" aria-label="Admin navigation tabs">
                    <button type="button" onclick="switchAdminTab('events')" id="adm-tab-events" class="btn btn-sm rounded-pill fw-semibold btn-dark shadow-xs px-3 py-2 text-nowrap flex-shrink-0">
                        Quản lý sự kiện
                    </button>
                    <button type="button" onclick="switchAdminTab('registrations')" id="adm-tab-registrations" class="btn btn-sm rounded-pill fw-medium text-secondary px-3 py-2 text-nowrap flex-shrink-0">
                        Duyệt đăng ký vé
                    </button>
                    <button type="button" onclick="switchAdminTab('categories')" id="adm-tab-categories" class="btn btn-sm rounded-pill fw-medium text-secondary px-3 py-2 text-nowrap flex-shrink-0">
                        Danh mục sự kiện
                    </button>
                    <button type="button" onclick="switchAdminTab('users')" id="adm-tab-users" class="btn btn-sm rounded-pill fw-medium text-secondary px-3 py-2 text-nowrap flex-shrink-0">
                        Người dùng
                    </button>
                    <button type="button" onclick="switchAdminTab('reviews')" id="adm-tab-reviews" class="btn btn-sm rounded-pill fw-medium text-secondary px-3 py-2 text-nowrap flex-shrink-0">
                        Đánh giá
                    </button>
                </div>
            </div>

            <!-- Action Buttons -->
            <div id="admin-action-btn-container" class="d-flex align-items-center gap-2 flex-wrap flex-shrink-0">
                <button type="button" onclick="openAddCategoryModal()" class="btn btn-sm btn-outline-secondary rounded-3 px-3 py-2 fw-medium small d-flex align-items-center gap-1.5">
                    <i class="fa-solid fa-plus me-1"></i>
                    <span>Thêm danh mục</span>
                </button>
                <button type="button" onclick="openAddEventModal()" class="btn btn-sm btn-dark rounded-3 px-3 py-2 fw-medium small d-flex align-items-center gap-1.5 shadow-xs">
                    <i class="fa-solid fa-plus me-1"></i>
                    <span>Thêm sự kiện mới</span>
                </button>
            </div>
        </div>

        <!-- Tab 1: Quản lý sự kiện -->
        @include('admin.tabs.events')

        <!-- Tab 2: Duyệt đăng ký vé -->
        @include('admin.tabs.registrations')

        <!-- Tab 3: Danh mục sự kiện -->
        @include('admin.tabs.categories')

        <!-- Tab 4: Quản lý người dùng -->
        @include('admin.tabs.users')

        <!-- Tab 5: Quản lý đánh giá -->
        @include('admin.tabs.reviews')
    </div>
</div>

<!-- Modals (Quản lý Sự kiện & Danh mục) -->
@include('admin.modals')

@endsection

@push('scripts')
<script>
    function switchAdminTab(tab) {
        const tabs = ["events", "registrations", "categories", "users", "reviews"];
        tabs.forEach((t) => {
            const btn = document.getElementById("adm-tab-" + t);
            const view = document.getElementById("adm-view-" + t);
            if (t === tab) {
                btn.className = "btn btn-sm rounded-pill fw-semibold btn-dark shadow-xs px-3 py-2 text-nowrap flex-shrink-0";
                view.classList.remove("d-none");
                if (tab === "registrations" && typeof khoiTaoScanner === "function") {
                    setTimeout(khoiTaoScanner, 100);
                }
            } else {
                btn.className = "btn btn-sm rounded-pill fw-medium text-secondary px-3 py-2 text-nowrap flex-shrink-0";
                view.classList.add("d-none");
            }
        });
    }

    const eventBaseUrl = "{{ url('admin/event') }}";
    const categoryBaseUrl = "{{ url('admin/categories') }}";
    const publicEventImagesUrl = "{{ asset('uploads/su_kien') }}";

    function showModal(id) {
        bootstrap.Modal.getOrCreateInstance(document.getElementById(id)).show();
    }

    function openAddEventModal() {
        showModal('add-event-modal');
    }

    function openAddCategoryModal() {
        showModal('add-category-modal');
    }

    function showImagePreview(previewId, source) {
        const preview = document.getElementById(previewId);
        if (!source) {
            preview.removeAttribute('src');
            preview.classList.add('d-none');
            return;
        }

        preview.src = source;
        preview.classList.remove('d-none');
    }

    function previewPublicImage(selectId, previewId) {
        const filename = document.getElementById(selectId).value;
        showImagePreview(previewId, filename ? `${publicEventImagesUrl}/${filename}` : '');
    }

    function previewUploadedImage(input, previewId) {
        const file = input.files[0];
        showImagePreview(previewId, file ? URL.createObjectURL(file) : '');
    }

    function openEditEventModal(button) {
        const data = button.dataset;
        document.getElementById('edit-event-form').action = `${eventBaseUrl}/${data.id}`;
        document.getElementById('edit-ev-id').value = data.id;
        document.getElementById('edit-ev-title').value = data.title || '';
        document.getElementById('edit-ev-category').value = data.category || '';
        document.getElementById('edit-ev-capacity').value = data.capacity || '';
        document.getElementById('edit-ev-start').value = data.start || '';
        document.getElementById('edit-ev-end').value = data.end || '';
        document.getElementById('edit-ev-location').value = data.location || '';
        document.getElementById('edit-ev-price').value = data.price || 0;
        const imageSelect = document.getElementById('edit-ev-image');
        imageSelect.value = [...imageSelect.options].some(option => option.value === data.image) ? data.image : '';
        previewPublicImage('edit-ev-image', 'edit-ev-image-preview');
        document.getElementById('edit-ev-status').value = data.status || 'cong_khai';
        document.getElementById('edit-ev-desc').value = data.description || '';
        showModal('edit-event-modal');
    }

    function openEditCategoryModal(button) {
        const data = button.dataset;
        document.getElementById('edit-category-form').action = `${categoryBaseUrl}/${data.id}`;
        document.getElementById('edit-cat-id').value = data.id;
        document.getElementById('edit-cat-name').value = data.name || '';
        document.getElementById('edit-cat-desc').value = data.description || '';
        document.getElementById('edit-cat-status').value = data.status || 'hoat_dong';
        showModal('edit-category-modal');
    }

    // Validation thời gian sự kiện phía client
    const newEvStart = document.getElementById('new-ev-start');
    const newEvEnd = document.getElementById('new-ev-end');
    const addEventForm = document.getElementById('add-event-form');

    if (newEvStart && newEvEnd) {
        newEvStart.addEventListener('change', function () {
            if (this.value) {
                newEvEnd.min = this.value;
                if (newEvEnd.value && newEvEnd.value < this.value) {
                    newEvEnd.value = this.value;
                }
            }
        });
    }

    if (addEventForm) {
        addEventForm.addEventListener('submit', function (e) {
            const now = new Date();
            const todayStart = new Date(now.getFullYear(), now.getMonth(), now.getDate(), 0, 0, 0);

            if (newEvStart && newEvStart.value) {
                const startDate = new Date(newEvStart.value);
                if (startDate < todayStart) {
                    e.preventDefault();
                    alert('Thời gian bắt đầu không được nhỏ hơn ngày hiện tại.');
                    newEvStart.focus();
                    return false;
                }
            }

            if (newEvEnd && newEvEnd.value) {
                const endDate = new Date(newEvEnd.value);
                if (endDate < todayStart) {
                    e.preventDefault();
                    alert('Thời gian kết thúc không được nhỏ hơn ngày hiện tại.');
                    newEvEnd.focus();
                    return false;
                }
            }

            if (newEvStart && newEvEnd && newEvStart.value && newEvEnd.value) {
                const startDate = new Date(newEvStart.value);
                const endDate = new Date(newEvEnd.value);
                if (endDate < startDate) {
                    e.preventDefault();
                    alert('Thời gian kết thúc không được nhỏ hơn ngày bắt đầu.');
                    newEvEnd.focus();
                    return false;
                }
            }
        });
    }

    const editEvStart = document.getElementById('edit-ev-start');
    const editEvEnd = document.getElementById('edit-ev-end');
    const editEventForm = document.getElementById('edit-event-form');

    if (editEvStart && editEvEnd) {
        editEvStart.addEventListener('change', function () {
            if (this.value) {
                editEvEnd.min = this.value;
                if (editEvEnd.value && editEvEnd.value < this.value) {
                    editEvEnd.value = this.value;
                }
            }
        });
    }

    if (editEventForm) {
        editEventForm.addEventListener('submit', function (e) {
            if (editEvStart && editEvEnd && editEvStart.value && editEvEnd.value) {
                const startDate = new Date(editEvStart.value);
                const endDate = new Date(editEvEnd.value);
                if (endDate < startDate) {
                    e.preventDefault();
                    alert('Thời gian kết thúc không được nhỏ hơn ngày bắt đầu.');
                    editEvEnd.focus();
                    return false;
                }
            }
        });
    }

    switchAdminTab("{{ $currentTab }}");
</script>
@endpush
