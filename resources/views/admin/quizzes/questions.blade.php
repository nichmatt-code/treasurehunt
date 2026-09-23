@extends('layouts.admin')

@section('title', 'Soal — ' . $quiz->title)

@section('content')
    <div class="mb-8 flex flex-wrap items-center justify-between gap-4">
        <div>
            <a href="{{ route('admin.quizzes.index') }}" class="text-xs text-white/50 hover:text-white">&larr; Semua Quiz</a>
            <h1 class="font-display mt-1 text-3xl font-bold">{{ $quiz->title }}</h1>
            <p class="mt-1 text-sm text-white/50">{{ $questions->count() }} soal.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-5 lg:gap-8">
        <div class="glass-strong h-fit rounded-3xl p-6 lg:col-span-2">
            <h2 class="font-display text-lg font-semibold">Tambah Soal</h2>
            <form method="POST" action="{{ route('admin.quizzes.questions.store', $quiz) }}" enctype="multipart/form-data" class="mt-4 space-y-4">
                @csrf
                <div>
                    <label class="mb-1 block text-xs font-medium text-white/70">Pertanyaan</label>
                    <textarea name="question" rows="2" required
                        class="w-full resize-none rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm text-white placeholder-white/30 outline-none transition focus:border-violet-400/60 focus:bg-white/10"
                        placeholder="Tulis pertanyaan..."></textarea>
                </div>

                <div>
                    <label class="mb-1 block text-xs font-medium text-white/70">Pilihan Jawaban (pilih radio untuk jawaban benar)</label>
                    <div class="options-container space-y-2" data-min="2" data-max="4">
                        @foreach (['A', 'B', 'C', 'D'] as $i => $label)
                            <div class="option-row flex items-center gap-2">
                                <input type="radio" name="correct_option_index" value="{{ $i }}" {{ $i === 0 ? 'checked' : '' }} required class="option-radio accent-emerald-400">
                                <span class="option-label w-5 text-xs text-white/40">{{ $label }}</span>
                                <input type="text" name="options[]" maxlength="150" required
                                    class="flex-1 rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-white placeholder-white/30 outline-none focus:border-violet-400/60"
                                    placeholder="Pilihan {{ $label }}">
                                <button type="button" class="remove-option-btn text-white/40 transition hover:text-rose-300">
                                    <x-icon name="close" class="h-3.5 w-3.5" />
                                </button>
                            </div>
                        @endforeach
                    </div>
                    <button type="button" class="add-option-btn mt-2 text-xs font-medium text-violet-300 hover:underline">+ Tambah pilihan</button>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="mb-1 block text-xs font-medium text-white/70">Waktu (detik)</label>
                        <input type="number" name="time_limit_seconds" value="10" min="5" max="120" required
                            class="w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-white outline-none focus:border-violet-400/60">
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium text-white/70">Poin</label>
                        <input type="number" name="points" value="1000" min="100" max="5000" step="50" required
                            class="w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-white outline-none focus:border-violet-400/60">
                    </div>
                </div>

                <div>
                    <label class="mb-1 block text-xs font-medium text-white/70">Gambar (opsional)</label>
                    <input type="file" name="image" accept="image/*"
                        class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-xs text-white/70 outline-none file:mr-3 file:rounded-lg file:border-0 file:bg-white/10 file:px-3 file:py-1.5 file:text-xs file:text-white">
                </div>

                <button type="submit" class="btn-glow w-full rounded-xl bg-gradient-to-r from-cyan-400 via-violet-400 to-pink-400 px-4 py-3 text-sm font-semibold text-slate-950 transition hover:brightness-110">
                    Tambah Soal
                </button>
            </form>
        </div>

        <div class="lg:col-span-3">
            <div class="space-y-3">
                @forelse ($questions as $question)
                    <div class="glass-strong rounded-2xl p-5">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div class="flex items-start gap-3">
                                @if ($question->image_url)
                                    <img src="{{ $question->image_url }}" alt="" class="h-14 w-14 shrink-0 rounded-xl object-cover">
                                @endif
                                <div>
                                    <p class="font-display font-semibold">{{ $question->question }}</p>
                                    <p class="mt-1 text-xs text-white/50">
                                        {{ $question->time_limit_seconds }}s &middot; {{ $question->points }} pts
                                        &middot; benar: {{ $question->options[$question->correct_option_index] ?? '-' }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <form method="POST" action="{{ route('admin.quizzes.questions.move', [$quiz, $question]) }}">
                                    @csrf
                                    <input type="hidden" name="direction" value="up">
                                    <button type="submit" class="glass rounded-full p-2 text-white/60 transition hover:bg-white/10">↑</button>
                                </form>
                                <form method="POST" action="{{ route('admin.quizzes.questions.move', [$quiz, $question]) }}">
                                    @csrf
                                    <input type="hidden" name="direction" value="down">
                                    <button type="submit" class="glass rounded-full p-2 text-white/60 transition hover:bg-white/10">↓</button>
                                </form>
                                <button type="button" class="toggle-edit-btn glass rounded-full px-3 py-1.5 text-xs font-medium text-white/80 transition hover:bg-white/10" data-target="edit-q-{{ $question->id }}">
                                    Edit
                                </button>
                                <form method="POST" action="{{ route('admin.quizzes.questions.destroy', [$quiz, $question]) }}" class="confirm-delete" data-message="Hapus soal ini?">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="glass rounded-full p-2 text-white/60 transition hover:bg-white/10 hover:text-rose-300">
                                        <x-icon name="close" class="h-3.5 w-3.5" />
                                    </button>
                                </form>
                            </div>
                        </div>

                        <form id="edit-q-{{ $question->id }}" method="POST" action="{{ route('admin.quizzes.questions.update', [$quiz, $question]) }}" enctype="multipart/form-data" class="mt-4 hidden space-y-3 border-t border-white/10 pt-4">
                            @csrf
                            @method('PUT')
                            <textarea name="question" rows="2" required
                                class="w-full resize-none rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm text-white outline-none focus:border-violet-400/60">{{ $question->question }}</textarea>

                            <div class="options-container space-y-2" data-min="2" data-max="4">
                                @foreach ($question->options as $i => $optionText)
                                    <div class="option-row flex items-center gap-2">
                                        <input type="radio" name="correct_option_index" value="{{ $i }}" {{ $i === $question->correct_option_index ? 'checked' : '' }} required class="option-radio accent-emerald-400">
                                        <span class="option-label w-5 text-xs text-white/40">{{ ['A','B','C','D'][$i] ?? '' }}</span>
                                        <input type="text" name="options[]" maxlength="150" required value="{{ $optionText }}"
                                            class="flex-1 rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-white outline-none focus:border-violet-400/60">
                                        <button type="button" class="remove-option-btn text-white/40 transition hover:text-rose-300">
                                            <x-icon name="close" class="h-3.5 w-3.5" />
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                            <button type="button" class="add-option-btn text-xs font-medium text-violet-300 hover:underline">+ Tambah pilihan</button>

                            <div class="grid grid-cols-2 gap-3">
                                <input type="number" name="time_limit_seconds" value="{{ $question->time_limit_seconds }}" min="5" max="120" required
                                    class="w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-white outline-none focus:border-violet-400/60">
                                <input type="number" name="points" value="{{ $question->points }}" min="100" max="5000" step="50" required
                                    class="w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-white outline-none focus:border-violet-400/60">
                            </div>

                            <input type="file" name="image" accept="image/*"
                                class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-xs text-white/70 outline-none file:mr-3 file:rounded-lg file:border-0 file:bg-white/10 file:px-3 file:py-1.5 file:text-xs file:text-white">

                            <button type="submit" class="w-full rounded-xl bg-gradient-to-r from-cyan-400 via-violet-400 to-pink-400 px-4 py-2.5 text-sm font-semibold text-slate-950 transition hover:brightness-110">
                                Simpan Perubahan
                            </button>
                        </form>
                    </div>
                @empty
                    <div class="glass-strong rounded-2xl p-8 text-center text-sm text-white/50">
                        Belum ada soal. Tambahkan soal pertama di sebelah kiri.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
