<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Participant;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TeamController extends Controller
{
    public function index()
    {
        $teams = Team::withCount('participants')->orderBy('name')->get();

        return view('admin.teams.index', [
            'teams' => $teams,
            'unassignedCount' => Participant::where('role', 'participant')->whereNull('team_id')->count(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'unique:teams,code'],
        ], [
            'name.required' => 'Nama tim wajib diisi.',
            'code.required' => 'Kode tim wajib diisi.',
            'code.unique' => 'Kode tim sudah dipakai.',
        ]);

        Team::create($validated);

        return redirect()->route('admin.teams.index')->with('status', 'Tim berhasil ditambahkan.');
    }

    public function update(Request $request, Team $team)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'unique:teams,code,' . $team->id],
        ], [
            'name.required' => 'Nama tim wajib diisi.',
            'code.required' => 'Kode tim wajib diisi.',
            'code.unique' => 'Kode tim sudah dipakai.',
        ]);

        $team->update($validated);

        return redirect()->route('admin.teams.index')->with('status', 'Tim berhasil diperbarui.');
    }

    public function destroy(Team $team)
    {
        $team->delete();

        return redirect()->route('admin.teams.index')->with('status', 'Tim berhasil dihapus.');
    }

    public function randomize(Request $request)
    {
        $validated = $request->validate([
            'mode' => ['required', Rule::in(['count', 'average'])],
            'value' => ['required', 'integer', 'min:1'],
        ], [
            'value.required' => 'Isi jumlahnya dulu.',
            'value.min' => 'Jumlah minimal 1.',
        ]);

        $participants = Participant::where('role', 'participant')->whereNull('team_id')->get()->shuffle()->values();
        $total = $participants->count();

        if ($total === 0) {
            return redirect()->route('admin.teams.index')->with('status', 'Tidak ada peserta yang belum memiliki tim.');
        }

        $groupCount = $validated['mode'] === 'count'
            ? (int) $validated['value']
            : (int) ceil($total / $validated['value']);

        $groupCount = max(1, min($groupCount, $total));

        $newTeams = [];
        $num = 1;
        for ($i = 0; $i < $groupCount; $i++) {
            while (Team::where('code', 'TIM-' . str_pad((string) $num, 2, '0', STR_PAD_LEFT))->exists()) {
                $num++;
            }

            $newTeams[] = Team::create([
                'name' => 'Tim ' . $num,
                'code' => 'TIM-' . str_pad((string) $num, 2, '0', STR_PAD_LEFT),
            ]);
            $num++;
        }

        foreach ($participants as $index => $participant) {
            $participant->update(['team_id' => $newTeams[$index % $groupCount]->id]);
        }

        return redirect()->route('admin.teams.index')->with('status', "{$total} peserta berhasil dibagi acak ke {$groupCount} tim baru.");
    }
}
