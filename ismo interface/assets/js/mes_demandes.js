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
        console.log('[mes_demandes.js] Initializing...');

        // Initialize search functionality
        initSearchAndFilter();

        // Initialize request list interactions
        initRequestInteractions();

        // Initialize action buttons
        initActionButtons();

        console.log('[mes_demandes.js] Ready');
    });

    // ══════════════════════════════════════════════════════════════════════════
    // SEARCH & FILTER
    // ══════════════════════════════════════════════════════════════════════════

    function initSearchAndFilter() {
        const searchInput = document.getElementById('search-input');
        const filterBtns = document.querySelectorAll('.filter-btn');

        if (!searchInput) return;

        // Search input listener
        searchInput.addEventListener('input', function (e) {
            const query = e.target.value.toLowerCase();
            const requestItems = document.querySelectorAll('.request-item');

            requestItems.forEach(function (item) {
                const title = item.querySelector('.request-title')?.textContent.toLowerCase() || '';
                const description = item.querySelector('.request-desc')?.textContent.toLowerCase() || '';

                if (title.includes(query) || description.includes(query)) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        });

        // Filter buttons
        if (filterBtns.length > 0) {
            filterBtns.forEach(function (btn) {
                btn.addEventListener('click', function () {
                    // Toggle active state
                    filterBtns.forEach(function (b) {
                        b.classList.remove('active');
                    });
                    this.classList.add('active');

                    const filter = this.dataset.filter;
                    const requestItems = document.querySelectorAll('.request-item');

                    requestItems.forEach(function (item) {
                        const status = item.dataset.status;
                        if (filter === 'all' || status === filter) {
                            item.style.display = '';
                        } else {
                            item.style.display = 'none';
                        }
                    });
                });
            });
        }
    }

    // ══════════════════════════════════════════════════════════════════════════
    // REQUEST INTERACTIONS
    // ══════════════════════════════════════════════════════════════════════════

    function initRequestInteractions() {
        const requestItems = document.querySelectorAll('.request-item');

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
                console.log('[mes_demandes.js] View responses for request:', requestId);
                // TODO: Implement backend call to fetch and display responses
            });
        });

        // Edit button
        const editBtns = document.querySelectorAll('[data-action="edit"]');
        editBtns.forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                const requestId = this.closest('.request-item')?.dataset.requestId;
                console.log('[mes_demandes.js] Edit request:', requestId);
                // TODO: Implement edit modal or redirect to edit page
            });
        });

        // Close button
        const closeBtns = document.querySelectorAll('[data-action="close"]');
        closeBtns.forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                const requestId = this.closest('.request-item')?.dataset.requestId;
                console.log('[mes_demandes.js] Close request:', requestId);
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
                console.log('[mes_demandes.js] Delete request:', requestId);
                if (confirm('Êtes-vous sûr de vouloir supprimer cette demande?')) {
                    // TODO: Implement backend call to delete request
                }
            });
        });

        // Create new request button
        const createNewBtn = document.querySelector('[data-action="create-request"]');
        if (createNewBtn) {
            createNewBtn.addEventListener('click', function () {
                console.log('[mes_demandes.js] Create new request');
                // TODO: Redirect to request creation form or open modal
            });
        }
    }

    // ══════════════════════════════════════════════════════════════════════════
    // UTILITY FUNCTIONS
    // ══════════════════════════════════════════════════════════════════════════

    /**
     * Load requests from backend (placeholder)
     */
    function loadRequests() {
        console.log('[mes_demandes.js] Loading requests from backend...');
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
