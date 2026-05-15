/* catalogue_admin.js - Basic admin actions for catalogue */

'use strict';

const addSkillBtn = document.querySelector('.btn-add-skill');

function toggleDisabled(card) {
    card.classList.toggle('is-disabled');
}

addSkillBtn?.addEventListener('click', () => {
    showToast?.('Ajout de competence en preparation...', 'info');
});

document.querySelectorAll('.action-btn').forEach((btn) => {
    btn.addEventListener('click', (event) => {
        event.preventDefault();
        const action = btn.dataset.action;
        const card = btn.closest('.skill-card');

        if (!card) return;

        if (action === 'disable') {
            toggleDisabled(card);
            showToast?.('Statut mis a jour.', 'success');
        }

        if (action === 'edit') {
            showToast?.('Edition de competence a venir.', 'info');
        }

        if (action === 'delete') {
            const confirmed = confirm('Supprimer cette competence ?');
            if (confirmed) {
                card.remove();
                showToast?.('Competence supprimee.', 'success');
            }
        }
    });
});
