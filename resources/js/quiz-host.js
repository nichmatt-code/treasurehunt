import { createEcho } from './echo';

const state = window.__HOST_STATE__;
if (!state) {
    throw new Error('Host state missing');
}

const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
const el = (id) => document.getElementById(id);
const roomCode = state.roomCode;

const phases = ['lobby', 'question', 'leaderboard', 'finished'];
let countdownTimer = null;
let autoAdvanceTimer = null;
let currentDistribution = {};
let currentOptions = [];

function showPhase(name) {
    phases.forEach((p) => el(`host-phase-${p}`).classList.toggle('hidden', p !== name));
}

async function api(path, options = {}) {
    return fetch(path, {
        method: 'POST',
        ...options,
        headers: {
            Accept: 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json',
            ...(options.headers || {}),
        },
    });
}

function renderPlayerList(nicknames) {
    el('stat-players').textContent = nicknames.length;
    el('host-player-list').innerHTML = nicknames.map((n) => `
        <span class="glass rounded-full px-3 py-1 text-xs">${n}</span>
    `).join('');
}

function renderBars(options, distribution, correctIndex = null) {
    currentOptions = options;
    currentDistribution = distribution;
    const total = Object.values(distribution).reduce((a, b) => a + b, 0) || 1;
    const labels = ['A', 'B', 'C', 'D'];

    el('host-q-bars').innerHTML = options.map((text, i) => {
        const count = distribution[i] ?? 0;
        const pct = Math.round((count / total) * 100);
        const isCorrect = correctIndex !== null && i === correctIndex;
        return `
            <div>
                <div class="mb-1 flex items-center justify-between text-xs ${isCorrect ? 'text-emerald-300 font-semibold' : 'text-white/60'}">
                    <span>${labels[i]}. ${text} ${isCorrect ? '✓' : ''}</span>
                    <span>${count}</span>
                </div>
                <div class="h-3 w-full overflow-hidden rounded-full bg-white/10">
                    <div class="h-full rounded-full ${isCorrect ? 'bg-emerald-400' : 'bg-gradient-to-r from-cyan-400 to-violet-400'}" style="width:${pct}%"></div>
                </div>
            </div>
        `;
    }).join('');
}

function startTimer(startedAtIso, limitSeconds, onExpire) {
    clearInterval(countdownTimer);
    clearTimeout(autoAdvanceTimer);
    const startedAt = new Date(startedAtIso).getTime();

    function tick() {
        const elapsed = (Date.now() - startedAt) / 1000;
        const remaining = Math.max(0, Math.ceil(limitSeconds - elapsed));
        el('stat-timer').textContent = remaining;
        if (remaining <= 0) {
            clearInterval(countdownTimer);
            if (onExpire) onExpire();
        }
    }

    tick();
    countdownTimer = setInterval(tick, 250);
}

function renderQuestionPhase(snapshot) {
    el('stat-question').textContent = `${snapshot.question_index + 1} / ${snapshot.total_questions}`;
    el('host-q-text').textContent = snapshot.question.question;

    const img = el('host-q-image');
    if (snapshot.question.image_url) {
        img.src = snapshot.question.image_url;
        img.classList.remove('hidden');
    } else {
        img.classList.add('hidden');
    }

    renderBars(snapshot.question.options, {});
    el('host-answer-progress').textContent = `0 / ${el('stat-players').textContent} sudah menjawab`;
    el('btn-next').textContent = 'END QUESTION';
    showPhase('question');

    if (snapshot.started_at) {
        startTimer(snapshot.started_at, snapshot.question.time_limit_seconds, () => {
            api(`/host/quiz/games/${roomCode}/next`);
        });
    }
}

function renderResultPhase(snapshot) {
    clearInterval(countdownTimer);
    el('stat-timer').textContent = '--';
    renderBars(snapshot.question.options, snapshot.distribution, snapshot.correct_option_index);
    el('host-answer-progress').textContent = `${snapshot.answered_count} / ${el('stat-players').textContent} menjawab`;
    el('btn-next').textContent = 'SHOW LEADERBOARD';
    showPhase('question');
}

function renderLeaderboardPhase(rows) {
    el('host-leaderboard-rows').innerHTML = rows.map((row, i) => {
        const medal = i === 0 ? '🥇' : i === 1 ? '🥈' : i === 2 ? '🥉' : `${i + 1}`;
        return `
            <div class="glass flex items-center justify-between rounded-xl px-4 py-2.5">
                <span class="flex items-center gap-2 text-sm font-medium"><span class="w-6 text-center">${medal}</span>${row.nickname}</span>
                <span class="font-display font-bold text-gradient">${row.score.toLocaleString('id-ID')}</span>
            </div>
        `;
    }).join('');
    showPhase('leaderboard');
}

function renderFinishedPhase(rows) {
    const medals = ['🥇', '🥈', '🥉'];
    el('host-podium').innerHTML = rows.slice(0, 3).map((row, i) => `
        <div class="glass-strong flex items-center justify-between rounded-2xl px-5 py-3">
            <span class="flex items-center gap-3 font-display text-lg font-semibold"><span>${medals[i]}</span> ${row.nickname}</span>
            <span class="font-display text-gradient">${row.score.toLocaleString('id-ID')}</span>
        </div>
    `).join('');
    showPhase('finished');
}

function hydrate(snapshot) {
    renderPlayerList(snapshot.players || []);
    el('stat-question').textContent = `${snapshot.question_index + 1} / ${snapshot.total_questions}`;

    if (snapshot.status === 'lobby') showPhase('lobby');
    else if (snapshot.status === 'question') renderQuestionPhase(snapshot);
    else if (snapshot.status === 'result') renderResultPhase(snapshot);
    else if (snapshot.status === 'leaderboard') renderLeaderboardPhase(snapshot.leaderboard);
    else if (snapshot.status === 'finished') renderFinishedPhase(snapshot.leaderboard);
}

hydrate(state.snapshot);

el('btn-start').addEventListener('click', async () => {
    el('btn-start').disabled = true;
    await api(`/host/quiz/games/${roomCode}/start`);
});

el('btn-next').addEventListener('click', async () => {
    el('btn-next').disabled = true;
    await api(`/host/quiz/games/${roomCode}/next`);
    el('btn-next').disabled = false;
});

el('btn-next-from-leaderboard').addEventListener('click', async () => {
    el('btn-next-from-leaderboard').disabled = true;
    await api(`/host/quiz/games/${roomCode}/next`);
    el('btn-next-from-leaderboard').disabled = false;
});

const echo = createEcho();
const channel = echo.channel(`quiz.${roomCode}`);

const joinedNicknames = new Set(state.snapshot.players || []);

channel.listen('.player.joined', (e) => {
    joinedNicknames.add(e.nickname);
    renderPlayerList(Array.from(joinedNicknames));
});

channel.listen('.question.started', (e) => {
    renderQuestionPhase({
        question_index: e.question_index,
        total_questions: e.total_questions,
        question: e,
        started_at: e.started_at,
    });
});

channel.listen('.answer.tally', (e) => {
    el('host-answer-progress').textContent = `${e.answered_count} / ${e.total_players} sudah menjawab`;
});

channel.listen('.question.ended', (e) => {
    renderResultPhase({
        question: { options: currentOptions },
        distribution: e.distribution,
        correct_option_index: e.correct_option_index,
        answered_count: Object.values(e.distribution).reduce((a, b) => a + b, 0),
    });
});

channel.listen('.leaderboard.updated', (e) => {
    renderLeaderboardPhase(e.leaderboard);
});

channel.listen('.game.finished', (e) => {
    renderFinishedPhase(e.leaderboard);
});
