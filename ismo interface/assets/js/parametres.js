// parametres.js - Settings page interactivity

document.addEventListener('DOMContentLoaded', function () {
    const toggleSwitches = document.querySelectorAll('.toggle input');
    const selectInputs = document.querySelectorAll('.select-input');
    const editButtons = document.querySelectorAll('.btn-edit');
    const dangerButtons = document.querySelectorAll('.btn-danger');
    const checkUpdateBtn = document.querySelector('.btn-check-update');

    // Toggle switches
    toggleSwitches.forEach(toggle => {
        toggle.addEventListener('change', function () {
            const label = this.parentElement.nextElementSibling?.textContent || 'Setting';
            const state = this.checked ? 'enabled' : 'disabled';
            showToast(`${label} ${state}`, 'success');
        });
    });

    // Select inputs (theme, language)
    selectInputs.forEach(select => {
        select.addEventListener('change', function () {
            const value = this.value;
            const label = this.parentElement.querySelector('.setting-info h3')?.textContent || 'Setting';

            if (this.name === 'theme') {
                showToast(`Theme changed to ${value}`, 'success');
                // In a real app, would apply theme
            } else if (this.name === 'language') {
                showToast(`Language changed to ${value}`, 'success');
                // In a real app, would change language
            }
        });
    });

    // Edit buttons (modify email, password, 2FA)
    editButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            const setting = this.parentElement.parentElement.querySelector('.setting-info h3')?.textContent || 'Setting';
            showToast(`Opening ${setting} editor...`, 'info');
            // In a real app, would open a modal to edit the setting
        });
    });

    // Danger zone buttons
    dangerButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            const action = this.textContent.trim();

            if (action.includes('Delete')) {
                const confirmed = confirm('Are you sure you want to delete your account? This action cannot be undone.');
                if (confirmed) {
                    showToast('Account deletion initiated', 'warning');
                    // In a real app, would process account deletion
                }
            } else if (action.includes('Download')) {
                btn.disabled = true;
                btn.textContent = 'Preparing...';
                showToast('Preparing your data for download...', 'info');

                setTimeout(() => {
                    btn.disabled = false;
                    btn.textContent = 'Download Data';
                    showToast('Your data has been downloaded', 'success');
                    // In a real app, would trigger actual download
                }, 2000);
            }
        });
    });

    // Check for updates button
    if (checkUpdateBtn) {
        checkUpdateBtn.addEventListener('click', function () {
            checkUpdateBtn.disabled = true;
            checkUpdateBtn.textContent = 'Checking...';
            showToast('Checking for updates...', 'info');

            setTimeout(() => {
                checkUpdateBtn.disabled = false;
                checkUpdateBtn.textContent = 'You\'re up to date!';
                showToast('You are running the latest version', 'success');

                setTimeout(() => {
                    checkUpdateBtn.textContent = 'Check for updates';
                }, 3000);
            }, 2000);
        });
    }
});
