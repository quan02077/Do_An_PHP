<?php

namespace App\Http\Controllers;

use App\Events\TicketCheckedIn;
use App\Events\TicketCountsChanged;
use App\Http\Requests\UpdateUserRoleRequest;
use App\Http\Requests\UpdateUserStatusRequest;
use App\Models\DangKy;
use App\Models\DanhGia;
use App\Models\DanhMuc;
use App\Models\NguoiDung;
use App\Models\SuKien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user || $user->vai_tro !== 'admin') {
            return redirect()->route('Home.index');
        }

        $currentTab = $request->query('tab', 'events');

        $now = now();
        $totalEvents = SuKien::count();
        $upcomingEvents = SuKien::where('trang_thai', 'cong_khai')->where('thoi_gian_bat_dau', '>', $now)->count();
        $totalBookings = DangKy::where('trang_thai', '!=', 'da_huy')->count();
        $checkedInTotal = DangKy::where('trang_thai', 'da_tham_gia')->count();
        $totalMembers = NguoiDung::where('vai_tro', 'user')->count();
        $allCategories = DanhMuc::withCount('suKiens')->orderBy('ten_danh_muc')->get();
        $categories = DanhMuc::withCount('suKiens')->orderBy('id')->paginate(10, ['*'], 'categories_page')->withQueryString();
        $eventsQuery = SuKien::with(['danhMuc', 'dangKys']);
        $eventSearch = trim((string) $request->query('event_search', ''));
        if ($eventSearch !== '') {
            $eventsQuery->where(function ($query) use ($eventSearch) {
                $query->where('ten_su_kien', 'like', '%'.$eventSearch.'%')
                    ->orWhere('mo_ta', 'like', '%'.$eventSearch.'%')
                    ->orWhere('dia_diem', 'like', '%'.$eventSearch.'%')
                    ->orWhereHas('danhMuc', fn ($category) => $category->where('ten_danh_muc', 'like', '%'.$eventSearch.'%'));
            });
        }
        $events = $eventsQuery->orderByDesc('id')->paginate(10, ['*'], 'events_page')->withQueryString();
        $registrations = DangKy::with(['nguoiDung', 'suKien'])->orderByDesc('id')
            ->paginate(10, ['*'], 'registrations_page')->withQueryString();

        // Nạp danh sách người dùng cho tab quản lý người dùng (tìm kiếm, lọc vai trò & trạng thái)
        $usersQuery = NguoiDung::query()->withCount('dangKys');
        $userSearch = trim((string) $request->query('user_search', ''));
        if ($userSearch !== '') {
            $usersQuery->where(function ($query) use ($userSearch) {
                $query->where('ho_ten', 'like', '%' . $userSearch . '%')
                    ->orWhere('email', 'like', '%' . $userSearch . '%')
                    ->orWhere('so_dien_thoai', 'like', '%' . $userSearch . '%');
            });
        }
        $userRole = trim((string) $request->query('user_role', ''));
        if (in_array($userRole, ['user', 'admin'], true)) {
            $usersQuery->where('vai_tro', $userRole);
        }
        $userStatus = trim((string) $request->query('user_status', ''));
        if (in_array($userStatus, ['hoat_dong', 'bi_khoa'], true)) {
            $usersQuery->where('trang_thai', $userStatus);
        }
        $users = $usersQuery->orderByDesc('id')->paginate(10, ['*'], 'users_page')->withQueryString();

        // Nạp danh sách đánh giá cho tab quản lý đánh giá (lọc theo sự kiện và số sao)
        $allEventsForFilter = SuKien::query()->orderByDesc('id')->get(['id', 'ten_su_kien']);
        $reviewsQuery = DanhGia::with(['nguoiDung', 'suKien']);
        $reviewEventId = $request->query('review_event');
        if (!empty($reviewEventId) && ctype_digit((string) $reviewEventId)) {
            $reviewsQuery->where('su_kien_id', (int) $reviewEventId);
        }
        $reviewRating = $request->query('review_rating');
        if (!empty($reviewRating) && in_array((int) $reviewRating, [1, 2, 3, 4, 5], true)) {
            $reviewsQuery->where('so_sao', (int) $reviewRating);
        }
        $reviews = $reviewsQuery->orderByDesc('id')->paginate(10, ['*'], 'reviews_page')->withQueryString();
        $uploadDir = public_path('uploads/su_kien');
        File::ensureDirectoryExists($uploadDir);
        $eventImages = collect(File::files($uploadDir))
            ->filter(fn ($file) => in_array(strtolower($file->getExtension()), ['jpg', 'jpeg', 'png', 'webp']))
            ->map(fn ($file) => $file->getFilename())
            ->values();

        // Chuẩn bị dữ liệu cho Biểu đồ Chart.js
        $categoryChart = [
            'labels' => $allCategories->pluck('ten_danh_muc')->values(),
            'counts' => $allCategories->pluck('su_kiens_count')->values(),
        ];

        $topEvents = SuKien::withCount(['dangKys' => function ($query) {
            $query->where('trang_thai', '!=', 'da_huy');
        }])
        ->orderByDesc('dang_kys_count')
        ->take(5)
        ->get();

        $eventBookingChart = [
            'labels' => $topEvents->pluck('ten_su_kien')->map(fn($name) => \Illuminate\Support\Str::limit($name, 22))->values(),
            'counts' => $topEvents->pluck('dang_kys_count')->values(),
        ];

        return view('admin.admin', compact(
            'user',
            'currentTab',
            'totalEvents',
            'upcomingEvents',
            'totalBookings',
            'checkedInTotal',
            'totalMembers',
            'events',
            'registrations',
            'categories',
            'users',
            'reviews',
            'allEventsForFilter',
            'allCategories',
            'eventImages',
            'categoryChart',
            'eventBookingChart'
        ));
    }

    public function storeCategory(Request $request)
    {
        if ($response = $this->requireAdmin()) {
            return $response;
        }

        $data = $request->validate([
            'ten_danh_muc' => ['required', 'string', 'max:100', 'unique:danh_muc,ten_danh_muc'],
            'mo_ta' => ['nullable', 'string', 'max:500'],
            'trang_thai' => ['required', 'in:hoat_dong,tam_dung'],
        ]);

        DanhMuc::create($data);

        return redirect()->route('admin.index', ['tab' => 'categories'])->with('success', 'Đã thêm danh mục.');
    }

    public function updateCategory(Request $request, $id)
    {
        if ($response = $this->requireAdmin()) {
            return $response;
        }

        $category = DanhMuc::findOrFail($id);
        $data = $request->validate([
            'ten_danh_muc' => ['required', 'string', 'max:100', 'unique:danh_muc,ten_danh_muc,' . $category->id],
            'mo_ta' => ['nullable', 'string', 'max:500'],
            'trang_thai' => ['required', 'in:hoat_dong,tam_dung'],
        ]);

        $category->update($data);

        return redirect()->route('admin.index', ['tab' => 'categories'])->with('success', 'Đã cập nhật danh mục.');
    }

    public function destroyCategory($id)
    {
        if ($response = $this->requireAdmin()) {
            return $response;
        }

        $category = DanhMuc::findOrFail($id);
        if ($category->suKiens()->exists()) {
            return redirect()->route('admin.index', ['tab' => 'categories'])
                ->with('warning', 'Không thể xóa danh mục đang có sự kiện. Hãy chuyển hoặc xóa các sự kiện trước.');
        }

        $category->delete();

        return redirect()->route('admin.index', ['tab' => 'categories'])->with('success', 'Đã xóa danh mục.');
    }

    public function updateRegistrationStatus(Request $request, $id)
    {
        if ($response = $this->requireAdmin()) {
            return $response;
        }

        $data = $request->validate([
            'trang_thai' => ['required', 'in:cho_duyet,da_xac_nhan,da_tham_gia,da_huy'],
        ]);

        return DB::transaction(function () use ($id, $data) {
            $registration = DangKy::findOrFail($id);
            $event = SuKien::whereKey($registration->su_kien_id)->lockForUpdate()->firstOrFail();
            $registration->refresh();

            if (!in_array($data['trang_thai'], self::registrationStatusTransitions()[$registration->trang_thai] ?? [], true)) {
                return back()->with('warning', 'Không thể chuyển vé từ trạng thái hiện tại sang trạng thái đã chọn.');
            }

            if ($registration->trang_thai === 'da_huy') {
                if ($event->trang_thai !== 'cong_khai' || $event->thoi_gian_bat_dau <= now()) {
                    return back()->with('warning', 'Sự kiện không còn nhận đăng ký; không thể khôi phục vé.');
                }
                if ($event->so_luong_toi_da > 0 && $event->so_luong_da_dang_ky >= $event->so_luong_toi_da) {
                    return back()->with('warning', 'Sự kiện đã hết chỗ; không thể khôi phục vé.');
                }
            }

            $registration->update($data);
            event(TicketCountsChanged::forEvent($event));

            // Check-in thủ công cũng phải cập nhật bộ đếm/bảng tin realtime như khi quét QR.
            if ($data['trang_thai'] === 'da_tham_gia') {
                $checkedInTotal = DangKy::where('trang_thai', 'da_tham_gia')->count();
                event(new TicketCheckedIn($registration->id, $event->id, $registration->nguoiDung->ho_ten, $checkedInTotal));
            }

            return redirect()->route('admin.index', ['tab' => 'registrations'])
                ->with('success', 'Đã cập nhật trạng thái đăng ký.');
        });
    }

    /** Các trạng thái vé được phép chuyển tiếp. */
    private static function registrationStatusTransitions(): array
    {
        return [
            'cho_duyet' => ['da_xac_nhan', 'da_huy'],
            'da_xac_nhan' => ['da_tham_gia', 'da_huy'],
            'da_huy' => ['da_xac_nhan'],
            'da_tham_gia' => [],
        ];
    }

    /**
     * Quét mã QR hoặc nhập mã vé để check-in tại sự kiện.
     */
    public function checkInTicket(Request $request)
    {
        // 1. Nhận mã vé gửi lên.
        $data = $request->validate(['ma_ve' => ['required', 'string', 'max:100']]);
        $maVe = trim($data['ma_ve']);

        return DB::transaction(function () use ($maVe) {
        // Khóa vé để hai lần quét gần nhau không thể cùng thành công.
        $ticket = DangKy::with(['nguoiDung', 'suKien'])->where('ma_ve', $maVe)->lockForUpdate()->first();

        // 3. Kiểm tra các trường hợp
        if (!$ticket) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy vé này!']);
        }
        if ($ticket->trang_thai === 'da_tham_gia') {
            return response()->json(['success' => false, 'message' => 'Cảnh báo: Vé này đã được check-in trước đó rồi!']);
        }
        if ($ticket->trang_thai === 'da_huy') {
            return response()->json(['success' => false, 'message' => 'Vé này đã bị hủy, không thể check-in!']);
        }
        if ($ticket->trang_thai !== 'da_xac_nhan' || $ticket->suKien?->trang_thai !== 'cong_khai') {
            return response()->json(['success' => false, 'message' => 'Vé chưa được xác nhận hoặc sự kiện không còn hoạt động.']);
        }

        // 4. Hợp lệ -> Đổi trạng thái vé sang 'da_tham_gia'
        $ticket->update(['trang_thai' => 'da_tham_gia']);
        $event = $ticket->suKien;
        event(TicketCountsChanged::forEvent($event));
        $checkedInTotal = DangKy::where('trang_thai', 'da_tham_gia')->count();
        event(new TicketCheckedIn($ticket->id, $event->id, $ticket->nguoiDung->ho_ten, $checkedInTotal));

        // 5. Trả về thông báo thành công dạng JSON
        return response()->json([
            'success' => true,
            'message' => 'Check-in thành công cho: ' . $ticket->nguoiDung->ho_ten,
            'ticket' => ['id' => $ticket->id],
            'checkedInTotal' => $checkedInTotal,
        ]);
        });
    }

    /**
     * Khóa hoặc mở khóa tài khoản người dùng (giữ nguyên các vé đã đăng ký).
     */
    public function updateUserStatus(UpdateUserStatusRequest $request, $id)
    {
        if ($response = $this->requireAdmin()) {
            return $response;
        }

        $target = NguoiDung::findOrFail($id);
        $newStatus = $request->validated('trang_thai');

        if ($target->id === Auth::id() && $newStatus === 'bi_khoa') {
            return back()->with('warning', 'Bạn không thể tự khóa tài khoản của chính mình.');
        }

        if ($target->vai_tro === 'admin' && $target->trang_thai === 'hoat_dong' && $newStatus === 'bi_khoa') {
            $otherActiveAdmins = NguoiDung::where('vai_tro', 'admin')
                ->where('trang_thai', 'hoat_dong')
                ->where('id', '!=', $target->id)
                ->count();

            if ($otherActiveAdmins === 0) {
                return back()->with('warning', 'Không thể khóa quản trị viên cuối cùng đang hoạt động.');
            }
        }

        $target->update(['trang_thai' => $newStatus]);

        $message = $newStatus === 'bi_khoa'
            ? 'Đã khóa tài khoản ' . $target->ho_ten . '.'
            : 'Đã mở khóa tài khoản ' . $target->ho_ten . '.';

        return back()->with('success', $message);
    }

    /**
     * Đổi vai trò người dùng giữa thành viên (user) và quản trị viên (admin).
     */
    public function updateUserRole(UpdateUserRoleRequest $request, $id)
    {
        if ($response = $this->requireAdmin()) {
            return $response;
        }

        $target = NguoiDung::findOrFail($id);
        $newRole = $request->validated('vai_tro');

        if ($target->id === Auth::id() && $newRole !== 'admin') {
            return back()->with('warning', 'Bạn không thể tự hạ quyền tài khoản của chính mình.');
        }

        if ($target->vai_tro === 'admin' && $newRole === 'user') {
            $otherActiveAdmins = NguoiDung::where('vai_tro', 'admin')
                ->where('trang_thai', 'hoat_dong')
                ->where('id', '!=', $target->id)
                ->count();

            if ($otherActiveAdmins === 0) {
                return back()->with('warning', 'Không thể hạ quyền quản trị viên cuối cùng đang hoạt động.');
            }
        }

        $target->update(['vai_tro' => $newRole]);

        $roleLabel = $newRole === 'admin' ? 'Quản trị viên' : 'Thành viên';

        return back()->with('success', 'Đã chuyển vai trò của ' . $target->ho_ten . ' thành ' . $roleLabel . '.');
    }

    /**
     * Ẩn hoặc hiện lại đánh giá sự kiện (kiểm duyệt nội dung vi phạm).
     */
    public function toggleReviewVisibility($id)
    {
        if ($response = $this->requireAdmin()) {
            return $response;
        }

        $review = DanhGia::findOrFail($id);
        $newStatus = $review->trang_thai === 'hien' ? 'an' : 'hien';
        $review->update(['trang_thai' => $newStatus]);

        return back()->with('success', $newStatus === 'an' ? 'Đã ẩn đánh giá.' : 'Đã hiển thị lại đánh giá.');
    }

    private function requireAdmin()
    {
        $user = Auth::user();

        return (!$user || $user->vai_tro !== 'admin')
            ? redirect()->route('Home.index')->with('warning', 'Bạn không có quyền thực hiện thao tác quản trị.')
            : null;
    }
}
