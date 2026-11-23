<?php

namespace App\Console\Commands;

use App\Mail\NhacLichSuKienMail;
use App\Models\DangKy;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NhacLichSuKien extends Command
{
    protected $signature = 'su-kien:nhac-lich {--gio=24 : Số giờ trước khi sự kiện bắt đầu để gửi nhắc lịch}';

    protected $description = 'Gửi email nhắc lịch cho các vé đã xác nhận của sự kiện sắp diễn ra';

    public function handle(): int
    {
        $hours = max(1, (int) $this->option('gio'));
        $now = now();
        $until = $now->copy()->addHours($hours);
        $sentCount = 0;

        // Chỉ chọn vé đã xác nhận, chưa gửi nhắc lịch, sự kiện công khai sắp diễn ra và tài khoản còn hoạt động
        DangKy::query()
            ->with(['suKien', 'nguoiDung'])
            ->where('trang_thai', 'da_xac_nhan')
            ->whereNull('da_nhac_lich_luc')
            ->whereHas('suKien', function ($query) use ($now, $until) {
                $query->where('trang_thai', 'cong_khai')
                    ->where('thoi_gian_bat_dau', '>', $now)
                    ->where('thoi_gian_bat_dau', '<=', $until);
            })
            ->whereHas('nguoiDung', function ($query) {
                $query->where('trang_thai', 'hoat_dong')
                    ->whereNotNull('email')
                    ->where('email', '!=', '');
            })
            ->chunkById(100, function ($tickets) use (&$sentCount) {
                foreach ($tickets as $ticket) {
                    $email = trim((string) ($ticket->nguoiDung->email ?? ''));
                    if ($email === '') {
                        continue;
                    }

                    try {
                        Mail::to($email)->send(new NhacLichSuKienMail($ticket));
                        // Chỉ đánh dấu đã nhắc sau khi gửi thành công để không bị mất nhắc lịch nếu gặp lỗi mạng
                        $ticket->update(['da_nhac_lich_luc' => now()]);
                        $sentCount++;
                    } catch (\Throwable $e) {
                        Log::warning('Gửi email nhắc lịch sự kiện thất bại', [
                            'dang_ky_id' => $ticket->id,
                            'su_kien_id' => $ticket->su_kien_id,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }
            });

        $this->info("Đã gửi nhắc lịch cho {$sentCount} vé.");

        return self::SUCCESS;
    }
}
