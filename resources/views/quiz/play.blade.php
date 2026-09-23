<!doctype html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quiz Rush — Mission One</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Russo+One&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/quiz-player.js'])
</head>
<body class="bg-app-gradient min-h-screen font-sans text-white antialiased">

    <div class="pointer-events-none fixed inset-0 -z-10 overflow-hidden">
        <div class="absolute -top-40 -left-40 h-96 w-96 animate-float-slow rounded-full bg-purple-600/30 blur-3xl"></div>
        <div class="absolute -right-32 top-1/3 h-[28rem] w-[28rem] animate-float-slower rounded-full bg-cyan-500/25 blur-3xl"></div>
    </div>

    <div id="reconnect-banner" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-950/90 p-4 text-center">
        <div>
            <p class="font-display text-lg font-semibold">Sesi tidak ditemukan</p>
            <p class="mt-2 text-sm text-white/60">Kamu belum join game ini di perangkat ini.</p>
            <a href="{{ route('quiz.join') }}" class="btn-glow mt-4 inline-block rounded-full bg-gradient-to-r from-cyan-400 via-violet-400 to-pink-400 px-6 py-2.5 text-sm font-semibold text-slate-950">Join Ulang</a>
        </div>
    </div>

    <main class="mx-auto flex min-h-screen max-w-lg flex-col items-center justify-center px-4 py-8 text-center">

        {{-- LOBBY --}}
        <section id="phase-lobby" class="hidden w-full">
            <div class="glass mx-auto flex h-16 w-16 items-center justify-center rounded-full">
                <x-icon name="gamepad" class="h-8 w-8 icon-glow text-cyan-300" />
            </div>
            <p class="font-display mt-4 text-2xl font-bold">Kamu masuk!</p>
            <p class="mt-1 text-sm text-white/60">Halo, <span id="lobby-nickname" class="font-semibold text-white"></span> — menunggu host memulai game...</p>
            <div class="glass mt-6 inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm">
                <span class="relative flex h-2 w-2">
                    <span class="absolute inline-flex h-full w-full animate-pulse-dot rounded-full bg-emerald-400"></span>
                </span>
                <span id="lobby-player-count">0</span> peserta sudah bergabung
            </div>
        </section>

        {{-- QUESTION --}}
        <section id="phase-question" class="hidden w-full">
            <div class="mb-3 flex items-center justify-between text-xs text-white/50">
                <span id="q-index">Soal 1/10</span>
                <span class="glass flex items-center gap-1.5 rounded-full px-3 py-1 font-display font-bold text-gradient">
                    <x-icon name="clock" class="h-3.5 w-3.5" />
                    <span id="q-timer">10</span>
                </span>
            </div>
            <div class="glass-strong rounded-3xl p-5">
                <p id="q-text" class="font-display text-lg font-semibold leading-snug"></p>
                <img id="q-image" src="" alt="" class="mt-3 hidden max-h-40 w-full rounded-xl object-cover">
            </div>
            <div id="q-options" class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2"></div>
            <p id="q-submitted" class="glass mt-4 hidden rounded-2xl px-4 py-3 text-sm font-semibold text-gradient">
                ✅ Answer Submitted! Menunggu peserta lain...
            </p>
        </section>

        {{-- RESULT --}}
        <section id="phase-result" class="hidden w-full">
            <div id="result-icon" class="glass mx-auto flex h-16 w-16 items-center justify-center rounded-full"></div>
            <p id="result-title" class="font-display mt-4 text-2xl font-bold"></p>
            <p id="result-points" class="mt-1 text-sm text-white/60"></p>
            <p id="result-correct" class="mt-3 text-xs text-white/40"></p>
        </section>

        {{-- LEADERBOARD --}}
        <section id="phase-leaderboard" class="hidden w-full">
            <p class="font-display text-2xl font-bold">🏆 Leaderboard</p>
            <div id="leaderboard-rows" class="mt-5 space-y-2 text-left"></div>
        </section>

        {{-- FINISHED --}}
        <section id="phase-finished" class="hidden w-full">
            <p class="font-display text-3xl font-bold text-gradient">🏆 WINNER</p>
            <div id="podium" class="mt-6 space-y-3"></div>
            <a href="{{ route('quiz.join') }}" class="glass mt-8 inline-block rounded-full px-6 py-2.5 text-sm font-semibold hover:bg-white/10">Main Lagi</a>
        </section>
    </main>

    <div id="confetti-layer" class="pointer-events-none fixed inset-0 z-40 overflow-hidden"></div>

    <script>
        window.__QUIZ_STATE__ = {!! Js::from(['roomCode' => $roomCode]) !!};
    </script>
</body>
</html>
