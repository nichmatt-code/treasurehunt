<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\Participant;
use App\Models\Question;
use App\Models\Team;
use App\Models\TeamAnswer;
use Illuminate\Http\Request;

class GameController extends Controller
{
    public function index(Request $request)
    {
        $participant = Participant::currentFromCookie($request);

        if (! $participant) {
            return redirect('/');
        }

        $isAdmin = $participant->isAdmin();
        $teams = $isAdmin ? Team::orderBy('name')->get() : collect();

        $selectedTeamId = $isAdmin
            ? (int) $request->query('team', $participant->team_id ?: optional($teams->first())->id)
            : $participant->team_id;

        $team = $selectedTeamId ? Team::find($selectedTeamId) : null;

        $questions = Question::orderBy('order_no')->orderBy('id')->get();

        $answers = $team
            ? TeamAnswer::where('team_id', $team->id)->get()->keyBy('question_id')
            : collect();

        $messages = $team
            ? ChatMessage::where('team_id', $team->id)->latest('id')->limit(50)->get()->sortBy('id')->values()
            : collect();

        return view('game', [
            'participant' => $participant,
            'isAdmin' => $isAdmin,
            'team' => $team,
            'teams' => $teams,
            'questions' => $questions,
            'answers' => $answers,
            'messages' => $messages,
            'messagesFeed' => $messages->map->toFeedArray()->values(),
        ]);
    }
}
