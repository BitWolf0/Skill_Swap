// marketplace.js - Marketplace page interactivity

document.addEventListener('DOMContentLoaded', function () {
    const filterBtns = document.querySelectorAll('.filter-btn');
    const helpBtns = document.querySelectorAll('.btn-help');

    // Filter functionality
    filterBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            filterBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            const filter = this.textContent.trim();
            showToast(`Filtre appliqué: ${filter}`, 'info');
        });
    });

    // Help proposal functionality
    helpBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            const title = this.closest('.request-card').querySelector('.request-header h3').textContent;
            btn.disabled = true;
            btn.textContent = 'Proposition envoyée...';

            setTimeout(() => {
                btn.disabled = false;
                btn.textContent = 'Proposer mon aide';
                showToast(`Votre aide a été proposée pour: ${title}`, 'success');
            }, 1500);
        });
    });
});
