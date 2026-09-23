<?php

namespace App\Http\Controllers;

use App\Events\Quiz\AnswerTally;
use App\Models\QuizAnswer;
use App\Models\QuizGame;
use App\Models\QuizPlayer;
use Illuminate\Http\Request;

class QuizPlayController extends Controller
{
    public function show(string $roomCode)
    {
        $game = QuizGame::where('room_code', $roomCode)->firstOrFail();

        return view('quiz.play', [
            'roomCode' => $game->room_code,
        ]);
    }

    public function state(Request $request, string $roomCode)
    {
        $game = QuizGame::with('quiz')->where('room_code', $roomCode)->first();

        if (! $game) {
            return response()->json(['ok' => false, 'error' => 'Game tidak ditemukan.'], 404);
        }

        $player = $this->resolvePlayer($request, $game);

        if (! $player) {
            return response()->json(['ok' => false, 'error' => 'Sesi kamu tidak valid, silakan join ulang.'], 403);
        }

        $payload = [
            'ok' => true,
            'status' => $game->status,
            'player' => ['nickname' => $player->nickname, 'score' => $player->score],
            'player_count' => $game->players()->count(),
        ];

        if (in_array($game->status, ['question', 'result'], true)) {
            $question = $game->currentQuestion();

            if ($question) {
                $payload['question_index'] = $game->current_question_index;
                $payload['total_questions'] = $game->quiz->questions()->count();

                $myAnswer = QuizAnswer::where('quiz_player_id', $player->id)
                    ->where('quiz_question_id', $question->id)
                    ->first();

                if ($game->status === 'question') {
                    $payload['question'] = $question->toPublicArray();
                    $payload['started_at'] = optional($game->current_question_started_at)->toIso8601String();
                    $payload['already_answered'] = (bool) $myAnswer;
                } else {
                    $distribution = $this->distributionFor($game, $question);
                    $payload['correct_option_index'] = $question->correct_option_index;
                    $payload['distribution'] = $distribution;
                    $payload['my_answer'] = $myAnswer ? [
                        'selected_option_index' => $myAnswer->selected_option_index,
                        'is_correct' => $myAnswer->is_correct,
                        'score_awarded' => $myAnswer->score_awarded,
                    ] : null;
                }
            }
        }

        if (in_array($game->status, ['leaderboard', 'finished'], true)) {
            $payload['leaderboard'] = $game->leaderboard();
        }

        return response()->json($payload);
    }

    public function answer(Request $request, string $roomCode)
    {
        $game = QuizGame::where('room_code', $roomCode)->first();

        if (! $game) {
            return response()->json(['ok' => false, 'error' => 'Game tidak ditemukan.'], 404);
        }

        $player = $this->resolvePlayer($request, $game);

        if (! $player) {
            return response()->json(['ok' => false, 'error' => 'Sesi kamu tidak valid.'], 403);
        }

        if ($game->status !== 'question') {
            return response()->json(['ok' => false, 'error' => 'Bukan waktunya menjawab.'], 422);
        }

        $question = $game->currentQuestion();

        $validated = $request->validate([
            'question_id' => ['required', 'integer'],
            'option_index' => ['required', 'integer', 'min:0'],
        ]);

        if (! $question || (int) $validated['question_id'] !== $question->id) {
            return response()->json(['ok' => false, 'error' => 'Pertanyaan sudah berganti.'], 422);
        }

        if ($validated['option_index'] >= count($question->options)) {
            return response()->json(['ok' => false, 'error' => 'Pilihan tidak valid.'], 422);
        }

        $alreadyAnswered = QuizAnswer::where('quiz_player_id', $player->id)
            ->where('quiz_question_id', $question->id)
            ->exists();

        if ($alreadyAnswered) {
            return response()->json(['ok' => false, 'error' => 'Kamu sudah menjawab pertanyaan ini.'], 422);
        }

        $startedAt = $game->current_question_started_at;
        $elapsedMs = $startedAt ? (int) round(abs(now()->diffInMilliseconds($startedAt))) : PHP_INT_MAX;
        $limitMs = $question->time_limit_seconds * 1000;

        if ($elapsedMs > $limitMs + 500) {
            return response()->json(['ok' => false, 'error' => 'Waktu sudah habis.'], 422);
        }

        $isCorrect = $validated['option_index'] === $question->correct_option_index;
        $score = 0;

        if ($isCorrect) {
            $ratio = min(1, $elapsedMs / max(1, $limitMs));
            $score = (int) round($question->points * (1 - $ratio * 0.5));
        }

        QuizAnswer::create([
            'quiz_game_id' => $game->id,
            'quiz_player_id' => $player->id,
            'quiz_question_id' => $question->id,
            'selected_option_index' => $validated['option_index'],
            'is_correct' => $isCorrect,
            'response_time_ms' => min($elapsedMs, $limitMs),
            'score_awarded' => $score,
            'answered_at' => now(),
        ]);

        $player->increment('score', $score);

        $answeredCount = QuizAnswer::where('quiz_game_id', $game->id)
            ->where('quiz_question_id', $question->id)
            ->count();

        broadcast(new AnswerTally($game->room_code, $answeredCount, $game->players()->count()));

        return response()->json(['ok' => true, 'submitted' => true]);
    }

    private function resolvePlayer(Request $request, QuizGame $game): ?QuizPlayer
    {
        $token = $request->header('X-Player-Token') ?? $request->query('token');

        if (! $token) {
            return null;
        }

        return QuizPlayer::where('quiz_game_id', $game->id)
            ->where('player_token', $token)
            ->first();
    }

    private function distributionFor(QuizGame $game, $question): array
    {
        $counts = QuizAnswer::where('quiz_game_id', $game->id)
            ->where('quiz_question_id', $question->id)
            ->selectRaw('selected_option_index, COUNT(*) as cnt')
            ->groupBy('selected_option_index')
            ->pluck('cnt', 'selected_option_index');

        $distribution = [];
        foreach (array_keys($question->options) as $index) {
            $distribution[$index] = (int) ($counts[$index] ?? 0);
        }

        return $distribution;
    }
}
