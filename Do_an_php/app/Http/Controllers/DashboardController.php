<?php

namespace App\Http\Controllers;

use App\Models\DangKy;
use App\Models\NguoiDung;
use App\Models\SuKien;
use App\Models\YeuThich;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Lấy người dùng hiện tại (qua Auth, Session, hoặc mặc định mẫu để không bị gián đoạn trải nghiệm)
     */
    protected function getCurrentUser()
    {
        return Auth::user() ?? session('user') ?? NguoiDung::find(2) ?? NguoiDung::first();
    }

    /**
     * Trang Vé của tôi: Hiển thị danh sách vé trong CSDL của tài khoản người dùng
     */
    public function myTicket(Request $request)
    {
        $user = $this->getCurrentUser();

        $tickets = collect();
        if ($user) {
            $tickets = DangKy::with(['suKien.danhMuc'])
                ->where('nguoi_dung_id', $user->id)
                ->orderBy('id', 'desc')
                ->get();
        }

        return view('home.myTicket', compact('user', 'tickets'));
    }

    /**
     * Hủy vé: Cập nhật trạng thái 'da_huy' trong bảng dang_ky theo đúng người dùng
     */
    public function cancelTicket(Request $request, $id)
    {
        $user = $this->getCurrentUser();
        if (!$user) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Vui lòng đăng nhập để thực hiện.'], 401);
            }
            return redirect()->route('Auth.index')->with('warning', 'Vui lòng đăng nhập để thực hiện.');
        }

        $ticket = DangKy::where('id', $id)
            ->where('nguoi_dung_id', $user->id)
            ->first();

        if (!$ticket) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Không tìm thấy vé hoặc bạn không có quyền hủy vé này.'], 404);
            }
            return back()->with('error', 'Không tìm thấy thông tin vé hoặc bạn không có quyền hủy vé này.');
        }

        $ticket->trang_thai = 'da_huy';
        $ticket->save();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã Xóa thành công!',
                'ticket_id' => $ticket->id,
                'status' => 'da_huy',
            ]);
        }

        return back()->with('success', 'Đã Xóa thành công!');
    }

    /**
     * Trang Yêu thích: Hiển thị các sự kiện đã lưu trong CSDL của tài khoản người dùng
     */
    public function favorite(Request $request)
    {
        $user = $this->getCurrentUser();

        $favorites = collect();
        if ($user) {
            $favorites = YeuThich::with(['suKien.danhMuc'])
                ->where('nguoi_dung_id', $user->id)
                ->orderBy('id', 'desc')
                ->get();
        }

        return view('home.favorite', compact('user', 'favorites'));
    }

    /**
     * Bỏ lưu sự kiện khỏi Yêu thích: Xóa bản ghi trong bảng yeu_thich theo đúng người dùng
     */
    public function removeFavorite(Request $request, $eventId)
    {
        $user = $this->getCurrentUser();
        if (!$user) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Vui lòng đăng nhập để thực hiện.'], 401);
            }
            return redirect()->route('Auth.index');
        }

        YeuThich::where('nguoi_dung_id', $user->id)
            ->where('su_kien_id', $eventId)
            ->delete();

        $count = YeuThich::where('nguoi_dung_id', $user->id)->count();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã Xóa thành công!',
                'event_id' => $eventId,
                'count' => $count,
            ]);
        }

        return back()->with('success', 'Đã Xóa thành công!');
    }

    /**
     * Thêm / Bỏ yêu thích (Toggle): Thao tác bấm nút trái tim ở Trang chủ hoặc Chi tiết sự kiện
     */
    public function toggleFavorite(Request $request, $eventId)
    {
        $user = $this->getCurrentUser();
        if (!$user) {
            return response()->json([
                'success' => false,
                'login_required' => true,
                'message' => 'Vui lòng đăng nhập để lưu sự kiện yêu thích.',
            ], 401);
        }

        $fav = YeuThich::where('nguoi_dung_id', $user->id)
            ->where('su_kien_id', $eventId)
            ->first();

        if ($fav) {
            $fav->delete();
            $isFavorited = false;
            $message = 'Đã Xóa thành công!';
        } else {
            YeuThich::create([
                'nguoi_dung_id' => $user->id,
                'su_kien_id' => $eventId,
            ]);
            $isFavorited = true;
            $message = 'Đã thêm vào yêu thích!';
        }

        $count = YeuThich::where('nguoi_dung_id', $user->id)->count();

        return response()->json([
            'success' => true,
            'is_favorited' => $isFavorited,
            'count' => $count,
            'message' => $message,
        ]);
    }
}
