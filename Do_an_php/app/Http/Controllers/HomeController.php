<?php

namespace App\Http\Controllers;

use App\Models\DanhMuc;
use App\Models\SuKien;
use App\Models\YeuThich;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // Phân quyền: Nếu là Quản trị viên (Admin) -> Hiển thị trang quản trị Admin
        if ($user && $user->vai_tro === 'admin') {
            return redirect()->route('admin.index');
        }

        // Nếu là Thành viên (User) hoặc Khách -> Hiển thị trang danh sách sự kiện (User index)
        $categories = DanhMuc::all();
        $eventsQuery = SuKien::with(['danhMuc', 'dangKys'])
            ->whereIn('trang_thai', ['cong_khai', 'da_huy']);

        $search = trim((string) $request->query('search', ''));
        $categoryId = $request->query('category');
        $status = $request->query('status');

        $userFavIds = $user ? YeuThich::where('nguoi_dung_id', $user->id)->pluck('su_kien_id')->toArray() : [];

        if ($search !== '') {
            $eventsQuery->where(function ($query) use ($search) {
                $query->where('ten_su_kien', 'like', '%'.$search.'%')
                    ->orWhere('mo_ta', 'like', '%'.$search.'%')
                    ->orWhere('dia_diem', 'like', '%'.$search.'%')
                    ->orWhereHas('danhMuc', fn ($category) => $category->where('ten_danh_muc', 'like', '%'.$search.'%'));
            });
        }

        if (!empty($categoryId)) {
            $eventsQuery->where('danh_muc_id', $categoryId);
        }

        if (!empty($status)) {
            if ($status === 'yeu_thich') {
                $eventsQuery->whereIn('id', $userFavIds);
            } elseif ($status === 'da_huy') {
                $eventsQuery->where('trang_thai', 'da_huy');
            } elseif (in_array($status, ['sap_dien_ra', 'dang_dien_ra', 'da_ket_thuc'], true)) {
                $eventsQuery->where('trang_thai', 'cong_khai');
                if ($status === 'sap_dien_ra') {
                    $eventsQuery->where('thoi_gian_bat_dau', '>', now());
                } elseif ($status === 'dang_dien_ra') {
                    $eventsQuery->where('thoi_gian_bat_dau', '<=', now())
                        ->where('thoi_gian_ket_thuc', '>=', now());
                } else {
                    $eventsQuery->where('thoi_gian_ket_thuc', '<', now());
                }
            }
        }

        $events = $eventsQuery->orderBy('thoi_gian_bat_dau')->orderBy('id')
            ->paginate(12)->withQueryString();

        return view('user.index', compact('categories', 'events', 'userFavIds', 'user'));
    }
}
