<?php

namespace App\Http\Controllers;

use App\Models\DanhMuc;
use App\Models\SuKien;
use Illuminate\Http\Request;

use function Laravel\Prompts\search;

class HomeController extends Controller
{
    public function index(){
        $categories = DanhMuc::all();
        $events = SuKien::with(['danhMuc', 'dangKys'])->get();

        $search = request('search');
        $categoryId = request('category');
        $status = request('status');

        if (!empty($search)) {
            $events = $events->filter(function ($item) use ($search) {
                $ten = $item->ten_su_kien ?? '';
                $diaDiem = $item->dia_diem ?? '';
                return str_contains(mb_strtolower($ten), mb_strtolower($search))
                    || str_contains(mb_strtolower($diaDiem), mb_strtolower($search));
            });
        }

        if(!empty($categoryId)){
            $events = $events->filter(function ($item) use ($categoryId){
                return $item->danh_muc_id == $categoryId;
            });
        }

        if(!empty($status)){
            $events = $events->filter(function ($item) use ($status) {
                return $item->trang_thai == $status;
            });
        }

        return view('home.index', compact('categories', 'events'));
    }
}

