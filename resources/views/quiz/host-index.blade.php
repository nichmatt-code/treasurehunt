@extends('layouts.admin')

@section('title', 'Host Quiz')

@section('content')
    <div class="mb-8 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="font-display text-3xl font-bold">Host a Game</h1>
            <p class="mt-1 text-sm text-white/50">Pilih quiz untuk dimulai sebagai game baru.</p>
        </div>
        <a href="{{ route('admin.quizzes.index') }}" class="glass rounded-full px-4 py-2 text-sm font-medium text-white/80 transition hover:bg-white/10">
            Kelola Quiz
        </a>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($quizzes as $quiz)
            <div class="glass-strong rounded-3xl p-6">
                <div class="glass flex h-12 w-12 items-center justify-center rounded-full">
                    <x-icon name="gamepad" class="h-6 w-6 icon-glow text-cyan-300" />
                </div>
                <p class="font-display mt-4 text-lg font-semibold">{{ $quiz->title }}</p>
                <p class="mt-1 text-xs text-white/50">{{ $quiz->questions_count }} soal</p>
                <form method="POST" action="{{ route('host.quiz.games.store') }}" class="mt-4">
                    @csrf
                    <input type="hidden" name="quiz_id" value="{{ $quiz->id }}">
                    <button type="submit" class="btn-glow w-full rounded-xl bg-gradient-to-r from-cyan-400 via-violet-400 to-pink-400 px-4 py-2.5 text-sm font-bold text-slate-950 transition hover:brightness-110" @disabled($quiz->questions_count === 0)>
                        {{ $quiz->questions_count === 0 ? 'Belum ada soal' : 'START GAME' }}
                    </button>
                </form>
            </div>
        @empty
            <div class="glass-strong col-span-full rounded-2xl p-8 text-center text-sm text-white/50">
                Belum ada quiz. <a href="{{ route('admin.quizzes.index') }}" class="text-violet-300 underline">Buat quiz baru</a>.
            </div>
        @endforelse
    </div>
@endsection
