<?php

namespace Tests\Feature;

use App\Models\DangKy;
use App\Models\DanhGia;
use App\Models\NguoiDung;
use App\Models\SuKien;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventReviewTest extends TestCase
{
    use RefreshDatabase;

    private function createEndedEvent(array $overrides = []): SuKien
    {
        return SuKien::factory()->create(array_merge([
            'trang_thai' => 'cong_khai',
            'thoi_gian_bat_dau' => now()->subDays(2),
            'thoi_gian_ket_thuc' => now()->subDay(),
        ], $overrides));
    }

    public function test_attended_user_of_ended_event_can_create_review(): void
    {
        $user = NguoiDung::factory()->create(['vai_tro' => 'user', 'trang_thai' => 'hoat_dong']);
        $event = $this->createEndedEvent();
        DangKy::factory()->create([
            'nguoi_dung_id' => $user->id,
            'su_kien_id' => $event->id,
            'trang_thai' => 'da_tham_gia',
        ]);

        $this->actingAs($user)
            ->post(route('Reviews.store', $event->id), [
                'so_sao' => 5,
                'noi_dung' => 'Sự kiện tổ chức rất chu đáo và bổ ích.',
            ])
            ->assertRedirect(route('Event.show', $event->id))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('danh_gia', [
            'nguoi_dung_id' => $user->id,
            'su_kien_id' => $event->id,
            'so_sao' => 5,
            'noi_dung' => 'Sự kiện tổ chức rất chu đáo và bổ ích.',
            'trang_thai' => 'hien',
        ]);
    }

    public function test_resubmitting_review_updates_existing_record_instead_of_duplicating(): void
    {
        $user = NguoiDung::factory()->create(['vai_tro' => 'user', 'trang_thai' => 'hoat_dong']);
        $event = $this->createEndedEvent();
        DangKy::factory()->create([
            'nguoi_dung_id' => $user->id,
            'su_kien_id' => $event->id,
            'trang_thai' => 'da_tham_gia',
        ]);

        $this->actingAs($user)->post(route('Reviews.store', $event->id), [
            'so_sao' => 4,
            'noi_dung' => 'Đánh giá ban đầu',
        ])->assertRedirect(route('Event.show', $event->id));

        $this->actingAs($user)->post(route('Reviews.store', $event->id), [
            'so_sao' => 5,
            'noi_dung' => 'Đánh giá sau khi chỉnh sửa',
        ])->assertRedirect(route('Event.show', $event->id));

        $this->assertSame(1, DanhGia::where('nguoi_dung_id', $user->id)->where('su_kien_id', $event->id)->count());
        $this->assertDatabaseHas('danh_gia', [
            'nguoi_dung_id' => $user->id,
            'su_kien_id' => $event->id,
            'so_sao' => 5,
            'noi_dung' => 'Đánh giá sau khi chỉnh sửa',
        ]);
    }

    public function test_review_is_rejected_if_event_has_not_ended(): void
    {
        $user = NguoiDung::factory()->create(['vai_tro' => 'user', 'trang_thai' => 'hoat_dong']);
        $ongoingEvent = SuKien::factory()->create([
            'trang_thai' => 'cong_khai',
            'thoi_gian_bat_dau' => now()->subHour(),
            'thoi_gian_ket_thuc' => now()->addHours(2),
        ]);
        DangKy::factory()->create([
            'nguoi_dung_id' => $user->id,
            'su_kien_id' => $ongoingEvent->id,
            'trang_thai' => 'da_tham_gia',
        ]);

        $this->actingAs($user)
            ->from(route('Event.show', $ongoingEvent->id))
            ->post(route('Reviews.store', $ongoingEvent->id), ['so_sao' => 5, 'noi_dung' => 'Chưa kết thúc'])
            ->assertRedirect()
            ->assertSessionHas('warning');

        $this->assertSame(0, DanhGia::count());
    }

    public function test_unattended_cancelled_or_ticketless_user_cannot_review(): void
    {
        $event = $this->createEndedEvent();

        foreach (['da_xac_nhan', 'da_huy', null] as $ticketStatus) {
            $user = NguoiDung::factory()->create(['vai_tro' => 'user', 'trang_thai' => 'hoat_dong']);
            if ($ticketStatus !== null) {
                DangKy::factory()->create([
                    'nguoi_dung_id' => $user->id,
                    'su_kien_id' => $event->id,
                    'trang_thai' => $ticketStatus,
                ]);
            }

            $this->actingAs($user)
                ->from(route('Event.show', $event->id))
                ->post(route('Reviews.store', $event->id), ['so_sao' => 4, 'noi_dung' => 'Thử đánh giá'])
                ->assertRedirect()
                ->assertSessionHas('warning');
        }

        $this->assertSame(0, DanhGia::count());
    }

    public function test_admin_guest_and_locked_user_cannot_review(): void
    {
        $event = $this->createEndedEvent();

        // Khách chưa đăng nhập
        $this->post(route('Reviews.store', $event->id), ['so_sao' => 5])
            ->assertRedirect(route('Auth.index'));

        // Admin
        $admin = NguoiDung::factory()->create(['vai_tro' => 'admin', 'trang_thai' => 'hoat_dong']);
        DangKy::factory()->create([
            'nguoi_dung_id' => $admin->id,
            'su_kien_id' => $event->id,
            'trang_thai' => 'da_tham_gia',
        ]);
        $this->actingAs($admin)
            ->from(route('Event.show', $event->id))
            ->post(route('Reviews.store', $event->id), ['so_sao' => 5])
            ->assertSessionHas('warning');

        // Tài khoản bị khóa
        $locked = NguoiDung::factory()->create(['vai_tro' => 'user', 'trang_thai' => 'bi_khoa']);
        DangKy::factory()->create([
            'nguoi_dung_id' => $locked->id,
            'su_kien_id' => $event->id,
            'trang_thai' => 'da_tham_gia',
        ]);
        $this->actingAs($locked)
            ->post(route('Reviews.store', $event->id), ['so_sao' => 5])
            ->assertRedirect(route('Auth.index'));

        $this->assertSame(0, DanhGia::count());
    }

    public function test_invalid_star_ratings_fail_validation(): void
    {
        $user = NguoiDung::factory()->create(['vai_tro' => 'user', 'trang_thai' => 'hoat_dong']);
        $event = $this->createEndedEvent();
        DangKy::factory()->create([
            'nguoi_dung_id' => $user->id,
            'su_kien_id' => $event->id,
            'trang_thai' => 'da_tham_gia',
        ]);

        foreach ([0, 6, 'abc'] as $invalidStar) {
            $this->actingAs($user)
                ->from(route('Event.show', $event->id))
                ->post(route('Reviews.store', $event->id), ['so_sao' => $invalidStar])
                ->assertSessionHasErrors('so_sao');
        }

        $this->assertSame(0, DanhGia::count());
    }

    public function test_other_user_cannot_delete_someones_review(): void
    {
        $owner = NguoiDung::factory()->create(['vai_tro' => 'user', 'trang_thai' => 'hoat_dong']);
        $other = NguoiDung::factory()->create(['vai_tro' => 'user', 'trang_thai' => 'hoat_dong']);
        $event = $this->createEndedEvent();

        $review = DanhGia::factory()->create([
            'nguoi_dung_id' => $owner->id,
            'su_kien_id' => $event->id,
            'so_sao' => 5,
        ]);

        $this->actingAs($other)
            ->delete(route('Reviews.destroy', $review->id))
            ->assertForbidden();

        $this->assertDatabaseHas('danh_gia', ['id' => $review->id]);
    }

    public function test_admin_can_hide_review_excluding_it_from_detail_and_average(): void
    {
        $admin = NguoiDung::factory()->create(['vai_tro' => 'admin', 'trang_thai' => 'hoat_dong']);
        $event = $this->createEndedEvent();

        $r1 = DanhGia::factory()->create([
            'su_kien_id' => $event->id,
            'so_sao' => 5,
            'noi_dung' => 'Đánh giá tốt được giữ lại',
            'trang_thai' => 'hien',
        ]);
        $r2 = DanhGia::factory()->create([
            'su_kien_id' => $event->id,
            'so_sao' => 1,
            'noi_dung' => 'Nội dung spam cần bị ẩn',
            'trang_thai' => 'hien',
        ]);

        $this->actingAs($admin)
            ->put(route('admin.reviews.visibility', $r2->id))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertSame('an', $r2->fresh()->trang_thai);

        $this->get(route('Event.show', $event->id))
            ->assertOk()
            ->assertSee('Đánh giá tốt được giữ lại')
            ->assertDontSee('Nội dung spam cần bị ẩn')
            ->assertSee('5.0 / 5')
            ->assertSee('(1 đánh giá)');

        $this->assertSame('hien', $r1->fresh()->trang_thai);
    }

    public function test_event_detail_displays_accurate_average_rating(): void
    {
        $event = $this->createEndedEvent();

        DanhGia::factory()->create(['su_kien_id' => $event->id, 'so_sao' => 4, 'trang_thai' => 'hien']);
        DanhGia::factory()->create(['su_kien_id' => $event->id, 'so_sao' => 5, 'trang_thai' => 'hien']);

        $this->get(route('Event.show', $event->id))
            ->assertOk()
            ->assertSee('4.5 / 5')
            ->assertSee('(2 đánh giá)');
    }

    public function test_review_content_with_script_tag_is_escaped(): void
    {
        $event = $this->createEndedEvent();
        $xssPayload = '<script>alert("xss")</script>';

        DanhGia::factory()->create([
            'su_kien_id' => $event->id,
            'so_sao' => 4,
            'noi_dung' => $xssPayload,
            'trang_thai' => 'hien',
        ]);

        $this->get(route('Event.show', $event->id))
            ->assertOk()
            ->assertDontSee($xssPayload, false)
            ->assertSee('&lt;script&gt;alert(&quot;xss&quot;)&lt;/script&gt;', false);
    }
}
