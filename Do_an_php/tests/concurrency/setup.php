<?php

// php setup.php <capacity> <users>
// Xóa dữ liệu nghiệp vụ trong DB test, tạo 1 admin, 1 sự kiện công khai và N người dùng.
// In ra JSON: {"event": id, "users": [...], "admin": id}

use App\Models\NguoiDung;
use App\Models\SuKien;
use Illuminate\Support\Facades\DB;

$app = require __DIR__.'/boot.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
concurrency_guard_database();

[, $capacity, $count] = $argv;

DB::statement('SET FOREIGN_KEY_CHECKS=0');
foreach (['dang_ky', 'yeu_thich', 'su_kien', 'danh_muc', 'nguoi_dung'] as $table) {
    DB::table($table)->truncate();
}
DB::statement('SET FOREIGN_KEY_CHECKS=1');

$admin = NguoiDung::factory()->create(['vai_tro' => 'admin']);
$event = SuKien::factory()->create([
    'so_luong_toi_da' => (int) $capacity,
    'trang_thai' => 'cong_khai',
    'thoi_gian_bat_dau' => now()->addDays(3),
    'thoi_gian_ket_thuc' => now()->addDays(3)->addHours(2),
]);
$users = NguoiDung::factory()->count((int) $count)->create()->pluck('id')->all();

echo json_encode(['event' => $event->id, 'users' => $users, 'admin' => $admin->id]);
