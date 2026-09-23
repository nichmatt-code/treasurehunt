<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_game_id',
        'quiz_player_id',
        'quiz_question_id',
        'selected_option_index',
        'is_correct',
        'response_time_ms',
        'score_awarded',
        'answered_at',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'answered_at' => 'datetime',
    ];

    public function game()
    {
        return $this->belongsTo(QuizGame::class, 'quiz_game_id');
    }

    public function player()
    {
        return $this->belongsTo(QuizPlayer::class, 'quiz_player_id');
    }

    public function question()
    {
        return $this->belongsTo(QuizQuestion::class, 'quiz_question_id');
    }
}
