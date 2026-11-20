<?php

namespace App\Http\Controllers;

use App\Models\NguoiDung;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\ResetPasswordRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::check()) {
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

        if (!Hash::isHashed($user->mat_khau) || !Hash::check($request->password, $user->mat_khau)) {
            return back()->withErrors(['password' => 'Mật khẩu không chính xác.'])->withInput();
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended(route('Home.index'))->with('success', 'Đăng nhập thành công! Chào mừng ' . $user->ho_ten . '.');
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
        $request->session()->regenerate();

        return redirect()->route('Home.index')->with('success', 'Đăng ký tài khoản thành công!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('Home.index')->with('success', 'Đã đăng xuất khỏi tài khoản.');
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        $status = Password::reset($request->validated(), function (NguoiDung $user, string $password) {
            $user->forceFill(['mat_khau' => Hash::make($password)])->save();
        });

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('Auth.index')->with('success', 'Đặt lại mật khẩu thành công!')
            : back()->withErrors(['email' => __($status)]);
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => ['required', 'email']]);
        Password::sendResetLink($request->only('email'));

        return back()->with('status', 'Nếu email tồn tại, liên kết đặt lại mật khẩu đã được gửi.');
    }

    public function showResetForm(string $token, Request $request)
    {
        return view('auth.auth', ['resetToken' => $token, 'resetEmail' => $request->query('email')]);
    }
}
