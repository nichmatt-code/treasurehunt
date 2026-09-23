@extends('layouts.admin')

@section('title', 'Kelola Soal')

@section('content')
    <div class="mb-8">
        <h1 class="font-display text-3xl font-bold">Kelola Soal</h1>
        <p class="mt-1 text-sm text-white/50">Atur daftar misi/soal yang tampil di Mission Board.</p>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3 lg:gap-8">
        <div class="glass-strong h-fit rounded-3xl p-6 lg:col-span-1">
            <h2 class="font-display text-lg font-semibold">Tambah Soal Baru</h2>
            <form method="POST" action="{{ route('admin.questions.store') }}" enctype="multipart/form-data" class="mt-4 space-y-4">
                @csrf
                <div>
                    <label class="mb-1 block text-xs font-medium text-white/70">Judul Soal</label>
                    <input type="text" name="title" required value="{{ old('title') }}"
                        class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm text-white placeholder-white/30 outline-none transition focus:border-violet-400/60 focus:bg-white/10"
                        placeholder="Contoh: Misi 1: Petunjuk Pertama">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium text-white/70">Deskripsi</label>
                    <textarea name="description" rows="3"
                        class="w-full resize-none rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm text-white placeholder-white/30 outline-none transition focus:border-violet-400/60 focus:bg-white/10"
                        placeholder="Instruksi/petunjuk soal...">{{ old('description') }}</textarea>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium text-white/70">Urutan</label>
                    <input type="number" name="order_no" min="0" value="{{ old('order_no', 0) }}"
                        class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm text-white outline-none transition focus:border-violet-400/60 focus:bg-white/10">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium text-white/70">Gambar (opsional)</label>
                    <input type="file" name="image" accept="image/*"
                        class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-xs text-white/70 outline-none transition file:mr-3 file:rounded-lg file:border-0 file:bg-white/10 file:px-3 file:py-1.5 file:text-xs file:text-white">
                </div>
                <button type="submit" class="btn-glow w-full rounded-xl bg-gradient-to-r from-cyan-400 via-violet-400 to-pink-400 px-4 py-3 text-sm font-semibold text-slate-950 transition hover:brightness-110">
                    Tambah Soal
                </button>
            </form>
        </div>

        <div class="lg:col-span-2">
            <div class="space-y-3">
                @forelse ($questions as $question)
                    <div class="glass-strong rounded-2xl p-5">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div class="flex items-start gap-3">
                                @if ($question->image_url)
                                    <img src="{{ $question->image_url }}" alt="" class="h-14 w-14 shrink-0 rounded-xl object-cover">
                                @else
                                    <div class="glass flex h-14 w-14 shrink-0 items-center justify-center rounded-xl">
                                        <x-icon name="shield" class="h-6 w-6 text-white/30" />
                                    </div>
                                @endif
                                <div>
                                    <p class="font-display font-semibold">#{{ $question->order_no }} &middot; {{ $question->title }}</p>
                                    @if ($question->description)
                                        <p class="mt-1 max-w-md text-xs text-white/50">{{ $question->description }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-2">
                                <button type="button" class="toggle-edit-btn glass rounded-full px-3 py-1.5 text-xs font-medium text-white/80 transition hover:bg-white/10" data-target="edit-question-{{ $question->id }}">
                                    Edit
                                </button>
                                <form method="POST" action="{{ route('admin.questions.destroy', $question) }}" class="confirm-delete" data-message="Hapus soal {{ $question->title }}? Semua jawaban tim untuk soal ini juga ikut terhapus.">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="glass rounded-full p-2 text-white/60 transition hover:bg-white/10 hover:text-rose-300">
                                        <x-icon name="close" class="h-3.5 w-3.5" />
                                    </button>
                                </form>
                            </div>
                        </div>

                        <form id="edit-question-{{ $question->id }}" method="POST" action="{{ route('admin.questions.update', $question) }}" enctype="multipart/form-data" class="mt-4 hidden space-y-3 border-t border-white/10 pt-4">
                            @csrf
                            @method('PUT')
                            <input type="text" name="title" value="{{ $question->title }}" required
                                class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm text-white outline-none focus:border-violet-400/60">
                            <textarea name="description" rows="2"
                                class="w-full resize-none rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm text-white outline-none focus:border-violet-400/60">{{ $question->description }}</textarea>
                            <div class="flex flex-wrap items-center gap-3">
                                <input type="number" name="order_no" min="0" value="{{ $question->order_no }}"
                                    class="w-24 rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-white outline-none focus:border-violet-400/60">
                                <input type="file" name="image" accept="image/*"
                                    class="flex-1 rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-xs text-white/70 outline-none file:mr-3 file:rounded-lg file:border-0 file:bg-white/10 file:px-3 file:py-1.5 file:text-xs file:text-white">
                                <button type="submit" class="rounded-xl bg-gradient-to-r from-cyan-400 via-violet-400 to-pink-400 px-4 py-2.5 text-sm font-semibold text-slate-950 transition hover:brightness-110">
                                    Simpan
                                </button>
                            </div>
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
