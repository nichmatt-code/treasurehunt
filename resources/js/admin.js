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
