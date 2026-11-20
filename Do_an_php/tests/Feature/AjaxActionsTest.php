<?php

namespace Tests\Feature;

use App\Models\DangKy;
use App\Models\NguoiDung;
use App\Models\SuKien;
use Tests\TestCase;

/** B5: yêu thích và hủy vé trả JSON khi gọi bằng AJAX, vẫn redirect khi tắt JS. */
class AjaxActionsTest extends TestCase
{
    public function test_toggle_favorite_returns_json_and_flips_state(): void
    {
        $user = NguoiDung::factory()->create();
        $event = SuKien::factory()->create();

        $this->actingAs($user)->postJson('/favorite/toggle/'.$event->id)
            ->assertOk()->assertJson(['success' => true, 'favorited' => true]);
        $this->assertDatabaseHas('yeu_thich', ['nguoi_dung_id' => $user->id, 'su_kien_id' => $event->id]);

        $this->actingAs($user)->postJson('/favorite/toggle/'.$event->id)
            ->assertOk()->assertJson(['success' => true, 'favorited' => false]);
        $this->assertDatabaseMissing('yeu_thich', ['nguoi_dung_id' => $user->id, 'su_kien_id' => $event->id]);
    }

    public function test_toggle_favorite_json_returns_404_for_draft_or_missing_event(): void
    {
        $user = NguoiDung::factory()->create();
        $draft = SuKien::factory()->create(['trang_thai' => 'nhap']);

        $this->actingAs($user)->postJson('/favorite/toggle/'.$draft->id)
            ->assertNotFound()->assertJson(['success' => false]);
        $this->actingAs($user)->postJson('/favorite/toggle/999999')
            ->assertNotFound()->assertJson(['success' => false]);
        $this->assertDatabaseCount('yeu_thich', 0);
    }

    public function test_toggle_favorite_without_js_still_redirects(): void
    {
        $user = NguoiDung::factory()->create();
        $event = SuKien::factory()->create();

        $this->actingAs($user)->from('/')->post('/favorite/toggle/'.$event->id)
            ->assertRedirect('/')->assertSessionHas('success');
    }

    public function test_cancel_ticket_returns_json(): void
    {
        $user = NguoiDung::factory()->create();
        $ticket = DangKy::factory()->create(['nguoi_dung_id' => $user->id]);

        $this->actingAs($user)->postJson('/tickets/'.$ticket->id.'/cancel')
            ->assertOk()->assertJson(['success' => true, 'status' => 'da_huy', 'ticket_id' => $ticket->id]);
        $this->assertDatabaseHas('dang_ky', ['id' => $ticket->id, 'trang_thai' => 'da_huy']);
    }

    public function test_cancel_ticket_json_rejects_checked_in_ticket(): void
    {
        $user = NguoiDung::factory()->create();
        $ticket = DangKy::factory()->create(['nguoi_dung_id' => $user->id, 'trang_thai' => 'da_tham_gia']);

        $this->actingAs($user)->postJson('/tickets/'.$ticket->id.'/cancel')
            ->assertStatus(422)->assertJson(['success' => false]);
        $this->assertDatabaseHas('dang_ky', ['id' => $ticket->id, 'trang_thai' => 'da_tham_gia']);
    }

    public function test_cancel_ticket_json_returns_404_for_other_users_ticket(): void
    {
        $ticket = DangKy::factory()->create();
        $other = NguoiDung::factory()->create();

        $this->actingAs($other)->postJson('/tickets/'.$ticket->id.'/cancel')
            ->assertNotFound()->assertJson(['success' => false]);
        $this->assertDatabaseHas('dang_ky', ['id' => $ticket->id, 'trang_thai' => 'da_xac_nhan']);
    }

    public function test_guest_ajax_request_is_not_processed(): void
    {
        $event = SuKien::factory()->create();

        // Middleware auth.custom chặn trước controller (302 về trang đăng nhập); JS xử lý bằng response.redirected.
        $this->postJson('/favorite/toggle/'.$event->id)->assertRedirect(route('Auth.index'));
        $this->assertDatabaseCount('yeu_thich', 0);
    }

    public function test_pages_load_ajax_script_and_markers(): void
    {
        $user = NguoiDung::factory()->create();
        $event = SuKien::factory()->create();
        DangKy::factory()->create(['nguoi_dung_id' => $user->id, 'su_kien_id' => $event->id]);

        $this->actingAs($user)->get('/myTicket')->assertOk()
            ->assertSee('data-ajax-cancel', false)->assertSee('qqq-toast-container', false);
        $this->actingAs($user)->get('/event/'.$event->id)->assertOk()
            ->assertSee('data-ajax-favorite', false);
    }
}
