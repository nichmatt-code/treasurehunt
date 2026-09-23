<?php

namespace App\Http\Controllers;

use App\Events\Quiz\PlayerJoined;
use App\Models\Participant;
use App\Models\QuizGame;
use App\Models\QuizPlayer;
use Illuminate\Http\Request;

class QuizJoinController extends Controller
{
    public function index(Request $request)
    {
        $participant = Participant::currentFromCookie($request);

        return view('quiz.join', [
            'isAdmin' => $participant && $participant->isAdmin(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_code' => ['required', 'string'],
            'nickname' => ['required', 'string', 'min:2', 'max:20'],
        ], [
            'room_code.required' => 'Masukkan Game PIN.',
            'nickname.required' => 'Masukkan nickname kamu.',
            'nickname.max' => 'Nickname maksimal 20 karakter.',
        ]);

        $roomCode = preg_replace('/\D/', '', $validated['room_code']);
        $game = QuizGame::where('room_code', $roomCode)->first();

        if (! $game) {
            return response()->json(['ok' => false, 'error' => 'Game PIN tidak ditemukan.'], 404);
        }

        if ($game->status !== 'lobby') {
            return response()->json(['ok' => false, 'error' => 'Game sudah dimulai, tidak bisa bergabung lagi.'], 422);
        }

        $nickname = trim($validated['nickname']);

        $taken = QuizPlayer::where('quiz_game_id', $game->id)
            ->whereRaw('LOWER(nickname) = ?', [mb_strtolower($nickname)])
            ->exists();

        if ($taken) {
            return response()->json(['ok' => false, 'error' => 'Nickname sudah dipakai peserta lain, coba yang lain.'], 422);
        }

        $player = QuizPlayer::create([
            'quiz_game_id' => $game->id,
            'nickname' => $nickname,
            'player_token' => QuizGame::generatePlayerToken(),
            'joined_at' => now(),
        ]);

        broadcast(new PlayerJoined($game->room_code, $player->nickname, $game->players()->count()));

        return response()->json([
            'ok' => true,
            'room_code' => $game->room_code,
            'player_token' => $player->player_token,
            'nickname' => $player->nickname,
        ]);
    }
}
