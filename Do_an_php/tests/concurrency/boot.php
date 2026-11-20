<?php

// Nạp ứng dụng Laravel của dự án. Cấu hình DB lấy từ biến môi trường do run.sh đặt (ghi đè .env).
$project = dirname(__DIR__, 2);
require $project.'/vendor/autoload.php';

$app = require $project.'/bootstrap/app.php';

/** Chặn chạy nhầm vào CSDL thật: tên database bắt buộc kết thúc bằng _test. */
function concurrency_guard_database(): void
{
    $name = config('database.connections.'.config('database.default').'.database');
    if (! str_ends_with((string) $name, '_test')) {
        fwrite(STDERR, "Từ chối chạy: database '$name' không kết thúc bằng _test.\n");
        exit(2);
    }
}

return $app;
