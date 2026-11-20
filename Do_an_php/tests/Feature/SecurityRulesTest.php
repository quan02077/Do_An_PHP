<?php

namespace Tests\Feature;

use App\Models\DangKy;
use App\Models\NguoiDung;
use App\Models\SuKien;
use Tests\TestCase;

class SecurityRulesTest extends TestCase
{
    public function test_guest_cannot_check_in_ticket_through_api(): void
    {
        $ticket = DangKy::factory()->create();
        $this->post('/api/check-in', ['ma_ve' => $ticket->ma_ve])->assertRedirect('/auth');
        $this->assertDatabaseHas('dang_ky', ['id' => $ticket->id, 'trang_thai' => 'da_xac_nhan']);
    }

    public function test_member_cannot_check_in_ticket_through_api(): void
    {
        $ticket = DangKy::factory()->create();
        $member = NguoiDung::factory()->create(['vai_tro' => 'user']);
        $this->actingAs($member)->post('/api/check-in', ['ma_ve' => $ticket->ma_ve])->assertRedirect('/');
        $this->assertDatabaseHas('dang_ky', ['id' => $ticket->id, 'trang_thai' => 'da_xac_nhan']);
    }

    public function test_guest_cannot_view_draft_event_by_id(): void
    {
        $event = SuKien::factory()->create(['trang_thai' => 'nhap']);
        $this->get('/event/'.$event->id)->assertNotFound();
    }

    public function test_admin_can_view_draft_and_check_in_ticket(): void
    {
        $admin = NguoiDung::factory()->create(['vai_tro' => 'admin']);
        $event = SuKien::factory()->create(['trang_thai' => 'nhap']);
        $ticket = DangKy::factory()->create();
        $this->actingAs($admin)->get('/event/'.$event->id)->assertOk();
        $this->actingAs($admin)->post('/api/check-in', ['ma_ve' => $ticket->ma_ve])
            ->assertOk()->assertJsonPath('success', true);
        $this->assertDatabaseHas('dang_ky', ['id' => $ticket->id, 'trang_thai' => 'da_tham_gia']);
    }

    public function test_check_in_requires_ticket_code(): void
    {
        $admin = NguoiDung::factory()->create(['vai_tro' => 'admin']);
        $this->actingAs($admin)->post('/api/check-in', [])->assertSessionHasErrors('ma_ve');
    }

    public function test_full_event_rejects_new_registration(): void
    {
        $event = SuKien::factory()->create(['so_luong_toi_da' => 1]);
        DangKy::factory()->create(['su_kien_id' => $event->id]);
        $member = NguoiDung::factory()->create();
        $this->actingAs($member)->from('/event/'.$event->id)->post('/event/'.$event->id.'/book')
            ->assertRedirect('/event/'.$event->id)->assertSessionHas('warning');
        $this->assertDatabaseMissing('dang_ky', ['nguoi_dung_id' => $member->id, 'su_kien_id' => $event->id]);
    }
}
