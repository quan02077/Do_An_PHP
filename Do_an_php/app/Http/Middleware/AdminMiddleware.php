<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /** Chỉ cho phép tài khoản có vai_tro = admin truy cập khu vực quản trị. */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user() ?? $request->session()->get('user');

        if (!$user || $user->vai_tro !== 'admin') {
            return redirect()->route('Home.index')
                ->with('warning', 'Bạn không có quyền truy cập khu vực quản trị.');
        }

        return $next($request);
    }
}
