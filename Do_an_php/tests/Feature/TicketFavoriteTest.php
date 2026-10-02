<?php

namespace Tests\Feature;

use App\Models\DangKy;
use App\Models\NguoiDung;
use App\Models\SuKien;
use App\Models\YeuThich;
use Tests\TestCase;

class TicketFavoriteTest extends TestCase
{
    public function test_my_ticket_page_loads_and_displays_user_tickets(): void
    {
        $user = NguoiDung::find(2);
        $response = $this->actingAs($user)->get('/myTicket');

        $response->assertStatus(200);
        $response->assertSee('Vé đã đăng ký của bạn');
        $response->assertSee($user->ho_ten);
    }

    public function test_favorite_page_loads_and_displays_user_favorites(): void
    {
        $user = NguoiDung::find(2);
        $response = $this->actingAs($user)->get('/favorite');

        $response->assertStatus(200);
        $response->assertSee('Sự kiện yêu thích');
        $response->assertSee($user->ho_ten);
    }

    public function test_cancel_ticket_updates_database_for_user(): void
    {
        $user = NguoiDung::find(2);
        $ticket = DangKy::where('nguoi_dung_id', $user->id)->first();
        $this->assertNotNull($ticket);

        $response = $this->actingAs($user)->postJson('/tickets/' . $ticket->id . '/cancel');

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'status' => 'da_huy',
        ]);

        $this->assertDatabaseHas('dang_ky', [
            'id' => $ticket->id,
            'trang_thai' => 'da_huy',
        ]);
    }

    public function test_toggle_favorite_updates_database_for_user(): void
    {
        $user = NguoiDung::find(2);
        $eventId = 7; // event not in favorites yet

        $response = $this->actingAs($user)->postJson('/favorite/toggle/' . $eventId);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'is_favorited' => true,
        ]);

        $this->assertDatabaseHas('yeu_thich', [
            'nguoi_dung_id' => $user->id,
            'su_kien_id' => $eventId,
        ]);

        // Toggle again to remove
        $response2 = $this->actingAs($user)->postJson('/favorite/toggle/' . $eventId);
        $response2->assertStatus(200);
        $response2->assertJson([
            'success' => true,
            'is_favorited' => false,
        ]);

        $this->assertDatabaseMissing('yeu_thich', [
            'nguoi_dung_id' => $user->id,
            'su_kien_id' => $eventId,
        ]);
    }

    public function test_book_ticket_creates_record_in_database(): void
    {
        $user = NguoiDung::find(3);
        $eventId = 6;

        $response = $this->actingAs($user)->post('/event/' . $eventId . '/book');

        $response->assertRedirect('/myTicket');

        $this->assertDatabaseHas('dang_ky', [
            'nguoi_dung_id' => $user->id,
            'su_kien_id' => $eventId,
            'trang_thai' => 'da_xac_nhan',
        ]);
    }
}
