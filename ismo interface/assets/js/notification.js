// notification.js - Notifications page interactivity

document.addEventListener('DOMContentLoaded', function () {
    const btnMarkAllRead = document.getElementById('btnMarkAllRead');
    const notificationItems = document.querySelectorAll('.notification-item');
    const prefItems = document.querySelectorAll('.pref-item input');

    // Mark all as read functionality
    if (btnMarkAllRead) {
        btnMarkAllRead.addEventListener('click', function () {
            notificationItems.forEach(item => {
                item.classList.remove('unread');
            });
            showToast('All notifications marked as read', 'success');
        });
    }

    // Mark individual notification as read/unread
    notificationItems.forEach(item => {
        item.addEventListener('click', function () {
            this.classList.toggle('unread');
        });

        // Close button for individual notifications
        const closeBtn = item.querySelector('.btn-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                item.style.transition = 'opacity 300ms ease-out';
                item.style.opacity = '0';
                setTimeout(() => {
                    item.remove();
                    showToast('Notification dismissed', 'info');
                }, 300);
            });
        }
    });

    // Notification preferences checkboxes
    prefItems.forEach(checkbox => {
        checkbox.addEventListener('change', function () {
            const label = this.parentElement.querySelector('span').textContent;
            const state = this.checked ? 'enabled' : 'disabled';
            showToast(`${label} ${state}`, 'success');
        });
    });
});
