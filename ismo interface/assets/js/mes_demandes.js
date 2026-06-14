/**
 * mes_demandes.js
 * Gère la page "Mes Demandes" - affichage et gestion des demandes d'aide créées par l'utilisateur
 * @version 1.0
 */

(function () {
    'use strict';

    // ══════════════════════════════════════════════════════════════════════════
    // INIT
    // ══════════════════════════════════════════════════════════════════════════

    document.addEventListener('DOMContentLoaded', function () {
        initRequestInteractions();
        initActionButtons();
    });

    // ══════════════════════════════════════════════════════════════════════════
    // REQUEST INTERACTIONS
    // ══════════════════════════════════════════════════════════════════════════

    function initRequestInteractions() {
        // support both `.request-item` (older) and `.request-card` (current markup)
        const requestItems = document.querySelectorAll('.request-item, .request-card');

        requestItems.forEach(function (item) {
            // Click to expand details
            item.addEventListener('click', function (e) {
                // Avoid triggering on button clicks
                if (e.target.tagName === 'BUTTON' || e.target.closest('button')) {
                    return;
                }

                item.classList.toggle('expanded');
            });
        });

        // Initialize rating buttons if present
        initRatingModal();
    }

    // ══════════════════════════════════════════════════════════════════════════
    // ACTION BUTTONS
    // ══════════════════════════════════════════════════════════════════════════

    function initActionButtons() {
        // View responses button
        const viewResponsesBtns = document.querySelectorAll('[data-action="view-responses"]');
        viewResponsesBtns.forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                const requestId = this.closest('.request-item')?.dataset.requestId;
                // TODO: Implement backend call to fetch and display responses
            });
        });

        // Edit button
        const editBtns = document.querySelectorAll('[data-action="edit"]');
        editBtns.forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                const requestId = this.closest('.request-item')?.dataset.requestId;
                // TODO: Implement edit modal or redirect to edit page
            });
        });

        // Close button
        const closeBtns = document.querySelectorAll('[data-action="close"]');
        closeBtns.forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                const requestId = this.closest('.request-item')?.dataset.requestId;
                if (confirm('Êtes-vous sûr de vouloir fermer cette demande?')) {
                    // TODO: Implement backend call to close request
                    // On success: removeItem or mark as closed
                }
            });
        });

        // Delete button
        const deleteBtns = document.querySelectorAll('[data-action="delete"]');
        deleteBtns.forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                const requestId = this.closest('.request-item')?.dataset.requestId;
                if (confirm('Êtes-vous sûr de vouloir supprimer cette demande?')) {
                    // TODO: Implement backend call to delete request
                }
            });
        });

        // Create new request button
        const createNewBtn = document.querySelector('[data-action="create-request"]');
        if (createNewBtn) {
            createNewBtn.addEventListener('click', function () {
                window.location.href = 'nouvelle_demande.php';
            });
        }
    }

    // ══════════════════════════════════════════════════════════════════════════
    // RATING MODAL
    // ══════════════════════════════════════════════════════════════════════════

    function initRatingModal() {
        const modal = document.getElementById('rating-modal');
        if (!modal) return;

        const openBtns = document.querySelectorAll('.btn-rate');
        const closeBtn = document.getElementById('rating-close');
        const cancelBtn = document.getElementById('rating-cancel');
        const submitBtn = document.getElementById('rating-submit');
        const backdrop = document.getElementById('rating-backdrop');
        const stars = Array.from(document.querySelectorAll('#rating-stars .star'));
        let selectedValue = 0;
        let currentRequestId = null;
        const ratingCommentEl = document.getElementById('rating-comment');

        // autosize helper for textarea
        function autosizeComment() {
            if (!ratingCommentEl) return;
            ratingCommentEl.style.height = 'auto';
            ratingCommentEl.style.height = ratingCommentEl.scrollHeight + 'px';
        }
        if (ratingCommentEl) {
            ratingCommentEl.addEventListener('input', autosizeComment);
        }

        openBtns.forEach(btn => btn.addEventListener('click', function (e) {
            e.stopPropagation();
            const card = this.closest('.request-card, .request-item');
            currentRequestId = card?.id || card?.dataset.requestId || null;
            const mentorName = card?.querySelector('.request-title')?.textContent || 'Mentor';

            document.getElementById('rating-mentor-name').textContent = mentorName;
            // reset
            selectedValue = 0;
            stars.forEach(s => { s.classList.remove('active'); s.setAttribute('aria-checked','false'); });
            if (ratingCommentEl) {
                ratingCommentEl.value = '';
                autosizeComment();
            }

            openModal(modal);
        }));

        function openModal(m) {
            m.removeAttribute('hidden');
            m.setAttribute('aria-hidden','false');
            document.body.classList.add('modal-open');
        }

        function closeModal(m) {
            m.setAttribute('hidden','');
            m.setAttribute('aria-hidden','true');
            document.body.classList.remove('modal-open');
        }

        // star selection
        stars.forEach(st => {
            st.addEventListener('click', function () {
                selectedValue = parseInt(this.dataset.value, 10) || 0;
                stars.forEach(s => {
                    const v = parseInt(s.dataset.value, 10);
                    if (v <= selectedValue) {
                        s.classList.add('active');
                        s.setAttribute('aria-checked','true');
                    } else {
                        s.classList.remove('active');
                        s.setAttribute('aria-checked','false');
                    }
                });
            });
        });

        // close handlers
        if (closeBtn) closeBtn.addEventListener('click', () => closeModal(modal));
        if (cancelBtn) cancelBtn.addEventListener('click', () => closeModal(modal));
        if (backdrop) backdrop.addEventListener('click', () => closeModal(modal));

        // submit
        if (submitBtn) submitBtn.addEventListener('click', function () {
            const comment = document.getElementById('rating-comment').value.trim();
            if (selectedValue <= 0) {
                showToast('Veuillez sélectionner une note entre 1 et 5.');
                return;
            }

            // simulate POST — keep graceful if backend missing
            const payload = { requestId: currentRequestId, rating: selectedValue, comment };

            // Try to POST if endpoint exists, fallback to simulated success
            (async function send() {
                try {
                    const resp = await fetch('/api/ratings', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '' },
                        body: JSON.stringify(payload)
                    });
                    if (!resp.ok) throw new Error('network');
                    showToast('Merci — votre note a été enregistrée.');
                } catch (err) {
                    console.warn('[mes_demandes] Rating submit failed, simulating success', err);
                    showToast('Note enregistrée (mode simulation).');
                } finally {
                    closeModal(modal);
                }
            })();
        });

        // close on ESC
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && modal.getAttribute('aria-hidden') === 'false') {
                closeModal(modal);
            }
        });
    }

    // Simple toast helper (uses existing #toast-container)
    function showToast(message, timeout = 3000) {
        const container = document.getElementById('toast-container');
        if (!container) {
            alert(message);
            return;
        }
        const el = document.createElement('div');
        el.className = 'toast';
        el.textContent = message;
        container.appendChild(el);
        setTimeout(() => el.classList.add('visible'), 10);
        setTimeout(() => el.classList.remove('visible'), timeout - 300);
        setTimeout(() => container.removeChild(el), timeout);
    }

    // ══════════════════════════════════════════════════════════════════════════
    // UTILITY FUNCTIONS
    // ══════════════════════════════════════════════════════════════════════════

    /**
     * Load requests from backend (placeholder)
     */
    function loadRequests() {
        // TODO: Implement fetch call to GET /api/help-requests
    }

    /**
     * Handle loading state
     */
    function showLoading() {
        const container = document.querySelector('.requests-container');
        if (container) {
            container.innerHTML = '<div class="loading-spinner">Chargement...</div>';
        }
    }

    /**
     * Handle empty state
     */
    function showEmpty() {
        const container = document.querySelector('.requests-container');
        if (container) {
            container.innerHTML =
                '<div class="empty-state"><p>Aucune demande pour le moment.</p></div>';
        }
    }
})();
