<?php

namespace App\Http\Controllers;

use App\Models\DangKy;
use App\Models\YeuThich;
use App\Models\NguoiDung;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\ChangePasswordRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class DashboardController extends Controller
{
    /**
     * Lấy người dùng hiện tại (qua Auth, Session, hoặc mặc định mẫu để không bị gián đoạn trải nghiệm)
     */
    protected function getCurrentUser()
    {
        return Auth::user() ?? session('user');
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

        return view('home.User.myTicket', compact('user', 'tickets'));
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

        $ticket->trang_thai = 'da_huy';
        $ticket->save();

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

        return view('home.User.favorite', compact('user', 'favorites'));
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

        return view('home.profile', compact('user', 'registeredCount', 'bookmarksCount', 'attendedCount'));
    }

    /**
     * Cập nhật thông tin cá nhân: Lưu họ tên, email, số điện thoại, ngày sinh, địa chỉ, tiểu sử
     */
     public function updateProfile(UpdateProfileRequest $request)
     {
         $user = $this->getCurrentUser();
         if (!$user) {
             return redirect()->route('Auth.index')->with('warning', 'Vui lòng đăng nhập để thực hiện.');
         }

         $userModel = NguoiDung::find($user->id);
         if (!$userModel) {
             return back()->with('error', 'Không tìm thấy thông tin tài khoản người dùng.');
         }

         $userModel->ho_ten = $request->ho_ten;
         $userModel->email = $request->email;
         $userModel->so_dien_thoai = $request->so_dien_thoai;
         $userModel->gioi_tinh = $request->gioi_tinh;
         $userModel->ngay_sinh = $request->ngay_sinh;
         $userModel->dia_chi = $request->dia_chi;
         $userModel->tieu_su = $request->tieu_su;
         $userModel->save();

         // Đồng bộ lại thông tin mới vào session và auth
         session(['user' => $userModel]);
         if (Auth::check()) {
             Auth::setUser($userModel);
         }

         return back()->with('success', 'Cập nhật thông tin cá nhân thành công!');
     }

    /**
     * Đổi mật khẩu đăng nhập của tài khoản
     */
     public function changePassword(ChangePasswordRequest $request)
     {
         $user = $this->getCurrentUser();
         if (!$user) {
             return redirect()->route('Auth.index')->with('warning', 'Vui lòng đăng nhập để thực hiện.');
         }

         $userModel = NguoiDung::find($user->id);
         if (!$userModel) {
             return back()->with('error', 'Không tìm thấy thông tin tài khoản người dùng.');
         }

         // Kiểm tra tính hợp lệ của mật khẩu hiện tại
         $currentValid = false;
         if (Hash::check($request->current_password, $userModel->mat_khau)) {
             $currentValid = true;
         } elseif ($userModel->mat_khau === $request->current_password || md5($request->current_password) === $userModel->mat_khau) {
             $currentValid = true;
         }

         if (!$currentValid) {
             return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không chính xác.']);
         }

         $userModel->mat_khau = Hash::make($request->password);
         $userModel->save();

         session(['user' => $userModel]);
         if (Auth::check()) {
             Auth::setUser($userModel);
         }

         return back()->with('success', 'Đổi mật khẩu tài khoản thành công!');
     }
}
