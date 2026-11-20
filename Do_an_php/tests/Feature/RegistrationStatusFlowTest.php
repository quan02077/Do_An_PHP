<?php

namespace Tests\Feature;

use App\Models\DangKy;
use App\Models\NguoiDung;
use App\Models\SuKien;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/** A3: Admin chỉ được chuyển trạng thái vé theo luồng hợp lệ. */
class RegistrationStatusFlowTest extends TestCase
{
    private function changeStatus(DangKy $ticket, string $status)
    {
        $admin = NguoiDung::factory()->create(['vai_tro' => 'admin']);

        return $this->actingAs($admin)->from('/admin?tab=registrations')
            ->put('/admin/registrations/'.$ticket->id.'/status', ['trang_thai' => $status]);
    }

    public static function allowedTransitions(): array
    {
        return [
            'chờ duyệt → đã xác nhận' => ['cho_duyet', 'da_xac_nhan'],
            'chờ duyệt → đã hủy' => ['cho_duyet', 'da_huy'],
            'đã xác nhận → đã tham gia' => ['da_xac_nhan', 'da_tham_gia'],
            'đã xác nhận → đã hủy' => ['da_xac_nhan', 'da_huy'],
            'đã hủy → đã xác nhận (còn chỗ)' => ['da_huy', 'da_xac_nhan'],
        ];
    }

    public static function forbiddenTransitions(): array
    {
        return [
            'đã hủy → đã tham gia' => ['da_huy', 'da_tham_gia'],
            'đã hủy → chờ duyệt' => ['da_huy', 'cho_duyet'],
            'đã tham gia → đã hủy' => ['da_tham_gia', 'da_huy'],
            'đã tham gia → chờ duyệt' => ['da_tham_gia', 'cho_duyet'],
            'đã tham gia → đã xác nhận' => ['da_tham_gia', 'da_xac_nhan'],
            'đã xác nhận → chờ duyệt' => ['da_xac_nhan', 'cho_duyet'],
            'chờ duyệt → đã tham gia' => ['cho_duyet', 'da_tham_gia'],
        ];
    }

    #[DataProvider('allowedTransitions')]
    public function test_admin_can_apply_allowed_transition(string $from, string $to): void
    {
        $ticket = DangKy::factory()->create(['trang_thai' => $from]);

        $this->changeStatus($ticket, $to)->assertSessionHas('success');
        $this->assertDatabaseHas('dang_ky', ['id' => $ticket->id, 'trang_thai' => $to]);
    }

    #[DataProvider('forbiddenTransitions')]
    public function test_admin_cannot_apply_forbidden_transition(string $from, string $to): void
    {
        $ticket = DangKy::factory()->create(['trang_thai' => $from]);

        $this->changeStatus($ticket, $to)->assertSessionHas('warning');
        $this->assertDatabaseHas('dang_ky', ['id' => $ticket->id, 'trang_thai' => $from]);
    }

    public function test_cannot_restore_cancelled_ticket_after_event_started(): void
    {
        $event = SuKien::factory()->create(['thoi_gian_bat_dau' => now()->subHour()]);
        $ticket = DangKy::factory()->create(['su_kien_id' => $event->id, 'trang_thai' => 'da_huy']);

        $this->changeStatus($ticket, 'da_xac_nhan')->assertSessionHas('warning');
        $this->assertDatabaseHas('dang_ky', ['id' => $ticket->id, 'trang_thai' => 'da_huy']);
    }

    public function test_check_in_page_loads_pinned_qr_library_with_integrity(): void
    {
        $admin = NguoiDung::factory()->create(['vai_tro' => 'admin']);

        $html = $this->actingAs($admin)->get('/admin?tab=registrations')->assertOk()->getContent();

        // Không ghim phiên bản thì CDN có thể đổi bản bất ngờ giữa buổi demo.
        $this->assertMatchesRegularExpression(
            '#<script src="https://unpkg\.com/html5-qrcode@\d+\.\d+\.\d+/html5-qrcode\.min\.js"\s+integrity="sha384-[A-Za-z0-9+/=]{64}"\s+crossorigin="anonymous"#',
            $html
        );
        $this->assertStringNotContainsString('src="https://unpkg.com/html5-qrcode"', $html);
    }

    public function test_registrations_tab_shows_only_valid_actions(): void
    {
        $admin = NguoiDung::factory()->create(['vai_tro' => 'admin']);
        $checkedIn = DangKy::factory()->create(['trang_thai' => 'da_tham_gia']);

        $html = $this->actingAs($admin)->get('/admin?tab=registrations')->assertOk()->getContent();

        // Vé đã tham gia là trạng thái cuối: không còn form đổi trạng thái nào cho vé này.
        $this->assertStringNotContainsString(route('admin.registrations.status', $checkedIn->id), $html);
    }
}
