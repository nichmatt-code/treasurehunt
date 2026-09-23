<?php

use App\Http\Controllers\Admin\AnswerController as AdminAnswerController;
use App\Http\Controllers\Admin\ParticipantController as AdminParticipantController;
use App\Http\Controllers\Admin\QuestionController as AdminQuestionController;
use App\Http\Controllers\Admin\TeamController as AdminTeamController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\TeamAnswerController;
use App\Http\Controllers\TeamChatController;
use App\Models\ChatMessage;
use App\Models\Participant;
use App\Models\Team;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $participant = Participant::currentFromCookie(request());

    $messages = ChatMessage::whereNull('team_id')->latest('id')->limit(50)->get()->sortBy('id')->values();

    return view('welcome', [
        'participant' => $participant,
        'messages' => $messages,
        'messagesFeed' => $messages->map->toFeedArray()->values(),
        'totalPeserta' => Participant::count(),
        'leaderboard' => Team::leaderboard(),
    ]);
});

Route::post('/register', [RegistrationController::class, 'store'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/chat/messages', [ChatController::class, 'index'])->name('chat.index');
Route::post('/chat/messages', [ChatController::class, 'store'])->name('chat.store');
Route::get('/chat/me', [ChatController::class, 'me'])->name('chat.me');

Route::get('/game', [GameController::class, 'index'])->name('game.index');
Route::post('/game/answers', [TeamAnswerController::class, 'store'])->name('game.answers.store');
Route::get('/game/chat/messages', [TeamChatController::class, 'index'])->name('game.chat.index');
Route::post('/game/chat/messages', [TeamChatController::class, 'store'])->name('game.chat.store');

Route::get('/leaderboard', [LeaderboardController::class, 'index'])->name('leaderboard.index');

Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
    Route::get('/', function () {
        return redirect()->route('admin.teams.index');
    })->name('dashboard');

    Route::get('/teams', [AdminTeamController::class, 'index'])->name('teams.index');
    Route::post('/teams', [AdminTeamController::class, 'store'])->name('teams.store');
    Route::post('/teams/randomize', [AdminTeamController::class, 'randomize'])->name('teams.randomize');
    Route::put('/teams/{team}', [AdminTeamController::class, 'update'])->name('teams.update');
    Route::delete('/teams/{team}', [AdminTeamController::class, 'destroy'])->name('teams.destroy');

    Route::get('/participants', [AdminParticipantController::class, 'index'])->name('participants.index');
    Route::put('/participants/{participant}', [AdminParticipantController::class, 'update'])->name('participants.update');
    Route::delete('/participants/{participant}', [AdminParticipantController::class, 'destroy'])->name('participants.destroy');

    Route::get('/questions', [AdminQuestionController::class, 'index'])->name('questions.index');
    Route::post('/questions', [AdminQuestionController::class, 'store'])->name('questions.store');
    Route::put('/questions/{question}', [AdminQuestionController::class, 'update'])->name('questions.update');
    Route::delete('/questions/{question}', [AdminQuestionController::class, 'destroy'])->name('questions.destroy');

    Route::get('/answers', [AdminAnswerController::class, 'index'])->name('answers.index');
    Route::patch('/answers/{answer}/grade', [AdminAnswerController::class, 'grade'])->name('answers.grade');
});
