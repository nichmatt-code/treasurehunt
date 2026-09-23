<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizPlayer extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_game_id',
        'nickname',
        'score',
        'player_token',
        'joined_at',
    ];

    protected $casts = [
        'joined_at' => 'datetime',
    ];

    protected $hidden = [
        'player_token',
    ];

    public function game()
    {
        return $this->belongsTo(QuizGame::class, 'quiz_game_id');
    }

    public function answers()
    {
        return $this->hasMany(QuizAnswer::class);
    }
}
