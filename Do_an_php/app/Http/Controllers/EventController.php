<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SuKien;

class EventController extends Controller
{
    public function show($id)
    {
        $event = SuKien::with(['danhMuc', 'nguoiTao'])->findOrFail($id);
        return view('home.event_detail', compact('event'));
    }
}
