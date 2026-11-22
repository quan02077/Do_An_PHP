<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Contracts\Broadcasting\ShouldRescue;
use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;

class TicketCheckedIn implements ShouldBroadcastNow, ShouldDispatchAfterCommit, ShouldRescue
{
    public function __construct(
        public int $ticketId,
        public int $eventId,
        public string $attendeeName,
        public int $checkedInTotal,
    ) {}

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('admin.checkins');
    }

    public function broadcastAs(): string
    {
        return 'ticket.checked.in';
    }

    public function broadcastWith(): array
    {
        return [
            'ticketId' => $this->ticketId,
            'eventId' => $this->eventId,
            'attendeeName' => $this->attendeeName,
            'checkedInTotal' => $this->checkedInTotal,
        ];
    }
}
