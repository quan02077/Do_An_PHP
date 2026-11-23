<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReviewRequest;
use App\Models\DangKy;
use App\Models\DanhGia;
use App\Models\SuKien;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Tạo mới hoặc cập nhật đánh giá của chính người tham gia sau khi sự kiện kết thúc.
     */
    public function store(StoreReviewRequest $request, $id)
    {
        $user = Auth::user();

        if (!$user || $user->trang_thai !== 'hoat_dong') {
            return redirect()->route('Auth.index')
                ->with('error', 'Tài khoản của bạn không thể thực hiện đánh giá.');
        }

        if ($user->vai_tro === 'admin') {
            return back()->with('warning', 'Tài khoản quản trị không thể gửi đánh giá sự kiện.');
        }

        $event = SuKien::findOrFail($id);

        if (!$event->thoi_gian_ket_thuc || $event->thoi_gian_ket_thuc >= now()) {
            return back()->with('warning', 'Chỉ có thể đánh giá sau khi sự kiện đã kết thúc.');
        }

        $hasAttended = DangKy::where('nguoi_dung_id', $user->id)
            ->where('su_kien_id', $event->id)
            ->where('trang_thai', 'da_tham_gia')
            ->exists();

        if (!$hasAttended) {
            return back()->with('warning', 'Chỉ người đã tham gia (check-in) sự kiện mới có thể đánh giá.');
        }

        $data = $request->validated();
        $review = DanhGia::updateOrCreate(
            [
                'nguoi_dung_id' => $user->id,
                'su_kien_id' => $event->id,
            ],
            [
                'so_sao' => (int) $data['so_sao'],
                'noi_dung' => isset($data['noi_dung']) && trim($data['noi_dung']) !== '' ? trim($data['noi_dung']) : null,
            ]
        );

        return redirect()->route('Event.show', $event->id)
            ->with('success', $review->wasRecentlyCreated ? 'Cảm ơn bạn đã gửi đánh giá sự kiện!' : 'Đã cập nhật đánh giá của bạn.');
    }

    /**
     * Xóa đánh giá của chính người dùng đang đăng nhập.
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $review = DanhGia::findOrFail($id);

        if (!$user || $review->nguoi_dung_id !== $user->id) {
            abort(403, 'Bạn không có quyền xóa đánh giá này.');
        }

        $eventId = $review->su_kien_id;
        $review->delete();

        return redirect()->route('Event.show', $eventId)
            ->with('success', 'Đã xóa đánh giá của bạn.');
    }
}
