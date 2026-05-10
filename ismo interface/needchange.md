# ISMO-SkillSwap — Missing Features & Implementation Guide

> Based on cross-referencing the **Cahier des Charges Fonctionnel** (PDF spec) against the current project JSON file audit.
> Every item below is either absent from the file inventory or incomplete relative to what the spec requires.

---

## Table of Contents

1. [Authentication — Registration Pages](#1-authentication--registration-pages)
2. [Skill Search & Filter Engine (OF8)](#2-skill-search--filter-engine-of8)
3. [Leaderboard & Top Mentors View](#3-leaderboard--top-mentors-view)
4. [Mentor Rating System (OF3)](#4-mentor-rating-system-of3)
5. [Notification Page for Mentor Role](#5-notification-page-for-mentor-role)
6. [Settings Pages for Mentor, Formateur & Admin](#6-settings-pages-for-mentor-formateur--admin)
7. [nouvelle_demande.js — Missing Script](#7-nouvelle_demandejs--missing-script)
8. [Statistics JS for Formateur & Admin](#8-statistics-js-for-formateur--admin)
9. [Admin Skill Catalogue Page](#9-admin-skill-catalogue-page)
10. [Responsive / Mobile CSS](#10-responsive--mobile-css)
11. [PHP Backend + MySQL (Required by Spec)](#11-php-backend--mysql-required-by-spec)

---

## 1. Authentication — Registration Pages

**What is missing:** The spec requires `Inscription` (registration) for all 4 roles. Currently only `login.html` exists, and only for the Stagiaire role. There is no registration page anywhere in the project.

**Functional objectives covered:** OF module — Authentication & profile management (Inscription row, all 4 roles = ✔)

---

### Steps to implement

**Step 1 — Create the shared registration page**

Create `pages_stagiaire/inscription.html` as the base registration form. It will be reused across roles via a role selector.

```html
<!-- pages_stagiaire/inscription.html -->
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Inscription — ISMO SkillSwap</title>
  <link rel="stylesheet" href="../assets/css/login.css" />
</head>
<body>
  <div class="auth-card">
    <h1>Créer un compte</h1>
    <form id="register-form">
      <label>Nom complet</label>
      <input type="text" name="nom" required />

      <label>Email institutionnel</label>
      <input type="email" name="email" required />

      <label>Filière</label>
      <input type="text" name="filiere" required />

      <label>Mot de passe</label>
      <input type="password" name="password" required />

      <label>Confirmer le mot de passe</label>
      <input type="password" name="password_confirm" required />

      <label>Rôle</label>
      <select name="role">
        <option value="stagiaire">Stagiaire</option>
        <option value="mentor">Mentor</option>
        <option value="formateur">Formateur</option>
      </select>
      <!-- Admin accounts are created by the Admin only — no self-registration -->

      <button type="submit">S'inscrire</button>
    </form>
    <p>Déjà inscrit ? <a href="login.html">Se connecter</a></p>
  </div>
  <script src="../assets/js/inscription.js"></script>
</body>
</html>
```

**Step 2 — Create `assets/js/inscription.js`**

```javascript
// assets/js/inscription.js
const form = document.getElementById('register-form');

form.addEventListener('submit', function (e) {
  e.preventDefault();

  const password = form.password.value;
  const confirm  = form.password_confirm.value;

  if (password !== confirm) {
    alert('Les mots de passe ne correspondent pas.');
    return;
  }

  const data = {
    nom:      form.nom.value.trim(),
    email:    form.email.value.trim(),
    filiere:  form.filiere.value.trim(),
    password: password,
    role:     form.role.value,
  };

  // TODO: Replace with real fetch() call to PHP backend
  console.log('Inscription data:', data);
  alert('Compte créé avec succès ! En attente de validation par l\'administrateur.');
  window.location.href = 'login.html';
});
```

**Step 3 — Create `assets/css/inscription.css`** (or reuse `login.css` — both share the same card layout)

Add a new entry in `login.css` if needed:

```css
/* Add to assets/css/login.css */
select {
  width: 100%;
  padding: 0.5rem 0.75rem;
  border: 1px solid var(--border-color);
  border-radius: var(--radius-md);
  font-size: 0.95rem;
  background: #fff;
  margin-bottom: 1rem;
}
```

**Step 4 — Add the link from `login.html`**

Open `pages_stagiaire/login.html` and add:

```html
<p>Pas encore de compte ? <a href="inscription.html">S'inscrire</a></p>
```

**Step 5 — Add role-specific login redirects**

In `assets/js/login.js`, after successful authentication, redirect based on the role returned:

```javascript
// In login.js — after auth success
const role = response.role; // value from backend
const redirectMap = {
  stagiaire: '../pages_stagiaire/dashboard.html',
  mentor:    '../pages_mentor/dashboard.html',
  formateur: '../formateur_pages/tableau_de_bord.html',
  admin:     '../pages_admin/tableau_de_bord.html',
};
window.location.href = redirectMap[role] || 'login.html';
```

---

## 2. Skill Search & Filter Engine (OF8)

**What is missing:** The spec defines OF8 as a full search module with filters by skill, level, filière, and mentor availability. No search page or search JS file exists anywhere in the project.

**Functional objectives covered:** OF8 — Moteur de recherche par compétence

---

### Steps to implement

**Step 1 — Create `pages_stagiaire/recherche.html`**

```html
<!-- pages_stagiaire/recherche.html -->
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Recherche — ISMO SkillSwap</title>
  <link rel="stylesheet" href="../assets/css/marketplace.css" />
  <link rel="stylesheet" href="../assets/css/recherche.css" />
</head>
<body>
  <!-- Sidebar (same as dashboard) -->
  <aside class="sidebar">...</aside>

  <main class="main-content">
    <h1>Rechercher un mentor / une compétence</h1>

    <div class="search-bar">
      <input type="text" id="search-input" placeholder="Ex: React, SQL, MERISE..." />
      <button id="search-btn">Rechercher</button>
    </div>

    <div class="filters">
      <select id="filter-niveau">
        <option value="">Tous les niveaux</option>
        <option value="debutant">Débutant</option>
        <option value="intermediaire">Intermédiaire</option>
        <option value="avance">Avancé</option>
      </select>

      <select id="filter-filiere">
        <option value="">Toutes les filières</option>
        <option value="DEV">DEV</option>
        <option value="TSDI">TSDI</option>
        <option value="TRI">TRI</option>
      </select>

      <label>
        <input type="checkbox" id="filter-disponible" />
        Disponibles seulement
      </label>
    </div>

    <div id="results-container" class="results-grid">
      <!-- Mentor cards injected here by JS -->
    </div>
  </main>

  <script src="../assets/js/recherche.js"></script>
</body>
</html>
```

**Step 2 — Create `assets/js/recherche.js`**

```javascript
// assets/js/recherche.js

// Mock data — replace with fetch() to PHP API
const mentors = [
  { nom: 'Yassine El Amrani', competences: ['React', 'JavaScript'], niveau: 'avance',   filiere: 'DEV',  disponible: true  },
  { nom: 'Sara Benali',       competences: ['SQL', 'MySQL'],         niveau: 'avance',   filiere: 'TSDI', disponible: false },
  { nom: 'Amine Fassi',       competences: ['MERISE', 'UML'],        niveau: 'intermediaire', filiere: 'DEV', disponible: true },
];

function renderCards(list) {
  const container = document.getElementById('results-container');
  if (list.length === 0) {
    container.innerHTML = '<p class="empty">Aucun résultat trouvé.</p>';
    return;
  }
  container.innerHTML = list.map(m => `
    <div class="mentor-card">
      <img src="../assets/images/default_avatar.svg" alt="avatar" class="avatar" />
      <h3>${m.nom}</h3>
      <p class="filiere">${m.filiere} — ${m.niveau}</p>
      <div class="tags">${m.competences.map(c => `<span class="tag">${c}</span>`).join('')}</div>
      <span class="status ${m.disponible ? 'available' : 'busy'}">
        ${m.disponible ? 'Disponible' : 'Occupé'}
      </span>
      <a href="nouvelle_demande.html?mentor=${encodeURIComponent(m.nom)}" class="btn-contact">Contacter</a>
    </div>
  `).join('');
}

function applyFilters() {
  const query     = document.getElementById('search-input').value.toLowerCase();
  const niveau    = document.getElementById('filter-niveau').value;
  const filiere   = document.getElementById('filter-filiere').value;
  const dispoOnly = document.getElementById('filter-disponible').checked;

  const filtered = mentors.filter(m => {
    const matchQuery  = !query   || m.competences.some(c => c.toLowerCase().includes(query)) || m.nom.toLowerCase().includes(query);
    const matchNiveau = !niveau  || m.niveau === niveau;
    const matchFiliere= !filiere || m.filiere === filiere;
    const matchDispo  = !dispoOnly || m.disponible;
    return matchQuery && matchNiveau && matchFiliere && matchDispo;
  });

  renderCards(filtered);
}

document.getElementById('search-btn').addEventListener('click', applyFilters);
document.getElementById('search-input').addEventListener('keyup', e => { if (e.key === 'Enter') applyFilters(); });
document.getElementById('filter-niveau').addEventListener('change', applyFilters);
document.getElementById('filter-filiere').addEventListener('change', applyFilters);
document.getElementById('filter-disponible').addEventListener('change', applyFilters);

// Initial render
renderCards(mentors);
```

**Step 3 — Create `assets/css/recherche.css`**

```css
/* assets/css/recherche.css */
.search-bar {
  display: flex;
  gap: 0.75rem;
  margin-bottom: 1.25rem;
}
.search-bar input {
  flex: 1;
  padding: 0.6rem 1rem;
  border: 1px solid var(--border-color);
  border-radius: var(--radius-md);
  font-size: 1rem;
}
.filters {
  display: flex;
  flex-wrap: wrap;
  gap: 1rem;
  margin-bottom: 1.5rem;
  align-items: center;
}
.results-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
  gap: 1.25rem;
}
.mentor-card {
  background: #fff;
  border: 1px solid var(--border-color);
  border-radius: var(--radius-lg);
  padding: 1.25rem;
  text-align: center;
}
.mentor-card .avatar { width: 56px; height: 56px; border-radius: 50%; margin-bottom: 0.5rem; }
.mentor-card h3 { font-size: 0.95rem; margin-bottom: 0.25rem; }
.mentor-card .tags { display: flex; flex-wrap: wrap; justify-content: center; gap: 0.4rem; margin: 0.5rem 0; }
.mentor-card .tag { background: #e8f0fe; color: #1a56db; border-radius: 20px; padding: 2px 10px; font-size: 0.75rem; }
.status.available { color: green; font-size: 0.8rem; }
.status.busy      { color: #aaa;   font-size: 0.8rem; }
.btn-contact { display: inline-block; margin-top: 0.75rem; padding: 0.4rem 1rem; background: var(--primary); color: #fff; border-radius: var(--radius-md); font-size: 0.85rem; text-decoration: none; }
```

**Step 4 — Add search link to all sidebars**

In each role's sidebar navigation, add:

```html
<a href="recherche.html" class="nav-item">
  <svg><!-- search icon --></svg>
  Recherche
</a>
```

Copy `pages_stagiaire/recherche.html` to `pages_mentor/recherche.html` and `formateur_pages/recherche.html` adjusting sidebar links and relative paths (`../`).

---

## 3. Leaderboard & Top Mentors View

**What is missing:** The spec requires "Voir classement filière" and "Voir Top Mentors" accessible to all roles. No such page or widget exists.

**Functional objectives covered:** Module Gamification — Voir Top Mentors (all roles ✔); Module Passeport — Voir classement filière (all roles ✔)

---

### Steps to implement

**Step 1 — Create `pages_stagiaire/classement.html`**

```html
<!-- pages_stagiaire/classement.html -->
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <title>Classement — ISMO SkillSwap</title>
  <link rel="stylesheet" href="../assets/css/mes_badges.css" />
  <link rel="stylesheet" href="../assets/css/classement.css" />
</head>
<body>
  <aside class="sidebar">...</aside>
  <main class="main-content">
    <h1>Top Mentors & Classement filière</h1>

    <div class="tabs">
      <button class="tab active" data-tab="top-mentors">Top Mentors</button>
      <button class="tab" data-tab="classement-filiere">Classement filière</button>
    </div>

    <div id="top-mentors" class="tab-content active">
      <table class="leaderboard-table">
        <thead>
          <tr><th>#</th><th>Mentor</th><th>Filière</th><th>Aides</th><th>Note</th><th>Badges</th></tr>
        </thead>
        <tbody id="mentors-body"></tbody>
      </table>
    </div>

    <div id="classement-filiere" class="tab-content">
      <table class="leaderboard-table">
        <thead>
          <tr><th>#</th><th>Stagiaire</th><th>Filière</th><th>Points</th><th>Compétences validées</th></tr>
        </thead>
        <tbody id="filiere-body"></tbody>
      </table>
    </div>
  </main>
  <script src="../assets/js/classement.js"></script>
</body>
</html>
```

**Step 2 — Create `assets/js/classement.js`**

```javascript
// assets/js/classement.js

const topMentors = [
  { rang: 1, nom: 'Yassine El Amrani', filiere: 'DEV',  aides: 24, note: 4.9, badges: 5 },
  { rang: 2, nom: 'Sara Benali',       filiere: 'TSDI', aides: 18, note: 4.7, badges: 3 },
  { rang: 3, nom: 'Amine Fassi',       filiere: 'DEV',  aides: 15, note: 4.6, badges: 4 },
];

const classementFiliere = [
  { rang: 1, nom: 'Nadia Chraibi',    filiere: 'DEV',  points: 320, competences: 8 },
  { rang: 2, nom: 'Omar Tazi',        filiere: 'DEV',  points: 280, competences: 6 },
  { rang: 3, nom: 'Hind Mansouri',    filiere: 'TSDI', points: 260, competences: 5 },
];

document.getElementById('mentors-body').innerHTML = topMentors.map(m => `
  <tr>
    <td>${m.rang === 1 ? '🥇' : m.rang === 2 ? '🥈' : m.rang === 3 ? '🥉' : m.rang}</td>
    <td>${m.nom}</td><td>${m.filiere}</td>
    <td>${m.aides}</td><td>${m.note} / 5</td><td>${m.badges}</td>
  </tr>`).join('');

document.getElementById('filiere-body').innerHTML = classementFiliere.map(s => `
  <tr>
    <td>${s.rang}</td><td>${s.nom}</td><td>${s.filiere}</td>
    <td>${s.points} pts</td><td>${s.competences}</td>
  </tr>`).join('');

// Tab switching
document.querySelectorAll('.tab').forEach(btn => {
  btn.addEventListener('click', () => {
    document.querySelectorAll('.tab, .tab-content').forEach(el => el.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById(btn.dataset.tab).classList.add('active');
  });
});
```

**Step 3 — Create `assets/css/classement.css`**

```css
/* assets/css/classement.css */
.tabs { display: flex; gap: 0.5rem; margin-bottom: 1.25rem; }
.tab { padding: 0.4rem 1.1rem; border: 1px solid var(--border-color); border-radius: 20px; background: none; cursor: pointer; font-size: 0.9rem; }
.tab.active { background: var(--primary); color: #fff; border-color: var(--primary); }
.tab-content { display: none; }
.tab-content.active { display: block; }
.leaderboard-table { width: 100%; border-collapse: collapse; }
.leaderboard-table th, .leaderboard-table td { padding: 0.75rem 1rem; border-bottom: 1px solid var(--border-color); text-align: left; font-size: 0.9rem; }
.leaderboard-table th { font-weight: 500; background: var(--bg-secondary); }
.leaderboard-table tr:first-child td { font-weight: 600; }
```

**Step 4 — Copy page to all roles**

Duplicate `classement.html` into `pages_mentor/`, `formateur_pages/`, and `pages_admin/` — adjust relative paths for CSS, JS, and sidebar links accordingly.

---

## 4. Mentor Rating System (OF3)

**What is missing:** No dedicated rating UI or JS file for rating a mentor after receiving help. OF3 requires a notation system visible to Stagiaire and Mentor roles.

**Functional objectives covered:** OF3 — Système de notation des mentors

---

### Steps to implement

**Step 1 — Add a rating modal component to `mes_demandes.html`**

In `pages_stagiaire/mes_demandes.html`, after each resolved request card, add a "Noter" button and a modal:

```html
<!-- Rating button on resolved request cards -->
<button class="btn-noter" data-mentor="Yassine El Amrani" data-demande-id="12">
  ⭐ Noter ce mentor
</button>

<!-- Rating modal (place at end of body) -->
<div id="rating-modal" class="modal hidden">
  <div class="modal-card">
    <h2>Noter <span id="mentor-name-display"></span></h2>
    <div class="stars" id="star-rating">
      <span data-value="1">★</span>
      <span data-value="2">★</span>
      <span data-value="3">★</span>
      <span data-value="4">★</span>
      <span data-value="5">★</span>
    </div>
    <textarea id="rating-comment" placeholder="Commentaire (optionnel)..." rows="3"></textarea>
    <div class="modal-actions">
      <button id="submit-rating">Envoyer</button>
      <button id="close-modal">Annuler</button>
    </div>
  </div>
</div>
```

**Step 2 — Add rating logic to `assets/js/mes_demandes.js`**

```javascript
// Add to mes_demandes.js

let selectedRating = 0;

document.querySelectorAll('.btn-noter').forEach(btn => {
  btn.addEventListener('click', () => {
    document.getElementById('mentor-name-display').textContent = btn.dataset.mentor;
    document.getElementById('rating-modal').classList.remove('hidden');
    document.getElementById('rating-modal').dataset.demandeId = btn.dataset.demandeId;
  });
});

document.getElementById('close-modal').addEventListener('click', () => {
  document.getElementById('rating-modal').classList.add('hidden');
  selectedRating = 0;
  resetStars();
});

document.querySelectorAll('#star-rating span').forEach(star => {
  star.addEventListener('click', () => {
    selectedRating = parseInt(star.dataset.value);
    highlightStars(selectedRating);
  });
  star.addEventListener('mouseover', () => highlightStars(parseInt(star.dataset.value)));
  star.addEventListener('mouseout',  () => highlightStars(selectedRating));
});

function highlightStars(count) {
  document.querySelectorAll('#star-rating span').forEach((s, i) => {
    s.style.color = i < count ? '#f5a623' : '#ccc';
  });
}
function resetStars() {
  document.querySelectorAll('#star-rating span').forEach(s => s.style.color = '#ccc');
}

document.getElementById('submit-rating').addEventListener('click', () => {
  if (selectedRating === 0) { alert('Veuillez sélectionner une note.'); return; }
  const payload = {
    demandeId: document.getElementById('rating-modal').dataset.demandeId,
    note:      selectedRating,
    commentaire: document.getElementById('rating-comment').value,
  };
  console.log('Rating submitted:', payload);
  // TODO: fetch('/api/ratings', { method: 'POST', body: JSON.stringify(payload) })
  alert('Merci pour votre évaluation !');
  document.getElementById('rating-modal').classList.add('hidden');
});
```

**Step 3 — Add rating modal styles to `assets/css/mes_demandes.css`**

```css
/* Add to mes_demandes.css */
.modal { position: fixed; inset: 0; background: rgba(0,0,0,0.4); display: flex; align-items: center; justify-content: center; z-index: 999; }
.modal.hidden { display: none; }
.modal-card { background: #fff; border-radius: var(--radius-lg); padding: 2rem; width: min(420px, 90vw); }
.modal-card h2 { margin-bottom: 1rem; font-size: 1.1rem; }
.stars span { font-size: 2rem; cursor: pointer; color: #ccc; transition: color 0.15s; }
#rating-comment { width: 100%; margin: 1rem 0; padding: 0.5rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); resize: vertical; }
.modal-actions { display: flex; gap: 0.75rem; justify-content: flex-end; }
.btn-noter { background: none; border: 1px solid var(--primary); color: var(--primary); border-radius: 20px; padding: 0.3rem 0.8rem; cursor: pointer; font-size: 0.85rem; }
```

---

## 5. Notification Page for Mentor Role

**What is missing:** `notification.html` and `notification.js` exist only under `pages_stagiaire/`. The Mentor role has no notification page listed.

---

### Steps to implement

**Step 1 — Duplicate the Stagiaire notification page**

```bash
cp pages_stagiaire/notification.html pages_mentor/notification.html
```

**Step 2 — Update relative paths inside `pages_mentor/notification.html`**

Change all `../assets/` paths — they are already correct if the file is one level deep. Verify the sidebar links point to the Mentor pages:

```html
<!-- In pages_mentor/notification.html, update nav links -->
<a href="dashboard.html">Tableau de bord</a>
<a href="marketplace.html">Marketplace</a>
<a href="mes_demandes.html">Mes demandes</a>
<a href="notification.html" class="active">Notifications</a>
```

**Step 3 — Customize notification types for Mentor role**

In the JS (can reuse `notification.js`), add mentor-specific notification categories:

```javascript
// Mentor-specific notification types to handle:
// - "Nouvelle demande d'aide" (someone requested help in your skill area)
// - "Votre aide a été acceptée"
// - "Vous avez reçu une note : ★★★★☆"
// - "Badge débloqué : Mentor confirmé"
// - "Demande marquée comme résolue"
const MENTOR_NOTIFICATION_TYPES = ['nouvelle_demande', 'aide_acceptee', 'notation', 'badge', 'resolution'];
```

**Step 4 — Add notification link in `pages_mentor` sidebar**

Verify every page under `pages_mentor/` has this nav item:

```html
<a href="notification.html" class="nav-item">Notifications</a>
```

---

## 6. Settings Pages for Mentor, Formateur & Admin

**What is missing:** `parametres.html` only exists for Stagiaire. The spec requires all roles to be able to "modifier profil". No settings page for Mentor, Formateur, or Admin.

---

### Steps to implement

**Step 1 — Copy the Stagiaire settings page to each role folder**

```bash
cp pages_stagiaire/parametres.html pages_mentor/parametres.html
cp pages_stagiaire/parametres.html formateur_pages/parametres.html
cp pages_stagiaire/parametres.html pages_admin/parametres.html
```

**Step 2 — Update sidebar links in each copy**

Each `parametres.html` copy should reference the correct role pages. Example for Mentor:

```html
<!-- pages_mentor/parametres.html — sidebar links -->
<a href="dashboard.html">Tableau de bord</a>
<a href="marketplace.html">Marketplace</a>
<a href="mes_aides.html">Mes aides</a>
<a href="parametres.html" class="active">Paramètres</a>
```

**Step 3 — Hide role-specific sections per role**

In `parametres.html`, wrap role-specific fields in a data-role div:

```html
<!-- Only show to Stagiaire/Mentor -->
<div class="role-section" data-roles="stagiaire,mentor">
  <h3>Mes compétences</h3>
  <a href="mes_competances.html">Gérer mes compétences →</a>
</div>

<!-- Only show to Admin -->
<div class="role-section" data-roles="admin">
  <h3>Gestion de la plateforme</h3>
  <a href="gestion_comptes.html">Gérer les comptes →</a>
</div>
```

Add to `assets/js/parametres.js`:

```javascript
// Show only sections relevant to the current role
const currentRole = localStorage.getItem('user_role') || 'stagiaire';
document.querySelectorAll('.role-section').forEach(section => {
  const allowed = section.dataset.roles.split(',');
  section.style.display = allowed.includes(currentRole) ? 'block' : 'none';
});
```

**Step 4 — Add "Paramètres" link to each role's sidebar**

In all pages under `pages_mentor/`, `formateur_pages/`, `pages_admin/`:

```html
<a href="parametres.html" class="nav-item">Paramètres</a>
```

---

## 7. nouvelle_demande.js — Missing Script

**What is missing:** `nouvelle_demande.html` exists under `pages_stagiaire/` but no corresponding `nouvelle_demande.js` is listed in `assets/js/`. The form has no submission logic.

**Functional objectives covered:** OF1 — Publier une demande d'aide technique

---

### Steps to implement

**Step 1 — Create `assets/js/nouvelle_demande.js`**

```javascript
// assets/js/nouvelle_demande.js

// Pre-fill mentor name if redirected from search page
const params     = new URLSearchParams(window.location.search);
const mentorPre  = params.get('mentor');
if (mentorPre) {
  const mentorField = document.getElementById('mentor-cible');
  if (mentorField) mentorField.value = mentorPre;
}

// Form validation and submission
const form = document.getElementById('demande-form');
if (form) {
  form.addEventListener('submit', function (e) {
    e.preventDefault();

    const demande = {
      titre:       form.titre.value.trim(),
      description: form.description.value.trim(),
      competence:  form.competence.value.trim(),
      urgence:     form.urgence?.value || 'normal',
      mentor:      form.querySelector('#mentor-cible')?.value || null,
    };

    if (!demande.titre || !demande.description || !demande.competence) {
      alert('Veuillez remplir tous les champs obligatoires.');
      return;
    }

    // TODO: POST to /api/demandes
    console.log('Nouvelle demande:', demande);
    alert('Demande publiée avec succès !');
    window.location.href = 'mes_demandes.html';
  });
}
```

**Step 2 — Add the script reference to `nouvelle_demande.html`**

Ensure the bottom of `pages_stagiaire/nouvelle_demande.html` includes:

```html
<script src="../assets/js/nouvelle_demande.js"></script>
```

**Step 3 — Verify the form has the correct field `id` attributes**

The form in `nouvelle_demande.html` must have these `id` values to match the JS:

```html
<form id="demande-form">
  <input  id="titre"       name="titre"       type="text"     placeholder="Titre de la demande" required />
  <textarea id="description" name="description" placeholder="Décrivez votre problème..." required></textarea>
  <input  id="competence"  name="competence"  type="text"     placeholder="Ex: React, SQL..." required />
  <select id="urgence"     name="urgence">
    <option value="normal">Normal</option>
    <option value="urgent">Urgent</option>
  </select>
  <input  id="mentor-cible" name="mentor" type="text" placeholder="Mentor souhaité (optionnel)" />
  <button type="submit">Publier la demande</button>
</form>
```

---

## 8. Statistics JS for Formateur & Admin

**What is missing:** `statistique.css` exists, and `statistique.html` exists under Formateur, and `statistique_adm.html` under Admin — but no JS file for either (`statistique.js`) is listed in `assets/js/`.

**Functional objectives covered:** OF9 — Tableau de bord statistique (Formateur + Admin global stats)

---

### Steps to implement

**Step 1 — Create `assets/js/statistique.js`**

```javascript
// assets/js/statistique.js
// Shared statistics script for Formateur and Admin pages

document.addEventListener('DOMContentLoaded', () => {
  renderStats();
  renderCharts();
});

// Mock data — replace with fetch('/api/statistiques')
const stats = {
  totalDemandes:        142,
  demandesResolues:     118,
  competencesValidees:   67,
  topCompetences:       [{ name: 'React', count: 34 }, { name: 'SQL', count: 28 }, { name: 'PHP', count: 22 }, { name: 'UML', count: 15 }],
  noteMoyenne:          4.6,
  mentorsActifs:        23,
  stagiairesInscrits:   87,
};

function renderStats() {
  const map = {
    'stat-demandes':    stats.totalDemandes,
    'stat-resolues':    stats.demandesResolues,
    'stat-competences': stats.competencesValidees,
    'stat-note':        stats.noteMoyenne + ' / 5',
    'stat-mentors':     stats.mentorsActifs,
    'stat-stagiaires':  stats.stagiairesInscrits,
  };
  Object.entries(map).forEach(([id, val]) => {
    const el = document.getElementById(id);
    if (el) el.textContent = val;
  });
}

function renderCharts() {
  const ctx = document.getElementById('competences-chart');
  if (!ctx) return;

  // Simple bar chart using Canvas API (no library dependency)
  const canvas = ctx.getContext('2d');
  const data   = stats.topCompetences;
  const max    = Math.max(...data.map(d => d.count));
  const barW   = 60, gap = 30, startX = 40, startY = 180, chartH = 140;

  data.forEach((item, i) => {
    const x   = startX + i * (barW + gap);
    const h   = (item.count / max) * chartH;
    const y   = startY - h;
    canvas.fillStyle = '#1a56db';
    canvas.fillRect(x, y, barW, h);
    canvas.fillStyle = '#333';
    canvas.font = '12px sans-serif';
    canvas.textAlign = 'center';
    canvas.fillText(item.name,  x + barW / 2, startY + 16);
    canvas.fillText(item.count, x + barW / 2, y - 6);
  });
}
```

**Step 2 — Add stat placeholder elements to `statistique.html` (Formateur)**

Ensure these `id` attributes exist in `formateur_pages/statistique.html`:

```html
<div class="stat-card"><h3>Total demandes</h3><p id="stat-demandes">—</p></div>
<div class="stat-card"><h3>Demandes résolues</h3><p id="stat-resolues">—</p></div>
<div class="stat-card"><h3>Compétences validées</h3><p id="stat-competences">—</p></div>
<div class="stat-card"><h3>Note moyenne mentors</h3><p id="stat-note">—</p></div>

<canvas id="competences-chart" width="400" height="200"></canvas>

<script src="../assets/js/statistique.js"></script>
```

**Step 3 — Add stat placeholders to `statistique_adm.html` (Admin)**

Admin gets additional stats:

```html
<div class="stat-card"><h3>Mentors actifs</h3><p id="stat-mentors">—</p></div>
<div class="stat-card"><h3>Stagiaires inscrits</h3><p id="stat-stagiaires">—</p></div>

<script src="../assets/js/statistique.js"></script>
```

---

## 9. Admin Skill Catalogue Page

**What is missing:** The spec states Admin can "modifier catalogue compétences". `catalogue.html` and `catalogue.js` exist only under `formateur_pages/`. No admin catalogue page is listed.

---

### Steps to implement

**Step 1 — Create `pages_admin/catalogue_admin.html`**

```html
<!-- pages_admin/catalogue_admin.html -->
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8"/>
  <title>Catalogue des compétences — Admin</title>
  <link rel="stylesheet" href="../assets/css/catalogue.css" />
</head>
<body>
  <aside class="sidebar">...</aside>
  <main class="main-content">
    <div class="page-header">
      <h1>Catalogue des compétences</h1>
      <button id="btn-add-competence" class="btn-primary">+ Ajouter une compétence</button>
    </div>

    <table id="catalogue-table" class="data-table">
      <thead>
        <tr><th>Compétence</th><th>Catégorie</th><th>Niveau max</th><th>Statut</th><th>Actions</th></tr>
      </thead>
      <tbody id="catalogue-body">
        <!-- Injected by JS -->
      </tbody>
    </table>

    <!-- Add / Edit modal -->
    <div id="competence-modal" class="modal hidden">
      <div class="modal-card">
        <h2 id="modal-title">Ajouter une compétence</h2>
        <label>Nom</label>
        <input type="text" id="comp-nom" placeholder="Ex: React, SQL..." />
        <label>Catégorie</label>
        <input type="text" id="comp-categorie" placeholder="Ex: Développement Web" />
        <label>Niveau maximum</label>
        <select id="comp-niveau">
          <option value="debutant">Débutant</option>
          <option value="intermediaire">Intermédiaire</option>
          <option value="avance">Avancé</option>
        </select>
        <div class="modal-actions">
          <button id="save-competence">Enregistrer</button>
          <button id="close-comp-modal">Annuler</button>
        </div>
      </div>
    </div>
  </main>
  <script src="../assets/js/catalogue_admin.js"></script>
</body>
</html>
```

**Step 2 — Create `assets/js/catalogue_admin.js`**

```javascript
// assets/js/catalogue_admin.js

let competences = [
  { id: 1, nom: 'React',      categorie: 'Développement Web', niveau: 'avance',       actif: true },
  { id: 2, nom: 'SQL',        categorie: 'Base de données',    niveau: 'avance',       actif: true },
  { id: 3, nom: 'MERISE',     categorie: 'Conception',         niveau: 'intermediaire',actif: true },
  { id: 4, nom: 'PHP',        categorie: 'Développement Web',  niveau: 'avance',       actif: false },
];
let editingId = null;

function renderTable() {
  document.getElementById('catalogue-body').innerHTML = competences.map(c => `
    <tr>
      <td>${c.nom}</td>
      <td>${c.categorie}</td>
      <td>${c.niveau}</td>
      <td><span class="status-badge ${c.actif ? 'active' : 'inactive'}">${c.actif ? 'Actif' : 'Inactif'}</span></td>
      <td>
        <button onclick="editComp(${c.id})">Modifier</button>
        <button onclick="toggleComp(${c.id})">${c.actif ? 'Désactiver' : 'Activer'}</button>
        <button onclick="deleteComp(${c.id})" class="btn-danger">Supprimer</button>
      </td>
    </tr>`).join('');
}

function openModal(comp = null) {
  editingId = comp ? comp.id : null;
  document.getElementById('modal-title').textContent = comp ? 'Modifier la compétence' : 'Ajouter une compétence';
  document.getElementById('comp-nom').value      = comp?.nom      || '';
  document.getElementById('comp-categorie').value= comp?.categorie|| '';
  document.getElementById('comp-niveau').value   = comp?.niveau   || 'debutant';
  document.getElementById('competence-modal').classList.remove('hidden');
}

window.editComp   = id => openModal(competences.find(c => c.id === id));
window.toggleComp = id => { competences = competences.map(c => c.id === id ? {...c, actif: !c.actif} : c); renderTable(); };
window.deleteComp = id => { if (confirm('Supprimer cette compétence ?')) { competences = competences.filter(c => c.id !== id); renderTable(); } };

document.getElementById('btn-add-competence').addEventListener('click', () => openModal());
document.getElementById('close-comp-modal').addEventListener('click', () => document.getElementById('competence-modal').classList.add('hidden'));

document.getElementById('save-competence').addEventListener('click', () => {
  const nom      = document.getElementById('comp-nom').value.trim();
  const categorie= document.getElementById('comp-categorie').value.trim();
  const niveau   = document.getElementById('comp-niveau').value;
  if (!nom || !categorie) { alert('Remplissez tous les champs.'); return; }

  if (editingId) {
    competences = competences.map(c => c.id === editingId ? {...c, nom, categorie, niveau} : c);
  } else {
    competences.push({ id: Date.now(), nom, categorie, niveau, actif: true });
  }
  document.getElementById('competence-modal').classList.add('hidden');
  renderTable();
});

renderTable();
```

**Step 3 — Add the page to Admin sidebar**

In all pages under `pages_admin/`, add:

```html
<a href="catalogue_admin.html" class="nav-item">Catalogue compétences</a>
```

---

## 10. Responsive / Mobile CSS

**What is missing:** The spec explicitly requires compatibility with desktop, tablets, and smartphones. The current JSON has no mention of media queries or responsive breakpoints.

---

### Steps to implement

**Step 1 — Add global responsive rules to `dashboard.css`** (shared across all roles)

```css
/* Responsive breakpoints — add to assets/css/dashboard.css */

/* Tablet: 768px–1024px */
@media (max-width: 1024px) {
  .layout {
    grid-template-columns: 60px 1fr; /* collapse sidebar to icon-only */
  }
  .sidebar .nav-label {
    display: none; /* hide text labels, keep icons */
  }
  .sidebar .nav-item {
    justify-content: center;
    padding: 0.75rem;
  }
}

/* Mobile: ≤768px */
@media (max-width: 768px) {
  .layout {
    grid-template-columns: 1fr;
    grid-template-rows: auto auto 1fr;
  }
  .sidebar {
    display: none; /* hide sidebar on mobile */
  }
  .topbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  #mobile-menu-btn {
    display: block; /* show hamburger button */
  }
  .sidebar.mobile-open {
    display: flex;
    position: fixed;
    inset: 0;
    z-index: 100;
    width: 260px;
    background: var(--sidebar-bg);
  }
  .main-content {
    padding: 1rem;
  }
  .results-grid, .stats-grid {
    grid-template-columns: 1fr; /* single column on mobile */
  }
  .leaderboard-table th:nth-child(4),
  .leaderboard-table td:nth-child(4) {
    display: none; /* hide less critical columns */
  }
}
```

**Step 2 — Add hamburger menu button to all topbars**

In every HTML page, add to the topbar:

```html
<button id="mobile-menu-btn" class="hamburger" aria-label="Ouvrir le menu">
  <span></span><span></span><span></span>
</button>
```

CSS for the hamburger:

```css
.hamburger { display: none; flex-direction: column; gap: 5px; background: none; border: none; cursor: pointer; padding: 4px; }
.hamburger span { width: 22px; height: 2px; background: var(--color-text-primary); display: block; border-radius: 2px; }
```

**Step 3 — Add the sidebar toggle script (add to `dashboard.js` or a shared script)**

```javascript
// Mobile sidebar toggle
const mobileBtn  = document.getElementById('mobile-menu-btn');
const sidebar    = document.querySelector('.sidebar');
const overlay    = document.createElement('div');
overlay.className = 'sidebar-overlay';
document.body.appendChild(overlay);

if (mobileBtn) {
  mobileBtn.addEventListener('click', () => {
    sidebar.classList.toggle('mobile-open');
    overlay.classList.toggle('visible');
  });
  overlay.addEventListener('click', () => {
    sidebar.classList.remove('mobile-open');
    overlay.classList.remove('visible');
  });
}
```

```css
.sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.4); z-index: 99; }
.sidebar-overlay.visible { display: block; }
```

**Step 4 — Verify all `<meta viewport>` tags are present**

Every HTML file must have this in `<head>`:

```html
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
```

---

## 11. PHP Backend + MySQL (Required by Spec)

**What is missing:** The PDF spec explicitly states: *"l'application sera développée en utilisant obligatoirement un langage de programmation côté serveur PHP"* and *"La base de données utilisée sera MySQL"*. The current project has zero backend files.

> This is the largest gap. Per the project timeline, backend development begins **26 May** — if you're approaching that date, this section is your priority.

---

### Steps to implement

**Step 1 — Set up the project folder structure**

```
/backend/
  /api/
    auth.php
    demandes.php
    competences.php
    badges.php
    users.php
    statistiques.php
  /config/
    database.php
  /middleware/
    auth_check.php
    role_check.php
  index.php
```

**Step 2 — Create the database connection file**

```php
<?php
// backend/config/database.php
define('DB_HOST', 'localhost');
define('DB_NAME', 'ismo_skillswap');
define('DB_USER', 'root');
define('DB_PASS', '');

function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    }
    return $pdo;
}
```

**Step 3 — Create the authentication API endpoint**

```php
<?php
// backend/api/auth.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
require_once '../config/database.php';

session_start();

$action = $_GET['action'] ?? '';

if ($action === 'login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $data  = json_decode(file_get_contents('php://input'), true);
    $email = trim($data['email'] ?? '');
    $pass  = $data['password'] ?? '';

    $pdo  = getDB();
    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($pass, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role']    = $user['role'];
        echo json_encode(['success' => true, 'role' => $user['role'], 'nom' => $user['nom']]);
    } else {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Email ou mot de passe incorrect.']);
    }
}

if ($action === 'register' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $pdo  = getDB();

    // Check duplicate email
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
    $stmt->execute([$data['email']]);
    if ($stmt->fetch()) {
        http_response_code(409);
        echo json_encode(['success' => false, 'message' => 'Email déjà utilisé.']);
        exit;
    }

    $hash = password_hash($data['password'], PASSWORD_BCRYPT);
    $stmt = $pdo->prepare('INSERT INTO users (nom, email, filiere, role, password_hash, statut) VALUES (?,?,?,?,?,?)');
    $stmt->execute([$data['nom'], $data['email'], $data['filiere'], $data['role'], $hash, 'en_attente']);
    echo json_encode(['success' => true, 'message' => 'Compte créé. En attente de validation.']);
}
```

**Step 4 — Create the main MySQL tables**

```sql
-- Run this in phpMyAdmin or MySQL CLI
CREATE DATABASE IF NOT EXISTS ismo_skillswap CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ismo_skillswap;

CREATE TABLE users (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    nom           VARCHAR(100) NOT NULL,
    email         VARCHAR(150) NOT NULL UNIQUE,
    filiere       VARCHAR(50),
    role          ENUM('stagiaire','mentor','formateur','admin') DEFAULT 'stagiaire',
    password_hash VARCHAR(255) NOT NULL,
    statut        ENUM('actif','suspendu','en_attente') DEFAULT 'en_attente',
    note_moyenne  DECIMAL(3,2) DEFAULT 0.00,
    points        INT DEFAULT 0,
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE competences (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    nom        VARCHAR(100) NOT NULL,
    categorie  VARCHAR(100),
    niveau_max ENUM('debutant','intermediaire','avance') DEFAULT 'avance',
    actif      TINYINT(1) DEFAULT 1
);

CREATE TABLE user_competences (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    user_id      INT NOT NULL,
    competence_id INT NOT NULL,
    niveau       ENUM('debutant','intermediaire','avance'),
    statut       ENUM('en_attente','validee','refusee') DEFAULT 'en_attente',
    validee_par  INT NULL,
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id)      REFERENCES users(id),
    FOREIGN KEY (competence_id) REFERENCES competences(id)
);

CREATE TABLE demandes (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    auteur_id    INT NOT NULL,
    mentor_id    INT NULL,
    titre        VARCHAR(200) NOT NULL,
    description  TEXT,
    competence   VARCHAR(100),
    urgence      ENUM('normal','urgent') DEFAULT 'normal',
    statut       ENUM('ouverte','en_cours','resolue') DEFAULT 'ouverte',
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (auteur_id) REFERENCES users(id),
    FOREIGN KEY (mentor_id) REFERENCES users(id)
);

CREATE TABLE notations (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    demande_id INT NOT NULL,
    noteur_id  INT NOT NULL,
    mentor_id  INT NOT NULL,
    note       TINYINT NOT NULL CHECK (note BETWEEN 1 AND 5),
    commentaire TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (demande_id) REFERENCES demandes(id),
    FOREIGN KEY (noteur_id)  REFERENCES users(id),
    FOREIGN KEY (mentor_id)  REFERENCES users(id)
);

CREATE TABLE badges (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    nom         VARCHAR(100) NOT NULL,
    description TEXT,
    icone       VARCHAR(50),
    points_requis INT DEFAULT 0
);

CREATE TABLE user_badges (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT NOT NULL,
    badge_id    INT NOT NULL,
    attribue_par INT NULL,
    attribue_le  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id)      REFERENCES users(id),
    FOREIGN KEY (badge_id)     REFERENCES badges(id),
    FOREIGN KEY (attribue_par) REFERENCES users(id)
);
```

**Step 5 — Connect frontend JS to the backend**

Replace all mock `console.log()` calls in JS files with real `fetch()` calls. Example for inscription:

```javascript
// assets/js/inscription.js — replace the TODO block with:
const response = await fetch('../backend/api/auth.php?action=register', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify(data),
});
const result = await response.json();
if (result.success) {
  alert(result.message);
  window.location.href = 'login.html';
} else {
  alert(result.message);
}
```

---

## Quick Summary Table

| # | Missing Feature | Files to Create | Priority |
|---|---|---|---|
| 1 | Registration page (all roles) | `inscription.html`, `inscription.js` | 🔴 High |
| 2 | Skill search engine (OF8) | `recherche.html`, `recherche.js`, `recherche.css` | 🔴 High |
| 3 | Leaderboard / Top Mentors | `classement.html`, `classement.js`, `classement.css` | 🟡 Medium |
| 4 | Mentor rating system (OF3) | Add to `mes_demandes.html` + `mes_demandes.js` | 🔴 High |
| 5 | Notification page for Mentor | `pages_mentor/notification.html` | 🟡 Medium |
| 6 | Settings for Mentor, Formateur, Admin | Copy + adjust `parametres.html` × 3 | 🟡 Medium |
| 7 | `nouvelle_demande.js` missing script | `nouvelle_demande.js` | 🔴 High |
| 8 | Statistics JS (Formateur + Admin) | `statistique.js` | 🟡 Medium |
| 9 | Admin skill catalogue page | `catalogue_admin.html`, `catalogue_admin.js` | 🟢 Low |
| 10 | Responsive / mobile CSS | Media queries in `dashboard.css` + each page | 🔴 High |
| 11 | PHP backend + MySQL | Full `backend/` folder + DB schema | 🔴 Critical |

---

*Document generated from: Cahier des Charges Fonctionnel CC3_Projet_2026_101.pdf vs project_overview.json*