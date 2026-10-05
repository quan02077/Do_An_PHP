<?php

namespace App\Http\Controllers;

use App\Models\NguoiDung;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::check() || session()->has('user')) {
            return redirect()->route('Dashboard.myTicket');
        }
        return view('auth.auth');
    }

    public function login(LoginRequest $request)
    {
        $user = NguoiDung::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Email này chưa được đăng ký trong hệ thống.'])->withInput();
        }

        $passwordValid = false;
        if (Hash::check($request->password, $user->mat_khau)) {
            $passwordValid = true;
        } elseif ($user->mat_khau === $request->password) {
            $passwordValid = true;
            // Nâng cấp mật khẩu thành hash bảo mật
            try {
                $user->mat_khau = Hash::make($request->password);
                $user->save();
            } catch (\Exception $e) {
                // bỏ qua nếu lỗi
            }
        } elseif (md5($request->password) === $user->mat_khau) {
            $passwordValid = true;
        }

        if (!$passwordValid) {
            return back()->withErrors(['password' => 'Mật khẩu không chính xác.'])->withInput();
        }

        Auth::login($user);
        session(['user' => $user]);

        return redirect()->intended(route('Dashboard.myTicket'))->with('success', 'Đăng nhập thành công! Chào mừng ' . $user->ho_ten . '.');
    }

    public function register(RegisterRequest $request)
    {
        $user = NguoiDung::create([
            'ho_ten' => $request->name,
            'email' => $request->email,
            'so_dien_thoai' => $request->phone,
            'mat_khau' => Hash::make($request->password),
            'vai_tro' => 'user',
            'trang_thai' => 'hoat_dong',
        ]);

        Auth::login($user);
        session(['user' => $user]);

        return redirect()->route('Home.index')->with('success', 'Đăng ký tài khoản thành công!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        session()->forget('user');
        session()->invalidate();
        session()->regenerateToken();

        return redirect()->route('Home.index')->with('success', 'Đã đăng xuất khỏi tài khoản.');
    }

    public function switchUser($id)
    {
        $user = NguoiDung::findOrFail($id);
        Auth::login($user);
        session(['user' => $user]);

        return back()->with('success', 'Đã chuyển sang tài khoản: ' . $user->ho_ten . ' (' . $user->email . ')');
    }
}
