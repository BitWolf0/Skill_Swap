/**
 * ISMO-SkillSwap — Gestion Comptes JS
 * Handles account management interactions
 */

'use strict';

/* ──────────────────────────────────────────────
   DOM References
────────────────────────────────────────────── */
const btnCreateAccount = document.querySelector('.btn-create-account');

/* ──────────────────────────────────────────────
   Create Account Button
────────────────────────────────────────────── */
if (btnCreateAccount) {
    btnCreateAccount.addEventListener('click', () => {
        // Show toast message
        showToast('Création de compte en cours...', 'info');
        
        // TODO: Implement account creation form or modal
        // For now, show a placeholder alert
        setTimeout(() => {
            showToast('Fonctionnalité à venir : Formulaire de création de compte', 'info');
        }, 1500);
    });
}

/* ──────────────────────────────────────────────
   Account Action Buttons
────────────────────────────────────────────── */
const approveButtons = document.querySelectorAll('.action-approve');
const rejectButtons = document.querySelectorAll('.action-reject');

approveButtons.forEach(btn => {
    btn.addEventListener('click', (e) => {
        e.preventDefault();
        const userName = btn.getAttribute('aria-label') || 'Compte';
        showToast(`${userName} approuvé`, 'success');
    });
});

rejectButtons.forEach(btn => {
    btn.addEventListener('click', (e) => {
        e.preventDefault();
        const userName = btn.getAttribute('aria-label') || 'Compte';
        showToast(`${userName} rejeté`, 'warning');
    });
});

/* ──────────────────────────────────────────────
   Dashboard.js Common Functionality
────────────────────────────────────────────── */
// Import shared toast function from dashboard.js (assumed to be loaded first)
