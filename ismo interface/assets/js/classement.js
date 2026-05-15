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
