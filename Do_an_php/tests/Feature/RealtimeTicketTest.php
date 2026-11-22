<?php

namespace Tests\Feature;

use App\Events\TicketCheckedIn;
use App\Events\TicketCountsChanged;
use App\Models\DangKy;
use App\Models\NguoiDung;
use App\Models\SuKien;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Broadcast;
use Tests\TestCase;

class RealtimeTicketTest extends TestCase
{
    public function test_booking_and_cancellation_publish_current_capacity(): void
    {
        Event::fake([TicketCountsChanged::class]);
        $user = NguoiDung::factory()->create();
        $event = SuKien::factory()->create(['so_luong_toi_da' => 2]);

        $this->actingAs($user)->post('/event/'.$event->id.'/book')->assertRedirect('/myTicket');
        Event::assertDispatched(TicketCountsChanged::class, fn ($message) =>
            $message->eventId === $event->id && $message->registered === 1 && $message->capacity === 2
        );

        $ticket = DangKy::where('nguoi_dung_id', $user->id)->where('su_kien_id', $event->id)->firstOrFail();
        $this->actingAs($user)->post('/tickets/'.$ticket->id.'/cancel')->assertRedirect();
        Event::assertDispatched(TicketCountsChanged::class, fn ($message) =>
            $message->eventId === $event->id && $message->registered === 0
        );
    }

    public function test_manual_status_check_in_also_publishes_admin_message(): void
    {
        Event::fake([TicketCountsChanged::class, TicketCheckedIn::class]);
        $admin = NguoiDung::factory()->create(['vai_tro' => 'admin']);
        $ticket = DangKy::factory()->create(['trang_thai' => 'da_xac_nhan']);

        $this->actingAs($admin)->from('/admin?tab=registrations')
            ->put('/admin/registrations/'.$ticket->id.'/status', ['trang_thai' => 'da_tham_gia'])
            ->assertSessionHas('success');

        Event::assertDispatched(TicketCheckedIn::class, fn ($message) =>
            $message->ticketId === $ticket->id && $message->checkedInTotal === 1
        );
    }

    public function test_manual_cancel_does_not_publish_check_in_message(): void
    {
        Event::fake([TicketCountsChanged::class, TicketCheckedIn::class]);
        $admin = NguoiDung::factory()->create(['vai_tro' => 'admin']);
        $ticket = DangKy::factory()->create(['trang_thai' => 'da_xac_nhan']);

        $this->actingAs($admin)->from('/admin?tab=registrations')
            ->put('/admin/registrations/'.$ticket->id.'/status', ['trang_thai' => 'da_huy'])
            ->assertSessionHas('success');

        Event::assertNotDispatched(TicketCheckedIn::class);
    }

    public function test_check_in_publishes_admin_message_without_public_personal_data(): void
    {
        Event::fake([TicketCountsChanged::class, TicketCheckedIn::class]);
        $admin = NguoiDung::factory()->create(['vai_tro' => 'admin']);
        $ticket = DangKy::factory()->create();

        $this->actingAs($admin)->post('/admin/check-in', ['ma_ve' => $ticket->ma_ve])
            ->assertOk()->assertJsonPath('checkedInTotal', 1)
            ->assertJsonPath('ticket.id', $ticket->id);

        Event::assertDispatched(TicketCheckedIn::class, fn ($message) =>
            $message->ticketId === $ticket->id && $message->checkedInTotal === 1
        );
        Event::assertDispatched(TicketCountsChanged::class, function ($message) use ($ticket) {
            return $message->eventId === $ticket->su_kien_id
                && ! array_key_exists('attendeeName', $message->broadcastWith());
        });
    }

    public function test_only_admin_can_join_private_check_in_channel(): void
    {
        config()->set('broadcasting.default', 'reverb');
        config()->set('broadcasting.connections.reverb.key', 'test-key');
        config()->set('broadcasting.connections.reverb.secret', 'test-secret');
        config()->set('broadcasting.connections.reverb.app_id', 'test-app');
        Broadcast::channel('admin.checkins', fn ($user) => $user->vai_tro === 'admin');

        $request = ['socket_id' => '123.456', 'channel_name' => 'private-admin.checkins'];
        $admin = NguoiDung::factory()->create(['vai_tro' => 'admin']);
        $this->actingAs($admin)->post('/broadcasting/auth', $request)->assertOk();

        $member = NguoiDung::factory()->create(['vai_tro' => 'user']);
        $this->actingAs($member)->post('/broadcasting/auth', $request)->assertForbidden();
    }

    public function test_booking_succeeds_when_reverb_is_unavailable(): void
    {
        config()->set('broadcasting.default', 'reverb');
        config()->set('broadcasting.connections.reverb.key', 'test-key');
        config()->set('broadcasting.connections.reverb.secret', 'test-secret');
        config()->set('broadcasting.connections.reverb.app_id', 'test-app');
        config()->set('broadcasting.connections.reverb.options.host', '127.0.0.1');
        config()->set('broadcasting.connections.reverb.options.port', 1);
        config()->set('broadcasting.connections.reverb.options.scheme', 'http');

        $user = NguoiDung::factory()->create();
        $event = SuKien::factory()->create();
        $this->actingAs($user)->post('/event/'.$event->id.'/book')->assertRedirect('/myTicket');
        $this->assertDatabaseHas('dang_ky', ['nguoi_dung_id' => $user->id, 'su_kien_id' => $event->id]);
    }
}
