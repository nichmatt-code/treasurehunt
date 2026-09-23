<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'participant_id',
        'team_id',
        'sender_name',
        'message',
        'type',
    ];

    public function participant()
    {
        return $this->belongsTo(Participant::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function toFeedArray(): array
    {
        return [
            'id' => $this->id,
            'participant_id' => $this->participant_id,
            'sender_name' => $this->sender_name,
            'message' => $this->message,
            'type' => $this->type,
            'created_at' => $this->created_at->toIso8601String(),
            'time' => $this->created_at->timezone(config('app.timezone'))->format('H:i'),
        ];
    }
}
