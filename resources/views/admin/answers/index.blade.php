@extends('layouts.admin')

@section('title', 'Penilaian Jawaban')

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="font-display text-3xl font-bold">Penilaian Jawaban</h1>
            <p class="mt-1 text-sm text-white/50">Nilai jawaban tim secara live — benar atau salah.</p>
        </div>

        <form method="GET" action="{{ route('admin.answers.index') }}" class="flex flex-wrap items-center gap-2">
            <select name="team_id" onchange="this.form.submit()" class="rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-white outline-none focus:border-violet-400/60">
                <option value="">Semua Tim</option>
                @foreach ($teams as $t)
                    <option value="{{ $t->id }}" {{ (string) $filterTeamId === (string) $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                @endforeach
            </select>
            <select name="question_id" onchange="this.form.submit()" class="rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-white outline-none focus:border-violet-400/60">
                <option value="">Semua Soal</option>
                @foreach ($questions as $q)
                    <option value="{{ $q->id }}" {{ (string) $filterQuestionId === (string) $q->id ? 'selected' : '' }}>{{ $q->title }}</option>
                @endforeach
            </select>
            <select name="status" onchange="this.form.submit()" class="rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-white outline-none focus:border-violet-400/60">
                <option value="">Semua Status</option>
                <option value="pending" {{ $filterStatus === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="correct" {{ $filterStatus === 'correct' ? 'selected' : '' }}>Benar</option>
                <option value="incorrect" {{ $filterStatus === 'incorrect' ? 'selected' : '' }}>Salah</option>
            </select>
        </form>
    </div>

    <div id="answers-list" class="space-y-3">
        @forelse ($answers as $answer)
            <div class="glass-strong grade-card rounded-2xl p-5" data-answer-id="{{ $answer->id }}" data-status="{{ $answer->status }}">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div class="flex-1">
                        <div class="flex flex-wrap items-center gap-2 text-xs text-white/50">
                            <span class="glass rounded-full px-2.5 py-1 font-semibold text-violet-300">{{ $answer->team->name ?? '—' }}</span>
                            <span>{{ $answer->question->title ?? '—' }}</span>
                            <span>&middot;</span>
                            <span>{{ optional($answer->participant)->nama_lengkap ?? '—' }}</span>
                            <span>&middot;</span>
                            <span>{{ $answer->updated_at->timezone(config('app.timezone'))->format('d M H:i') }}</span>
                        </div>

                        @if ($answer->answer_text)
                            <p class="mt-2 text-sm text-white/90">{{ $answer->answer_text }}</p>
                        @endif

                        @if ($answer->image_url)
                            <a href="{{ $answer->image_url }}" target="_blank" class="mt-2 inline-block">
                                <img src="{{ $answer->image_url }}" alt="" class="h-24 rounded-xl object-cover">
                            </a>
                        @endif

                        <p class="status-label mt-2 text-xs {{ $answer->status === 'correct' ? 'text-emerald-300' : ($answer->status === 'incorrect' ? 'text-rose-300' : 'text-white/40') }}">
                            @if ($answer->status === 'correct')
                                ✅ Benar &middot; dinilai oleh {{ optional($answer->gradedBy)->nama_lengkap ?? '—' }}
                            @elseif ($answer->status === 'incorrect')
                                ❌ Salah &middot; dinilai oleh {{ optional($answer->gradedBy)->nama_lengkap ?? '—' }}
                            @else
                                ⏳ Menunggu penilaian
                            @endif
                        </p>
                    </div>

                    <div class="flex shrink-0 items-center gap-2">
                        <button type="button" class="grade-btn rounded-full border border-emerald-400/40 bg-emerald-400/10 px-4 py-2 text-xs font-semibold text-emerald-200 transition hover:bg-emerald-400/20" data-status="correct">
                            Benar
                        </button>
                        <button type="button" class="grade-btn rounded-full border border-rose-400/40 bg-rose-400/10 px-4 py-2 text-xs font-semibold text-rose-200 transition hover:bg-rose-400/20" data-status="incorrect">
                            Salah
                        </button>
                        <button type="button" class="grade-btn glass rounded-full px-4 py-2 text-xs font-semibold text-white/70 transition hover:bg-white/10" data-status="pending">
                            Reset
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="glass-strong rounded-2xl p-8 text-center text-sm text-white/50">
                Belum ada jawaban yang masuk.
            </div>
        @endforelse
    </div>
@endsection
