<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class RegistrationController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:participants,email'],
            'no_hp' => ['required', 'string', 'max:20', 'regex:/^[0-9+\-\s]+$/'],
            'alamat' => ['required', 'string', 'max:1000'],
            'sekolah' => ['required', 'string', 'max:255'],
            'sudah_cg' => ['required', 'boolean'],
            'no_cg' => ['required_if:sudah_cg,1', 'nullable', 'string', 'max:100'],
            'coach' => ['required_if:sudah_cg,1', 'nullable', Rule::in(['Nichmatt', 'Yoyo', 'Stefani'])],
        ], [
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar.',
            'no_hp.required' => 'Nomor handphone wajib diisi.',
            'no_hp.regex' => 'Format nomor handphone tidak valid.',
            'alamat.required' => 'Alamat wajib diisi.',
            'sekolah.required' => 'Nama sekolah wajib diisi.',
            'no_cg.required_if' => 'Nomor CG wajib diisi jika kamu sudah ber-CG.',
            'coach.required_if' => 'Silakan pilih coach kamu.',
            'coach.in' => 'Coach tidak valid.',
        ]);

        $participant = Participant::create([
            'nama_lengkap' => $validated['nama_lengkap'],
            'email' => $validated['email'],
            'no_hp' => $validated['no_hp'],
            'alamat' => $validated['alamat'],
            'sekolah' => $validated['sekolah'],
            'sudah_cg' => $validated['sudah_cg'],
            'no_cg' => $validated['sudah_cg'] ? $validated['no_cg'] : null,
            'coach' => $validated['sudah_cg'] ? $validated['coach'] : null,
            'session_token' => Str::random(60),
        ]);

        $message = ChatMessage::create([
            'participant_id' => $participant->id,
            'sender_name' => $participant->nama_lengkap,
            'message' => "🎉 {$participant->nama_lengkap} baru saja bergabung dalam Mission One!",
            'type' => 'system',
        ]);

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
            'message' => $message->toFeedArray(),
            'total_peserta' => Participant::count(),
        ])->withCookie($cookie);
    }
}
