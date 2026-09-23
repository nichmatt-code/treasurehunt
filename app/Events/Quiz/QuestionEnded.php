<?php

namespace App\Events\Quiz;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QuestionEnded implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @param  array<int, int>  $distribution  option index => count of players who picked it
     */
    public function __construct(
        public string $roomCode,
        public int $correctOptionIndex,
        public array $distribution,
    ) {}

    public function broadcastOn(): array
    {
        return [new Channel('quiz.'.$this->roomCode)];
    }

    public function broadcastAs(): string
    {
        return 'question.ended';
    }

    public function broadcastWith(): array
    {
        return [
            'correct_option_index' => $this->correctOptionIndex,
            'distribution' => $this->distribution,
        ];
    }
}
