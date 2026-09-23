<?php

namespace App\Http\Controllers;

use App\Models\Participant;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'no_hp' => ['required', 'string'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'no_hp.required' => 'Nomor handphone wajib diisi.',
        ]);

        $participant = Participant::where('email', $validated['email'])
            ->where('no_hp', $validated['no_hp'])
            ->first();

        if (! $participant) {
            return response()->json([
                'ok' => false,
                'error' => 'Email atau nomor handphone tidak cocok dengan data pendaftaran.',
            ], 422);
        }

        $cookie = cookie(
            'participant_token',
            $participant->session_token,
            60 * 24 * 365,
            null,
            null,
            false,
            true,
            false,
            'lax'
        );

        return response()->json([
            'ok' => true,
            'participant' => [
                'id' => $participant->id,
                'nama_lengkap' => $participant->nama_lengkap,
            ],
            'total_peserta' => Participant::count(),
        ])->withCookie($cookie);
    }

    public function logout(Request $request)
    {
        return response()->json(['ok' => true])->withCookie(cookie()->forget('participant_token'));
    }
}
