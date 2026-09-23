<?php

namespace App\Events\Quiz;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AnswerTally implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public string $roomCode,
        public int $answeredCount,
        public int $totalPlayers,
    ) {}

    public function broadcastOn(): array
    {
        return [new Channel('quiz.'.$this->roomCode)];
    }

    public function broadcastAs(): string
    {
        return 'answer.tally';
    }

    public function broadcastWith(): array
    {
        return [
            'answered_count' => $this->answeredCount,
            'total_players' => $this->totalPlayers,
        ];
    }
}
