<?php

namespace Tests\Feature;

use App\Models\NguoiDung;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AuthSecurityTest extends TestCase
{
    public function test_plain_text_and_md5_passwords_cannot_log_in(): void
    {
        $plain = NguoiDung::factory()->create();
        DB::table('nguoi_dung')->where('id', $plain->id)->update(['mat_khau' => 'secret123']);
        $this->from('/auth')->post('/login', ['email' => $plain->email, 'password' => 'secret123'])
            ->assertSessionHasErrors('password');

        $legacy = NguoiDung::factory()->create();
        DB::table('nguoi_dung')->where('id', $legacy->id)->update(['mat_khau' => md5('secret123')]);
        $this->from('/auth')->post('/login', ['email' => $legacy->email, 'password' => 'secret123'])
            ->assertSessionHasErrors('password');
    }

    public function test_password_reset_requires_emailed_token_and_cannot_be_reused(): void
    {
        Notification::fake();
        $user = NguoiDung::factory()->create(['so_dien_thoai' => '0912345678']);
        $oldHash = $user->mat_khau;
        $this->post('/reset-password', [
            'email' => $user->email, 'phone' => '0912345678',
            'password' => 'newsecret123', 'password_confirmation' => 'newsecret123',
        ])->assertSessionHasErrors('token');
        $this->assertSame($oldHash, $user->fresh()->mat_khau);

        $this->from('/auth?mode=forgot')->post('/forgot-password', ['email' => $user->email])
            ->assertRedirect('/auth?mode=forgot')->assertSessionHas('status');
        Notification::assertSentTo($user, ResetPassword::class, function (ResetPassword $notification) use ($user) {
            $mail = $notification->toMail($user);

            return str_contains($mail->subject, 'Đặt lại mật khẩu')
                && $mail->view === 'emails.reset_password'
                && ($mail->viewData['user']->id ?? null) === $user->id;
        });

        $this->from('/auth?mode=forgot')->post('/forgot-password', ['email' => $user->email])
            ->assertRedirect('/auth?mode=forgot')->assertSessionHasErrors('email');

        $token = Password::createToken($user);
        $this->get('/reset-password/' . $token)->assertOk()->assertSee('name="email"', false);
        $this->get('/reset-password/' . $token . '?email=' . urlencode($user->email))
            ->assertOk()
            ->assertSee($user->email);

        $this->post('/reset-password', [
            'email' => $user->email, 'token' => $token,
            'password' => 'newsecret123', 'password_confirmation' => 'newsecret123',
        ])->assertRedirect('/auth');
        $this->assertTrue(Hash::check('newsecret123', $user->fresh()->mat_khau));
        $this->post('/reset-password', [
            'email' => $user->email, 'token' => $token,
            'password' => 'anothersecret', 'password_confirmation' => 'anothersecret',
        ])->assertSessionHasErrors([
            'email' => 'Liên kết đặt lại mật khẩu không hợp lệ hoặc đã hết hạn. Vui lòng gửi yêu cầu mới.',
        ]);
        $this->assertTrue(Hash::check('newsecret123', $user->fresh()->mat_khau));
    }

    public function test_authenticated_user_is_redirected_away_from_password_reset_routes(): void
    {
        /** @var NguoiDung $user */
        $user = NguoiDung::factory()->createOne();

        $this->actingAs($user)->get('/reset-password/sample-token')
            ->assertRedirect(route('Dashboard.myTicket'));
        $this->actingAs($user)->post('/forgot-password', ['email' => $user->email])
            ->assertRedirect(route('Dashboard.myTicket'));
    }

    public function test_hashed_password_still_logs_in_and_plain_current_password_cannot_change_it(): void
    {
        /** @var NguoiDung $user */
        $user = NguoiDung::factory()->createOne(['mat_khau' => Hash::make('secret123')]);
        $this->post('/login', ['email' => $user->email, 'password' => 'secret123'])
            ->assertRedirect('/');
        $this->assertAuthenticatedAs($user);

        DB::table('nguoi_dung')->where('id', $user->id)->update(['mat_khau' => 'secret123']);
        $this->actingAs($user)->from('/profile')->post('/profile/change-password', [
            'current_password' => 'secret123', 'password' => 'newsecret123',
            'password_confirmation' => 'newsecret123',
        ])->assertSessionHasErrors('current_password');
    }

    public function test_login_is_rate_limited_after_repeated_attempts(): void
    {
        $this->withServerVariables(['REMOTE_ADDR' => '192.0.2.99']);
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->post('/login', ['email' => 'unknown@example.test', 'password' => 'wrong'])
                ->assertRedirect();
        }
        $this->post('/login', ['email' => 'unknown@example.test', 'password' => 'wrong'])
            ->assertStatus(429);
    }
}
