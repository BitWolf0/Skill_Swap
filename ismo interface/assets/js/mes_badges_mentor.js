// mes_badges_mentor.js - Mentor badges page interactivity

document.addEventListener('DOMContentLoaded', () => {
    const filterButtons = document.querySelectorAll('.filter-chip');
    const badgeCards = document.querySelectorAll('.badge-card[data-category]');
    const progressBars = document.querySelectorAll('.progress-fill');

    progressBars.forEach((bar) => {
        const targetWidth = bar.dataset.progress || '0%';
        bar.style.width = '0%';
        requestAnimationFrame(() => {
            setTimeout(() => {
                bar.style.width = targetWidth;
            }, 120);
        });
    });

    filterButtons.forEach((button) => {
        button.addEventListener('click', () => {
            const selectedFilter = button.dataset.filter;

            filterButtons.forEach((item) => {
                const isActive = item === button;
                item.classList.toggle('active', isActive);
                item.setAttribute('aria-pressed', String(isActive));
            });

            badgeCards.forEach((card) => {
                const matches = selectedFilter === 'all' || card.dataset.category === selectedFilter;
                card.hidden = !matches;
            });
        });
    });

    badgeCards.forEach((card) => {
        card.addEventListener('click', () => {
            const title = card.querySelector('.badge-title')?.textContent?.trim();
            if (typeof showToast === 'function' && title) {
                showToast(`Badge : ${title}`, 'info');
            }
        });

        card.addEventListener('keydown', (event) => {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                card.click();
            }
        });
    });
});
