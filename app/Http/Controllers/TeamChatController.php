<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\Participant;
use Illuminate\Http\Request;

class TeamChatController extends Controller
{
    public function index(Request $request)
    {
        $participant = Participant::currentFromCookie($request);

        if (! $participant) {
            return response()->json(['ok' => false, 'error' => 'Silakan login dulu.'], 403);
        }

        $teamId = $this->resolveTeamId($request, $participant);

        if (! $teamId) {
            return response()->json(['ok' => true, 'messages' => []]);
        }

        $query = ChatMessage::query()->where('team_id', $teamId)->orderBy('id');

        if ($request->filled('after_id')) {
            $query->where('id', '>', (int) $request->query('after_id'));
        } else {
            $lastId = ChatMessage::where('team_id', $teamId)->max('id') ?? 0;
            $query->where('id', '>', max(0, $lastId - 50));
        }

        return response()->json([
            'ok' => true,
            'messages' => $query->get()->map->toFeedArray(),
        ]);
    }

    public function store(Request $request)
    {
        $participant = Participant::currentFromCookie($request);

        if (! $participant) {
            return response()->json(['ok' => false, 'error' => 'Silakan login dulu.'], 403);
        }

        $teamId = $this->resolveTeamId($request, $participant);

        if (! $teamId) {
            return response()->json(['ok' => false, 'error' => 'Kamu belum tergabung dalam tim.'], 422);
        }

        $validated = $request->validate([
            'message' => ['required', 'string', 'max:500'],
        ], [
            'message.required' => 'Pesan tidak boleh kosong.',
            'message.max' => 'Pesan maksimal 500 karakter.',
        ]);

        $message = ChatMessage::create([
            'participant_id' => $participant->id,
            'team_id' => $teamId,
            'sender_name' => $participant->nama_lengkap,
            'message' => $validated['message'],
            'type' => 'user',
        ]);

        return response()->json(['ok' => true, 'message' => $message->toFeedArray()]);
    }

    private function resolveTeamId(Request $request, Participant $participant): ?int
    {
        if ($participant->isAdmin()) {
            return $request->input('team_id') ? (int) $request->input('team_id') : null;
        }

        return $participant->team_id;
    }
}
