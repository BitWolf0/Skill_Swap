// mes_badges.js - Badges page interactivity

document.addEventListener('DOMContentLoaded', function () {
    const badgeItems = document.querySelectorAll('.badge-item');
    const btnSeeMore = document.querySelector('.btn-see-more');

    // Add click interaction to badge items
    badgeItems.forEach(item => {
        item.addEventListener('click', function () {
            const badgeTitle = this.querySelector('h3').textContent;
            showToast(`Viewing details for: ${badgeTitle}`, 'info');
        });
    });

    // See more button for closest badge
    if (btnSeeMore) {
        btnSeeMore.addEventListener('click', function () {
            showToast('Loading badge path...', 'info');
            // In a real app, would show a modal with badge progression details
        });
    }

    // Animate progress bars on load
    const progressFills = document.querySelectorAll('.progress-fill');
    progressFills.forEach(fill => {
        const width = fill.style.width;
        fill.style.width = '0';
        setTimeout(() => {
            fill.style.transition = 'width 1s ease-out';
            fill.style.width = width;
        }, 100);
    });
});
