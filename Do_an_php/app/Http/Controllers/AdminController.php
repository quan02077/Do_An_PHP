<?php

namespace App\Http\Controllers;

use App\Models\DangKy;
use App\Models\DanhMuc;
use App\Models\NguoiDung;
use App\Models\SuKien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user() ?? session('user');

        if (!$user || $user->vai_tro !== 'admin') {
            return redirect()->route('Home.index');
        }

        $currentTab = $request->query('tab', 'events');

        $now = now();
        $totalEvents = SuKien::count();
        $upcomingEvents = SuKien::where('trang_thai', 'cong_khai')->where('thoi_gian_bat_dau', '>', $now)->count();
        $totalBookings = DangKy::where('trang_thai', '!=', 'da_huy')->count();
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
            'totalMembers',
            'events',
            'registrations',
            'categories',
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

        $registration = DangKy::findOrFail($id);
        $registration->update($data);

        return redirect()->route('admin.index', ['tab' => 'registrations'])->with('success', 'Đã cập nhật trạng thái đăng ký.');
    }

    /**
     * RESTful API: Quét mã QR hoặc nhập mã vé để Check-in tại sự kiện
     */
    public function checkInTicket(Request $request)
    {
        // 1. Nhận mã vé gửi lên (Ví dụ: QQQ-2026-B8D2F1)
        $data = $request->validate(['ma_ve' => ['required', 'string', 'max:100']]);
        $maVe = trim($data['ma_ve']);

        // 2. Tìm vé trong CSDL
        $ticket = DangKy::with('nguoiDung')->where('ma_ve', $maVe)->first();

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

        // 4. Hợp lệ -> Đổi trạng thái vé sang 'da_tham_gia'
        $ticket->update(['trang_thai' => 'da_tham_gia']);

        // 5. Trả về thông báo thành công dạng JSON
        return response()->json([
            'success' => true,
            'message' => 'Check-in thành công cho: ' . $ticket->nguoiDung->ho_ten
        ]);
    }

    private function requireAdmin()
    {
        $user = Auth::user() ?? session('user');

        return (!$user || $user->vai_tro !== 'admin')
            ? redirect()->route('Home.index')->with('warning', 'Bạn không có quyền thực hiện thao tác quản trị.')
            : null;
    }
}
