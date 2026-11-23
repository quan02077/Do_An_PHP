<?php

namespace App\Services;

use App\Exceptions\AiDraftException;
use App\Models\DanhMuc;
use Carbon\Carbon;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiEventDraftService
{
    /**
     * Gọi Groq Chat Completions để soạn nháp thông tin sự kiện từ mô tả ngắn.
     * Không ghi vào CSDL; mọi trường trả về đều được làm sạch lại ở server.
     *
     * @param  Collection<int, DanhMuc>  $danhMucs
     * @return array<string, mixed>
     */
    public function draft(string $moTa, Collection $danhMucs): array
    {
        $apiKey = trim((string) config('services.groq.key', ''));
        if ($apiKey === '') {
            throw new AiDraftException('Chưa cấu hình GROQ_API_KEY để sử dụng tính năng soạn bằng AI.', 503);
        }

        $timezone = 'Asia/Ho_Chi_Minh';
        $now = Carbon::now($timezone);

        $categoryLines = $danhMucs
            ->map(fn (DanhMuc $cat) => "{$cat->id}: {$cat->ten_danh_muc}")
            ->implode("\n");

        $systemPrompt = implode("\n", [
            'Bạn là trợ lý soạn nháp thông tin sự kiện bằng tiếng Việt cho quản trị viên.',
            'Ngày giờ hiện tại (múi giờ Asia/Ho_Chi_Minh): ' . $now->format('Y-m-d\TH:i') . ' (thứ ' . $now->dayOfWeekIso . ').',
            'Hãy phân tích câu mô tả nhanh của quản trị viên (kể cả mốc thời gian tương đối như "thứ bảy tuần sau", "15/12") và trả về duy nhất một đối tượng JSON khớp đúng schema.',
            'Danh sách danh mục hiện có (định dạng id: tên):',
            $categoryLines !== '' ? $categoryLines : '(Chưa có danh mục)',
            'Quy tắc bắt buộc:',
            '- Chỉ trả về JSON đúng schema, không thêm trường thừa và không giải thích ngoài JSON.',
            '- Không bịa thông tin không có trong câu mô tả: trường nào không suy ra được hãy để null.',
            '- Chọn danh_muc_id đúng theo danh sách trên; nếu không khớp danh mục nào hãy để null.',
            '- thoi_gian_bat_dau và thoi_gian_ket_thuc phải theo định dạng Y-m-d\\TH:i (ví dụ 2026-12-15T09:00), không ở quá khứ.',
            '- gia_ve là số VNĐ (>= 0, miễn phí = 0); so_luong_toi_da là số nguyên dương (>= 1).',
            '- mo_ta: viết 2–4 câu giới thiệu ngắn gọn, mạch lạc dựa trên câu mô tả của quản trị viên.',
        ]);

        $baseUrl = rtrim((string) config('services.groq.url', 'https://api.groq.com/openai/v1'), '/');
        $model = (string) config('services.groq.model', 'openai/gpt-oss-120b');
        $timeout = (int) config('services.groq.timeout', 20);

        try {
            $response = Http::withToken($apiKey)
                ->timeout($timeout)
                ->post($baseUrl . '/chat/completions', [
                    'model' => $model,
                    'temperature' => 0.2,
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => trim($moTa)],
                    ],
                    'response_format' => [
                        'type' => 'json_schema',
                        'json_schema' => [
                            'name' => 'event_draft',
                            'strict' => true,
                            'schema' => [
                                'type' => 'object',
                                'additionalProperties' => false,
                                'properties' => [
                                    'ten_su_kien' => ['type' => ['string', 'null']],
                                    'mo_ta' => ['type' => ['string', 'null']],
                                    'danh_muc_id' => ['type' => ['integer', 'null']],
                                    'thoi_gian_bat_dau' => ['type' => ['string', 'null']],
                                    'thoi_gian_ket_thuc' => ['type' => ['string', 'null']],
                                    'dia_diem' => ['type' => ['string', 'null']],
                                    'ban_to_chuc' => ['type' => ['string', 'null']],
                                    'so_luong_toi_da' => ['type' => ['integer', 'null']],
                                    'gia_ve' => ['type' => ['number', 'null']],
                                ],
                                'required' => [
                                    'ten_su_kien',
                                    'mo_ta',
                                    'danh_muc_id',
                                    'thoi_gian_bat_dau',
                                    'thoi_gian_ket_thuc',
                                    'dia_diem',
                                    'ban_to_chuc',
                                    'so_luong_toi_da',
                                    'gia_ve',
                                ],
                            ],
                        ],
                    ],
                ]);
        } catch (ConnectionException $e) {
            Log::warning('AI event draft connection failed', [
                'model' => $model,
                'reason' => $e->getMessage(),
            ]);

            throw new AiDraftException('Không kết nối được dịch vụ AI, vui lòng thử lại sau.', 503);
        }

        if ($response->status() === 429) {
            Log::warning('AI event draft rate limited', [
                'model' => $model,
                'status' => 429,
            ]);

            throw new AiDraftException('AI đang quá tải, vui lòng thử lại sau.', 429);
        }

        if (!$response->successful()) {
            Log::warning('AI event draft upstream error', [
                'model' => $model,
                'status' => $response->status(),
                'error_code' => data_get($response->json(), 'error.code'),
            ]);

            throw new AiDraftException('Không kết nối được dịch vụ AI, vui lòng thử lại sau.', 503);
        }

        $content = data_get($response->json(), 'choices.0.message.content');
        $decoded = $this->decodeJsonContent($content);

        if ($decoded === null) {
            Log::warning('AI event draft returned invalid JSON', [
                'model' => $model,
            ]);

            throw new AiDraftException('AI trả về dữ liệu không phải JSON hợp lệ, vui lòng thử lại.', 422);
        }

        $validCategoryIds = DanhMuc::query()->pluck('id')->map(fn ($id) => (int) $id)->all();

        return $this->sanitize($decoded, $validCategoryIds, $now, $timezone);
    }

    /**
     * Decode JSON trực tiếp; nếu lỗi thì thử trích khối {...} đầu tiên rồi decode lại.
     *
     * @return array<string, mixed>|null
     */
    protected function decodeJsonContent(mixed $content): ?array
    {
        if (!is_string($content) || trim($content) === '') {
            return null;
        }

        $decoded = json_decode($content, true);
        if (is_array($decoded) && json_last_error() === JSON_ERROR_NONE) {
            return $decoded;
        }

        if (preg_match('/\{[\s\S]*\}/', $content, $matches)) {
            $fallback = json_decode($matches[0], true);
            if (is_array($fallback) && json_last_error() === JSON_ERROR_NONE) {
                return $fallback;
            }
        }

        return null;
    }

    /**
     * Làm sạch kết quả AI: chỉ giữ 9 khóa cho phép và kiểm tra lại toàn bộ ràng buộc nghiệp vụ.
     *
     * @param  array<string, mixed>  $raw
     * @param  array<int, int>  $validCategoryIds
     * @return array<string, mixed>
     */
    protected function sanitize(array $raw, array $validCategoryIds, Carbon $now, string $timezone): array
    {
        $categoryId = $this->sanitizePositiveInt($raw['danh_muc_id'] ?? null);
        if ($categoryId !== null && !in_array($categoryId, $validCategoryIds, true)) {
            $categoryId = null;
        }

        $start = $this->parseFutureDatetime($raw['thoi_gian_bat_dau'] ?? null, $now, $timezone);
        $end = $this->parseFutureDatetime($raw['thoi_gian_ket_thuc'] ?? null, $now, $timezone);

        if ($start !== null && $end !== null && $end->lessThan($start)) {
            $end = null;
        }

        $capacity = $this->sanitizePositiveInt($raw['so_luong_toi_da'] ?? null);

        $price = null;
        if (isset($raw['gia_ve']) && is_numeric($raw['gia_ve']) && !is_bool($raw['gia_ve'])) {
            $parsedPrice = (float) $raw['gia_ve'];
            if ($parsedPrice >= 0) {
                $price = (int) round($parsedPrice);
            }
        }

        return [
            'ten_su_kien' => $this->sanitizeString($raw['ten_su_kien'] ?? null, 255),
            'mo_ta' => $this->sanitizeString($raw['mo_ta'] ?? null, 2000),
            'danh_muc_id' => $categoryId,
            'thoi_gian_bat_dau' => $start?->format('Y-m-d\TH:i'),
            'thoi_gian_ket_thuc' => $end?->format('Y-m-d\TH:i'),
            'dia_diem' => $this->sanitizeString($raw['dia_diem'] ?? null, 255),
            'ban_to_chuc' => $this->sanitizeString($raw['ban_to_chuc'] ?? null, 255),
            'so_luong_toi_da' => $capacity,
            'gia_ve' => $price,
        ];
    }

    protected function sanitizeString(mixed $value, int $maxLength): ?string
    {
        if (!is_string($value)) {
            return null;
        }

        $trimmed = trim($value);
        if ($trimmed === '') {
            return null;
        }

        return mb_substr($trimmed, 0, $maxLength);
    }

    protected function sanitizePositiveInt(mixed $value): ?int
    {
        if ($value === null || is_bool($value)) {
            return null;
        }

        if (is_int($value)) {
            return $value >= 1 ? $value : null;
        }

        if (is_string($value) && preg_match('/^\d+$/', trim($value))) {
            $parsed = (int) trim($value);

            return $parsed >= 1 ? $parsed : null;
        }

        return null;
    }

    protected function parseFutureDatetime(mixed $value, Carbon $now, string $timezone): ?Carbon
    {
        if (!is_string($value) || trim($value) === '') {
            return null;
        }

        try {
            $parsed = Carbon::parse(trim($value), $timezone);
        } catch (\Throwable) {
            return null;
        }

        if ($parsed->lessThan($now)) {
            return null;
        }

        return $parsed;
    }
}
