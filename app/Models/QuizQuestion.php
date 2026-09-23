<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id',
        'question',
        'image_path',
        'options',
        'correct_option_index',
        'time_limit_seconds',
        'points',
        'order_no',
    ];

    protected $casts = [
        'options' => 'array',
    ];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function answers()
    {
        return $this->hasMany(QuizAnswer::class);
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path ? asset('storage/'.$this->image_path) : null;
    }

    /**
     * Player/broadcast-safe payload — never includes the correct answer.
     */
    public function toPublicArray(): array
    {
        return [
            'id' => $this->id,
            'question' => $this->question,
            'image_url' => $this->image_url,
            'options' => $this->options,
            'time_limit_seconds' => $this->time_limit_seconds,
            'points' => $this->points,
        ];
    }
}
