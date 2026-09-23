<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id',
        'question_id',
        'participant_id',
        'answer_text',
        'image_path',
        'status',
        'graded_by',
        'graded_at',
    ];

    protected $casts = [
        'graded_at' => 'datetime',
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function participant()
    {
        return $this->belongsTo(Participant::class);
    }

    public function gradedBy()
    {
        return $this->belongsTo(Participant::class, 'graded_by');
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path ? asset('storage/' . $this->image_path) : null;
    }
}
