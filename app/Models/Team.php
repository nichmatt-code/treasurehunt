<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
    ];

    public function participants()
    {
        return $this->hasMany(Participant::class);
    }

    public function answers()
    {
        return $this->hasMany(TeamAnswer::class);
    }

    public function chatMessages()
    {
        return $this->hasMany(ChatMessage::class);
    }

    public static function leaderboard()
    {
        return static::with('answers')->get()->map(function (Team $team) {
            $correct = $team->answers->where('status', 'correct');

            return [
                'id' => $team->id,
                'name' => $team->name,
                'code' => $team->code,
                'correct_count' => $correct->count(),
                'answered_count' => $team->answers->count(),
                'last_correct_at' => $correct->max('graded_at'),
            ];
        })->sort(function ($a, $b) {
            if ($a['correct_count'] !== $b['correct_count']) {
                return $b['correct_count'] <=> $a['correct_count'];
            }

            $aTime = $a['last_correct_at']?->timestamp ?? PHP_INT_MAX;
            $bTime = $b['last_correct_at']?->timestamp ?? PHP_INT_MAX;

            return $aTime <=> $bTime;
        })->values()->map(function ($row, $index) {
            $row['rank'] = $index + 1;
            $row['last_correct_at'] = $row['last_correct_at']?->toIso8601String();

            return $row;
        });
    }
}
