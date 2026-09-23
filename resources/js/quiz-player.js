import { createEcho } from './echo';

const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
const el = (id) => document.getElementById(id);

const OPTION_STYLES = [
    { label: 'A', classes: 'from-rose-400 to-rose-500' },
    { label: 'B', classes: 'from-cyan-400 to-cyan-500' },
    { label: 'C', classes: 'from-amber-400 to-amber-500' },
    { label: 'D', classes: 'from-violet-400 to-violet-500' },
];

// ---------- Join form (quiz/join page) ----------
const joinForm = el('join-form');

if (joinForm) {
    const joinError = el('join-error');
    const joinSubmit = el('join-submit');

    joinForm.addEventListener('submit', async (event) => {
        event.preventDefault();
        joinError.classList.add('hidden');
        joinSubmit.disabled = true;
        joinSubmit.textContent = 'Joining...';

        const formData = new FormData(joinForm);
        const payload = Object.fromEntries(formData.entries());

        try {
            const response = await fetch('/quiz/join', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify(payload),
            });

            const data = await response.json();

            if (!response.ok || !data.ok) {
                joinError.textContent = data.error || 'Gagal join, coba lagi.';
                joinError.classList.remove('hidden');
                return;
            }

            localStorage.setItem(`quiz_token_${data.room_code}`, data.player_token);
            localStorage.setItem(`quiz_nickname_${data.room_code}`, data.nickname);
            window.location.href = `/quiz/p/${data.room_code}`;
        } catch (error) {
            joinError.textContent = 'Tidak bisa terhubung ke server.';
            joinError.classList.remove('hidden');
        } finally {
            joinSubmit.disabled = false;
            joinSubmit.textContent = 'JOIN GAME';
        }
    });
}

// ---------- Play page (quiz/p/{roomCode}) ----------
const quizState = window.__QUIZ_STATE__;

if (quizState) {
    const roomCode = quizState.roomCode;
    const token = localStorage.getItem(`quiz_token_${roomCode}`);

    if (!token) {
        el('reconnect-banner').classList.remove('hidden');
    } else {
        runPlayer(roomCode, token);
    }
}

function runPlayer(roomCode, token) {
    const phases = ['lobby', 'question', 'result', 'leaderboard', 'finished'];
    let countdownTimer = null;
    let currentQuestionId = null;
    let hasAnsweredCurrent = false;

    function showPhase(name) {
        phases.forEach((p) => el(`phase-${p}`).classList.toggle('hidden', p !== name));
    }

    async function api(path, options = {}) {
        return fetch(path, {
            ...options,
            headers: {
                Accept: 'application/json',
                'X-Player-Token': token,
                ...(options.headers || {}),
            },
        });
    }

    function startCountdown(startedAtIso, limitSeconds) {
        clearInterval(countdownTimer);
        const startedAt = new Date(startedAtIso).getTime();

        function tick() {
            const elapsed = (Date.now() - startedAt) / 1000;
            const remaining = Math.max(0, Math.ceil(limitSeconds - elapsed));
            el('q-timer').textContent = remaining;
            if (remaining <= 0) clearInterval(countdownTimer);
        }

        tick();
        countdownTimer = setInterval(tick, 250);
    }

    function renderQuestion(payload) {
        hasAnsweredCurrent = !!payload.already_answered;
        currentQuestionId = payload.question.id;

        el('q-index').textContent = `Soal ${payload.question_index + 1}/${payload.total_questions}`;
        el('q-text').textContent = payload.question.question;

        const img = el('q-image');
        if (payload.question.image_url) {
            img.src = payload.question.image_url;
            img.classList.remove('hidden');
        } else {
            img.classList.add('hidden');
        }

        const optionsEl = el('q-options');
        optionsEl.innerHTML = '';
        payload.question.options.forEach((text, index) => {
            const style = OPTION_STYLES[index] ?? OPTION_STYLES[0];
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = `answer-btn rounded-2xl bg-gradient-to-br ${style.classes} px-4 py-5 text-left text-sm font-semibold text-slate-950 shadow-lg transition active:scale-95 disabled:opacity-40`;
            btn.innerHTML = `<span class="mr-2 inline-flex h-6 w-6 items-center justify-center rounded-full bg-black/20 text-xs">${style.label}</span>${text}`;
            btn.addEventListener('click', () => submitAnswer(index));
            optionsEl.appendChild(btn);
        });

        el('q-submitted').classList.toggle('hidden', !hasAnsweredCurrent);
        optionsEl.classList.toggle('hidden', hasAnsweredCurrent);

        startCountdown(payload.started_at, payload.question.time_limit_seconds);
        showPhase('question');
    }

    async function submitAnswer(optionIndex) {
        if (hasAnsweredCurrent) return;
        hasAnsweredCurrent = true;

        document.querySelectorAll('.answer-btn').forEach((b) => (b.disabled = true));
        el('q-submitted').classList.remove('hidden');
        el('q-options').classList.add('hidden');

        try {
            await api(`/quiz/p/${roomCode}/answer`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ question_id: currentQuestionId, option_index: optionIndex }),
            });
        } catch (error) {
            // server remains the source of truth; UI stays locked either way
        }
    }

    function renderResult(payload) {
        const icon = el('result-icon');
        const isCorrect = payload.my_answer && payload.my_answer.is_correct;

        icon.innerHTML = isCorrect
            ? '<span class="text-3xl">✅</span>'
            : '<span class="text-3xl">❌</span>';

        el('result-title').textContent = isCorrect ? 'Correct!' : (payload.my_answer ? 'Wrong!' : 'No Answer');
        el('result-points').textContent = isCorrect ? `+${payload.my_answer.score_awarded} pts` : '+0 pts';

        const correctText = payload.correct_option_index != null
            ? `Jawaban benar: ${OPTION_STYLES[payload.correct_option_index]?.label ?? ''}`
            : '';
        el('result-correct').textContent = correctText;

        showPhase('result');
    }

    function renderLeaderboard(rows, myNickname) {
        const container = el('leaderboard-rows');
        container.innerHTML = rows.map((row, i) => {
            const medal = i === 0 ? '🥇' : i === 1 ? '🥈' : i === 2 ? '🥉' : `${i + 1}`;
            const isMe = row.nickname === myNickname;
            return `
                <div class="glass flex items-center justify-between rounded-xl px-4 py-2.5 ${isMe ? 'border border-violet-400/60' : ''}">
                    <span class="flex items-center gap-2 text-sm font-medium">
                        <span class="w-6 text-center">${medal}</span>
                        ${row.nickname}${isMe ? ' <span class="text-xs text-violet-300">(kamu)</span>' : ''}
                    </span>
                    <span class="font-display font-bold text-gradient">${row.score.toLocaleString('id-ID')}</span>
                </div>
            `;
        }).join('');

        showPhase('leaderboard');
    }

    function renderFinished(rows) {
        const podium = el('podium');
        const medals = ['🥇', '🥈', '🥉'];
        podium.innerHTML = rows.slice(0, 3).map((row, i) => `
            <div class="glass-strong flex items-center justify-between rounded-2xl px-5 py-3">
                <span class="flex items-center gap-3 font-display text-lg font-semibold">
                    <span>${medals[i]}</span> ${row.nickname}
                </span>
                <span class="font-display text-gradient">${row.score.toLocaleString('id-ID')}</span>
            </div>
        `).join('');

        showPhase('finished');
        launchConfetti();
    }

    function launchConfetti() {
        const layer = el('confetti-layer');
        const colors = ['#22d3ee', '#a78bfa', '#f472b6', '#facc15', '#34d399'];

        for (let i = 0; i < 80; i++) {
            const piece = document.createElement('span');
            piece.style.position = 'absolute';
            piece.style.left = `${Math.random() * 100}%`;
            piece.style.top = '-10px';
            piece.style.width = '8px';
            piece.style.height = '8px';
            piece.style.borderRadius = Math.random() > 0.5 ? '50%' : '2px';
            piece.style.background = colors[i % colors.length];
            piece.style.opacity = '0.9';
            piece.style.transform = `rotate(${Math.random() * 360}deg)`;
            piece.style.transition = `transform ${2 + Math.random() * 1.5}s ease-in, top ${2 + Math.random() * 1.5}s ease-in, opacity 0.5s ease ${1.8 + Math.random()}s`;
            layer.appendChild(piece);

            requestAnimationFrame(() => {
                piece.style.top = '110%';
                piece.style.transform = `translateX(${(Math.random() - 0.5) * 200}px) rotate(${Math.random() * 720}deg)`;
                piece.style.opacity = '0';
            });

            setTimeout(() => piece.remove(), 4000);
        }
    }

    // Initial hydration
    (async () => {
        const response = await api(`/quiz/p/${roomCode}/state`);
        const data = await response.json();

        if (!data.ok) {
            el('reconnect-banner').classList.remove('hidden');
            return;
        }

        el('lobby-nickname').textContent = data.player.nickname;
        el('lobby-player-count').textContent = data.player_count;

        if (data.status === 'lobby') showPhase('lobby');
        else if (data.status === 'question') renderQuestion(data);
        else if (data.status === 'result') renderResult(data);
        else if (data.status === 'leaderboard') renderLeaderboard(data.leaderboard, data.player.nickname);
        else if (data.status === 'finished') renderFinished(data.leaderboard);
    })();

    // Realtime
    const echo = createEcho();
    const channel = echo.channel(`quiz.${roomCode}`);

    channel.listen('.player.joined', (e) => {
        el('lobby-player-count').textContent = e.player_count;
    });

    channel.listen('.question.started', (e) => {
        renderQuestion({
            question_index: e.question_index,
            total_questions: e.total_questions,
            question: e,
            started_at: e.started_at,
            already_answered: false,
        });
    });

    channel.listen('.question.ended', async (e) => {
        const response = await api(`/quiz/p/${roomCode}/state`);
        const data = await response.json();
        if (data.ok) renderResult(data);
        else renderResult({ correct_option_index: e.correct_option_index, my_answer: null });
    });

    channel.listen('.leaderboard.updated', (e) => {
        const nickname = el('lobby-nickname').textContent;
        renderLeaderboard(e.leaderboard, nickname);
    });

    channel.listen('.game.finished', (e) => {
        renderFinished(e.leaderboard);
    });
}
