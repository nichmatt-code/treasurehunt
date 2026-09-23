<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\Participant;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        $query = ChatMessage::query()->whereNull('team_id')->orderBy('id');

        if ($request->filled('after_id')) {
            $query->where('id', '>', (int) $request->query('after_id'));
        } else {
            $lastId = ChatMessage::max('id') ?? 0;
            $query->where('id', '>', max(0, $lastId - 50));
        }

        $messages = $query->get()->map->toFeedArray();

        return response()->json([
            'ok' => true,
            'messages' => $messages,
            'total_peserta' => Participant::count(),
        ]);
    }

    public function store(Request $request)
    {
        $participant = Participant::currentFromCookie($request);

        if (! $participant) {
            return response()->json([
                'ok' => false,
                'error' => 'Kamu harus daftar dulu sebelum bisa chat.',
            ], 403);
        }

        $validated = $request->validate([
            'message' => ['required', 'string', 'max:500'],
        ], [
            'message.required' => 'Pesan tidak boleh kosong.',
            'message.max' => 'Pesan maksimal 500 karakter.',
        ]);

        $message = ChatMessage::create([
            'participant_id' => $participant->id,
            'sender_name' => $participant->nama_lengkap,
            'message' => $validated['message'],
            'type' => 'user',
        ]);

        return response()->json([
            'ok' => true,
            'message' => $message->toFeedArray(),
        ]);
    }

    public function me(Request $request)
    {
        $participant = Participant::currentFromCookie($request);

        return response()->json([
            'ok' => true,
            'participant' => $participant ? [
                'id' => $participant->id,
                'nama_lengkap' => $participant->nama_lengkap,
            ] : null,
            'total_peserta' => Participant::count(),
        ]);
    }
}
