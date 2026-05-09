/* ======================================
   Catalogue Page JavaScript
   ====================================== */

document.addEventListener('DOMContentLoaded', function() {
    // Tab filtering functionality
    const tabButtons = document.querySelectorAll('.tab-button');
    const skillCards = document.querySelectorAll('.skill-card');

    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons
            tabButtons.forEach(btn => btn.classList.remove('active'));
            
            // Add active class to clicked button
            this.classList.add('active');
            
            // Get filter value
            const filterValue = this.getAttribute('data-filter');
            
            // Filter cards
            skillCards.forEach(card => {
                if (filterValue === 'all') {
                    card.style.display = 'block';
                    // Fade in animation
                    card.style.opacity = '0';
                    setTimeout(() => {
                        card.style.transition = 'opacity 0.3s ease';
                        card.style.opacity = '1';
                    }, 10);
                } else {
                    if (card.getAttribute('data-filter') === filterValue) {
                        card.style.display = 'block';
                        card.style.opacity = '0';
                        setTimeout(() => {
                            card.style.transition = 'opacity 0.3s ease';
                            card.style.opacity = '1';
                        }, 10);
                    } else {
                        card.style.display = 'none';
                    }
                }
            });
        });
    });

    // Add skill button functionality
    const addSkillBtn = document.querySelector('.btn-add-skill');
    if (addSkillBtn) {
        addSkillBtn.addEventListener('click', function() {
            showToast('Fonctionnalité à venir : Ajout de compétence', 'info');
            // TODO: Open add skill form modal
        });
    }

    // Action buttons functionality
    const actionButtons = document.querySelectorAll('.action-btn');
    actionButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const title = this.getAttribute('title');
            
            if (title === 'Supprimer') {
                if (confirm('Êtes-vous sûr de vouloir supprimer cette compétence ?')) {
                    this.closest('.skill-card').remove();
                }
            } else if (title === 'Éditer') {
                alert('Functionality to edit this skill will be implemented soon!');
            } else if (title === 'Voir') {
                alert('Functionality to view skill details will be implemented soon!');
            }
        });
    });

    // Search functionality
    const searchInput = document.querySelector('.search-input');
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            
            skillCards.forEach(card => {
                const title = card.querySelector('.skill-title').textContent.toLowerCase();
                const description = card.querySelector('.skill-description').textContent.toLowerCase();
                
                if (title.includes(searchTerm) || description.includes(searchTerm)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    }
});
