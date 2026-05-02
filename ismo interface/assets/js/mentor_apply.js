/**
 * ISMO-SkillSwap — Mentor Apply JS
 * Handles mentor application interactions and skill selection.
 */

'use strict';

const mentorSkills = document.querySelectorAll('.mentor-skills-grid .skill-pill');
const btnApply = document.getElementById('btn-apply');
const motivationField = document.getElementById('mentor-motivation');
const selectedCountBadge = document.querySelector('.card-title-row .badge-active');

function updateSelectedCount() {
    const count = Array.from(mentorSkills).filter(skill => skill.classList.contains('active')).length;
    if (selectedCountBadge) {
        selectedCountBadge.textContent = `${count} sélectionnée${count > 1 ? 's' : ''}`;
    }
}

mentorSkills.forEach((skill) => {
    skill.addEventListener('click', () => {
        skill.classList.toggle('active');
        updateSelectedCount();
    });
});

btnApply?.addEventListener('click', () => {
    const selectedSkills = Array.from(mentorSkills)
        .filter(skill => skill.classList.contains('active'))
        .map(skill => skill.textContent.trim());

    if (selectedSkills.length === 0) {
        showToast('Sélectionnez au moins une compétence avant de postuler.', 'warning');
        return;
    }

    const motivation = motivationField?.value.trim();
    const message = motivation
        ? 'Candidature envoyée avec succès ! Nous avons bien pris en compte votre motivation.'
        : 'Candidature envoyée avec succès ! N’oubliez pas d’ajouter une motivation pour augmenter vos chances.';

    showToast(message, 'success');
    btnApply.disabled = true;
    btnApply.textContent = 'Candidature en cours...';

    setTimeout(() => {
        btnApply.disabled = false;
        btnApply.textContent = 'Soumettre ma candidature';
    }, 2500);
});

updateSelectedCount();
