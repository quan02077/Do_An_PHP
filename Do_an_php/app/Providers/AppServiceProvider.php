<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        view()->composer('*', function ($view) {
            try {
                $currentUser = auth()->user() ?? session('user') ?? \App\Models\NguoiDung::find(2) ?? \App\Models\NguoiDung::first();
                $favCount = $currentUser ? \App\Models\YeuThich::where('nguoi_dung_id', $currentUser->id)->count() : 0;
                $ticketCount = $currentUser ? \App\Models\DangKy::where('nguoi_dung_id', $currentUser->id)->where('trang_thai', '!=', 'da_huy')->count() : 0;
                $view->with([
                    'currentUser' => $currentUser,
                    'favCount' => $favCount,
                    'ticketCount' => $ticketCount,
                ]);
            } catch (\Throwable $e) {
                $view->with([
                    'currentUser' => null,
                    'favCount' => 0,
                    'ticketCount' => 0,
                ]);
            }
        });
    }
}
