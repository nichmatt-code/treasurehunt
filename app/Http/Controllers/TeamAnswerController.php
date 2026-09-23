<?php

namespace App\Http\Controllers;

use App\Models\Participant;
use App\Models\TeamAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TeamAnswerController extends Controller
{
    public function store(Request $request)
    {
        $participant = Participant::currentFromCookie($request);

        if (! $participant) {
            return response()->json(['ok' => false, 'error' => 'Silakan login dulu.'], 403);
        }

        $teamId = $participant->isAdmin()
            ? (int) $request->input('team_id')
            : $participant->team_id;

        if (! $teamId) {
            return response()->json(['ok' => false, 'error' => 'Kamu belum tergabung dalam tim.'], 422);
        }

        $validated = $request->validate([
            'question_id' => ['required', 'exists:questions,id'],
            'answer_text' => ['nullable', 'string', 'max:5000'],
            'answer_image' => ['nullable', 'image', 'max:4096'],
            'remove_image' => ['nullable', 'boolean'],
        ], [
            'answer_image.image' => 'File harus berupa gambar.',
            'answer_image.max' => 'Ukuran gambar maksimal 4MB.',
        ]);

        if (empty($validated['answer_text']) && ! $request->hasFile('answer_image') && ! $request->boolean('remove_image')) {
            return response()->json(['ok' => false, 'error' => 'Isi teks jawaban atau upload gambar dulu.'], 422);
        }

        $existing = TeamAnswer::where('team_id', $teamId)->where('question_id', $validated['question_id'])->first();

        $data = [
            'answer_text' => $validated['answer_text'] ?? '',
            'participant_id' => $participant->id,
            'status' => 'pending',
            'graded_by' => null,
            'graded_at' => null,
        ];

        if ($request->hasFile('answer_image')) {
            if ($existing && $existing->image_path) {
                Storage::disk('public')->delete($existing->image_path);
            }
            $data['image_path'] = $request->file('answer_image')->store('answers', 'public');
        } elseif ($request->boolean('remove_image')) {
            if ($existing && $existing->image_path) {
                Storage::disk('public')->delete($existing->image_path);
            }
            $data['image_path'] = null;
        }

        $answer = TeamAnswer::updateOrCreate(
            ['team_id' => $teamId, 'question_id' => $validated['question_id']],
            $data
        );

        return response()->json([
            'ok' => true,
            'answer' => [
                'question_id' => $answer->question_id,
                'answer_text' => $answer->answer_text,
                'image_url' => $answer->image_url,
                'status' => $answer->status,
                'updated_by' => $participant->nama_lengkap,
                'updated_at' => $answer->updated_at->timezone(config('app.timezone'))->format('H:i'),
            ],
        ]);
    }
}
