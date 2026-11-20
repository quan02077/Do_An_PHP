<?php

namespace App\Events;

use App\Models\DangKy;
use App\Models\SuKien;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Contracts\Broadcasting\ShouldRescue;
use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;

class TicketCountsChanged implements ShouldBroadcastNow, ShouldDispatchAfterCommit, ShouldRescue
{
    public function __construct(
        public int $eventId,
        public int $registered,
        public int $capacity,
        public int $checkedIn,
    ) {}

    public static function forEvent(SuKien $event): self
    {
        $counts = DangKy::where('su_kien_id', $event->id)
            ->selectRaw("SUM(CASE WHEN trang_thai != 'da_huy' THEN 1 ELSE 0 END) AS registered")
            ->selectRaw("SUM(CASE WHEN trang_thai = 'da_tham_gia' THEN 1 ELSE 0 END) AS checked_in")
            ->first();

        return new self(
            $event->id,
            (int) ($counts->registered ?? 0),
            (int) $event->so_luong_toi_da,
            (int) ($counts->checked_in ?? 0),
        );
    }

    public function broadcastOn(): Channel
    {
        return new Channel('events.'.$this->eventId);
    }

    public function broadcastAs(): string
    {
        return 'ticket.counts.changed';
    }

    public function broadcastWith(): array
    {
        return [
            'eventId' => $this->eventId,
            'registered' => $this->registered,
            'capacity' => $this->capacity,
            'checkedIn' => $this->checkedIn,
        ];
    }
}
