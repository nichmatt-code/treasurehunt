const state = window.__GAME_STATE__;
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

const el = (id) => document.getElementById(id);

const chatMessagesEl = el('chat-messages');
const chatFormEl = el('chat-form');
const chatInputEl = el('chat-input');
const chatSendEl = el('chat-send');
const toastEl = el('toast');
const navLogoutBtn = el('nav-logout-btn');

function isNearBottom() {
    if (!chatMessagesEl) return true;
    return chatMessagesEl.scrollHeight - chatMessagesEl.scrollTop - chatMessagesEl.clientHeight < 120;
}

function scrollChatToBottom() {
    if (!chatMessagesEl) return;
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
    if (!chatMessagesEl) return;
    const wasNearBottom = isNearBottom();
    const isMe = message.participant_id === state.participant.id;

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

if (chatFormEl) {
    chatFormEl.addEventListener('submit', async (event) => {
        event.preventDefault();
        const text = chatInputEl.value.trim();
        if (!text) return;

        chatInputEl.disabled = true;
        chatSendEl.disabled = true;

        try {
            const response = await fetch('/game/chat/messages', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({ message: text, team_id: state.team ? state.team.id : null }),
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

    async function pollMessages() {
        try {
            const params = new URLSearchParams({ after_id: state.lastMessageId });
            if (state.team) params.set('team_id', state.team.id);

            const response = await fetch(`/game/chat/messages?${params.toString()}`, {
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
        } catch (error) {
            // silent retry on next interval
        }
    }

    scrollChatToBottom();
    setInterval(pollMessages, 3000);
}

document.querySelectorAll('.remove-answer-image-btn').forEach((btn) => {
    btn.addEventListener('click', () => {
        const form = btn.closest('form');
        form.querySelector('.answer-image-wrap').classList.add('hidden');
        form.querySelector('.remove-image-flag').value = '1';
        const fileInput = form.querySelector('.answer-image-input');
        if (fileInput) fileInput.value = '';
    });
});

document.querySelectorAll('.answer-image-input').forEach((input) => {
    input.addEventListener('change', () => {
        const form = input.closest('form');
        form.querySelector('.remove-image-flag').value = '0';
        if (input.files && input.files[0]) {
            const wrap = form.querySelector('.answer-image-wrap');
            const preview = form.querySelector('.answer-image-preview');
            preview.src = URL.createObjectURL(input.files[0]);
            wrap.classList.remove('hidden');
        }
    });
});

document.querySelectorAll('.answer-form').forEach((form) => {
    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        const questionId = form.dataset.questionId;
        const textarea = form.querySelector('textarea[name="answer_text"]');
        const fileInput = form.querySelector('.answer-image-input');
        const removeFlag = form.querySelector('.remove-image-flag');
        const metaEl = form.querySelector('.answer-meta');
        const statusEl = form.querySelector('.answer-status-badge');
        const submitBtn = form.querySelector('button[type="submit"]');

        const formData = new FormData();
        formData.append('question_id', questionId);
        formData.append('answer_text', textarea.value);
        formData.append('remove_image', removeFlag.value);
        if (state.team) formData.append('team_id', state.team.id);
        if (fileInput.files && fileInput.files[0]) {
            formData.append('answer_image', fileInput.files[0]);
        }

        submitBtn.disabled = true;
        try {
            const response = await fetch('/game/answers', {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: formData,
            });

            const data = await response.json();

            if (!response.ok || !data.ok) {
                showToast(data.error || 'Jawaban gagal disimpan.', 'error');
                return;
            }

            metaEl.textContent = `Terakhir diisi oleh ${data.answer.updated_by} · ${data.answer.updated_at}`;
            statusEl.textContent = '⏳ Menunggu dinilai';
            statusEl.className = 'answer-status-badge mt-0.5 text-xs text-white/30';
            removeFlag.value = '0';
            fileInput.value = '';
            showToast('Jawaban tersimpan!');
        } catch (error) {
            showToast('Tidak bisa terhubung ke server.', 'error');
        } finally {
            submitBtn.disabled = false;
        }
    });
});

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
            // ignore, redirect anyway
        } finally {
            window.location.href = '/';
        }
    });
}
