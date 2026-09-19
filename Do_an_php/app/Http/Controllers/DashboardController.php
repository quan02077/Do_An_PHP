<?php

namespace App\Http\Controllers;

use App\Models\DangKy;
use App\Models\NguoiDung;
use App\Models\YeuThich;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function myTicket(Request $request)
    {
        $user = session('user') ?? NguoiDung::find(2) ?? NguoiDung::first();

        $tickets = DangKy::with(['suKien.danhMuc'])
            ->where('nguoi_dung_id', $user->id ?? 0)
            ->orderBy('id', 'desc')
            ->get();
        return view('home.myTicket', compact('user', 'tickets'));
    }

    public function favorite(Request $request)
    {
        $user = session('user') ?? NguoiDung::find(2) ?? NguoiDung::first();

        $favorites = YeuThich::with(['suKien.danhMuc'])
            ->where('nguoi_dung_id', $user->id ?? 0)
            ->orderBy('id', 'desc')
            ->get();
        return view('home.favorite', compact('user', 'favorites'));
    }
}
