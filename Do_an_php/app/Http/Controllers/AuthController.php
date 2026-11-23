<?php

namespace App\Http\Controllers;

use App\Models\NguoiDung;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\ResetPasswordRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
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
        if (Auth::check()) {
            return redirect()->route('Dashboard.myTicket');
        }

        $status = Password::reset($request->validated(), function (NguoiDung $user, string $password) {
            $user->forceFill(['mat_khau' => Hash::make($password)])->save();
        });

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('Auth.index')
                ->with('success', 'Đặt lại mật khẩu thành công! Vui lòng đăng nhập với mật khẩu mới.');
        }

        $errorMessage = match ($status) {
            Password::INVALID_TOKEN => 'Liên kết đặt lại mật khẩu không hợp lệ hoặc đã hết hạn. Vui lòng gửi yêu cầu mới.',
            Password::INVALID_USER => 'Không tìm thấy tài khoản tương ứng với địa chỉ email này.',
            Password::RESET_THROTTLED => 'Bạn thao tác quá nhanh. Vui lòng thử lại sau ít phút.',
            default => 'Không thể đặt lại mật khẩu. Vui lòng thử lại.',
        };

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => $errorMessage]);
    }

    public function sendResetLink(ForgotPasswordRequest $request)
    {
        if (Auth::check()) {
            return redirect()->route('Dashboard.myTicket');
        }

        try {
            $status = Password::sendResetLink($request->only('email'));
        } catch (\Throwable $e) {
            Log::error('Password reset email failed', [
                'email' => $request->email,
                'error' => $e->getMessage(),
            ]);

            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Không thể gửi email đặt lại mật khẩu lúc này. Vui lòng kiểm tra cấu hình SMTP hoặc thử lại sau.']);
        }

        if ($status === Password::RESET_THROTTLED) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Bạn vừa gửi yêu cầu đặt lại mật khẩu. Vui lòng đợi ít nhất 1 phút trước khi thử lại.']);
        }

        return back()->with('status', 'Nếu email tồn tại trong hệ thống, liên kết đặt lại mật khẩu đã được gửi. Vui lòng kiểm tra hộp thư đến hoặc thư rác (Spam).');
    }

    public function showResetForm(string $token, Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('Dashboard.myTicket');
        }

        return view('auth.auth', [
            'resetToken' => $token,
            'resetEmail' => $request->query('email'),
        ]);
    }
}
