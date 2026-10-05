@extends('layouts.app')

@section('title', 'Hồ sơ cá nhân — QQQ')

@section('content')
<div class="container-xl py-4">

    <!-- Tiêu đề trang -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="fs-4 fw-bold text-dark mb-1">Hồ sơ cá nhân</h1>
            <p class="text-secondary small mb-0">Quản lý thông tin tài khoản và mật khẩu của bạn</p>
        </div>
        <a href="{{ route('Home.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
            Quay lại Trang chủ
        </a>
    </div>

    <!-- Thông báo Flash Messages -->
    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4 shadow-sm" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if (session('warning'))
    <div class="alert alert-warning alert-dismissible fade show rounded-3 mb-4 shadow-sm" role="alert">
        {{ session('warning') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if (isset($errors) && $errors->any())
    <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-4 shadow-sm" role="alert">
        <ul class="mb-0 ps-3 small">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="row g-4">
        <!-- Cột trái: Cập nhật thông tin cá nhân -->
        <div class="col-lg-7">
            <div class="bg-white rounded-3 border p-4 shadow-sm">
                <h2 class="fs-5 fw-bold text-dark mb-3 pb-2 border-bottom">Thông tin cá nhân</h2>

                <form method="POST" action="{{ route('Dashboard.updateProfile') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-medium text-dark">Họ và tên <span class="text-danger">*</span></label>
                        <input type="text" name="ho_ten" class="form-control form-control-sm py-2" value="{{ old('ho_ten', $user->ho_ten) }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-medium text-dark">Địa chỉ Email</label>
                        <input type="email" class="form-control form-control-sm py-2 bg-light text-muted" value="{{ $user->email }}" readonly>
                        <div class="form-text text-muted" style="font-size: 12px;">Email tài khoản không thể thay đổi</div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <label class="form-label small fw-medium text-dark">Số điện thoại</label>
                            <input type="tel" name="so_dien_thoai" class="form-control form-control-sm py-2" value="{{ old('so_dien_thoai', $user->so_dien_thoai) }}" placeholder="0912 345 678">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label small fw-medium text-dark">Giới tính</label>
                            <select name="gioi_tinh" class="form-select form-select-sm py-2">
                                <option value="Nam" {{ old('gioi_tinh', $user->gioi_tinh) === 'Nam' ? 'selected' : '' }}>Nam</option>
                                <option value="Nữ" {{ old('gioi_tinh', $user->gioi_tinh) === 'Nữ' ? 'selected' : '' }}>Nữ</option>
                                <option value="Không biết" {{ old('gioi_tinh', $user->gioi_tinh) === 'Không biết' ? 'selected' : '' }}>Không biết</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <label class="form-label small fw-medium text-dark">Ngày sinh</label>
                            <input type="date" name="ngay_sinh" class="form-control form-control-sm py-2" value="{{ old('ngay_sinh', $user->ngay_sinh ? \Carbon\Carbon::parse($user->ngay_sinh)->format('Y-m-d') : '') }}">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label small fw-medium text-dark">Địa chỉ</label>
                            <input type="text" name="dia_chi" class="form-control form-control-sm py-2" value="{{ old('dia_chi', $user->dia_chi) }}" placeholder="Hà Nội, Việt Nam">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-medium text-dark">Tiểu sử</label>
                        <textarea name="tieu_su" rows="3" class="form-control form-control-sm" placeholder="Giới thiệu đôi nét về bản thân...">{{ old('tieu_su', $user->tieu_su) }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-dark btn-sm px-4 py-2 rounded-3 fw-medium">
                        Lưu thay đổi
                    </button>
                </form>
            </div>
        </div>

        <!-- Cột phải: Thông tin tài khoản & Đổi mật khẩu -->
        <div class="col-lg-5">
            <!-- Card tóm tắt tài khoản -->
            <div class="bg-white rounded-3 border p-4 shadow-sm mb-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-dark text-white fw-bold d-flex align-items-center justify-content-center fs-4" style="width: 50px; height: 50px;">
                        {{ mb_strtoupper(mb_substr($user->ho_ten ?? 'U', 0, 1)) }}
                    </div>
                    <div>
                        <div class="fw-bold text-dark fs-6">{{ $user->ho_ten }}</div>
                        <div class="small text-muted">{{ $user->email }}</div>
                        <span class="badge {{ $user->vai_tro === 'admin' ? 'bg-danger' : 'bg-primary-subtle text-primary border border-primary-subtle' }} mt-1">
                            {{ $user->vai_tro === 'admin' ? 'Quản trị viên' : 'Thành viên' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Card Đổi mật khẩu -->
            <div class="bg-white rounded-3 border p-4 shadow-sm">
                <h2 class="fs-5 fw-bold text-dark mb-3 pb-2 border-bottom">Đổi mật khẩu</h2>

                <form method="POST" action="{{ route('Dashboard.changePassword') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-medium text-dark">Mật khẩu hiện tại <span class="text-danger">*</span></label>
                        <input type="password" name="current_password" class="form-control form-control-sm py-2" placeholder="••••••••" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-medium text-dark">Mật khẩu mới <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control form-control-sm py-2" placeholder="Tối thiểu 6 ký tự" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-medium text-dark">Xác nhận mật khẩu mới <span class="text-danger">*</span></label>
                        <input type="password" name="password_confirmation" class="form-control form-control-sm py-2" placeholder="Nhập lại mật khẩu mới" required>
                    </div>

                    <button type="submit" class="btn btn-outline-dark btn-sm px-4 py-2 rounded-3 fw-medium">
                        Đổi mật khẩu
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection