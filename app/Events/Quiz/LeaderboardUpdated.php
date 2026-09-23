<?php

namespace App\Events\Quiz;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LeaderboardUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @param  array<int, array{nickname: string, score: int}>  $leaderboard
     */
    public function __construct(
        public string $roomCode,
        public array $leaderboard,
    ) {}

    public function broadcastOn(): array
    {
        return [new Channel('quiz.'.$this->roomCode)];
    }

    public function broadcastAs(): string
    {
        return 'leaderboard.updated';
    }

    public function broadcastWith(): array
    {
        return ['leaderboard' => $this->leaderboard];
    }
}
