document.querySelectorAll('.toggle-edit-btn').forEach((btn) => {
    btn.addEventListener('click', () => {
        const target = document.getElementById(btn.dataset.target);
        if (!target) return;
        const isHidden = target.classList.contains('hidden');
        target.classList.toggle('hidden', !isHidden);
        target.classList.toggle('grid', isHidden);
    });
});

document.querySelectorAll('.confirm-delete').forEach((form) => {
    form.addEventListener('submit', (event) => {
        const message = form.dataset.message || 'Yakin ingin menghapus data ini?';
        if (!window.confirm(message)) {
            event.preventDefault();
        }
    });
});

const csrfTokenEl = document.querySelector('meta[name="csrf-token"]');
const csrfToken = csrfTokenEl ? csrfTokenEl.content : null;

function applyGradeUi(card, status, gradedBy) {
    card.dataset.status = status;
    const label = card.querySelector('.status-label');
    if (!label) return;

    label.classList.remove('text-emerald-300', 'text-rose-300', 'text-white/40');

    if (status === 'correct') {
        label.classList.add('text-emerald-300');
        label.textContent = `✅ Benar · dinilai oleh ${gradedBy || '—'}`;
    } else if (status === 'incorrect') {
        label.classList.add('text-rose-300');
        label.textContent = `❌ Salah · dinilai oleh ${gradedBy || '—'}`;
    } else {
        label.classList.add('text-white/40');
        label.textContent = '⏳ Menunggu penilaian';
    }
}

const OPTION_LABELS = ['A', 'B', 'C', 'D'];

function renumberOptions(container) {
    container.querySelectorAll('.option-row').forEach((row, index) => {
        row.querySelector('.option-radio').value = index;
        row.querySelector('.option-label').textContent = OPTION_LABELS[index] ?? '';
    });
    const rows = container.querySelectorAll('.option-row');
    container.querySelectorAll('.remove-option-btn').forEach((btn) => {
        btn.disabled = rows.length <= Number(container.dataset.min ?? 2);
        btn.classList.toggle('opacity-30', btn.disabled);
    });
}

function makeOptionRow(index) {
    const row = document.createElement('div');
    row.className = 'option-row flex items-center gap-2';
    row.innerHTML = `
        <input type="radio" name="correct_option_index" value="${index}" class="option-radio accent-emerald-400">
        <span class="option-label w-5 text-xs text-white/40">${OPTION_LABELS[index] ?? ''}</span>
        <input type="text" name="options[]" maxlength="150" required
            class="flex-1 rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-white placeholder-white/30 outline-none focus:border-violet-400/60"
            placeholder="Pilihan ${OPTION_LABELS[index] ?? ''}">
        <button type="button" class="remove-option-btn text-white/40 transition hover:text-rose-300">✕</button>
    `;
    return row;
}

document.querySelectorAll('.options-container').forEach((container) => {
    renumberOptions(container);
});

document.addEventListener('click', (event) => {
    const removeBtn = event.target.closest('.remove-option-btn');
    if (removeBtn) {
        const container = removeBtn.closest('.options-container');
        const min = Number(container.dataset.min ?? 2);
        if (container.querySelectorAll('.option-row').length > min) {
            removeBtn.closest('.option-row').remove();
            renumberOptions(container);
        }
        return;
    }

    const addBtn = event.target.closest('.add-option-btn');
    if (addBtn) {
        const container = addBtn.previousElementSibling;
        if (!container || !container.classList.contains('options-container')) return;
        const max = Number(container.dataset.max ?? 4);
        const currentCount = container.querySelectorAll('.option-row').length;
        if (currentCount < max) {
            container.appendChild(makeOptionRow(currentCount));
            renumberOptions(container);
        }
    }
});

document.querySelectorAll('.grade-card').forEach((card) => {
    card.querySelectorAll('.grade-btn').forEach((btn) => {
        btn.addEventListener('click', async () => {
            const answerId = card.dataset.answerId;
            const status = btn.dataset.status;

            card.querySelectorAll('.grade-btn').forEach((b) => (b.disabled = true));

            try {
                const response = await fetch(`/admin/answers/${answerId}/grade`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({ status }),
                });

                const data = await response.json();
                if (data.ok) {
                    applyGradeUi(card, data.answer.status, data.answer.graded_by);
                }
            } catch (error) {
                // ignore, admin can retry
            } finally {
                card.querySelectorAll('.grade-btn').forEach((b) => (b.disabled = false));
            }
        });
    });
});
