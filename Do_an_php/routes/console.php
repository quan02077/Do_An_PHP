<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Chạy tự động mỗi giờ để gửi email nhắc lịch cho các sự kiện sắp diễn ra
Schedule::command('su-kien:nhac-lich')->hourly()->withoutOverlapping();
