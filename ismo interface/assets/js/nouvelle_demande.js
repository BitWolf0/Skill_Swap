/* nouvelle_demande.js - Form validation for new help request */

'use strict';

const requestForm = document.getElementById('request-form');
const fieldMap = {
    titre: document.getElementById('titre'),
    competence: document.getElementById('competence'),
    urgence: document.getElementById('urgence'),
    description: document.getElementById('description'),
    mentor: document.getElementById('mentor')
};

function setFieldError(field, message) {
    const errorEl = document.getElementById(`error-${field.id}`);
    field.setAttribute('aria-invalid', 'true');
    if (errorEl) {
        errorEl.textContent = message;
    }
}

function clearFieldError(field) {
    const errorEl = document.getElementById(`error-${field.id}`);
    field.setAttribute('aria-invalid', 'false');
    if (errorEl) {
        errorEl.textContent = '';
    }
}

function validateForm() {
    let isValid = true;

    ['titre', 'competence', 'urgence', 'description'].forEach((key) => {
        const field = fieldMap[key];
        if (!field) return;
        const value = field.value.trim();
        if (!value) {
            setFieldError(field, 'Champ requis');
            isValid = false;
        } else {
            clearFieldError(field);
        }
    });

    return isValid;
}

function prefillMentor() {
    const params = new URLSearchParams(window.location.search);
    const mentor = params.get('mentor');
    if (mentor && fieldMap.mentor) {
        fieldMap.mentor.value = mentor;
    }
}

requestForm?.addEventListener('submit', (event) => {
    event.preventDefault();

    if (!validateForm()) {
        showToast?.('Merci de verifier les champs requis.', 'info');
        return;
    }

    showToast?.('Demande envoyee avec succes.', 'success');

    setTimeout(() => {
        window.location.href = 'mes_demandes.html';
    }, 900);
});

prefillMentor();
