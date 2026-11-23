<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateCustom
{
    /** Chỉ cho phép người dùng đã đăng nhập truy cập route được bảo vệ. */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('Auth.index')
                ->with('warning', 'Vui lòng đăng nhập để sử dụng chức năng này.');
        }

        if ($user->trang_thai === 'bi_khoa') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('Auth.index')
                ->with('error', 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ quản trị viên.');
        }

        return $next($request);
    }
}
