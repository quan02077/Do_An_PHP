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
        $user = Auth::user() ?? $request->session()->get('user');

        if (!$user) {
            return redirect()->route('Auth.index')
                ->with('warning', 'Vui lòng đăng nhập để sử dụng chức năng này.');
        }

        return $next($request);
    }
}
