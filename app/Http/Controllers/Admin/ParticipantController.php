<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Participant;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ParticipantController extends Controller
{
    public function index(Request $request)
    {
        $query = Participant::query()->with('team')->orderByDesc('created_at');

        if ($request->filled('team_id')) {
            $request->query('team_id') === 'none'
                ? $query->whereNull('team_id')
                : $query->where('team_id', $request->query('team_id'));
        }

        if ($request->filled('q')) {
            $search = $request->query('q');
            $query->where(function ($sub) use ($search) {
                $sub->where('nama_lengkap', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('sekolah', 'like', "%{$search}%");
            });
        }

        return view('admin.participants.index', [
            'participants' => $query->paginate(20)->withQueryString(),
            'teams' => Team::orderBy('name')->get(),
            'filterTeamId' => $request->query('team_id'),
            'search' => $request->query('q'),
        ]);
    }

    public function update(Request $request, Participant $participant)
    {
        $validated = $request->validate([
            'team_id' => ['nullable', 'exists:teams,id'],
            'role' => ['required', Rule::in(['participant', 'admin'])],
        ]);

        $participant->update([
            'team_id' => $validated['team_id'] ?: null,
            'role' => $validated['role'],
        ]);

        return redirect()->back()->with('status', 'Data peserta berhasil diperbarui.');
    }

    public function destroy(Participant $participant)
    {
        $participant->delete();

        return redirect()->back()->with('status', 'Peserta berhasil dihapus.');
    }
}
