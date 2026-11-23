<div id="adm-view-users" class="d-none">
    <form method="GET" action="{{ route('admin.index') }}" class="row g-2 mb-3">
        <input type="hidden" name="tab" value="users">
        <div class="col-md-5">
            <input type="search" name="user_search" value="{{ request('user_search') }}" class="form-control form-control-sm py-2" placeholder="Tìm theo họ tên, email, số điện thoại..." aria-label="Tìm người dùng">
        </div>
        <div class="col-sm-6 col-md-3">
            <select name="user_role" class="form-select form-select-sm py-2" aria-label="Lọc theo vai trò">
                <option value="">Tất cả vai trò</option>
                <option value="user" {{ request('user_role') === 'user' ? 'selected' : '' }}>Thành viên</option>
                <option value="admin" {{ request('user_role') === 'admin' ? 'selected' : '' }}>Quản trị viên</option>
            </select>
        </div>
        <div class="col-sm-6 col-md-2">
            <select name="user_status" class="form-select form-select-sm py-2" aria-label="Lọc theo trạng thái">
                <option value="">Tất cả trạng thái</option>
                <option value="hoat_dong" {{ request('user_status') === 'hoat_dong' ? 'selected' : '' }}>Hoạt động</option>
                <option value="bi_khoa" {{ request('user_status') === 'bi_khoa' ? 'selected' : '' }}>Bị khóa</option>
            </select>
        </div>
        <div class="col-md-2 d-flex gap-1">
            <button class="btn btn-sm btn-dark flex-grow-1 py-2" type="submit">Lọc</button>
            @if(request()->filled('user_search') || request()->filled('user_role') || request()->filled('user_status'))
                <a href="{{ route('admin.index', ['tab' => 'users']) }}" class="btn btn-sm btn-outline-secondary py-2" title="Xóa bộ lọc">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            @endif
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 small">
            <thead class="table-light text-secondary text-uppercase small">
                <tr>
                    <th class="py-3 px-3">Họ tên</th>
                    <th class="py-3 px-3">Email</th>
                    <th class="py-3 px-3">Số điện thoại</th>
                    <th class="py-3 px-3">Vai trò</th>
                    <th class="py-3 px-3">Trạng thái</th>
                    <th class="py-3 px-3 text-center">Số vé</th>
                    <th class="py-3 px-3">Ngày tạo</th>
                    <th class="py-3 px-3 text-end">Thao tác</th>
                </tr>
            </thead>
            <tbody id="admin-users-tbody">
                @forelse($users ?? [] as $u)
                @php
                    $isSelf = isset($user) && $u->id === $user->id;
                    $isLocked = $u->trang_thai === 'bi_khoa';
                    $isAdmin = $u->vai_tro === 'admin';
                @endphp
                <tr>
                    <td class="py-3 px-3 fw-semibold text-dark">
                        {{ $u->ho_ten }}
                        @if($isSelf)
                            <span class="badge rounded-pill bg-light text-secondary border ms-1">Bạn</span>
                        @endif
                    </td>
                    <td class="py-3 px-3 text-secondary">{{ $u->email }}</td>
                    <td class="py-3 px-3 text-secondary">{{ $u->so_dien_thoai ?: '—' }}</td>
                    <td class="py-3 px-3">
                        @if($isAdmin)
                            <span class="badge rounded-pill bg-primary-subtle text-primary border border-primary-subtle">Quản trị</span>
                        @else
                            <span class="badge rounded-pill bg-light text-secondary border">Thành viên</span>
                        @endif
                    </td>
                    <td class="py-3 px-3">
                        @if($isLocked)
                            <span class="badge rounded-pill bg-danger-subtle text-danger border border-danger-subtle">Bị khóa</span>
                        @else
                            <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle">Hoạt động</span>
                        @endif
                    </td>
                    <td class="py-3 px-3 text-center fw-medium text-dark">{{ $u->dang_kys_count ?? 0 }}</td>
                    <td class="py-3 px-3 text-secondary">{{ optional($u->created_at)->format('d/m/Y') ?? '—' }}</td>
                    <td class="py-3 px-3 text-end">
                        <div class="d-inline-flex align-items-center justify-content-end gap-1 flex-wrap">
                            {{-- Khóa / Mở khóa tài khoản --}}
                            <form method="POST" action="{{ route('admin.users.status', $u->id) }}" class="d-inline"
                                onsubmit="return confirm('{{ $isLocked ? 'Bạn có chắc chắn muốn mở khóa tài khoản này?' : 'Bạn có chắc chắn muốn khóa tài khoản này?' }}')">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="trang_thai" value="{{ $isLocked ? 'hoat_dong' : 'bi_khoa' }}">
                                <button type="submit"
                                    class="btn btn-sm {{ $isLocked ? 'btn-outline-success' : 'btn-outline-danger' }} py-1 px-2 rounded-2"
                                    {{ $isSelf ? 'disabled' : '' }}
                                    title="{{ $isLocked ? 'Mở khóa tài khoản' : 'Khóa tài khoản' }}">
                                    <i class="fa-solid {{ $isLocked ? 'fa-lock-open' : 'fa-lock' }} me-1"></i>{{ $isLocked ? 'Mở khóa' : 'Khóa' }}
                                </button>
                            </form>

                            {{-- Đổi vai trò --}}
                            <form method="POST" action="{{ route('admin.users.role', $u->id) }}" class="d-inline"
                                onsubmit="return confirm('{{ $isAdmin ? 'Bạn có chắc chắn muốn hạ quyền quản trị viên này thành Thành viên?' : 'Bạn có chắc chắn muốn cấp quyền Quản trị viên cho tài khoản này?' }}')">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="vai_tro" value="{{ $isAdmin ? 'user' : 'admin' }}">
                                <button type="submit"
                                    class="btn btn-sm btn-outline-secondary py-1 px-2 rounded-2"
                                    {{ $isSelf ? 'disabled' : '' }}
                                    title="{{ $isAdmin ? 'Chuyển thành Thành viên' : 'Cấp quyền Quản trị' }}">
                                    <i class="fa-solid fa-user-shield me-1"></i>{{ $isAdmin ? 'Hạ thành User' : 'Cấp Admin' }}
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-4 text-muted">
                        Không tìm thấy người dùng nào phù hợp.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(isset($users) && $users->hasPages())
        <div class="mt-3">{{ $users->links('pagination::bootstrap-5') }}</div>
    @endif
</div>
