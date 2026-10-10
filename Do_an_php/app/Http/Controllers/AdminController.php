<?php

namespace App\Http\Controllers;

use App\Models\DangKy;
use App\Models\DanhMuc;
use App\Models\NguoiDung;
use App\Models\SuKien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $categories = DanhMuc::withCount('suKiens')->get();
        $events = SuKien::with(['danhMuc', 'dangKys'])->orderByDesc('id')->get();
        $registrations = DangKy::with(['nguoiDung', 'suKien'])->orderByDesc('id')->get();

        return view('home.Admin.admin', compact(
            'user',
            'currentTab',
            'totalEvents',
            'upcomingEvents',
            'totalBookings',
            'totalMembers',
            'events',
            'registrations',
            'categories'
        ));
    }
}
