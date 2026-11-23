<?php

namespace Tests\Feature;

use App\Models\DanhMuc;
use App\Models\NguoiDung;
use App\Models\SuKien;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AiEventDraftTest extends TestCase
{
    use RefreshDatabase;

    private function createAdmin(): NguoiDung
    {
        return NguoiDung::factory()->create(['vai_tro' => 'admin']);
    }

    private function fakeGroqSuccess(array $draftPayload): void
    {
        Http::fake([
            'https://api.groq.com/*' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'content' => json_encode($draftPayload, JSON_UNESCAPED_UNICODE),
                        ],
                    ],
                ],
            ], 200),
        ]);
    }

    public function test_guest_is_blocked_and_no_http_request_is_sent(): void
    {
        config(['services.groq.key' => 'test-key']);
        Http::fake();

        $response = $this->postJson(route('admin.event.ai-draft'), [
            'mo_ta_nhanh' => 'Hội thảo công nghệ AI cho sinh viên tại Hội trường A',
        ]);

        $this->assertTrue(in_array($response->getStatusCode(), [302, 401, 403], true));
        Http::assertNothingSent();
    }

    public function test_regular_user_is_blocked_and_no_http_request_is_sent(): void
    {
        config(['services.groq.key' => 'test-key']);
        Http::fake();

        $user = NguoiDung::factory()->create(['vai_tro' => 'user']);

        $response = $this->actingAs($user)->postJson(route('admin.event.ai-draft'), [
            'mo_ta_nhanh' => 'Hội thảo công nghệ AI cho sinh viên tại Hội trường A',
        ]);

        $this->assertTrue(in_array($response->getStatusCode(), [302, 403], true));
        Http::assertNothingSent();
    }

    public function test_admin_gets_valid_draft_and_database_is_unchanged(): void
    {
        config(['services.groq.key' => 'test-key']);

        $admin = $this->createAdmin();
        $category = DanhMuc::factory()->create(['ten_danh_muc' => 'Công nghệ']);
        $start = now('Asia/Ho_Chi_Minh')->addDays(5)->setTime(9, 0)->format('Y-m-d\TH:i');
        $end = now('Asia/Ho_Chi_Minh')->addDays(5)->setTime(11, 30)->format('Y-m-d\TH:i');

        $this->fakeGroqSuccess([
            'ten_su_kien' => 'Hội thảo AI 2026',
            'mo_ta' => 'Hội thảo chia sẻ kiến thức thực tế về AI. Phù hợp cho sinh viên công nghệ.',
            'danh_muc_id' => $category->id,
            'thoi_gian_bat_dau' => $start,
            'thoi_gian_ket_thuc' => $end,
            'dia_diem' => 'Hội trường A',
            'ban_to_chuc' => 'CLB Tin học',
            'so_luong_toi_da' => 200,
            'gia_ve' => 50000,
        ]);

        $beforeCount = SuKien::count();

        $response = $this->actingAs($admin)->postJson(route('admin.event.ai-draft'), [
            'mo_ta_nhanh' => 'Hội thảo AI 200 chỗ, 9h sáng 15/12 đến 11h30 ở hội trường A, vé 50 nghìn, do CLB Tin học tổ chức',
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'data' => [
                    'ten_su_kien' => 'Hội thảo AI 2026',
                    'mo_ta' => 'Hội thảo chia sẻ kiến thức thực tế về AI. Phù hợp cho sinh viên công nghệ.',
                    'danh_muc_id' => $category->id,
                    'thoi_gian_bat_dau' => $start,
                    'thoi_gian_ket_thuc' => $end,
                    'dia_diem' => 'Hội trường A',
                    'ban_to_chuc' => 'CLB Tin học',
                    'so_luong_toi_da' => 200,
                    'gia_ve' => 50000,
                ],
            ]);

        $this->assertSame($beforeCount, SuKien::count());
    }

    public function test_invalid_ai_values_become_null_and_unknown_keys_are_stripped(): void
    {
        config(['services.groq.key' => 'test-key']);

        $admin = $this->createAdmin();
        DanhMuc::factory()->create();

        $this->fakeGroqSuccess([
            'ten_su_kien' => 'Workshop Kiểm thử',
            'mo_ta' => 'Nội dung workshop kiểm thử tự động cho sinh viên.',
            'danh_muc_id' => 999999,
            'thoi_gian_bat_dau' => now('Asia/Ho_Chi_Minh')->subDays(2)->format('Y-m-d\TH:i'),
            'thoi_gian_ket_thuc' => now('Asia/Ho_Chi_Minh')->subDay()->format('Y-m-d\TH:i'),
            'dia_diem' => 'Phòng B201',
            'ban_to_chuc' => null,
            'so_luong_toi_da' => -50,
            'gia_ve' => -10000,
            'khoa_la' => 'khong-duoc-phep',
            'trang_thai' => 'cong_khai',
        ]);

        $response = $this->actingAs($admin)->postJson(route('admin.event.ai-draft'), [
            'mo_ta_nhanh' => 'Workshop kiểm thử phần mềm tại Phòng B201',
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'data' => [
                    'ten_su_kien' => 'Workshop Kiểm thử',
                    'mo_ta' => 'Nội dung workshop kiểm thử tự động cho sinh viên.',
                    'danh_muc_id' => null,
                    'thoi_gian_bat_dau' => null,
                    'thoi_gian_ket_thuc' => null,
                    'dia_diem' => 'Phòng B201',
                    'ban_to_chuc' => null,
                    'so_luong_toi_da' => null,
                    'gia_ve' => null,
                ],
            ])
            ->assertJsonMissingPath('data.khoa_la')
            ->assertJsonMissingPath('data.trang_thai');
    }

    public function test_non_json_ai_response_returns_422_with_vietnamese_message(): void
    {
        config(['services.groq.key' => 'test-key']);

        $admin = $this->createAdmin();

        Http::fake([
            'https://api.groq.com/*' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'content' => 'Xin lỗi, tôi không thể tạo JSON lúc này.',
                        ],
                    ],
                ],
            ], 200),
        ]);

        $response = $this->actingAs($admin)->postJson(route('admin.event.ai-draft'), [
            'mo_ta_nhanh' => 'Ngày hội việc làm sinh viên công nghệ thông tin',
        ]);

        $response->assertStatus(422)
            ->assertJson(['success' => false]);
        $this->assertStringContainsString('JSON', (string) $response->json('message'));
    }

    public function test_groq_429_and_500_return_handled_vietnamese_errors_without_app_500(): void
    {
        config(['services.groq.key' => 'test-key']);

        $admin = $this->createAdmin();

        Http::fake([
            'https://api.groq.com/*' => Http::sequence()
                ->push(['error' => ['message' => 'Rate limit reached']], 429)
                ->push(['error' => ['message' => 'Internal server error']], 500),
        ]);

        $rateLimited = $this->actingAs($admin)->postJson(route('admin.event.ai-draft'), [
            'mo_ta_nhanh' => 'Đêm nhạc sinh viên cuối năm tại sân trường',
        ]);
        $rateLimited->assertStatus(429)
            ->assertJson(['success' => false]);
        $this->assertNotSame(500, $rateLimited->getStatusCode());
        $this->assertNotEmpty($rateLimited->json('message'));

        $upstreamError = $this->actingAs($admin)->postJson(route('admin.event.ai-draft'), [
            'mo_ta_nhanh' => 'Đêm nhạc sinh viên cuối năm tại sân trường',
        ]);
        $upstreamError->assertStatus(503)
            ->assertJson(['success' => false]);
        $this->assertNotSame(500, $upstreamError->getStatusCode());
        $this->assertNotEmpty($upstreamError->json('message'));
    }

    public function test_missing_groq_api_key_returns_configuration_message_and_sends_nothing(): void
    {
        config(['services.groq.key' => '']);
        Http::fake();

        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->postJson(route('admin.event.ai-draft'), [
            'mo_ta_nhanh' => 'Hội thảo khởi nghiệp sáng tạo dành cho sinh viên',
        ]);

        $response->assertStatus(503)
            ->assertJson(['success' => false]);
        $this->assertStringContainsString('Chưa cấu hình', (string) $response->json('message'));
        Http::assertNothingSent();
    }

    public function test_empty_or_too_short_prompt_returns_422_validation_error(): void
    {
        config(['services.groq.key' => 'test-key']);
        Http::fake();

        $admin = $this->createAdmin();

        $this->actingAs($admin)->postJson(route('admin.event.ai-draft'), [
            'mo_ta_nhanh' => '',
        ])->assertStatus(422)->assertJsonValidationErrors('mo_ta_nhanh');

        $this->actingAs($admin)->postJson(route('admin.event.ai-draft'), [
            'mo_ta_nhanh' => 'Ngắn',
        ])->assertStatus(422)->assertJsonValidationErrors('mo_ta_nhanh');

        Http::assertNothingSent();
    }

    public function test_outgoing_groq_request_uses_expected_model_schema_and_bearer_header(): void
    {
        config(['services.groq.key' => 'test-key']);

        $admin = $this->createAdmin();
        $category = DanhMuc::factory()->create(['ten_danh_muc' => 'Học thuật']);

        $this->fakeGroqSuccess([
            'ten_su_kien' => 'Cuộc thi Lập trình Olympic',
            'mo_ta' => 'Cuộc thi lập trình dành cho sinh viên toàn trường.',
            'danh_muc_id' => $category->id,
            'thoi_gian_bat_dau' => null,
            'thoi_gian_ket_thuc' => null,
            'dia_diem' => 'Phòng máy C102',
            'ban_to_chuc' => 'Khoa CNTT',
            'so_luong_toi_da' => 100,
            'gia_ve' => 0,
        ]);

        $this->actingAs($admin)->postJson(route('admin.event.ai-draft'), [
            'mo_ta_nhanh' => 'Cuộc thi Lập trình Olympic tại phòng máy C102 cho 100 sinh viên',
        ])->assertOk();

        Http::assertSent(function (Request $request) {
            return $request->url() === 'https://api.groq.com/openai/v1/chat/completions'
                && $request->hasHeader('Authorization', 'Bearer test-key')
                && $request['model'] === 'openai/gpt-oss-120b'
                && data_get($request->data(), 'response_format.type') === 'json_schema';
        });
    }

    public function test_admin_page_contains_ai_draft_button(): void
    {
        $admin = $this->createAdmin();

        $this->actingAs($admin)
            ->get(route('admin.index'))
            ->assertOk()
            ->assertSee('id="ai-draft-btn"', false);
    }
}
