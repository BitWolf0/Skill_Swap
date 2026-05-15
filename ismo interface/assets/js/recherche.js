/* recherche.js - Filter mentors by name, level, and availability */

'use strict';

const searchInput = document.getElementById('search-mentor');
const levelFilter = document.getElementById('filter-level');
const filiereFilter = document.getElementById('filter-filiere');
const availabilityFilter = document.getElementById('filter-available');
const mentorCards = Array.from(document.querySelectorAll('.mentor-card'));

function normalize(value) {
    return value.toLowerCase().trim();
}

function applyFilters() {
    const query = normalize(searchInput?.value || '');
    const level = normalize(levelFilter?.value || '');
    const filiere = normalize(filiereFilter?.value || '');
    const availableOnly = availabilityFilter?.checked;

    mentorCards.forEach((card) => {
        const name = normalize(card.dataset.name || '');
        const cardLevel = normalize(card.dataset.level || '');
        const cardFiliere = normalize(card.dataset.filiere || '');
        const availability = normalize(card.dataset.availability || '');

        const matchQuery = !query || name.includes(query) || cardFiliere.includes(query);
        const matchLevel = !level || level === 'tous' || cardLevel === level;
        const matchFiliere = !filiere || filiere === 'toutes' || cardFiliere === filiere;
        const matchAvailability = !availableOnly || availability === 'disponible';

        card.style.display = matchQuery && matchLevel && matchFiliere && matchAvailability ? '' : 'none';
    });
}

searchInput?.addEventListener('input', applyFilters);
levelFilter?.addEventListener('change', applyFilters);
filiereFilter?.addEventListener('change', applyFilters);
availabilityFilter?.addEventListener('change', applyFilters);

mentorCards.forEach((card) => {
    const actionBtn = card.querySelector('.btn-demander');
    actionBtn?.addEventListener('click', () => {
        const mentorName = card.dataset.name || '';
        const url = `nouvelle_demande.html?mentor=${encodeURIComponent(mentorName)}`;
        window.location.href = url;
    });
});
