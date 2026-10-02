<?php

namespace App\Http\Controllers;

use App\Models\NguoiDung;
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

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'Vui lòng nhập địa chỉ email.',
            'email.email' => 'Địa chỉ email không đúng định dạng.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
        ]);

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

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:150|unique:nguoi_dung,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|min:6',
        ], [
            'name.required' => 'Vui lòng nhập họ và tên.',
            'email.required' => 'Vui lòng nhập địa chỉ email.',
            'email.unique' => 'Địa chỉ email này đã được sử dụng.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min' => 'Mật khẩu phải có tối thiểu 6 ký tự.',
        ]);

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
