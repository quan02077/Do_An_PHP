<?php

namespace App\Providers;

use App\Mail\Transport\GmailOAuth2Transport;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
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
        Mail::extend('gmail_oauth2', function (array $config = []) {
            return new GmailOAuth2Transport(
                clientId: (string) ($config['client_id'] ?? ''),
                clientSecret: (string) ($config['client_secret'] ?? ''),
                refreshToken: (string) ($config['refresh_token'] ?? ''),
                username: (string) ($config['username'] ?? ''),
                mode: (string) ($config['mode'] ?? 'smtp'),
                host: (string) ($config['host'] ?? 'smtp.gmail.com'),
                port: (int) ($config['port'] ?? 587),
                timeout: (int) ($config['timeout'] ?? 15),
            );
        });

        ResetPassword::toMailUsing(function (object $notifiable, string $token) {
            $url = url(route('password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false));
            $expire = config('auth.passwords.' . config('auth.defaults.passwords') . '.expire', 60);

            return (new MailMessage)
                ->subject('Đặt lại mật khẩu — ' . config('app.name', 'QQQ'))
                ->view('emails.reset_password', [
                    'user' => $notifiable,
                    'url' => $url,
                    'expire' => $expire,
                ]);
        });

        view()->composer('*', function ($view) {
            try {
                $currentUser = Auth::user();
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
