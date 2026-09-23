<!doctype html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin') — Mission One</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Russo+One&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/admin.js'])
</head>
<body class="bg-app-gradient min-h-screen font-sans text-white antialiased">

    <div class="pointer-events-none fixed inset-0 -z-10 overflow-hidden">
        <div class="absolute -top-40 -left-40 h-96 w-96 animate-float-slow rounded-full bg-purple-600/30 blur-3xl"></div>
        <div class="absolute -right-32 top-1/3 h-[28rem] w-[28rem] animate-float-slower rounded-full bg-cyan-500/25 blur-3xl"></div>
    </div>

    <header class="sticky top-4 z-40 mx-4 lg:mx-8">
        <div class="glass flex items-center justify-between rounded-2xl px-5 py-3">
            <a href="/" class="flex items-center gap-2">
                <x-icon name="compass" class="h-6 w-6 icon-glow text-cyan-300" />
                <span class="font-accent text-lg tracking-wide">Mission<span class="text-gradient">One</span></span>
                <span class="glass ml-1 rounded-full px-2.5 py-0.5 text-[10px] font-semibold uppercase tracking-wider text-violet-300">Admin</span>
            </a>
            <nav class="hidden items-center gap-5 text-sm text-white/70 lg:flex">
                <a href="{{ route('admin.teams.index') }}" class="transition hover:text-white {{ request()->routeIs('admin.teams.*') ? 'text-white' : '' }}">Tim</a>
                <a href="{{ route('admin.participants.index') }}" class="transition hover:text-white {{ request()->routeIs('admin.participants.*') ? 'text-white' : '' }}">Peserta</a>
                <a href="{{ route('admin.questions.index') }}" class="transition hover:text-white {{ request()->routeIs('admin.questions.*') ? 'text-white' : '' }}">Soal</a>
                <a href="{{ route('admin.answers.index') }}" class="transition hover:text-white {{ request()->routeIs('admin.answers.*') ? 'text-white' : '' }}">Penilaian</a>
                <a href="{{ route('game.index') }}" class="transition hover:text-white">Mission Board</a>
            </nav>
            <a href="/" class="glass rounded-full px-4 py-1.5 text-xs font-semibold text-white/90 transition hover:bg-white/10">
                Keluar Admin
            </a>
        </div>
    </header>

    <main class="mx-auto max-w-6xl px-4 pb-24 pt-10 lg:px-8">
        @if (session('status'))
            <div class="glass mb-6 rounded-xl border border-emerald-400/30 bg-emerald-400/10 px-4 py-3 text-sm text-emerald-100">
                {{ session('status') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="glass mb-6 rounded-xl border border-rose-400/30 bg-rose-400/10 px-4 py-3 text-sm text-rose-200">
                <ul class="list-inside list-disc space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>
</body>
</html>
