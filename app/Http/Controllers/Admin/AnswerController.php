<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Team;
use App\Models\TeamAnswer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AnswerController extends Controller
{
    public function index(Request $request)
    {
        $query = TeamAnswer::with(['team', 'question', 'participant', 'gradedBy'])
            ->orderByDesc('updated_at');

        if ($request->filled('team_id')) {
            $query->where('team_id', $request->query('team_id'));
        }

        if ($request->filled('question_id')) {
            $query->where('question_id', $request->query('question_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        return view('admin.answers.index', [
            'answers' => $query->get(),
            'teams' => Team::orderBy('name')->get(),
            'questions' => Question::orderBy('order_no')->orderBy('id')->get(),
            'filterTeamId' => $request->query('team_id'),
            'filterQuestionId' => $request->query('question_id'),
            'filterStatus' => $request->query('status'),
        ]);
    }

    public function grade(Request $request, TeamAnswer $answer)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['pending', 'correct', 'incorrect'])],
        ]);

        $participant = $request->attributes->get('participant');

        $answer->update([
            'status' => $validated['status'],
            'graded_by' => $validated['status'] === 'pending' ? null : $participant->id,
            'graded_at' => $validated['status'] === 'pending' ? null : now(),
        ]);

        return response()->json([
            'ok' => true,
            'answer' => [
                'id' => $answer->id,
                'status' => $answer->status,
                'graded_by' => $answer->gradedBy?->nama_lengkap,
                'graded_at' => $answer->graded_at?->timezone(config('app.timezone'))->format('H:i'),
            ],
        ]);
    }
}
