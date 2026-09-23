<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Participant extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_lengkap',
        'email',
        'no_hp',
        'alamat',
        'sekolah',
        'sudah_cg',
        'no_cg',
        'coach',
        'session_token',
        'role',
        'team_id',
    ];

    protected $casts = [
        'sudah_cg' => 'boolean',
    ];

    protected $hidden = [
        'session_token',
    ];

    public function chatMessages()
    {
        return $this->hasMany(ChatMessage::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public static function currentFromCookie(\Illuminate\Http\Request $request): ?self
    {
        $token = $request->cookie('participant_token');

        return $token ? static::where('session_token', $token)->first() : null;
    }
}
