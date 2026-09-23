<!doctype html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Host — Quiz Rush</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Russo+One&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/quiz-host.js'])
</head>
<body class="bg-app-gradient min-h-screen font-sans text-white antialiased">

    <div class="pointer-events-none fixed inset-0 -z-10 overflow-hidden">
        <div class="absolute -top-40 -left-40 h-96 w-96 animate-float-slow rounded-full bg-purple-600/30 blur-3xl"></div>
        <div class="absolute -right-32 top-1/3 h-[28rem] w-[28rem] animate-float-slower rounded-full bg-cyan-500/25 blur-3xl"></div>
    </div>

    <header class="sticky top-4 z-40 mx-4 lg:mx-8">
        <div class="glass flex flex-wrap items-center justify-between gap-3 rounded-2xl px-5 py-3">
            <div class="flex items-center gap-2">
                <x-icon name="gamepad" class="h-6 w-6 icon-glow text-cyan-300" />
                <span class="font-accent text-lg tracking-wide">Quiz <span class="text-gradient">Rush</span></span>
            </div>
            <div class="glass flex items-center gap-2 rounded-full px-4 py-1.5">
                <span class="text-xs text-white/50">Game PIN</span>
                <span class="font-display text-xl font-bold tracking-[0.2em] text-gradient">{{ $game->room_code }}</span>
            </div>
            <a href="{{ route('host.quiz.index') }}" class="glass rounded-full px-3 py-1.5 text-xs font-medium text-white/70 transition hover:bg-white/10">Keluar</a>
        </div>
    </header>

    <main class="mx-auto max-w-5xl px-4 pb-24 pt-10 lg:px-8">

        <div class="mb-6 grid grid-cols-3 gap-3 text-center sm:gap-4">
            <div class="glass rounded-2xl px-4 py-4">
                <p class="font-display text-2xl font-bold text-gradient" id="stat-players">0</p>
                <p class="mt-1 text-xs text-white/60">Players</p>
            </div>
            <div class="glass rounded-2xl px-4 py-4">
                <p class="font-display text-2xl font-bold text-gradient" id="stat-question">- / {{ $totalQuestions }}</p>
                <p class="mt-1 text-xs text-white/60">Current Question</p>
            </div>
            <div class="glass rounded-2xl px-4 py-4">
                <p class="font-display text-2xl font-bold text-gradient" id="stat-timer">--</p>
                <p class="mt-1 text-xs text-white/60">Timer</p>
            </div>
        </div>

        {{-- LOBBY --}}
        <section id="host-phase-lobby" class="hidden">
            <div class="glass-strong rounded-3xl p-8 text-center">
                <p class="font-display text-xl font-semibold">Menunggu peserta bergabung...</p>
                <p class="mt-1 text-sm text-white/50">Bagikan Game PIN <span class="font-bold text-gradient">{{ $game->room_code }}</span> ke peserta.</p>
                <div id="host-player-list" class="mt-6 flex flex-wrap justify-center gap-2"></div>
                <button id="btn-start" type="button" class="btn-glow mt-8 rounded-full bg-gradient-to-r from-cyan-400 via-violet-400 to-pink-400 px-10 py-3.5 text-base font-bold text-slate-950 transition hover:brightness-110">
                    START GAME
                </button>
            </div>
        </section>

        {{-- QUESTION / RESULT --}}
        <section id="host-phase-question" class="hidden">
            <div class="glass-strong rounded-3xl p-6">
                <p id="host-q-text" class="font-display text-xl font-semibold"></p>
                <img id="host-q-image" src="" alt="" class="mt-3 hidden max-h-56 w-full rounded-xl object-cover">
                <div id="host-q-bars" class="mt-6 space-y-3"></div>
            </div>
            <div class="mt-4 flex items-center justify-between">
                <p id="host-answer-progress" class="text-sm text-white/60"></p>
                <button id="btn-next" type="button" class="btn-glow rounded-full bg-gradient-to-r from-cyan-400 via-violet-400 to-pink-400 px-8 py-3 text-sm font-bold text-slate-950 transition hover:brightness-110">
                    NEXT
                </button>
            </div>
        </section>

        {{-- LEADERBOARD --}}
        <section id="host-phase-leaderboard" class="hidden">
            <p class="font-display text-2xl font-bold">🏆 Leaderboard</p>
            <div id="host-leaderboard-rows" class="mt-5 space-y-2"></div>
            <button id="btn-next-from-leaderboard" type="button" class="btn-glow mt-6 rounded-full bg-gradient-to-r from-cyan-400 via-violet-400 to-pink-400 px-8 py-3 text-sm font-bold text-slate-950 transition hover:brightness-110">
                NEXT QUESTION
            </button>
        </section>

        {{-- FINISHED --}}
        <section id="host-phase-finished" class="hidden text-center">
            <p class="font-display text-3xl font-bold text-gradient">🏆 GAME FINISHED</p>
            <div id="host-podium" class="mx-auto mt-6 max-w-md space-y-3"></div>
            <a href="{{ route('host.quiz.index') }}" class="glass mt-8 inline-block rounded-full px-6 py-2.5 text-sm font-semibold hover:bg-white/10">Kembali</a>
        </section>
    </main>

    <script>
        window.__HOST_STATE__ = {!! Js::from([
            'roomCode' => $game->room_code,
            'snapshot' => $snapshot,
        ]) !!};
    </script>
</body>
</html>
