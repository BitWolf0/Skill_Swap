// mes_aides.js - My help sessions page interactivity

document.addEventListener('DOMContentLoaded', function () {
    const tabBtns = document.querySelectorAll('.tab-btn');
    const continueBtn = document.querySelectorAll('.btn-continue');
    const secondaryBtn = document.querySelectorAll('.btn-secondary');

    // Tab switching
    tabBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            tabBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            const tab = this.getAttribute('data-tab');
            showToast(`Affichage: ${this.textContent.trim()}`, 'info');
        });
    });

    // Continue conversation
    continueBtn.forEach(btn => {
        btn.addEventListener('click', function () {
            const title = this.closest('.aide-card').querySelector('.aide-info h3').textContent;
            showToast(`Ouverture de la conversation: ${title}`, 'info');
        });
    });

    // Secondary actions (Mark resolved / View history / Reply)
    secondaryBtn.forEach(btn => {
        btn.addEventListener('click', function () {
            const action = this.textContent.trim();
            showToast(`${action} - En cours...`, 'info');
        });
    });
});
