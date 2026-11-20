<?php

// php worker.php <mode> <userId> <arg> <startAtMicrotime> <delayMs>
//   mode=book    : POST /event/{arg}/book qua HTTP kernel thật (route, middleware, controller).
//   mode=checkin : POST /admin/check-in với ma_ve={arg}, userId là admin.
//   mode=naive   : cùng logic đặt vé nhưng KHÔNG lockForUpdate — nhóm đối chứng để chứng minh
//                  harness thực sự bắt được race condition.
// Mỗi tiến trình có kết nối MySQL riêng, chờ đến cùng một mốc thời gian rồi gửi yêu cầu.
// In ra một dòng JSON kết quả.

use App\Models\DangKy;
use App\Models\NguoiDung;
use App\Models\SuKien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

$app = require __DIR__.'/boot.php';
[, $mode, $userId, $arg, $startAt, $delayMs] = $argv;

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();
concurrency_guard_database();

// Nới rộng cửa sổ race mà không sửa mã nguồn ứng dụng (móc vào sự kiện truy vấn của Laravel):
//   book/naive: ngủ sau truy vấn đếm vé, tức giữa "kiểm tra còn chỗ" và "insert".
//   cancel    : ngủ sau khi đọc dòng vé, tức giữa "kiểm tra trạng thái" và "lưu da_huy".
$delayPattern = $mode === 'cancel' ? '/from `dang_ky` where `id`/i' : '/count\(\*\).*dang_ky/i';
DB::listen(function ($query) use ($delayMs, $delayPattern) {
    if ((int) $delayMs > 0 && preg_match($delayPattern, $query->sql)) {
        usleep((int) $delayMs * 1000);
    }
});

$user = NguoiDung::findOrFail($userId);
DB::connection()->getPdo(); // mở kết nối trước khi chờ vạch xuất phát

while (microtime(true) < (float) $startAt) {
    usleep(500);
}

$started = microtime(true);
$out = ['mode' => $mode];

try {
    if ($mode === 'naive') {
        $out['result'] = DB::transaction(function () use ($user, $arg) {
            $event = SuKien::whereKey($arg)->firstOrFail(); // cố ý không khóa
            if ($event->so_luong_da_dang_ky >= $event->so_luong_toi_da) {
                return 'full';
            }
            DangKy::create([
                'nguoi_dung_id' => $user->id,
                'su_kien_id' => $arg,
                'ma_ve' => 'NAIVE-'.$user->id.'-'.uniqid(),
                'thoi_gian_dang_ky' => now(),
                'trang_thai' => 'da_xac_nhan',
            ]);

            return 'booked';
        });
    } else {
        $request = match ($mode) {
            'book' => Request::create("/event/$arg/book", 'POST'),
            'cancel' => Request::create("/tickets/$arg/cancel", 'POST'),
            default => Request::create('/admin/check-in', 'POST', ['ma_ve' => $arg], [], [], ['HTTP_ACCEPT' => 'application/json']),
        };

        // APP_ENV=testing nên middleware CSRF tự bỏ qua; đăng nhập bằng cách gắn user vào guard.
        $app->instance('request', $request);
        Auth::guard()->setUser($user);

        $response = $kernel->handle($request);
        $out['status'] = $response->getStatusCode();

        if ($mode === 'book' || $mode === 'cancel') {
            $session = $app['session.store'];
            $out['result'] = $session->get('success') ? 'success' : ($session->get('warning') ?? 'none');
        } else {
            $json = json_decode($response->getContent(), true) ?? [];
            $out['ok'] = $json['success'] ?? null;
            $out['result'] = $json['message'] ?? substr($response->getContent(), 0, 120);
        }
    }
} catch (Throwable $e) {
    $out['error'] = get_class($e).': '.substr($e->getMessage(), 0, 160);
}

$out['ms'] = (int) ((microtime(true) - $started) * 1000);
echo json_encode($out, JSON_UNESCAPED_UNICODE), "\n";
