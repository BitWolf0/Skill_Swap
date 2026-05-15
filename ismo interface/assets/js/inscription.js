/* inscription.js - Registration form validation */

'use strict';

const signupForm = document.getElementById('signup-form');
const alertBox = document.getElementById('signup-alert');

const fields = {
    firstname: document.getElementById('signup-firstname'),
    lastname: document.getElementById('signup-lastname'),
    email: document.getElementById('signup-email'),
    password: document.getElementById('signup-password'),
    confirm: document.getElementById('signup-confirm'),
    role: document.querySelector('input[name="role"]:checked')
};

function setAlert(message, type) {
    if (!alertBox) return;
    alertBox.textContent = message;
    alertBox.className = `form-alert ${type}`;
}

function setFieldError(field, message) {
    const errorEl = document.getElementById(`${field.id}-error`);
    field.setAttribute('aria-invalid', 'true');
    if (errorEl) {
        errorEl.textContent = message;
    }
}

function clearFieldError(field) {
    const errorEl = document.getElementById(`${field.id}-error`);
    field.setAttribute('aria-invalid', 'false');
    if (errorEl) {
        errorEl.textContent = '';
    }
}

function validateEmail(value) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
}

function validateForm() {
    let isValid = true;

    ['firstname', 'lastname', 'email', 'password', 'confirm'].forEach((key) => {
        const field = fields[key];
        if (!field) return;
        if (!field.value.trim()) {
            setFieldError(field, 'Champ requis');
            isValid = false;
        } else {
            clearFieldError(field);
        }
    });

    if (fields.email && fields.email.value && !validateEmail(fields.email.value)) {
        setFieldError(fields.email, 'Email invalide');
        isValid = false;
    }

    if (fields.password && fields.password.value.length < 8) {
        setFieldError(fields.password, '8 caracteres minimum');
        isValid = false;
    }

    if (fields.password && fields.confirm && fields.password.value !== fields.confirm.value) {
        setFieldError(fields.confirm, 'Les mots de passe ne correspondent pas');
        isValid = false;
    }

    const selectedRole = document.querySelector('input[name="role"]:checked');
    if (!selectedRole) {
        setAlert('Selectionnez un role.', 'error');
        isValid = false;
    }

    return isValid;
}

signupForm?.addEventListener('submit', (event) => {
    event.preventDefault();
    setAlert('', '');

    if (!validateForm()) {
        setAlert('Merci de verifier les informations.', 'error');
        return;
    }

    setAlert('Compte cree avec succes. Redirection...', 'success');

    setTimeout(() => {
        window.location.href = 'login.html';
    }, 1200);
});
