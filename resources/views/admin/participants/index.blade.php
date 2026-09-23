@extends('layouts.admin')

@section('title', 'Kelola Peserta')

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="font-display text-3xl font-bold">Kelola Peserta</h1>
            <p class="mt-1 text-sm text-white/50">{{ $participants->total() }} peserta terdaftar.</p>
        </div>

        <form method="GET" action="{{ route('admin.participants.index') }}" class="flex flex-wrap items-center gap-2">
            <input type="text" name="q" value="{{ $search }}" placeholder="Cari nama, email, sekolah..."
                class="rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm text-white placeholder-white/30 outline-none focus:border-violet-400/60">
            <select name="team_id" onchange="this.form.submit()" class="rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-white outline-none focus:border-violet-400/60">
                <option value="">Semua Tim</option>
                <option value="none" {{ $filterTeamId === 'none' ? 'selected' : '' }}>Belum Bertim</option>
                @foreach ($teams as $t)
                    <option value="{{ $t->id }}" {{ (string) $filterTeamId === (string) $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="glass rounded-xl px-4 py-2 text-sm font-medium text-white/80 transition hover:bg-white/10">Cari</button>
        </form>
    </div>

    <div class="glass-strong overflow-x-auto rounded-3xl p-2">
        <table class="w-full min-w-[900px] border-collapse text-sm">
            <thead>
                <tr class="text-left text-xs uppercase tracking-wider text-white/40">
                    <th class="px-4 py-3">Nama</th>
                    <th class="px-4 py-3">Kontak</th>
                    <th class="px-4 py-3">Sekolah</th>
                    <th class="px-4 py-3">CG / Coach</th>
                    <th class="px-4 py-3">Tim &amp; Role</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($participants as $p)
                    <tr class="border-t border-white/5 align-top">
                        <td class="px-4 py-3">
                            <p class="font-medium">{{ $p->nama_lengkap }}</p>
                            <p class="text-xs text-white/40">Daftar {{ $p->created_at->timezone(config('app.timezone'))->format('d M Y') }}</p>
                        </td>
                        <td class="px-4 py-3 text-white/70">
                            <p>{{ $p->email }}</p>
                            <p class="text-xs text-white/40">{{ $p->no_hp }}</p>
                        </td>
                        <td class="px-4 py-3 text-white/70">{{ $p->sekolah }}</td>
                        <td class="px-4 py-3 text-white/70">
                            @if ($p->sudah_cg)
                                <p>{{ $p->no_cg }}</p>
                                <p class="text-xs text-white/40">{{ $p->coach }}</p>
                            @else
                                <span class="text-xs text-white/40">Belum CG</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <form method="POST" action="{{ route('admin.participants.update', $p) }}" class="flex flex-wrap items-center gap-2">
                                @csrf
                                @method('PUT')
                                <select name="team_id" class="rounded-lg border border-white/10 bg-white/5 px-2 py-1.5 text-xs text-white outline-none focus:border-violet-400/60">
                                    <option value="">Tanpa Tim</option>
                                    @foreach ($teams as $t)
                                        <option value="{{ $t->id }}" {{ $p->team_id === $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                                    @endforeach
                                </select>
                                <select name="role" class="rounded-lg border border-white/10 bg-white/5 px-2 py-1.5 text-xs text-white outline-none focus:border-violet-400/60">
                                    <option value="participant" {{ $p->role === 'participant' ? 'selected' : '' }}>Peserta</option>
                                    <option value="admin" {{ $p->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                </select>
                                <button type="submit" class="glass rounded-lg px-3 py-1.5 text-xs font-medium text-white/80 transition hover:bg-white/10">
                                    Simpan
                                </button>
                            </form>
                        </td>
                        <td class="px-4 py-3">
                            <form method="POST" action="{{ route('admin.participants.destroy', $p) }}" class="confirm-delete" data-message="Hapus peserta {{ $p->nama_lengkap }}? Tindakan ini tidak bisa dibatalkan.">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="glass rounded-full p-2 text-white/60 transition hover:bg-white/10 hover:text-rose-300" title="Hapus">
                                    <x-icon name="close" class="h-3.5 w-3.5" />
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-sm text-white/50">Belum ada peserta yang cocok.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $participants->links() }}
    </div>
@endsection
