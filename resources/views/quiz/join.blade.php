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
        <div class="absolute bottom-0 left-1/4 h-80 w-80 animate-float-slow rounded-full bg-pink-500/20 blur-3xl"></div>
    </div>

    <header class="sticky top-4 z-40 mx-4 lg:mx-8">
        <div class="glass flex items-center justify-between rounded-2xl px-5 py-3">
            <a href="/" class="flex items-center gap-2">
                <x-icon name="compass" class="h-6 w-6 icon-glow text-cyan-300" />
                <span class="font-accent text-lg tracking-wide">Mission<span class="text-gradient">One</span></span>
            </a>
            @if ($isAdmin)
                <a href="{{ route('host.quiz.index') }}" class="glass inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-xs font-semibold text-white/90 transition hover:bg-white/10">
                    <x-icon name="gamepad" class="h-3.5 w-3.5" />
                    Host a Game
                </a>
            @endif
        </div>
    </header>

    <section class="mx-auto flex max-w-md flex-col items-center px-4 pb-24 pt-16 text-center lg:pt-24">
        <div class="glass mb-6 inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-xs font-medium text-white/80">
            <x-icon name="zap" class="h-3.5 w-3.5 icon-glow text-cyan-300" />
            Realtime Quiz Battle
        </div>
        <h1 class="font-display text-5xl font-bold leading-tight tracking-tight sm:text-6xl">
            Quiz <span class="font-accent text-gradient tracking-wide">Rush</span>
        </h1>
        <p class="mt-4 max-w-sm text-sm text-white/60 sm:text-base">
            Masukkan Game PIN dari host dan nickname kamu untuk gabung.
        </p>

        <form id="join-form" class="glass-strong mt-8 w-full space-y-4 rounded-3xl p-6">
            <p id="join-error" class="hidden rounded-xl border border-rose-400/30 bg-rose-400/10 px-4 py-2 text-sm text-rose-200"></p>

            <div>
                <label class="mb-1 block text-left text-xs font-medium text-white/70">Game PIN</label>
                <input type="text" inputmode="numeric" pattern="[0-9]*" name="room_code" maxlength="6" required
                    class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3.5 text-center text-2xl font-display tracking-[0.3em] text-white placeholder-white/20 outline-none transition focus:border-violet-400/60 focus:bg-white/10"
                    placeholder="000000">
            </div>

            <div>
                <label class="mb-1 block text-left text-xs font-medium text-white/70">Nickname</label>
                <input type="text" name="nickname" maxlength="20" required
                    class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-center text-sm text-white placeholder-white/30 outline-none transition focus:border-violet-400/60 focus:bg-white/10"
                    placeholder="Nama panggilan kamu">
            </div>

            <button type="submit" id="join-submit" class="btn-glow w-full rounded-xl bg-gradient-to-r from-cyan-400 via-violet-400 to-pink-400 px-4 py-3.5 text-base font-bold text-slate-950 transition hover:brightness-110 disabled:opacity-60">
                JOIN GAME
            </button>
        </form>
    </section>
</body>
</html>
