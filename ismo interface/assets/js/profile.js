// profile.js - Profile page interactivity

document.addEventListener('DOMContentLoaded', function () {
    const btnEditProfile = document.getElementById('btn-edit-profile') || document.getElementById('btnEditProfile');
    const btnFollowProfile = document.getElementById('btnFollowProfile');
    const btnShareProfile = document.getElementById('btnShareProfile');

    if (btnEditProfile) {
        btnEditProfile.addEventListener('click', function () {
            window.location.href = 'parametres.php';
        });
    }

    if (btnFollowProfile) {
        btnFollowProfile.addEventListener('click', function () {
            const isFollowing = btnFollowProfile.classList.toggle('following');
            if (isFollowing) {
                btnFollowProfile.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:4px"><polyline points="20 6 9 17 4 12"/></svg> Following';
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
