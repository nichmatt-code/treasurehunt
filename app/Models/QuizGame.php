<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class QuizGame extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id',
        'host_participant_id',
        'room_code',
        'status',
        'current_question_index',
        'current_question_started_at',
        'finished_at',
    ];

    protected $casts = [
        'current_question_started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function host()
    {
        return $this->belongsTo(Participant::class, 'host_participant_id');
    }

    public function players()
    {
        return $this->hasMany(QuizPlayer::class);
    }

    public function answers()
    {
        return $this->hasMany(QuizAnswer::class);
    }

    public function currentQuestion(): ?QuizQuestion
    {
        return $this->quiz->questions->values()->get($this->current_question_index);
    }

    public static function generateRoomCode(): string
    {
        do {
            $code = (string) random_int(100000, 999999);
        } while (static::where('room_code', $code)->exists());

        return $code;
    }

    public static function generatePlayerToken(): string
    {
        return Str::random(48);
    }

    public function leaderboard(int $limit = 10): array
    {
        return $this->players()
            ->orderByDesc('score')
            ->limit($limit)
            ->get(['nickname', 'score'])
            ->map(fn ($p) => ['nickname' => $p->nickname, 'score' => $p->score])
            ->values()
            ->all();
    }
}
