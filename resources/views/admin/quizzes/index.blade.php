@extends('layouts.admin')

@section('title', 'Kelola Quiz')

@section('content')
    <div class="mb-8 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="font-display text-3xl font-bold">Kelola Quiz</h1>
            <p class="mt-1 text-sm text-white/50">Buat set soal untuk dipakai di Quiz Rush.</p>
        </div>
        <a href="{{ route('host.quiz.index') }}" class="glass rounded-full px-4 py-2 text-sm font-medium text-white/80 transition hover:bg-white/10">
            Host a Game
        </a>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3 lg:gap-8">
        <div class="glass-strong h-fit rounded-3xl p-6 lg:col-span-1">
            <h2 class="font-display text-lg font-semibold">Quiz Baru</h2>
            <form method="POST" action="{{ route('admin.quizzes.store') }}" class="mt-4 space-y-4">
                @csrf
                <div>
                    <label class="mb-1 block text-xs font-medium text-white/70">Judul</label>
                    <input type="text" name="title" required value="{{ old('title') }}"
                        class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm text-white placeholder-white/30 outline-none transition focus:border-violet-400/60 focus:bg-white/10"
                        placeholder="Contoh: Quiz Seru Mission One">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium text-white/70">Deskripsi</label>
                    <textarea name="description" rows="2"
                        class="w-full resize-none rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm text-white placeholder-white/30 outline-none transition focus:border-violet-400/60 focus:bg-white/10">{{ old('description') }}</textarea>
                </div>
                <button type="submit" class="btn-glow w-full rounded-xl bg-gradient-to-r from-cyan-400 via-violet-400 to-pink-400 px-4 py-3 text-sm font-semibold text-slate-950 transition hover:brightness-110">
                    Buat Quiz
                </button>
            </form>
        </div>

        <div class="lg:col-span-2">
            <div class="space-y-3">
                @forelse ($quizzes as $quiz)
                    <div class="glass-strong rounded-2xl p-5">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div class="glass flex h-11 w-11 items-center justify-center rounded-full">
                                    <x-icon name="gamepad" class="h-5 w-5 icon-glow text-cyan-300" />
                                </div>
                                <div>
                                    <p class="font-display font-semibold">{{ $quiz->title }}</p>
                                    <p class="text-xs text-white/50">{{ $quiz->questions_count }} soal</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.quizzes.questions', $quiz) }}" class="glass rounded-full px-3 py-1.5 text-xs font-medium text-white/80 transition hover:bg-white/10">
                                    Kelola Soal
                                </a>
                                <button type="button" class="toggle-edit-btn glass rounded-full px-3 py-1.5 text-xs font-medium text-white/80 transition hover:bg-white/10" data-target="edit-quiz-{{ $quiz->id }}">
                                    Edit
                                </button>
                                <form method="POST" action="{{ route('admin.quizzes.destroy', $quiz) }}" class="confirm-delete" data-message="Hapus quiz {{ $quiz->title }}? Semua soalnya ikut terhapus.">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="glass rounded-full p-2 text-white/60 transition hover:bg-white/10 hover:text-rose-300">
                                        <x-icon name="close" class="h-3.5 w-3.5" />
                                    </button>
                                </form>
                            </div>
                        </div>

                        <form id="edit-quiz-{{ $quiz->id }}" method="POST" action="{{ route('admin.quizzes.update', $quiz) }}" class="mt-4 hidden space-y-3 border-t border-white/10 pt-4">
                            @csrf
                            @method('PUT')
                            <input type="text" name="title" value="{{ $quiz->title }}" required
                                class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm text-white outline-none focus:border-violet-400/60">
                            <textarea name="description" rows="2"
                                class="w-full resize-none rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm text-white outline-none focus:border-violet-400/60">{{ $quiz->description }}</textarea>
                            <button type="submit" class="rounded-xl bg-gradient-to-r from-cyan-400 via-violet-400 to-pink-400 px-4 py-2.5 text-sm font-semibold text-slate-950 transition hover:brightness-110">
                                Simpan
                            </button>
                        </form>
                    </div>
                @empty
                    <div class="glass-strong rounded-2xl p-8 text-center text-sm text-white/50">
                        Belum ada quiz. Buat quiz pertama di sebelah kiri.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
