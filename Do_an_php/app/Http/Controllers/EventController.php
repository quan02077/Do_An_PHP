<?php

namespace App\Http\Controllers;

use App\Models\DangKy;
use App\Models\NguoiDung;
use App\Models\SuKien;
use App\Models\YeuThich;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class EventController extends Controller
{
    protected function getCurrentUser()
    {
        return Auth::user() ?? session('user');
    }

    public function show($id)
    {
        $event = SuKien::with(['danhMuc', 'nguoiTao', 'dangKys'])->findOrFail($id);
        $user = $this->getCurrentUser();

        $isFavorited = false;
        $userTicket = null;

        if ($user) {
            $isFavorited = YeuThich::where('nguoi_dung_id', $user->id)
                ->where('su_kien_id', $id)
                ->exists();

            $userTicket = DangKy::where('nguoi_dung_id', $user->id)
                ->where('su_kien_id', $id)
                ->first();
        }

        return view('home.event_detail', compact('event', 'user', 'isFavorited', 'userTicket'));
    }

    /**
     * Đặt vé / Đăng ký tham gia sự kiện: Lưu vào bảng dang_ky trong CSDL
     */
    public function bookTicket(Request $request, $id)
    {
        $user = $this->getCurrentUser();
        if (!$user) {
            return redirect()->route('Auth.index')->with('warning', 'Vui lòng đăng nhập để đăng ký vé.');
        }

        $event = SuKien::findOrFail($id);

        // Kiểm tra sự kiện có bị hủy không
        if ($event->trang_thai === 'da_huy') {
            return back()->with('warning', 'Sự kiện này đã bị hủy, không thể đăng ký vé.');
        }

        // Kiểm tra tình trạng còn vé
        if ($event->so_luong_toi_da > 0 && $event->so_luong_da_dang_ky >= $event->so_luong_toi_da) {
            return back()->with('warning', 'Sự kiện này đã hết vé!');
        }

        // Kiểm tra xem đã từng đăng ký chưa
        $existingTicket = DangKy::where('nguoi_dung_id', $user->id)
            ->where('su_kien_id', $id)
            ->first();

        if ($existingTicket) {
            if ($existingTicket->trang_thai === 'da_huy') {
                $existingTicket->trang_thai = 'da_xac_nhan';
                $existingTicket->thoi_gian_dang_ky = now();
                $existingTicket->save();

                return redirect()->route('Dashboard.myTicket')->with('success', 'Bạn đã kích hoạt lại vé thành công! Mã vé: ' . $existingTicket->ma_ve);
            }

            return redirect()->route('Dashboard.myTicket')->with('warning', 'Bạn đã đăng ký vé cho sự kiện này rồi! Mã vé: ' . $existingTicket->ma_ve);
        }

        // Tạo mã vé duy nhất
        $maVe = 'QQQ-' . date('Y') . '-' . strtoupper(Str::random(6));

        $ticket = DangKy::create([
            'nguoi_dung_id' => $user->id,
            'su_kien_id' => $id,
            'ma_ve' => $maVe,
            'thoi_gian_dang_ky' => now(),
            'trang_thai' => 'da_xac_nhan',
            'ghi_chu' => 'Đăng ký vé trực tuyến trên website',
        ]);

        return redirect()->route('Dashboard.myTicket')->with('success', 'Chúc mừng! Đăng ký vé thành công! Mã vé của bạn là ' . $maVe);
    }
}
