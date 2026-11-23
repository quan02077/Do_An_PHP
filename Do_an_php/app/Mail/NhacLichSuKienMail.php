<?php

namespace App\Mail;

use App\Models\DangKy;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NhacLichSuKienMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public DangKy $dangKy,
    ) {
        $this->dangKy->loadMissing(['suKien', 'nguoiDung']);
    }

    public function envelope(): Envelope
    {
        $tenSuKien = $this->dangKy->suKien->ten_su_kien ?? 'Sự kiện sắp diễn ra';

        return new Envelope(
            subject: 'Nhắc lịch sự kiện: ' . $tenSuKien . ' — ' . config('app.name', 'QQQ'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.nhac_lich',
            with: [
                'dangKy' => $this->dangKy,
                'suKien' => $this->dangKy->suKien,
                'nguoiDung' => $this->dangKy->nguoiDung,
                'eventUrl' => route('Event.show', $this->dangKy->su_kien_id),
                'myTicketUrl' => route('Dashboard.myTicket'),
            ],
        );
    }
}
