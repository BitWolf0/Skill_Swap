/* classement.js - Toggle classement tabs */

'use strict';

const tabButtons = document.querySelectorAll('.tab-btn');
const tabPanels = document.querySelectorAll('.tab-panel');

function activateTab(target) {
    tabButtons.forEach((btn) => {
        const isActive = btn.dataset.tab === target;
        btn.classList.toggle('active', isActive);
        btn.setAttribute('aria-pressed', isActive ? 'true' : 'false');
    });

    tabPanels.forEach((panel) => {
        panel.hidden = panel.dataset.panel !== target;
    });
}

tabButtons.forEach((btn) => {
    btn.addEventListener('click', () => {
        activateTab(btn.dataset.tab);
    });
});

// If a mentors table exists, render a minimal leaderboard
(function renderLeaderboard(){
    const body = document.getElementById('mentors-body');
    if (!body) return;
    const mentors = [
        {rang:1, nom:'Yassine El Amrani', filiere:'DEV', aides:24, note:4.9, badges:5},
        {rang:2, nom:'Lea Bernard', filiere:'FRONT', aides:18, note:4.8, badges:4},
        {rang:3, nom:'Sofia Martin', filiere:'DATA', aides:15, note:4.7, badges:4}
    ];
    body.innerHTML = mentors.map(m => `\n    <tr>\n      <td>${m.rang}</td>\n      <td>${m.nom}</td>\n      <td>${m.filiere}</td>\n      <td>${m.aides}</td>\n      <td>${m.note}</td>\n      <td>${m.badges}</td>\n    </tr>\n  `).join('');
})();
