<?php

namespace Tests\Feature;

use App\Mail\NhacLichSuKienMail;
use App\Models\DangKy;
use App\Models\NguoiDung;
use App\Models\SuKien;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class EventReminderTest extends TestCase
{
    use RefreshDatabase;

    public function test_confirmed_ticket_starting_within_24h_receives_reminder_and_marks_timestamp(): void
    {
        $this->travelTo(now()->setDate(2026, 12, 10)->setTime(8, 0, 0));
        Mail::fake();

        $user = NguoiDung::factory()->create(['trang_thai' => 'hoat_dong']);
        $event = SuKien::factory()->create([
            'trang_thai' => 'cong_khai',
            'thoi_gian_bat_dau' => now()->addHours(10),
            'thoi_gian_ket_thuc' => now()->addHours(12),
        ]);
        $ticket = DangKy::factory()->create([
            'nguoi_dung_id' => $user->id,
            'su_kien_id' => $event->id,
            'trang_thai' => 'da_xac_nhan',
            'da_nhac_lich_luc' => null,
        ]);

        $this->artisan('su-kien:nhac-lich')
            ->expectsOutput('Đã gửi nhắc lịch cho 1 vé.')
            ->assertSuccessful();

        Mail::assertSent(NhacLichSuKienMail::class, 1);
        Mail::assertSent(NhacLichSuKienMail::class, fn (NhacLichSuKienMail $mail) => $mail->hasTo($user->email));
        $this->assertNotNull($ticket->fresh()->da_nhac_lich_luc);
    }

    public function test_running_command_twice_does_not_send_duplicate_reminders(): void
    {
        $this->travelTo(now()->setDate(2026, 12, 10)->setTime(8, 0, 0));
        Mail::fake();

        $user = NguoiDung::factory()->create(['trang_thai' => 'hoat_dong']);
        $event = SuKien::factory()->create([
            'trang_thai' => 'cong_khai',
            'thoi_gian_bat_dau' => now()->addHours(8),
            'thoi_gian_ket_thuc' => now()->addHours(10),
        ]);
        DangKy::factory()->create([
            'nguoi_dung_id' => $user->id,
            'su_kien_id' => $event->id,
            'trang_thai' => 'da_xac_nhan',
        ]);

        $this->artisan('su-kien:nhac-lich')->expectsOutput('Đã gửi nhắc lịch cho 1 vé.')->assertSuccessful();
        $this->artisan('su-kien:nhac-lich')->expectsOutput('Đã gửi nhắc lịch cho 0 vé.')->assertSuccessful();

        Mail::assertSent(NhacLichSuKienMail::class, 1);
    }

    public function test_event_three_days_away_is_skipped_for_24h_but_sent_with_96h_option(): void
    {
        $this->travelTo(now()->setDate(2026, 12, 10)->setTime(8, 0, 0));
        Mail::fake();

        $user = NguoiDung::factory()->create(['trang_thai' => 'hoat_dong']);
        $event = SuKien::factory()->create([
            'trang_thai' => 'cong_khai',
            'thoi_gian_bat_dau' => now()->addDays(3),
            'thoi_gian_ket_thuc' => now()->addDays(3)->addHours(2),
        ]);
        DangKy::factory()->create([
            'nguoi_dung_id' => $user->id,
            'su_kien_id' => $event->id,
            'trang_thai' => 'da_xac_nhan',
        ]);

        $this->artisan('su-kien:nhac-lich')->expectsOutput('Đã gửi nhắc lịch cho 0 vé.')->assertSuccessful();
        Mail::assertNothingSent();

        $this->artisan('su-kien:nhac-lich --gio=96')->expectsOutput('Đã gửi nhắc lịch cho 1 vé.')->assertSuccessful();
        Mail::assertSent(NhacLichSuKienMail::class, 1);
    }

    public function test_cancelled_pending_and_checked_in_tickets_do_not_receive_reminders(): void
    {
        $this->travelTo(now()->setDate(2026, 12, 10)->setTime(8, 0, 0));
        Mail::fake();

        $event = SuKien::factory()->create([
            'trang_thai' => 'cong_khai',
            'thoi_gian_bat_dau' => now()->addHours(6),
            'thoi_gian_ket_thuc' => now()->addHours(8),
        ]);

        foreach (['da_huy', 'cho_duyet', 'da_tham_gia'] as $status) {
            DangKy::factory()->create([
                'nguoi_dung_id' => NguoiDung::factory()->create(['trang_thai' => 'hoat_dong'])->id,
                'su_kien_id' => $event->id,
                'trang_thai' => $status,
            ]);
        }

        $this->artisan('su-kien:nhac-lich')->expectsOutput('Đã gửi nhắc lịch cho 0 vé.')->assertSuccessful();
        Mail::assertNothingSent();
    }

    public function test_cancelled_or_draft_events_do_not_receive_reminders(): void
    {
        $this->travelTo(now()->setDate(2026, 12, 10)->setTime(8, 0, 0));
        Mail::fake();

        foreach (['da_huy', 'nhap'] as $eventStatus) {
            $event = SuKien::factory()->create([
                'trang_thai' => $eventStatus,
                'thoi_gian_bat_dau' => now()->addHours(5),
                'thoi_gian_ket_thuc' => now()->addHours(7),
            ]);
            DangKy::factory()->create([
                'nguoi_dung_id' => NguoiDung::factory()->create(['trang_thai' => 'hoat_dong'])->id,
                'su_kien_id' => $event->id,
                'trang_thai' => 'da_xac_nhan',
            ]);
        }

        $this->artisan('su-kien:nhac-lich')->expectsOutput('Đã gửi nhắc lịch cho 0 vé.')->assertSuccessful();
        Mail::assertNothingSent();
    }

    public function test_locked_user_does_not_receive_reminder(): void
    {
        $this->travelTo(now()->setDate(2026, 12, 10)->setTime(8, 0, 0));
        Mail::fake();

        $lockedUser = NguoiDung::factory()->create(['trang_thai' => 'bi_khoa']);
        $event = SuKien::factory()->create([
            'trang_thai' => 'cong_khai',
            'thoi_gian_bat_dau' => now()->addHours(5),
            'thoi_gian_ket_thuc' => now()->addHours(7),
        ]);
        DangKy::factory()->create([
            'nguoi_dung_id' => $lockedUser->id,
            'su_kien_id' => $event->id,
            'trang_thai' => 'da_xac_nhan',
        ]);

        $this->artisan('su-kien:nhac-lich')->expectsOutput('Đã gửi nhắc lịch cho 0 vé.')->assertSuccessful();
        Mail::assertNothingSent();
    }

    public function test_already_started_event_does_not_receive_reminder(): void
    {
        $this->travelTo(now()->setDate(2026, 12, 10)->setTime(8, 0, 0));
        Mail::fake();

        $user = NguoiDung::factory()->create(['trang_thai' => 'hoat_dong']);
        $event = SuKien::factory()->create([
            'trang_thai' => 'cong_khai',
            'thoi_gian_bat_dau' => now()->subMinute(),
            'thoi_gian_ket_thuc' => now()->addHours(2),
        ]);
        DangKy::factory()->create([
            'nguoi_dung_id' => $user->id,
            'su_kien_id' => $event->id,
            'trang_thai' => 'da_xac_nhan',
        ]);

        $this->artisan('su-kien:nhac-lich')->expectsOutput('Đã gửi nhắc lịch cho 0 vé.')->assertSuccessful();
        Mail::assertNothingSent();
    }

    public function test_reminder_command_is_registered_in_schedule(): void
    {
        Artisan::call('schedule:list');
        $output = Artisan::output();

        $this->assertStringContainsString('su-kien:nhac-lich', $output);
    }
}
