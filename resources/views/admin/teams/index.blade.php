@extends('layouts.admin')

@section('title', 'Kelola Tim')

@section('content')
    <div class="mb-8">
        <h1 class="font-display text-3xl font-bold">Kelola Tim</h1>
        <p class="mt-1 text-sm text-white/50">Buat dan atur tim peserta Mission One.</p>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3 lg:gap-8">
        <div class="glass-strong h-fit rounded-3xl p-6 lg:col-span-1">
            <h2 class="font-display text-lg font-semibold">Tambah Tim Baru</h2>
            <form method="POST" action="{{ route('admin.teams.store') }}" class="mt-4 space-y-4">
                @csrf
                <div>
                    <label class="mb-1 block text-xs font-medium text-white/70">Nama Tim</label>
                    <input type="text" name="name" required value="{{ old('name') }}"
                        class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm text-white placeholder-white/30 outline-none transition focus:border-violet-400/60 focus:bg-white/10"
                        placeholder="Contoh: Tim Elang">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium text-white/70">Kode Tim</label>
                    <input type="text" name="code" required value="{{ old('code') }}"
                        class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm text-white placeholder-white/30 outline-none transition focus:border-violet-400/60 focus:bg-white/10"
                        placeholder="Contoh: TIM-01">
                </div>
                <button type="submit" class="btn-glow w-full rounded-xl bg-gradient-to-r from-cyan-400 via-violet-400 to-pink-400 px-4 py-3 text-sm font-semibold text-slate-950 transition hover:brightness-110">
                    Tambah Tim
                </button>
            </form>

            <div class="mt-6 border-t border-white/10 pt-6">
                <h2 class="font-display text-lg font-semibold">Bagi Tim Otomatis</h2>
                <p class="mt-1 text-xs text-white/50">
                    Membuat tim baru dan membagi <span class="font-semibold text-white">{{ $unassignedCount }}</span> peserta yang belum punya tim secara acak &amp; merata.
                </p>

                <form method="POST" action="{{ route('admin.teams.randomize') }}" class="mt-4 space-y-3">
                    @csrf
                    <div class="space-y-2">
                        <label class="flex cursor-pointer items-center gap-2 rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm has-checked:border-violet-400/60 has-checked:bg-violet-400/10">
                            <input type="radio" name="mode" value="count" checked class="accent-violet-400">
                            Berdasarkan jumlah kelompok
                        </label>
                        <label class="flex cursor-pointer items-center gap-2 rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm has-checked:border-violet-400/60 has-checked:bg-violet-400/10">
                            <input type="radio" name="mode" value="average" class="accent-violet-400">
                            Berdasarkan rata-rata per kelompok
                        </label>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium text-white/70">Jumlah</label>
                        <input type="number" name="value" min="1" required value="{{ old('value', 4) }}"
                            class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm text-white placeholder-white/30 outline-none transition focus:border-violet-400/60 focus:bg-white/10">
                    </div>
                    <button type="submit" class="w-full rounded-xl border border-violet-400/40 bg-violet-400/10 px-4 py-3 text-sm font-semibold text-violet-200 transition hover:bg-violet-400/20">
                        🎲 Acak &amp; Bagi Tim
                    </button>
                </form>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div class="space-y-3">
                @forelse ($teams as $team)
                    <div class="glass-strong rounded-2xl p-5">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div class="glass flex h-11 w-11 items-center justify-center rounded-full">
                                    <x-icon name="shield" class="h-5 w-5 icon-glow text-violet-300" />
                                </div>
                                <div>
                                    <p class="font-display font-semibold">{{ $team->name }}</p>
                                    <p class="text-xs text-white/50">{{ $team->code }} &middot; {{ $team->participants_count }} peserta</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <button type="button" class="toggle-edit-btn glass rounded-full px-3 py-1.5 text-xs font-medium text-white/80 transition hover:bg-white/10" data-target="edit-team-{{ $team->id }}">
                                    Edit
                                </button>
                                <form method="POST" action="{{ route('admin.teams.destroy', $team) }}" class="confirm-delete" data-message="Hapus tim {{ $team->name }}? Peserta di tim ini akan jadi tanpa tim.">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="glass rounded-full p-2 text-white/60 transition hover:bg-white/10 hover:text-rose-300">
                                        <x-icon name="close" class="h-3.5 w-3.5" />
                                    </button>
                                </form>
                            </div>
                        </div>

                        <form id="edit-team-{{ $team->id }}" method="POST" action="{{ route('admin.teams.update', $team) }}" class="mt-4 hidden grid-cols-1 gap-3 border-t border-white/10 pt-4 sm:grid-cols-3">
                            @csrf
                            @method('PUT')
                            <input type="text" name="name" value="{{ $team->name }}" required
                                class="rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm text-white outline-none focus:border-violet-400/60 sm:col-span-1">
                            <input type="text" name="code" value="{{ $team->code }}" required
                                class="rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm text-white outline-none focus:border-violet-400/60 sm:col-span-1">
                            <button type="submit" class="rounded-xl bg-gradient-to-r from-cyan-400 via-violet-400 to-pink-400 px-4 py-2.5 text-sm font-semibold text-slate-950 transition hover:brightness-110 sm:col-span-1">
                                Simpan Perubahan
                            </button>
                        </form>
                    </div>
                @empty
                    <div class="glass-strong rounded-2xl p-8 text-center text-sm text-white/50">
                        Belum ada tim. Tambahkan tim pertama di sebelah kiri.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
