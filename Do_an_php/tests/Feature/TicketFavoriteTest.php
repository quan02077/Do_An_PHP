<?php

namespace Tests\Feature;

use App\Models\DangKy;
use App\Models\NguoiDung;
use App\Models\SuKien;
use App\Models\YeuThich;
use Tests\TestCase;

class TicketFavoriteTest extends TestCase
{
    public function test_my_ticket_page_displays_own_tickets(): void
    {
        $user = NguoiDung::factory()->create();
        DangKy::factory()->create(['nguoi_dung_id' => $user->id]);
        $this->actingAs($user)->get('/myTicket')->assertOk()->assertSee('Vé đã đăng ký của bạn');
    }

    public function test_favorite_page_displays_own_favorites(): void
    {
        $user = NguoiDung::factory()->create();
        YeuThich::factory()->create(['nguoi_dung_id' => $user->id]);
        $this->actingAs($user)->get('/favorite')->assertOk()->assertSee('Sự kiện yêu thích');
    }

    public function test_user_can_cancel_own_future_ticket(): void
    {
        $user = NguoiDung::factory()->create();
        $ticket = DangKy::factory()->create(['nguoi_dung_id' => $user->id]);
        $this->actingAs($user)->from('/myTicket')->post('/tickets/'.$ticket->id.'/cancel')
            ->assertRedirect('/myTicket')->assertSessionHas('success');
        $this->assertDatabaseHas('dang_ky', ['id' => $ticket->id, 'trang_thai' => 'da_huy']);
    }

    public function test_user_cannot_cancel_ticket_already_checked_in(): void
    {
        // Check-in tại cửa thường diễn ra trước giờ bắt đầu sự kiện.
        $user = NguoiDung::factory()->create();
        $ticket = DangKy::factory()->create([
            'nguoi_dung_id' => $user->id,
            'trang_thai' => 'da_tham_gia',
        ]);

        $this->actingAs($user)->get('/myTicket')
            ->assertOk()
            ->assertDontSee('btn-cancel-'.$ticket->id, false);

        $this->actingAs($user)->from('/myTicket')->post('/tickets/'.$ticket->id.'/cancel')
            ->assertRedirect('/myTicket')->assertSessionHas('warning');
        $this->assertDatabaseHas('dang_ky', ['id' => $ticket->id, 'trang_thai' => 'da_tham_gia']);
    }

    public function test_user_cannot_cancel_another_users_ticket(): void
    {
        $ticket = DangKy::factory()->create();
        $otherUser = NguoiDung::factory()->create();
        $this->actingAs($otherUser)->from('/myTicket')->post('/tickets/'.$ticket->id.'/cancel')
            ->assertRedirect('/myTicket')->assertSessionHas('warning');
        $this->assertDatabaseHas('dang_ky', ['id' => $ticket->id, 'trang_thai' => 'da_xac_nhan']);
    }

    public function test_toggle_favorite_adds_and_removes_record(): void
    {
        $user = NguoiDung::factory()->create();
        $event = SuKien::factory()->create();
        $this->actingAs($user)->from('/')->post('/favorite/toggle/'.$event->id)->assertRedirect('/');
        $this->assertDatabaseHas('yeu_thich', ['nguoi_dung_id' => $user->id, 'su_kien_id' => $event->id]);
        $this->actingAs($user)->from('/')->post('/favorite/toggle/'.$event->id)->assertRedirect('/');
        $this->assertDatabaseMissing('yeu_thich', ['nguoi_dung_id' => $user->id, 'su_kien_id' => $event->id]);
    }

    public function test_book_ticket_creates_record(): void
    {
        $user = NguoiDung::factory()->create();
        $event = SuKien::factory()->create();
        $this->actingAs($user)->post('/event/'.$event->id.'/book')->assertRedirect('/myTicket');
        $this->assertDatabaseHas('dang_ky', ['nguoi_dung_id' => $user->id, 'su_kien_id' => $event->id, 'trang_thai' => 'da_xac_nhan']);
    }
}
