<?php

namespace App\Http\Controllers\Host;

use App\Events\Quiz\GameFinished;
use App\Events\Quiz\LeaderboardUpdated;
use App\Events\Quiz\QuestionEnded;
use App\Events\Quiz\QuestionStarted;
use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizAnswer;
use App\Models\QuizGame;
use Illuminate\Http\Request;

class QuizGameController extends Controller
{
    public function index(Request $request)
    {
        return view('quiz.host-index', [
            'quizzes' => Quiz::withCount('questions')->orderByDesc('created_at')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $participant = $request->attributes->get('participant');

        $validated = $request->validate([
            'quiz_id' => ['required', 'exists:quizzes,id'],
        ]);

        $quiz = Quiz::findOrFail($validated['quiz_id']);

        if ($quiz->questions()->count() === 0) {
            return redirect()->route('host.quiz.index')->with('status', 'Quiz ini belum punya soal.');
        }

        $game = QuizGame::create([
            'quiz_id' => $quiz->id,
            'host_participant_id' => $participant->id,
            'room_code' => QuizGame::generateRoomCode(),
            'status' => 'lobby',
        ]);

        return redirect()->route('host.quiz.show', $game->room_code);
    }

    public function show(Request $request, string $roomCode)
    {
        $game = $this->ownedGame($request, $roomCode);

        return view('quiz.host', [
            'game' => $game,
            'quiz' => $game->quiz,
            'totalQuestions' => $game->quiz->questions()->count(),
            'snapshot' => $this->snapshot($game),
        ]);
    }

    private function snapshot(QuizGame $game): array
    {
        $data = [
            'status' => $game->status,
            'player_count' => $game->players()->count(),
            'players' => $game->players()->orderBy('joined_at')->pluck('nickname'),
            'total_questions' => $game->quiz->questions()->count(),
            'question_index' => $game->current_question_index,
        ];

        if (in_array($game->status, ['question', 'result'], true)) {
            $question = $game->currentQuestion();

            if ($question) {
                $data['question'] = $question->toPublicArray();
                $data['started_at'] = optional($game->current_question_started_at)->toIso8601String();

                if ($game->status === 'result') {
                    $counts = QuizAnswer::where('quiz_game_id', $game->id)
                        ->where('quiz_question_id', $question->id)
                        ->selectRaw('selected_option_index, COUNT(*) as cnt')
                        ->groupBy('selected_option_index')
                        ->pluck('cnt', 'selected_option_index');

                    $distribution = [];
                    foreach (array_keys($question->options) as $optIndex) {
                        $distribution[$optIndex] = (int) ($counts[$optIndex] ?? 0);
                    }

                    $data['correct_option_index'] = $question->correct_option_index;
                    $data['distribution'] = $distribution;
                }

                $data['answered_count'] = QuizAnswer::where('quiz_game_id', $game->id)
                    ->where('quiz_question_id', $question->id)
                    ->count();
            }
        }

        if (in_array($game->status, ['leaderboard', 'finished'], true)) {
            $data['leaderboard'] = $game->leaderboard();
        }

        return $data;
    }

    public function start(Request $request, string $roomCode)
    {
        $game = $this->ownedGame($request, $roomCode);

        if ($game->status !== 'lobby') {
            return response()->json(['ok' => false, 'error' => 'Game sudah berjalan.'], 422);
        }

        $this->beginQuestion($game, 0);

        return response()->json(['ok' => true]);
    }

    public function next(Request $request, string $roomCode)
    {
        $game = $this->ownedGame($request, $roomCode);
        $totalQuestions = $game->quiz->questions()->count();

        if ($game->status === 'question') {
            $this->endQuestion($game);
        } elseif ($game->status === 'result') {
            $game->update(['status' => 'leaderboard']);
            broadcast(new LeaderboardUpdated($game->room_code, $game->leaderboard()));
        } elseif ($game->status === 'leaderboard') {
            $nextIndex = $game->current_question_index + 1;

            if ($nextIndex < $totalQuestions) {
                $this->beginQuestion($game, $nextIndex);
            } else {
                $this->finishGame($game);
            }
        }

        return response()->json(['ok' => true, 'status' => $game->fresh()->status]);
    }

    public function finish(Request $request, string $roomCode)
    {
        $game = $this->ownedGame($request, $roomCode);

        if ($game->status !== 'finished') {
            $this->finishGame($game);
        }

        return response()->json(['ok' => true]);
    }

    private function ownedGame(Request $request, string $roomCode): QuizGame
    {
        $participant = $request->attributes->get('participant');

        $game = QuizGame::with('quiz')->where('room_code', $roomCode)->firstOrFail();

        abort_unless($game->host_participant_id === $participant->id, 403, 'Kamu bukan host game ini.');

        return $game;
    }

    private function beginQuestion(QuizGame $game, int $index): void
    {
        $question = $game->quiz->questions()->orderBy('order_no')->get()->values()->get($index);

        abort_if(! $question, 404, 'Soal tidak ditemukan.');

        $startedAt = now();

        $game->update([
            'status' => 'question',
            'current_question_index' => $index,
            'current_question_started_at' => $startedAt,
        ]);

        broadcast(new QuestionStarted(
            $game->room_code,
            $index,
            $game->quiz->questions()->count(),
            $question,
            $startedAt->toIso8601String(),
        ));
    }

    private function endQuestion(QuizGame $game): void
    {
        $question = $game->currentQuestion();

        $counts = QuizAnswer::where('quiz_game_id', $game->id)
            ->where('quiz_question_id', $question->id)
            ->selectRaw('selected_option_index, COUNT(*) as cnt')
            ->groupBy('selected_option_index')
            ->pluck('cnt', 'selected_option_index');

        $distribution = [];
        foreach (array_keys($question->options) as $optIndex) {
            $distribution[$optIndex] = (int) ($counts[$optIndex] ?? 0);
        }

        $game->update(['status' => 'result']);

        broadcast(new QuestionEnded($game->room_code, $question->correct_option_index, $distribution));
    }

    private function finishGame(QuizGame $game): void
    {
        $game->update(['status' => 'finished', 'finished_at' => now()]);

        broadcast(new GameFinished($game->room_code, $game->leaderboard()));
    }
}
