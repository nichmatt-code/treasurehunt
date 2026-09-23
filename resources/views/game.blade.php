<!doctype html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mission Board — Mission One</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Russo+One&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/game.js'])
</head>
<body class="bg-app-gradient min-h-screen font-sans text-white antialiased">

    <div class="pointer-events-none fixed inset-0 -z-10 overflow-hidden">
        <div class="absolute -top-40 -left-40 h-96 w-96 animate-float-slow rounded-full bg-purple-600/30 blur-3xl"></div>
        <div class="absolute -right-32 top-1/3 h-[28rem] w-[28rem] animate-float-slower rounded-full bg-cyan-500/25 blur-3xl"></div>
        <div class="absolute bottom-0 left-1/4 h-80 w-80 animate-float-slow rounded-full bg-pink-500/20 blur-3xl"></div>
    </div>

    <header class="sticky top-4 z-40 mx-4 lg:mx-8">
        <div class="glass flex items-center justify-between rounded-2xl px-5 py-3">
            <a href="/" class="flex items-center gap-2">
                <x-icon name="compass" class="h-6 w-6 icon-glow text-cyan-300" />
                <span class="font-accent text-lg tracking-wide">Mission<span class="text-gradient">One</span></span>
            </a>
            <div class="flex items-center gap-2">
                @if ($isAdmin)
                    <a href="{{ route('admin.teams.index') }}" class="glass hidden rounded-full px-3 py-1.5 text-xs font-medium text-white/80 transition hover:bg-white/10 sm:inline-flex">
                        Admin Panel
                    </a>
                @endif
                <span class="glass flex items-center gap-1.5 rounded-full px-3 py-1.5 text-xs font-medium text-white/80">
                    <x-icon name="user" class="h-3.5 w-3.5 text-cyan-300" />
                    {{ $participant->nama_lengkap }}
                </span>
                <button id="nav-logout-btn" type="button" title="Logout" class="glass rounded-full p-2 text-white/70 transition hover:bg-white/10 hover:text-rose-300">
                    <x-icon name="logout" class="h-4 w-4" />
                </button>
            </div>
        </div>
    </header>

    <section class="mx-auto max-w-7xl px-4 pb-24 pt-10 lg:px-8">

        <div class="mb-8 flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="font-display text-3xl font-bold sm:text-4xl">Mission Board</h1>
                <p class="mt-1 text-sm text-white/50">
                    @if ($team)
                        Tim: <span class="font-semibold text-gradient">{{ $team->name }}</span>
                    @else
                        Belum ada tim yang dipilih.
                    @endif
                </p>
            </div>

            @if ($isAdmin)
                <form method="GET" action="{{ route('game.index') }}" class="flex items-center gap-2">
                    <label class="text-xs text-white/50">Lihat tim:</label>
                    <select name="team" onchange="this.form.submit()" class="rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-white outline-none focus:border-violet-400/60">
                        <option value="">— Pilih tim —</option>
                        @foreach ($teams as $t)
                            <option value="{{ $t->id }}" {{ $team && $team->id === $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                        @endforeach
                    </select>
                </form>
            @endif
        </div>

        @if (! $team)
            <div class="glass-strong mx-auto max-w-lg rounded-3xl p-8 text-center">
                <div class="glass mx-auto flex h-14 w-14 items-center justify-center rounded-full">
                    <x-icon name="shield" class="h-7 w-7 icon-glow text-violet-300" />
                </div>
                <p class="font-display mt-4 text-lg font-semibold">Kamu belum ditempatkan di tim</p>
                <p class="mt-2 text-sm text-white/50">Hubungi admin untuk ditempatkan ke sebuah tim sebelum bisa mengakses misi &amp; live chat tim.</p>
            </div>
        @else
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-5 lg:gap-8">

                <div class="space-y-4 lg:col-span-2">
                    @foreach ($questions as $question)
                        @php $existing = $answers->get($question->id); @endphp
                        <div class="glass-strong rounded-3xl p-5">
                            <p class="font-display text-base font-semibold">{{ $question->title }}</p>
                            @if ($question->description)
                                <p class="mt-1 text-sm text-white/50">{{ $question->description }}</p>
                            @endif
                            @if ($question->image_url)
                                <img src="{{ $question->image_url }}" alt="" class="mt-3 max-h-48 w-full rounded-xl object-cover">
                            @endif

                            <form class="answer-form mt-4 space-y-2" data-question-id="{{ $question->id }}">
                                <textarea
                                    name="answer_text"
                                    rows="2"
                                    class="w-full resize-none rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm text-white placeholder-white/30 outline-none transition focus:border-violet-400/60 focus:bg-white/10"
                                    placeholder="Tulis jawaban tim kamu di sini..."
                                >{{ $existing->answer_text ?? '' }}</textarea>

                                <div class="answer-image-wrap {{ $existing && $existing->image_url ? '' : 'hidden' }} flex items-center gap-2">
                                    <img src="{{ $existing->image_url ?? '' }}" alt="" class="answer-image-preview h-14 w-14 rounded-lg object-cover">
                                    <button type="button" class="remove-answer-image-btn text-xs text-rose-300 hover:underline">Hapus gambar</button>
                                </div>
                                <input type="hidden" name="remove_image" class="remove-image-flag" value="0">
                                <input type="file" name="answer_image" accept="image/*"
                                    class="answer-image-input w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-xs text-white/70 outline-none transition file:mr-3 file:rounded-lg file:border-0 file:bg-white/10 file:px-3 file:py-1.5 file:text-xs file:text-white">

                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <div>
                                        <p class="answer-meta text-xs text-white/40">
                                            @if ($existing)
                                                Terakhir diisi oleh {{ optional($existing->participant)->nama_lengkap ?? '—' }} &middot; {{ $existing->updated_at->timezone(config('app.timezone'))->format('H:i') }}
                                            @else
                                                Belum dijawab
                                            @endif
                                        </p>
                                        <p class="answer-status-badge mt-0.5 text-xs {{ ($existing->status ?? null) === 'correct' ? 'text-emerald-300' : (($existing->status ?? null) === 'incorrect' ? 'text-rose-300' : 'text-white/30') }}">
                                            @if (($existing->status ?? 'pending') === 'correct')
                                                ✅ Benar
                                            @elseif (($existing->status ?? 'pending') === 'incorrect')
                                                ❌ Salah
                                            @elseif ($existing)
                                                ⏳ Menunggu dinilai
                                            @endif
                                        </p>
                                    </div>
                                    <button type="submit" class="btn-glow shrink-0 rounded-full bg-gradient-to-r from-cyan-400 via-violet-400 to-pink-400 px-4 py-1.5 text-xs font-semibold text-slate-950 transition hover:brightness-110">
                                        Simpan
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endforeach

                    @if ($questions->isEmpty())
                        <div class="glass-strong rounded-3xl p-5 text-center text-sm text-white/50">
                            Belum ada misi yang ditambahkan.
                        </div>
                    @endif
                </div>

                <div class="glass-strong flex h-[640px] flex-col rounded-3xl p-4 lg:col-span-3 lg:p-6">
                    <div class="mb-3 flex items-center justify-between border-b border-white/10 pb-3">
                        <div class="flex items-center gap-2">
                            <x-icon name="chat" class="h-5 w-5 icon-glow text-violet-300" />
                            <h2 class="font-display text-lg font-semibold">Live Chat Tim</h2>
                        </div>
                        <span class="flex items-center gap-1.5 text-xs text-white/50">
                            <span class="relative flex h-2 w-2">
                                <span class="absolute inline-flex h-full w-full animate-pulse-dot rounded-full bg-emerald-400"></span>
                            </span>
                            Live
                        </span>
                    </div>

                    <div id="chat-messages" class="chat-scroll flex-1 space-y-3 overflow-y-auto pr-1">
                        @foreach ($messages as $message)
                            @php $isMe = $message->participant_id === $participant->id; @endphp
                            @if ($message->type === 'system')
                                <div class="flex justify-center" data-message-id="{{ $message->id }}">
                                    <span class="rounded-full bg-white/5 px-3 py-1 text-center text-xs text-white/50">{{ $message->message }}</span>
                                </div>
                            @else
                                <div class="flex {{ $isMe ? 'justify-end' : 'justify-start' }}" data-message-id="{{ $message->id }}">
                                    <div class="max-w-[75%] rounded-2xl px-4 py-2.5 {{ $isMe ? 'bg-gradient-to-r from-cyan-400/90 to-violet-400/90 text-slate-950' : 'glass' }}">
                                        @unless ($isMe)
                                            <p class="mb-0.5 text-xs font-semibold text-violet-300">{{ $message->sender_name }}</p>
                                        @endunless
                                        <p class="text-sm leading-snug">{{ $message->message }}</p>
                                        <p class="mt-1 text-right text-[10px] opacity-60">{{ $message->created_at->format('H:i') }}</p>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>

                    <form id="chat-form" class="mt-3 flex items-center gap-2 border-t border-white/10 pt-3">
                        <input type="text" id="chat-input" autocomplete="off" maxlength="500"
                            class="flex-1 rounded-full border border-white/10 bg-white/5 px-4 py-2.5 text-sm text-white placeholder-white/30 outline-none transition focus:border-violet-400/60 focus:bg-white/10"
                            placeholder="Tulis pesan ke tim...">
                        <button type="submit" id="chat-send"
                            class="btn-glow flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gradient-to-r from-cyan-400 via-violet-400 to-pink-400 text-slate-950 transition hover:brightness-110">
                            <x-icon name="send" class="h-4 w-4" />
                        </button>
                    </form>
                </div>
            </div>
        @endif
    </section>

    <div id="toast" class="pointer-events-none fixed bottom-4 right-4 z-50 flex flex-col gap-2"></div>

    <script>
        window.__GAME_STATE__ = {!! Js::from([
            'participant' => ['id' => $participant->id, 'nama_lengkap' => $participant->nama_lengkap],
            'team' => $team ? ['id' => $team->id, 'name' => $team->name] : null,
            'isAdmin' => $isAdmin,
            'lastMessageId' => optional($messagesFeed->last())['id'] ?? 0,
        ]) !!};
    </script>
</body>
</html>
