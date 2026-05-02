// profile.js - Profile page interactivity

document.addEventListener('DOMContentLoaded', function () {
    const btnEditProfile = document.getElementById('btnEditProfile');
    const btnFollowProfile = document.getElementById('btnFollowProfile');
    const btnShareProfile = document.getElementById('btnShareProfile');

    if (btnEditProfile) {
        btnEditProfile.addEventListener('click', function () {
            showToast('Redirecting to edit profile...', 'info');
            // In a real app, would navigate to profile edit page
        });
    }

    if (btnFollowProfile) {
        btnFollowProfile.addEventListener('click', function () {
            const isFollowing = btnFollowProfile.classList.toggle('following');
            if (isFollowing) {
                btnFollowProfile.textContent = '✓ Following';
                showToast('User added to your network', 'success');
            } else {
                btnFollowProfile.textContent = 'Follow';
                showToast('User removed from your network', 'info');
            }
        });
    }

    if (btnShareProfile) {
        btnShareProfile.addEventListener('click', function () {
            const profileLink = window.location.href;
            navigator.clipboard.writeText(profileLink);
            showToast('Profile link copied to clipboard!', 'success');
        });
    }
});
