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
        $categories = DanhMuc::all();
        $events = SuKien::with(['danhMuc', 'dangKys'])
            ->whereIn('trang_thai', ['cong_khai', 'da_huy'])
            ->get();

        $search = $request->query('search');
        $categoryId = $request->query('category');
        $status = $request->query('status');

        $user = Auth::user() ?? session('user');
        $userFavIds = $user ? YeuThich::where('nguoi_dung_id', $user->id)->pluck('su_kien_id')->toArray() : [];

        if (!empty($search)) {
            $events = $events->filter(function ($item) use ($search) {
                $ten = $item->ten_su_kien ?? '';
                $diaDiem = $item->dia_diem ?? '';
                return str_contains(mb_strtolower($ten), mb_strtolower($search))
                    || str_contains(mb_strtolower($diaDiem), mb_strtolower($search));
            });
        }

        if (!empty($categoryId)) {
            $events = $events->filter(function ($item) use ($categoryId) {
                return $item->danh_muc_id == $categoryId;
            });
        }

        if (!empty($status)) {
            if ($status === 'yeu_thich') {
                $events = $events->filter(function ($item) use ($userFavIds) {
                    return in_array($item->id, $userFavIds);
                });
            } elseif ($status === 'da_huy') {
                $events = $events->filter(function ($item) {
                    return $item->trang_thai === 'da_huy';
                });
            } else {
                $events = $events->filter(function ($item) use ($status) {
                    return $item->trang_thai_dien_ra === $status;
                });
            }
        }

        return view('home.index', compact('categories', 'events', 'userFavIds'));
    }
}
