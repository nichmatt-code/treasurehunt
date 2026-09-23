const state = window.__TH_STATE__;
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

const el = (id) => document.getElementById(id);

const chatMessagesEl = el('chat-messages');
const chatFormEl = el('chat-form');
const chatInputEl = el('chat-input');
const chatSendEl = el('chat-send');
const registerFormEl = el('register-form');
const alreadyRegisteredEl = el('already-registered');
const registerErrorEl = el('register-error');
const registerSubmitEl = el('register-submit');
const cgExtraEl = el('cg-extra');
const sudahCgInput = el('sudah_cg');
const coachInput = el('coach');
const toastEl = el('toast');
const navLoginBtn = el('nav-login-btn');
const navProfileEl = el('nav-profile');
const navProfileNameEl = el('nav-profile-name');
const navLogoutBtn = el('nav-logout-btn');
const loginModalEl = el('login-modal');
const loginModalBackdropEl = el('login-modal-backdrop');
const closeLoginModalBtn = el('close-login-modal');
const loginFormEl = el('login-form');
const loginErrorEl = el('login-error');
const loginSubmitEl = el('login-submit');

function isNearBottom() {
    return chatMessagesEl.scrollHeight - chatMessagesEl.scrollTop - chatMessagesEl.clientHeight < 120;
}

function scrollChatToBottom() {
    chatMessagesEl.scrollTop = chatMessagesEl.scrollHeight;
}

function showToast(text, variant = 'success') {
    const toast = document.createElement('div');
    const palette = variant === 'error'
        ? 'border-rose-400/30 bg-rose-400/10 text-rose-100'
        : 'border-emerald-400/30 bg-emerald-400/10 text-emerald-100';
    toast.className = `glass pointer-events-auto rounded-xl border px-4 py-3 text-sm shadow-lg ${palette}`;
    toast.textContent = text;
    toastEl.appendChild(toast);
    setTimeout(() => {
        toast.style.transition = 'opacity 0.3s ease';
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 300);
    }, 3200);
}

function renderMessage(message) {
    const wasNearBottom = isNearBottom();
    const isMe = state.participant && message.participant_id === state.participant.id;

    const row = document.createElement('div');
    row.dataset.messageId = message.id;
    row.classList.add('animate-message-in');

    if (message.type === 'system') {
        row.className += ' flex justify-center';
        const badge = document.createElement('span');
        badge.className = 'rounded-full bg-white/5 px-3 py-1 text-center text-xs text-white/50';
        badge.textContent = message.message;
        row.appendChild(badge);
    } else {
        row.className += ` flex ${isMe ? 'justify-end' : 'justify-start'}`;
        const bubble = document.createElement('div');
        bubble.className = `max-w-[75%] rounded-2xl px-4 py-2.5 ${isMe ? 'bg-gradient-to-r from-cyan-400/90 to-violet-400/90 text-slate-950' : 'glass'}`;

        if (!isMe) {
            const name = document.createElement('p');
            name.className = 'mb-0.5 text-xs font-semibold text-violet-300';
            name.textContent = message.sender_name;
            bubble.appendChild(name);
        }

        const text = document.createElement('p');
        text.className = 'text-sm leading-snug';
        text.textContent = message.message;
        bubble.appendChild(text);

        const time = document.createElement('p');
        time.className = 'mt-1 text-right text-[10px] opacity-60';
        time.textContent = message.time;
        bubble.appendChild(time);

        row.appendChild(bubble);
    }

    chatMessagesEl.appendChild(row);

    if (wasNearBottom) {
        scrollChatToBottom();
    }
}

function updateTotalPeserta(total) {
    if (typeof total !== 'number') return;
    state.totalPeserta = total;
    document.querySelectorAll('#total-peserta-badge, #total-peserta-stat').forEach((node) => {
        node.textContent = total;
    });
}

function unlockChat(participant) {
    state.participant = participant;
    chatInputEl.disabled = false;
    chatInputEl.placeholder = 'Tulis pesan...';
    chatSendEl.disabled = false;
}

function applyParticipant(participant) {
    unlockChat(participant);

    registerFormEl.classList.add('hidden');
    el('already-registered-name').textContent = participant.nama_lengkap;
    alreadyRegisteredEl.classList.remove('hidden');

    navLoginBtn.classList.add('hidden');
    navProfileNameEl.textContent = participant.nama_lengkap;
    navProfileEl.classList.remove('hidden');
}

function openLoginModal() {
    loginErrorEl.classList.add('hidden');
    loginFormEl.reset();
    loginModalEl.classList.remove('hidden');
    loginModalEl.classList.add('flex');
}

function closeLoginModal() {
    loginModalEl.classList.add('hidden');
    loginModalEl.classList.remove('flex');
}

function clearFieldErrors() {
    registerErrorEl.classList.add('hidden');
    registerErrorEl.textContent = '';
    document.querySelectorAll('#register-form .err').forEach((node) => {
        node.classList.add('hidden');
        node.textContent = '';
    });
}

function showFieldErrors(errors) {
    Object.entries(errors).forEach(([field, messages]) => {
        const node = document.querySelector(`#register-form .err[data-for="${field}"]`);
        if (node) {
            node.textContent = messages[0];
            node.classList.remove('hidden');
        }
    });
}

document.querySelectorAll('.cg-btn').forEach((btn) => {
    btn.addEventListener('click', () => {
        const value = btn.dataset.cg;
        sudahCgInput.value = value;
        document.querySelectorAll('.cg-btn').forEach((b) => {
            b.classList.remove('border-violet-400/60', 'bg-violet-400/20', 'text-white');
            b.classList.add('border-white/10', 'bg-white/5', 'text-white/80');
        });
        btn.classList.remove('border-white/10', 'bg-white/5', 'text-white/80');
        btn.classList.add('border-violet-400/60', 'bg-violet-400/20', 'text-white');
        cgExtraEl.classList.toggle('hidden', value !== '1');
    });
});

document.querySelectorAll('.coach-btn').forEach((btn) => {
    btn.addEventListener('click', () => {
        coachInput.value = btn.dataset.coach;
        document.querySelectorAll('.coach-btn').forEach((b) => {
            b.classList.remove('border-violet-400/60', 'bg-violet-400/20', 'text-white');
            b.classList.add('border-white/10', 'bg-white/5', 'text-white/80');
        });
        btn.classList.remove('border-white/10', 'bg-white/5', 'text-white/80');
        btn.classList.add('border-violet-400/60', 'bg-violet-400/20', 'text-white');
    });
});

if (registerFormEl) {
    registerFormEl.addEventListener('submit', async (event) => {
        event.preventDefault();
        clearFieldErrors();
        registerSubmitEl.disabled = true;
        registerSubmitEl.textContent = 'Mendaftar...';

        const formData = new FormData(registerFormEl);
        const payload = Object.fromEntries(formData.entries());

        try {
            const response = await fetch('/register', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify(payload),
            });

            const data = await response.json();

            if (!response.ok) {
                if (response.status === 422 && data.errors) {
                    showFieldErrors(data.errors);
                } else {
                    registerErrorEl.textContent = data.message || 'Terjadi kesalahan, coba lagi.';
                    registerErrorEl.classList.remove('hidden');
                }
                return;
            }

            applyParticipant(data.participant);
            renderMessage(data.message);
            state.lastMessageId = data.message.id;
            updateTotalPeserta(data.total_peserta);
            showToast(`Selamat datang, ${data.participant.nama_lengkap}! 🎉`);
        } catch (error) {
            registerErrorEl.textContent = 'Tidak bisa terhubung ke server. Coba lagi.';
            registerErrorEl.classList.remove('hidden');
        } finally {
            registerSubmitEl.disabled = false;
            registerSubmitEl.textContent = 'Daftar Sekarang';
        }
    });
}

chatFormEl.addEventListener('submit', async (event) => {
    event.preventDefault();
    const text = chatInputEl.value.trim();
    if (!text || !state.participant) return;

    chatInputEl.disabled = true;
    chatSendEl.disabled = true;

    try {
        const response = await fetch('/chat/messages', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({ message: text }),
        });

        const data = await response.json();

        if (!response.ok || !data.ok) {
            showToast(data.error || 'Pesan gagal terkirim.', 'error');
            return;
        }

        renderMessage(data.message);
        state.lastMessageId = data.message.id;
        chatInputEl.value = '';
        scrollChatToBottom();
    } catch (error) {
        showToast('Tidak bisa terhubung ke server.', 'error');
    } finally {
        chatInputEl.disabled = false;
        chatSendEl.disabled = false;
        chatInputEl.focus();
    }
});

document.querySelectorAll('.open-login-trigger').forEach((btn) => {
    btn.addEventListener('click', openLoginModal);
});
if (navLoginBtn) navLoginBtn.addEventListener('click', openLoginModal);
if (closeLoginModalBtn) closeLoginModalBtn.addEventListener('click', closeLoginModal);
if (loginModalBackdropEl) loginModalBackdropEl.addEventListener('click', closeLoginModal);
document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && !loginModalEl.classList.contains('hidden')) {
        closeLoginModal();
    }
});

if (loginFormEl) {
    loginFormEl.addEventListener('submit', async (event) => {
        event.preventDefault();
        loginErrorEl.classList.add('hidden');
        loginSubmitEl.disabled = true;
        loginSubmitEl.textContent = 'Masuk...';

        const formData = new FormData(loginFormEl);
        const payload = Object.fromEntries(formData.entries());

        try {
            const response = await fetch('/login', {
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
                loginErrorEl.textContent = data.error || 'Login gagal, coba lagi.';
                loginErrorEl.classList.remove('hidden');
                return;
            }

            applyParticipant(data.participant);
            updateTotalPeserta(data.total_peserta);
            closeLoginModal();
            showToast(`Selamat datang kembali, ${data.participant.nama_lengkap}! 👋`);
        } catch (error) {
            loginErrorEl.textContent = 'Tidak bisa terhubung ke server. Coba lagi.';
            loginErrorEl.classList.remove('hidden');
        } finally {
            loginSubmitEl.disabled = false;
            loginSubmitEl.textContent = 'Masuk';
        }
    });
}

if (navLogoutBtn) {
    navLogoutBtn.addEventListener('click', async () => {
        navLogoutBtn.disabled = true;
        try {
            await fetch('/logout', {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
            });
        } catch (error) {
            // ignore, reload anyway
        } finally {
            window.location.reload();
        }
    });
}

async function pollMessages() {
    try {
        const response = await fetch(`/chat/messages?after_id=${state.lastMessageId}`, {
            headers: { Accept: 'application/json' },
        });
        const data = await response.json();

        if (data.ok && data.messages.length) {
            data.messages.forEach((message) => {
                if (!chatMessagesEl.querySelector(`[data-message-id="${message.id}"]`)) {
                    renderMessage(message);
                }
            });
            state.lastMessageId = data.messages[data.messages.length - 1].id;
        }

        if (data.ok) {
            updateTotalPeserta(data.total_peserta);
        }
    } catch (error) {
        // silent retry on next interval
    }
}

scrollChatToBottom();
setInterval(pollMessages, 3000);

const COUNTDOWN_TARGET = new Date('2026-10-21T19:00:00+07:00').getTime();
const countdownWrapEl = el('countdown-wrap');
const countdownDoneEl = el('countdown-done');
const cdDaysEl = el('cd-days');
const cdHoursEl = el('cd-hours');
const cdMinutesEl = el('cd-minutes');
const cdSecondsEl = el('cd-seconds');

function pad2(n) {
    return String(n).padStart(2, '0');
}

function updateCountdown() {
    const diff = COUNTDOWN_TARGET - Date.now();

    if (diff <= 0) {
        if (countdownWrapEl) countdownWrapEl.classList.add('hidden');
        if (countdownDoneEl) countdownDoneEl.classList.remove('hidden');
        return;
    }

    const days = Math.floor(diff / 86400000);
    const hours = Math.floor((diff % 86400000) / 3600000);
    const minutes = Math.floor((diff % 3600000) / 60000);
    const seconds = Math.floor((diff % 60000) / 1000);

    if (cdDaysEl) cdDaysEl.textContent = pad2(days);
    if (cdHoursEl) cdHoursEl.textContent = pad2(hours);
    if (cdMinutesEl) cdMinutesEl.textContent = pad2(minutes);
    if (cdSecondsEl) cdSecondsEl.textContent = pad2(seconds);
}

updateCountdown();
setInterval(updateCountdown, 1000);

const leaderboardListEl = el('leaderboard-list');

function medalOrRank(rank) {
    if (rank === 1) return '🥇';
    if (rank === 2) return '🥈';
    if (rank === 3) return '🥉';
    return rank;
}

function renderLeaderboard(rows) {
    if (!leaderboardListEl) return;

    if (!rows.length) {
        leaderboardListEl.innerHTML = '<div class="px-5 py-8 text-center text-sm text-white/50">Belum ada tim yang bertanding.</div>';
        return;
    }

    leaderboardListEl.innerHTML = rows.map((row) => {
        const time = row.last_correct_at
            ? new Date(row.last_correct_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })
            : '—';

        return `
            <div class="grid grid-cols-12 items-center gap-2 border-b border-white/5 px-5 py-3 text-sm last:border-b-0">
                <span class="col-span-2 font-display font-semibold text-white/70 sm:col-span-1">${medalOrRank(row.rank)}</span>
                <span class="col-span-5 truncate font-medium sm:col-span-6">${row.name}</span>
                <span class="col-span-2 text-center font-display font-semibold text-emerald-300">${row.correct_count}</span>
                <span class="col-span-3 text-center text-white/50 sm:col-span-2">${row.answered_count}</span>
                <span class="hidden text-center text-xs text-white/40 sm:col-span-1 sm:block">${time}</span>
            </div>
        `;
    }).join('');
}

async function pollLeaderboard() {
    try {
        const response = await fetch('/leaderboard', { headers: { Accept: 'application/json' } });
        const data = await response.json();
        if (data.ok) {
            renderLeaderboard(data.leaderboard);
        }
    } catch (error) {
        // silent retry on next interval
    }
}

if (leaderboardListEl) {
    setInterval(pollLeaderboard, 5000);
}
