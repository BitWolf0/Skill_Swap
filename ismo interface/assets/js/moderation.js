/**
 * ISMO-SkillSwap — Modération JS
 * Handles moderation page interactions and quick actions
 */

'use strict';

/* ──────────────────────────────────────────────
   DOM References
────────────────────────────────────────────── */
const quickActionCards = document.querySelectorAll('.quick-action-card');
const examineBtns = document.querySelectorAll('.examine-btn');
const approveBtns = document.querySelectorAll('.approve-btn');
const deleteBtns = document.querySelectorAll('.delete-btn');
const suspendBtns = document.querySelectorAll('.suspend-user-btn');

/* ──────────────────────────────────────────────
   Quick Actions
────────────────────────────────────────────── */
quickActionCards.forEach((card, index) => {
    card.addEventListener('click', () => {
        const titleEl = card.querySelector('.action-card-title');
        const action = titleEl ? titleEl.textContent.trim() : '';
        
        handleQuickAction(action);
    });
});

function handleQuickAction(action) {
    switch(action.toLowerCase()) {
        case 'messages en masse':
            showToast('Fonctionnalité à venir : Envoi de messages en masse', 'info');
            // TODO: Open bulk message modal
            break;
        case 'règles de modération':
            showToast('Fonctionnalité à venir : Consultation des règles de modération', 'info');
            // TODO: Open moderation guidelines modal
            break;
        case 'historique':
            showToast('Fonctionnalité à venir : Historique des signalements', 'info');
            // TODO: Open history view
            break;
        default:
            showToast('Action non reconnue', 'warning');
    }
}

/* ──────────────────────────────────────────────
   Moderation Action Buttons
────────────────────────────────────────────── */
examineBtns.forEach(btn => {
    btn.addEventListener('click', (e) => {
        e.preventDefault();
        showToast('Ouverture des détails...', 'info');
        // TODO: Open detailed view of the item
    });
});

approveBtns.forEach(btn => {
    btn.addEventListener('click', (e) => {
        e.preventDefault();
        showToast('Contenu approuvé', 'success');
        // TODO: Send approval to backend
    });
});

deleteBtns.forEach(btn => {
    btn.addEventListener('click', (e) => {
        e.preventDefault();
        const confirmed = confirm('Êtes-vous sûr de vouloir supprimer ce contenu ?');
        if (confirmed) {
            showToast('Contenu supprimé', 'success');
            // TODO: Send delete request to backend
        }
    });
});

suspendBtns.forEach(btn => {
    btn.addEventListener('click', (e) => {
        e.preventDefault();
        const confirmed = confirm('Êtes-vous sûr de vouloir suspendre cet utilisateur ?');
        if (confirmed) {
            showToast('Utilisateur suspendu', 'success');
            // TODO: Send suspension request to backend
        }
    });
});

/* ──────────────────────────────────────────────
   Dashboard.js Common Functionality
────────────────────────────────────────────── */
// Import shared toast function from dashboard.js (assumed to be loaded first)
