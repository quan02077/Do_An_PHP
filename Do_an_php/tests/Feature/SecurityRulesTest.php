<?php

namespace Tests\Feature;

use App\Models\DangKy;
use App\Models\NguoiDung;
use App\Models\SuKien;
use Illuminate\Support\Str;
use Tests\TestCase;

class SecurityRulesTest extends TestCase
{
    public function test_old_check_in_route_is_removed(): void
    {
        $this->post('/api/check-in', ['ma_ve' => 'QQQ-0000-ABCDEF'])->assertNotFound();
    }

    public function test_stale_user_model_in_session_does_not_grant_admin_access(): void
    {
        $admin = NguoiDung::factory()->create(['vai_tro' => 'admin']);
        $this->withSession(['user' => $admin])->get('/admin')->assertRedirect('/auth');
    }

    public function test_logout_requires_post(): void
    {
        $user = NguoiDung::factory()->create();
        $this->actingAs($user)->get('/logout')->assertStatus(405);
        $this->actingAs($user)->post('/logout')->assertRedirect('/');
    }

    public function test_member_cannot_favorite_missing_event(): void
    {
        $member = NguoiDung::factory()->create();
        $this->actingAs($member)->post('/favorite/toggle/999999')->assertNotFound();
    }

    public function test_admin_cannot_book_ticket(): void
    {
        $admin = NguoiDung::factory()->create(['vai_tro' => 'admin']);
        $event = SuKien::factory()->create();
        $this->actingAs($admin)->post('/event/'.$event->id.'/book')->assertRedirect('/admin');
        $this->assertDatabaseMissing('dang_ky', ['nguoi_dung_id' => $admin->id, 'su_kien_id' => $event->id]);
    }

    public function test_admin_cannot_restore_cancelled_ticket_when_full(): void
    {
        $admin = NguoiDung::factory()->create(['vai_tro' => 'admin']);
        $event = SuKien::factory()->create(['so_luong_toi_da' => 1]);
        DangKy::factory()->create(['su_kien_id' => $event->id]);
        $cancelled = DangKy::factory()->create(['su_kien_id' => $event->id, 'trang_thai' => 'da_huy']);
        $this->actingAs($admin)->from('/admin?tab=registrations')
            ->put('/admin/registrations/'.$cancelled->id.'/status', ['trang_thai' => 'da_xac_nhan'])
            ->assertSessionHas('warning');
        $this->assertDatabaseHas('dang_ky', ['id' => $cancelled->id, 'trang_thai' => 'da_huy']);
    }

    public function test_member_cannot_reactivate_cancelled_ticket_when_full(): void
    {
        $event = SuKien::factory()->create(['so_luong_toi_da' => 1]);
        DangKy::factory()->create(['su_kien_id' => $event->id]);
        $member = NguoiDung::factory()->create();
        $cancelled = DangKy::factory()->create([
            'nguoi_dung_id' => $member->id, 'su_kien_id' => $event->id, 'trang_thai' => 'da_huy',
        ]);
        $this->actingAs($member)->from('/event/'.$event->id)
            ->post('/event/'.$event->id.'/book')->assertSessionHas('warning');
        $this->assertDatabaseHas('dang_ky', ['id' => $cancelled->id, 'trang_thai' => 'da_huy']);
    }

    public function test_ticket_codes_remain_unique_when_random_part_repeats(): void
    {
        $event = SuKien::factory()->create();
        $first = NguoiDung::factory()->create();
        $second = NguoiDung::factory()->create();
        Str::createRandomStringsUsing(fn (int $length) => str_repeat('A', $length));
        try {
            $this->actingAs($first)->post('/event/'.$event->id.'/book')->assertRedirect('/myTicket');
            $this->actingAs($second)->post('/event/'.$event->id.'/book')->assertRedirect('/myTicket');
        } finally {
            Str::createRandomStringsNormally();
        }
        $codes = DangKy::where('su_kien_id', $event->id)->pluck('ma_ve');
        $this->assertCount(2, $codes);
        $this->assertSame(2, $codes->unique()->count());
    }

    public function test_chart_labels_do_not_inject_script_tags(): void
    {
        $admin = NguoiDung::factory()->create(['vai_tro' => 'admin']);
        \App\Models\DanhMuc::factory()->create(['ten_danh_muc' => '</script><script>alert(1)</script>']);
        $this->actingAs($admin)->get('/admin')->assertOk()
            ->assertDontSee('</script><script>alert(1)</script>', false);
    }

    public function test_guest_cannot_check_in_ticket_through_api(): void
    {
        $ticket = DangKy::factory()->create();
        $this->post('/admin/check-in', ['ma_ve' => $ticket->ma_ve])->assertRedirect('/auth');
        $this->assertDatabaseHas('dang_ky', ['id' => $ticket->id, 'trang_thai' => 'da_xac_nhan']);
    }

    public function test_member_cannot_check_in_ticket_through_api(): void
    {
        $ticket = DangKy::factory()->create();
        $member = NguoiDung::factory()->create(['vai_tro' => 'user']);
        $this->actingAs($member)->post('/admin/check-in', ['ma_ve' => $ticket->ma_ve])->assertRedirect('/');
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
        $this->actingAs($admin)->post('/admin/check-in', ['ma_ve' => $ticket->ma_ve])
            ->assertOk()->assertJsonPath('success', true);
        $this->assertDatabaseHas('dang_ky', ['id' => $ticket->id, 'trang_thai' => 'da_tham_gia']);
    }

    public function test_check_in_requires_ticket_code(): void
    {
        $admin = NguoiDung::factory()->create(['vai_tro' => 'admin']);
        $this->actingAs($admin)->post('/admin/check-in', [])->assertSessionHasErrors('ma_ve');
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
