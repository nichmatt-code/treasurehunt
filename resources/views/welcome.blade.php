<!doctype html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mission One — Get Ready for the Mission</title>
    <meta name="description" content="Mission One, misi game terbesar untuk seluruh pelajar BSD. Daftar sekarang dan gabung di live chat bareng peserta lain.">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Russo+One&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-app-gradient min-h-screen font-sans text-white antialiased">

    <div class="pointer-events-none fixed inset-0 -z-10 overflow-hidden">
        <div class="absolute -top-40 -left-40 h-96 w-96 animate-float-slow rounded-full bg-purple-600/30 blur-3xl"></div>
        <div class="absolute -right-32 top-1/3 h-[28rem] w-[28rem] animate-float-slower rounded-full bg-cyan-500/25 blur-3xl"></div>
        <div class="absolute bottom-0 left-1/4 h-80 w-80 animate-float-slow rounded-full bg-pink-500/20 blur-3xl"></div>
    </div>

    <header class="sticky top-4 z-40 mx-4 lg:mx-8">
        <div class="glass flex items-center justify-between rounded-2xl px-5 py-3">
            <div class="flex items-center gap-2">
                <x-icon name="compass" class="h-6 w-6 icon-glow text-cyan-300" />
                <span class="font-accent text-lg tracking-wide">Mission<span class="text-gradient">One</span></span>
            </div>
            <nav class="hidden items-center gap-6 text-sm text-white/70 sm:flex">
                <a href="#daftar" class="transition hover:text-white">Daftar</a>
                <a href="#chat" class="transition hover:text-white">Live Chat</a>
                <a href="#klasemen" class="transition hover:text-white">Klasemen</a>
                <a href="{{ route('quiz.join') }}" class="flex items-center gap-1 transition hover:text-white">
                    <x-icon name="gamepad" class="h-3.5 w-3.5" />
                    Quiz Rush
                </a>
            </nav>
            <div class="flex items-center gap-2">
                <div class="hidden items-center gap-2 rounded-full bg-white/10 px-3 py-1.5 text-xs font-medium text-white/80 sm:flex">
                    <span class="relative flex h-2 w-2">
                        <span class="absolute inline-flex h-full w-full animate-pulse-dot rounded-full bg-emerald-400"></span>
                    </span>
                    <span id="total-peserta-badge">{{ $totalPeserta }}</span>&nbsp;peserta bergabung
                </div>

                <button id="nav-login-btn" type="button" class="{{ $participant ? 'hidden' : '' }} glass rounded-full px-4 py-1.5 text-xs font-semibold text-white/90 transition hover:bg-white/10">
                    Login
                </button>

                <div id="nav-profile" class="{{ $participant ? '' : 'hidden' }} flex items-center gap-2">
                    <span class="glass flex items-center gap-1.5 rounded-full px-3 py-1.5 text-xs font-medium text-white/80">
                        <x-icon name="user" class="h-3.5 w-3.5 text-cyan-300" />
                        <span id="nav-profile-name">{{ $participant->nama_lengkap ?? '' }}</span>
                    </span>
                    <button id="nav-logout-btn" type="button" title="Logout" class="glass rounded-full p-2 text-white/70 transition hover:bg-white/10 hover:text-rose-300">
                        <x-icon name="logout" class="h-4 w-4" />
                    </button>
                </div>
            </div>
        </div>
    </header>

    <section class="relative mx-auto max-w-7xl px-4 pb-10 pt-16 lg:px-8 lg:pt-24">
        <div class="grid grid-cols-1 items-center gap-10 lg:grid-cols-2 lg:gap-14">

            <div class="text-center lg:text-left">
                <span class="glass inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-xs font-medium text-white/80">
                    <x-icon name="compass" class="h-3.5 w-3.5 icon-glow text-cyan-300" />
                    The Biggest Mission Game in BSD
                </span>
                <h1 class="font-display mx-auto mt-6 max-w-xl text-5xl font-bold leading-tight tracking-tight sm:text-6xl lg:mx-0 lg:text-6xl xl:text-7xl">
                    Get Ready for the <span class="font-accent text-gradient tracking-wide">Mission</span>
                </h1>
                <p class="mx-auto mt-5 max-w-xl text-base font-medium text-white/80 sm:text-lg lg:mx-0">
                    The biggest mission game around all students in BSD.
                </p>
                <p class="mx-auto mt-2 max-w-xl text-sm text-white/50 sm:text-base lg:mx-0">
                    Daftarkan dirimu, temukan petunjuk, dan jadi bagian dari misi seru bareng ratusan pelajar BSD lainnya. Gabung live chat dan rasakan keseruannya sekarang.
                </p>

                <div id="countdown-wrap" class="mx-auto mt-8 max-w-lg lg:mx-0">
                    <div id="countdown" class="flex items-center justify-center gap-2 sm:gap-4 lg:justify-start">
                        <div class="glass rounded-2xl px-3 py-3 text-center sm:px-5 sm:py-4">
                            <p class="font-display text-2xl font-bold text-gradient sm:text-3xl" id="cd-days">00</p>
                            <p class="mt-1 text-[10px] uppercase tracking-wider text-white/50 sm:text-xs">Days</p>
                        </div>
                        <span class="font-display pb-4 text-xl text-white/20 sm:text-2xl">:</span>
                        <div class="glass rounded-2xl px-3 py-3 text-center sm:px-5 sm:py-4">
                            <p class="font-display text-2xl font-bold text-gradient sm:text-3xl" id="cd-hours">00</p>
                            <p class="mt-1 text-[10px] uppercase tracking-wider text-white/50 sm:text-xs">Hours</p>
                        </div>
                        <span class="font-display pb-4 text-xl text-white/20 sm:text-2xl">:</span>
                        <div class="glass rounded-2xl px-3 py-3 text-center sm:px-5 sm:py-4">
                            <p class="font-display text-2xl font-bold text-gradient sm:text-3xl" id="cd-minutes">00</p>
                            <p class="mt-1 text-[10px] uppercase tracking-wider text-white/50 sm:text-xs">Min</p>
                        </div>
                        <span class="font-display pb-4 text-xl text-white/20 sm:text-2xl">:</span>
                        <div class="glass rounded-2xl px-3 py-3 text-center sm:px-5 sm:py-4">
                            <p class="font-display text-2xl font-bold text-gradient sm:text-3xl" id="cd-seconds">00</p>
                            <p class="mt-1 text-[10px] uppercase tracking-wider text-white/50 sm:text-xs">Sec</p>
                        </div>
                    </div>
                    <p class="mt-3 text-xs text-white/40">Menuju 21 Oktober 2026 &middot; 19:00 WIB</p>
                </div>
                <p id="countdown-done" class="glass mx-auto mt-8 hidden max-w-md rounded-2xl px-5 py-3 text-sm font-semibold text-gradient lg:mx-0">
                    🚀 Misi telah dimulai!
                </p>

                <div class="mt-8 flex flex-wrap items-center justify-center gap-3 lg:justify-start">
                    <a href="#daftar" class="btn-glow rounded-full bg-gradient-to-r from-cyan-400 via-violet-400 to-pink-400 px-6 py-3 text-sm font-semibold text-slate-950 transition hover:brightness-110">
                        Daftar Sekarang
                    </a>
                    <a href="#chat" class="glass inline-flex items-center gap-2 rounded-full px-6 py-3 text-sm font-semibold text-white/90 transition hover:bg-white/10">
                        Lihat Live Chat
                        <x-icon name="chat" class="h-4 w-4 icon-glow text-violet-300" />
                    </a>
                </div>
            </div>

            <div class="flex flex-col items-center gap-6 lg:items-stretch">
                @if (file_exists(public_path('videos/mission-teaser.mp4')))
                    <div class="mx-auto w-full max-w-xl lg:mx-0">
                        <div class="glass overflow-hidden rounded-2xl p-1.5">
                            <video
                                class="aspect-video w-full rounded-xl bg-black object-cover"
                                src="{{ asset('videos/mission-teaser.mp4') }}"
                                autoplay muted loop playsinline controls
                            ></video>
                        </div>
                        <p class="mt-2 text-center text-xs text-white/40 lg:text-left">Mission Teaser</p>
                    </div>
                @endif

                <div class="mx-auto grid w-full max-w-sm grid-cols-3 gap-3 lg:mx-0 lg:max-w-none">
                    <div class="glass rounded-2xl px-4 py-4">
                        <x-icon name="users" class="mx-auto h-5 w-5 icon-glow text-cyan-300 lg:mx-0" />
                        <p class="font-display mt-2 text-2xl font-bold text-gradient" id="total-peserta-stat">{{ $totalPeserta }}</p>
                        <p class="mt-1 text-xs text-white/60">Peserta Terdaftar</p>
                    </div>
                    <div class="glass rounded-2xl px-4 py-4">
                        <x-icon name="shield" class="mx-auto h-5 w-5 icon-glow text-violet-300 lg:mx-0" />
                        <p class="font-display mt-2 text-2xl font-bold text-gradient">-</p>
                        <p class="mt-1 text-xs text-white/60">Jumlah Tim</p>
                    </div>
                    <div class="glass rounded-2xl px-4 py-4">
                        <x-icon name="gift" class="mx-auto h-5 w-5 icon-glow text-pink-300 lg:mx-0" />
                        <p class="font-display mt-2 text-2xl font-bold text-gradient">???</p>
                        <p class="mt-1 text-xs text-white/60">Hadiah Misterius</p>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <section class="mx-auto grid max-w-7xl gap-6 px-4 pb-24 lg:grid-cols-5 lg:gap-8 lg:px-8">

        <div id="daftar" class="glass-strong h-fit scroll-mt-28 rounded-3xl p-6 lg:col-span-2 lg:p-8">

            <div id="already-registered" class="{{ $participant ? '' : 'hidden' }} flex flex-col items-center gap-3 py-6 text-center">
                <div class="glass flex h-14 w-14 items-center justify-center rounded-full">
                    <x-icon name="check-circle" class="h-7 w-7 icon-glow text-emerald-300" />
                </div>
                <p class="font-display text-lg font-semibold">Kamu sudah terdaftar!</p>
                <p class="text-sm text-white/60">
                    Halo, <span id="already-registered-name" class="font-medium text-white">{{ $participant->nama_lengkap ?? '' }}</span>. Yuk gabung ngobrol di live chat.
                </p>
                <div class="mt-2 flex flex-wrap items-center justify-center gap-2">
                    <a href="#chat" class="glass inline-flex items-center gap-2 rounded-full px-5 py-2 text-sm font-semibold hover:bg-white/10">
                        Buka Live Chat
                        <x-icon name="chat" class="h-4 w-4 icon-glow text-violet-300" />
                    </a>
                    <a href="{{ route('game.index') }}" class="btn-glow inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-cyan-400 via-violet-400 to-pink-400 px-5 py-2 text-sm font-semibold text-slate-950 hover:brightness-110">
                        Mission Board
                        <x-icon name="shield" class="h-4 w-4" />
                    </a>
                    @if ($participant && $participant->isAdmin())
                        <a href="{{ route('admin.teams.index') }}" class="glass inline-flex items-center gap-2 rounded-full px-5 py-2 text-sm font-semibold hover:bg-white/10">
                            Admin Panel
                        </a>
                    @endif
                </div>
            </div>

            <form id="register-form" class="{{ $participant ? 'hidden' : '' }} space-y-4">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h2 class="font-display text-xl font-semibold">Form Pendaftaran</h2>
                        <p class="mt-1 text-sm text-white/50">Isi data diri kamu untuk ikut Mission One.</p>
                    </div>
                    <button type="button" class="open-login-trigger shrink-0 whitespace-nowrap text-xs font-medium text-violet-300 underline-offset-2 hover:underline">
                        Sudah daftar?
                    </button>
                </div>

                <p id="register-error" class="hidden rounded-xl border border-rose-400/30 bg-rose-400/10 px-4 py-2 text-sm text-rose-200"></p>

                <div>
                    <label class="mb-1 block text-xs font-medium text-white/70">Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" required
                        class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm text-white placeholder-white/30 outline-none transition focus:border-violet-400/60 focus:bg-white/10"
                        placeholder="Nama sesuai identitas">
                    <p class="err mt-1 hidden text-xs text-rose-300" data-for="nama_lengkap"></p>
                </div>

                <div>
                    <label class="mb-1 block text-xs font-medium text-white/70">Email</label>
                    <input type="email" name="email" required
                        class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm text-white placeholder-white/30 outline-none transition focus:border-violet-400/60 focus:bg-white/10"
                        placeholder="nama@email.com">
                    <p class="err mt-1 hidden text-xs text-rose-300" data-for="email"></p>
                </div>

                <div>
                    <label class="mb-1 block text-xs font-medium text-white/70">Nomor Handphone</label>
                    <input type="tel" name="no_hp" required
                        class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm text-white placeholder-white/30 outline-none transition focus:border-violet-400/60 focus:bg-white/10"
                        placeholder="08xxxxxxxxxx">
                    <p class="err mt-1 hidden text-xs text-rose-300" data-for="no_hp"></p>
                </div>

                <div>
                    <label class="mb-1 block text-xs font-medium text-white/70">Alamat</label>
                    <textarea name="alamat" required rows="2"
                        class="w-full resize-none rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm text-white placeholder-white/30 outline-none transition focus:border-violet-400/60 focus:bg-white/10"
                        placeholder="Alamat domisili"></textarea>
                    <p class="err mt-1 hidden text-xs text-rose-300" data-for="alamat"></p>
                </div>

                <div>
                    <label class="mb-1 block text-xs font-medium text-white/70">Sekolah</label>
                    <input type="text" name="sekolah" required
                        class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm text-white placeholder-white/30 outline-none transition focus:border-violet-400/60 focus:bg-white/10"
                        placeholder="Nama sekolah">
                    <p class="err mt-1 hidden text-xs text-rose-300" data-for="sekolah"></p>
                </div>

                <div>
                    <label class="mb-1 block text-xs font-medium text-white/70">Apakah sudah ber-CG?</label>
                    <input type="hidden" name="sudah_cg" id="sudah_cg" value="0">
                    <div class="grid grid-cols-2 gap-2">
                        <button type="button" data-cg="1" class="cg-btn rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm font-medium text-white/80 transition hover:bg-white/10">
                            Ya, sudah
                        </button>
                        <button type="button" data-cg="0" class="cg-btn rounded-xl border border-violet-400/60 bg-violet-400/20 px-4 py-2.5 text-sm font-medium text-white transition">
                            Belum
                        </button>
                    </div>
                </div>

                <div id="cg-extra" class="hidden space-y-4 rounded-2xl border border-white/10 bg-white/5 p-4">
                    <div>
                        <label class="mb-1 block text-xs font-medium text-white/70">Nomor CG</label>
                        <input type="text" name="no_cg"
                            class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm text-white placeholder-white/30 outline-none transition focus:border-violet-400/60 focus:bg-white/10"
                            placeholder="Contoh: CG-021">
                        <p class="err mt-1 hidden text-xs text-rose-300" data-for="no_cg"></p>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium text-white/70">Coach</label>
                        <input type="hidden" name="coach" id="coach">
                        <div class="grid grid-cols-3 gap-2">
                            @foreach (['Nichmatt', 'Yoyo', 'Stefani'] as $coachName)
                                <button type="button" data-coach="{{ $coachName }}" class="coach-btn rounded-xl border border-white/10 bg-white/5 px-2 py-2.5 text-xs font-medium text-white/80 transition hover:bg-white/10">
                                    {{ $coachName }}
                                </button>
                            @endforeach
                        </div>
                        <p class="err mt-1 hidden text-xs text-rose-300" data-for="coach"></p>
                    </div>
                </div>

                <button type="submit" id="register-submit" class="btn-glow w-full rounded-xl bg-gradient-to-r from-cyan-400 via-violet-400 to-pink-400 px-4 py-3 text-sm font-semibold text-slate-950 transition hover:brightness-110 disabled:opacity-60">
                    Daftar Sekarang
                </button>
            </form>
        </div>

        <div id="chat" class="glass-strong flex h-[640px] scroll-mt-28 flex-col rounded-3xl p-4 lg:col-span-3 lg:p-6">
            <div class="mb-3 flex items-center justify-between border-b border-white/10 pb-3">
                <div class="flex items-center gap-2">
                    <x-icon name="chat" class="h-5 w-5 icon-glow text-violet-300" />
                    <h2 class="font-display text-lg font-semibold">Live Chat Peserta</h2>
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
                    @php $isMe = $participant && $message->participant_id === $participant->id; @endphp
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
                    class="flex-1 rounded-full border border-white/10 bg-white/5 px-4 py-2.5 text-sm text-white placeholder-white/30 outline-none transition focus:border-violet-400/60 focus:bg-white/10 disabled:cursor-not-allowed disabled:opacity-50"
                    placeholder="{{ $participant ? 'Tulis pesan...' : 'Daftar dulu untuk ikut chat' }}"
                    {{ $participant ? '' : 'disabled' }}>
                <button type="submit" id="chat-send"
                    class="btn-glow flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gradient-to-r from-cyan-400 via-violet-400 to-pink-400 text-slate-950 transition hover:brightness-110 disabled:cursor-not-allowed disabled:opacity-50"
                    {{ $participant ? '' : 'disabled' }}>
                    <x-icon name="send" class="h-4 w-4" />
                </button>
            </form>
        </div>
    </section>

    <section id="klasemen" class="mx-auto max-w-4xl px-4 pb-24 lg:px-8">
        <div class="mb-8 text-center">
            <h2 class="font-display text-3xl font-bold">🏆 Klasemen Live</h2>
            <p class="mt-2 text-sm text-white/60">Peringkat tim berdasarkan jumlah misi benar &amp; kecepatan menjawab.</p>
        </div>

        <div class="glass-strong overflow-hidden rounded-3xl">
            <div class="grid grid-cols-12 gap-2 border-b border-white/10 px-5 py-3 text-xs uppercase tracking-wider text-white/40">
                <span class="col-span-2 sm:col-span-1">#</span>
                <span class="col-span-5 sm:col-span-6">Tim</span>
                <span class="col-span-2 text-center">Benar</span>
                <span class="col-span-3 text-center sm:col-span-2">Terjawab</span>
                <span class="hidden text-center sm:col-span-1 sm:block">Waktu</span>
            </div>
            <div id="leaderboard-list">
                @forelse ($leaderboard as $row)
                    <div class="grid grid-cols-12 items-center gap-2 border-b border-white/5 px-5 py-3 text-sm last:border-b-0">
                        <span class="col-span-2 font-display font-semibold text-white/70 sm:col-span-1">
                            {{ $row['rank'] === 1 ? '🥇' : ($row['rank'] === 2 ? '🥈' : ($row['rank'] === 3 ? '🥉' : $row['rank'])) }}
                        </span>
                        <span class="col-span-5 truncate font-medium sm:col-span-6">{{ $row['name'] }}</span>
                        <span class="col-span-2 text-center font-display font-semibold text-emerald-300">{{ $row['correct_count'] }}</span>
                        <span class="col-span-3 text-center text-white/50 sm:col-span-2">{{ $row['answered_count'] }}</span>
                        <span class="hidden text-center text-xs text-white/40 sm:col-span-1 sm:block">
                            {{ $row['last_correct_at'] ? \Illuminate\Support\Carbon::parse($row['last_correct_at'])->timezone(config('app.timezone'))->format('H:i') : '—' }}
                        </span>
                    </div>
                @empty
                    <div class="px-5 py-8 text-center text-sm text-white/50" id="leaderboard-empty">
                        Belum ada tim yang bertanding.
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <footer class="px-4 pb-10 text-center text-xs text-white/40">
        &copy; {{ date('Y') }} Mission One. Dibuat untuk pelajar BSD yang siap beraksi.
    </footer>

    <div id="toast" class="pointer-events-none fixed bottom-4 right-4 z-50 flex flex-col gap-2"></div>

    <div id="login-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
        <div id="login-modal-backdrop" class="absolute inset-0 bg-black/70 backdrop-blur-sm"></div>
        <div class="glass-strong relative w-full max-w-sm rounded-3xl p-6">
            <button type="button" id="close-login-modal" class="absolute right-4 top-4 text-white/50 transition hover:text-white">
                <x-icon name="close" class="h-5 w-5" />
            </button>

            <h3 class="font-display text-xl font-semibold">Login Peserta</h3>
            <p class="mt-1 text-sm text-white/50">Sudah pernah daftar? Masuk pakai email &amp; nomor HP kamu.</p>

            <form id="login-form" class="mt-5 space-y-4">
                <p id="login-error" class="hidden rounded-xl border border-rose-400/30 bg-rose-400/10 px-4 py-2 text-sm text-rose-200"></p>

                <div>
                    <label class="mb-1 block text-xs font-medium text-white/70">Email</label>
                    <input type="email" name="email" required
                        class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm text-white placeholder-white/30 outline-none transition focus:border-violet-400/60 focus:bg-white/10"
                        placeholder="nama@email.com">
                </div>

                <div>
                    <label class="mb-1 block text-xs font-medium text-white/70">Nomor Handphone</label>
                    <input type="tel" name="no_hp" required
                        class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm text-white placeholder-white/30 outline-none transition focus:border-violet-400/60 focus:bg-white/10"
                        placeholder="08xxxxxxxxxx">
                </div>

                <button type="submit" id="login-submit" class="btn-glow w-full rounded-xl bg-gradient-to-r from-cyan-400 via-violet-400 to-pink-400 px-4 py-3 text-sm font-semibold text-slate-950 transition hover:brightness-110 disabled:opacity-60">
                    Masuk
                </button>
            </form>
        </div>
    </div>

    <script>
        window.__TH_STATE__ = {!! Js::from([
            'participant' => $participant ? ['id' => $participant->id, 'nama_lengkap' => $participant->nama_lengkap] : null,
            'lastMessageId' => optional($messagesFeed->last())['id'] ?? 0,
            'totalPeserta' => $totalPeserta,
            'coaches' => ['Nichmatt', 'Yoyo', 'Stefani'],
        ]) !!};
    </script>
</body>
</html>
