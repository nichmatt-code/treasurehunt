<?php

namespace App\Events\Quiz;

use App\Models\QuizQuestion;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QuestionStarted implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public string $roomCode,
        public int $questionIndex,
        public int $totalQuestions,
        public QuizQuestion $question,
        public string $startedAt,
    ) {}

    public function broadcastOn(): array
    {
        return [new Channel('quiz.'.$this->roomCode)];
    }

    public function broadcastAs(): string
    {
        return 'question.started';
    }

    public function broadcastWith(): array
    {
        return array_merge($this->question->toPublicArray(), [
            'question_index' => $this->questionIndex,
            'total_questions' => $this->totalQuestions,
            'started_at' => $this->startedAt,
        ]);
    }
}
