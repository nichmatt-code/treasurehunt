<?php

namespace App\Events\Quiz;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PlayerJoined implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public string $roomCode,
        public string $nickname,
        public int $playerCount,
    ) {}

    public function broadcastOn(): array
    {
        return [new Channel('quiz.'.$this->roomCode)];
    }

    public function broadcastAs(): string
    {
        return 'player.joined';
    }

    public function broadcastWith(): array
    {
        return [
            'nickname' => $this->nickname,
            'player_count' => $this->playerCount,
        ];
    }
}
