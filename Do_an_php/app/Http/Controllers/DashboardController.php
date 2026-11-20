<?php

namespace App\Http\Controllers;

use App\Events\TicketCountsChanged;
use App\Models\DangKy;
use App\Models\YeuThich;
use App\Models\NguoiDung;
use App\Models\SuKien;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\ChangePasswordRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class DashboardController extends Controller
{
    /**
     * Lấy người dùng hiện tại từ Laravel Auth.
     */
    protected function getCurrentUser()
    {
        return Auth::user();
    }

    /**
     * Trang Vé của tôi: Hiển thị danh sách vé trong CSDL của tài khoản người dùng
     */
    public function myTicket(Request $request)
    {
        $user = $this->getCurrentUser();

        // Nếu là Admin thì không có vé tham gia, chuyển hướng về trang chủ
        if ($user && $user->vai_tro === 'admin') {
            return redirect()->route('Home.index');
        }

        $tickets = collect();
        if ($user) {
            $tickets = DangKy::with(['suKien.danhMuc'])
                ->where('nguoi_dung_id', $user->id)
                ->orderBy('id', 'desc')
                ->get();
        }

        return view('user.myTicket', compact('user', 'tickets'));
    }

    /**
     * Hủy vé: Cập nhật trạng thái 'da_huy' trong bảng dang_ky theo đúng người dùng
     */
    public function cancelTicket(Request $request, $id)
    {
        $user = $this->getCurrentUser();
        if (!$user) {
            return redirect()->route('Auth.index')->with('warning', 'Vui lòng đăng nhập để thực hiện.');
        }

        $ticket = DangKy::where('id', $id)
            ->where('nguoi_dung_id', $user->id)
            ->first();

        if (!$ticket) {
            return back()->with('warning', 'Không tìm thấy vé hoặc bạn không có quyền hủy vé này.');
        }

        if ($ticket->trang_thai === 'da_huy') {
            return back()->with('warning', 'Vé này đã được hủy trước đó.');
        }

        if ($ticket->trang_thai === 'da_tham_gia') {
            return back()->with('warning', 'Vé đã được check-in nên không thể hủy.');
        }

        if ($ticket->suKien && $ticket->suKien->thoi_gian_bat_dau <= now()) {
            return back()->with('warning', 'Không thể hủy vé khi sự kiện đã bắt đầu.');
        }

        $ticket->trang_thai = 'da_huy';
        $ticket->save();
        event(TicketCountsChanged::forEvent($ticket->suKien));

        return back()->with('success', 'Đã hủy vé thành công!');
    }

    /**
     * Trang Yêu thích: Hiển thị các sự kiện đã lưu trong CSDL của tài khoản người dùng
     */
    public function favorite(Request $request)
    {
        $user = $this->getCurrentUser();

        // Nếu là Admin thì chuyển hướng về trang chủ
        if ($user && $user->vai_tro === 'admin') {
            return redirect()->route('Home.index');
        }

        $favorites = collect();
        if ($user) {
            $favorites = YeuThich::with(['suKien.danhMuc'])
                ->where('nguoi_dung_id', $user->id)
                ->orderBy('id', 'desc')
                ->get();
        }

        return view('user.favorite', compact('user', 'favorites'));
    }

    /**
     * Thêm / Bỏ yêu thích (Toggle): Thao tác bấm nút trái tim ở Trang chủ hoặc Chi tiết sự kiện
     */
    public function toggleFavorite(Request $request, $eventId)
    {
        $user = $this->getCurrentUser();
        if (!$user) {
            return redirect()->route('Auth.index')->with('warning', 'Vui lòng đăng nhập để lưu sự kiện yêu thích.');
        }

        SuKien::whereKey($eventId)->where('trang_thai', 'cong_khai')->firstOrFail();

        $fav = YeuThich::where('nguoi_dung_id', $user->id)
            ->where('su_kien_id', $eventId)
            ->first();

        if ($fav) {
            $fav->delete();
            $message = 'Đã xóa sự kiện khỏi danh sách yêu thích!';
        } else {
            YeuThich::create([
                'nguoi_dung_id' => $user->id,
                'su_kien_id' => $eventId,
            ]);
            $message = 'Đã thêm sự kiện vào danh sách yêu thích!';
        }

        return back()->with('success', $message);
    }

    /**
     * Trang Hồ sơ cá nhân: Hiển thị thông tin người dùng và thống kê
     */
    public function profile(Request $request)
    {
        $user = $this->getCurrentUser();
        if (!$user) {
            return redirect()->route('Auth.index')->with('warning', 'Vui lòng đăng nhập để xem thông tin hồ sơ.');
        }

        $registeredCount = DangKy::where('nguoi_dung_id', $user->id)->count();
        $bookmarksCount = YeuThich::where('nguoi_dung_id', $user->id)->count();
        $attendedCount = DangKy::where('nguoi_dung_id', $user->id)
            ->whereHas('suKien', function ($q) {
                $q->where('thoi_gian_ket_thuc', '<', now());
            })
            ->count();

        return view('user.profile', compact('user', 'registeredCount', 'bookmarksCount', 'attendedCount'));
    }

    /**
     * Cập nhật thông tin cá nhân: Lưu họ tên, email, số điện thoại, ngày sinh, địa chỉ, tiểu sử
     */
    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = $this->getCurrentUser();
        if (!$user) {
            return redirect()->route('Auth.index')->with('warning', 'Vui lòng đăng nhập để cập nhật hồ sơ.');
        }

        $userModel = NguoiDung::find($user->id);
        if (!$userModel) {
            return back()->with('error', 'Không tìm thấy thông tin tài khoản người dùng.');
        }

        $data = $request->validated();
        if (!empty($data['ngay_sinh'])) {
            $data['ngay_sinh'] = Carbon::createFromFormat('d/m/Y', $data['ngay_sinh'])->format('Y-m-d');
        }

        $userModel->update($data);

        // Cập nhật user đang đăng nhập sau khi lưu hồ sơ.
        $freshUser = $userModel->fresh();
        if (Auth::check()) {
            Auth::setUser($freshUser);
        }

        return redirect()->route('Dashboard.profile')->with('success', 'Cập nhật thông tin cá nhân thành công!');
    }

    /**
     * Đổi mật khẩu đăng nhập của tài khoản
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        $user = $this->getCurrentUser();
        if (!$user) {
            return redirect()->route('Auth.index')->with('warning', 'Vui lòng đăng nhập để đổi mật khẩu.');
        }

        $userModel = NguoiDung::find($user->id);
        if (!$userModel) {
            return back()->with('error', 'Không tìm thấy thông tin tài khoản người dùng.');
        }

        if (!Hash::isHashed($userModel->mat_khau) || !Hash::check($request->current_password, $userModel->mat_khau)) {
            return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không chính xác.'])->withInput();
        }

        $userModel->mat_khau = Hash::make($request->password);
        $userModel->save();

        $freshUser = $userModel->fresh();
        if (Auth::check()) {
            Auth::setUser($freshUser);
        }

        return redirect()->route('Dashboard.profile')->with('success', 'Đổi mật khẩu tài khoản thành công!');
    }

}
