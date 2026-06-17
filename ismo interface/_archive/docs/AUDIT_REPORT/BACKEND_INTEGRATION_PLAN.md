# BACKEND INTEGRATION PLAN — ISMO-SkillSwap

> Basé sur CC3_Projet_2026_101.pdf + audit complet du frontend statique

---

## 1. CE QUE LE CC3 IMPOSE

| Exigence | Détail |
|---|---|
| **Langage serveur** | PHP (obligatoire — pas de Node/Python) |
| **Base de données** | MySQL |
| **Frontend** | HTML/CSS/JS existant (ou React/Vue/Angular — mais le tien est déjà fait) |
| **Sécurité** | Hachage mots de passe, anti-SQL-injection, anti-XSS, HTTPS |
| **Responsive** | Déjà fait (dashboard.css media queries) |

---

## 2. SCHÉMA DB À UTILISER

**Utilise `ismo_skillswap_v3.sql`** — c'est la version consolidée (10 tables + 3 vues).

Tables clés :
- `users` — profils + rôles (stagiaire/mentor/formateur/admin) + suspension
- `skills` — catalogue de compétences
- `user_skills` — compétences déclarées, avec `validation_status` (pending/verified/rejected)
- `mentor_applications` — demandes pour devenir mentor
- `skill_posts` — remplace `help_requests` + `marketplace_listings` (post_type: request/offer)
- `skill_responses` — remplace `help_responses`, `mentor_relationships`, sessions, interactions
- `badges` + `user_badges` — gamification
- `notifications` — système de notification
- `service_cases` + `case_replies` — support + modération
- Vues : `vw_user_dashboard`, `vw_platform_statistics`, `vw_moderation_queue`

---

## 3. ARCHITECTURE BACKEND RECOMMANDÉE

```
/ismo-skillswap/
├── index.php                 # Entry point (routeur frontal)
├── .htaccess                 # Réécriture d'URL + sécurité
├── config/
│   └── database.php          # Connexion PDO MySQL
├── routes/
│   └── api.php               # Définition des routes REST
├── controllers/
│   ├── AuthController.php    # Login, register, logout
│   ├── UserController.php    # Profil, paramètres
│   ├── SkillController.php   # CRUD compétences
│   ├── PostController.php    # Demandes + offres
│   ├── ResponseController.php# Réponses aux posts
│   ├── BadgeController.php   # Badges
│   ├── NotificationController.php
│   ├── AdminController.php   # Modération, statistiques
│   ├── SearchController.php  # Recherche + filtres
│   └── PassportController.php# PDF passeport
├── models/
│   ├── User.php
│   ├── Skill.php
│   ├── UserSkill.php
│   ├── SkillPost.php
│   ├── SkillResponse.php
│   ├── Badge.php
│   ├── UserBadge.php
│   ├── Notification.php
│   └── ServiceCase.php
├── middleware/
│   ├── AuthMiddleware.php    # Vérifie session JWT/token
│   ├── RoleMiddleware.php    # Vérifie rôle (stagiaire/mentor/...)
│   └── CsrfMiddleware.php    # Protection CSRF
├── helpers/
│   ├── JwtHelper.php         # Génération/validation JWT
│   ├── Response.php          # JSON response helper
│   ├── Validator.php         # Validation des entrées
│   └── PdfGenerator.php      # Génération PDF (mpdf/tcpdf)
├── uploads/                  # Avatars, fichiers
├── vendor/                   # Dépendances Composer (mpdf, phpdotenv, etc.)
└── public/                   # Point d'entrée web (optionnel)
```

---

## 4. API REST — ENDPOINTS À CRÉER

### Authentification
| Méthode | Endpoint | Description |
|---|---|---|
| POST | `/api/auth/register` | Inscription |
| POST | `/api/auth/login` | Connexion → retourne JWT |
| POST | `/api/auth/logout` | Déconnexion |
| GET | `/api/auth/me` | Profil utilisateur connecté |

### Utilisateurs
| GET | `/api/users/{id}` | Profil public |
|---|---|---|
| PUT | `/api/users/{id}` | Modifier profil |
| POST | `/api/users/{id}/avatar` | Upload avatar |

### Compétences
| GET | `/api/skills` | Catalogue (filtré) |
|---|---|---|
| POST | `/api/skills` | Admin crée une compétence |
| PUT | `/api/skills/{id}` | Admin modifie |
| GET | `/api/users/{id}/skills` | Compétences d'un user |
| POST | `/api/users/{id}/skills` | Déclarer compétence |
| PUT | `/api/user-skills/{id}` | Modifier niveau |
| PUT | `/api/user-skills/{id}/validate` | Formateur valide/refuse |

### Demandes & Offres (skill_posts)
| GET | `/api/posts` | Liste (filtres: type, skill, status, urgent) |
|---|---|---|
| POST | `/api/posts` | Créer demande/offre |
| PUT | `/api/posts/{id}` | Modifier (auteur seulement) |
| DELETE | `/api/posts/{id}` | Supprimer (auteur ou admin) |
| PUT | `/api/posts/{id}/resolve` | Marquer comme résolu |

### Réponses (skill_responses)
| GET | `/api/posts/{id}/responses` | Voir réponses à un post |
|---|---|---|
| POST | `/api/posts/{id}/responses` | Proposer son aide |
| PUT | `/api/responses/{id}` | Accepter/refuser |
| PUT | `/api/responses/{id}/rate` | Noter le mentor |

### Badges
| GET | `/api/badges` | Liste des badges |
|---|---|---|
| GET | `/api/users/{id}/badges` | Badges d'un user |
| POST | `/api/users/{id}/badges` | Admin attribue un badge |

### Mentor
| POST | `/api/mentor/apply` | Candidature mentor |
|---|---|---|
| GET | `/api/mentor/applications` | Liste (admin/formateur) |
| PUT | `/api/mentor/applications/{id}` | Approuver/refuser |

### Recherche
| GET | `/api/search?q=...&level=...&role=...` | Recherche unifiée |

### Notifications
| GET | `/api/notifications` | Liste notifications |
|---|---|---|
| PUT | `/api/notifications/{id}/read` | Marquer comme lue |

### Admin
| GET | `/api/admin/stats` | Statistiques plateforme |
|---|---|---|
| GET | `/api/admin/users` | Gestion comptes |
| PUT | `/api/admin/users/{id}/suspend` | Suspendre |
| GET | `/api/admin/moderation` | File modération |
| PUT | `/api/admin/moderation/{id}` | Action modération |

### Passeport PDF
| GET | `/api/passport/{id}/pdf` | Générer PDF dynamique |

---

## 5. CE QUI DOIT CHANGER DANS LE FRONTEND

### 5.1 Remplacer les données statiques

Chaque page HTML contient des données en dur. Exemples :

| Fichier | Donnée statique → API |
|---|---|
| `dashboard.html` (tous rôles) | Nom user, stats, demandes récentes, badge count |
| `mes_demandes.html` | Liste des demandes → `GET /api/posts?owner_id=X` |
| `mes_competances.html` | Compétences → `GET /api/users/X/skills` |
| `mes_badges.html` | Badges → `GET /api/users/X/badges` |
| `marketplace.html` | Offres → `GET /api/posts?type=offer` |
| `notification.html` | Notifications → `GET /api/notifications` |
| `classement.html` | Classement → `GET /api/users?sort=reputation` |
| `profile.html` | Profil → `GET /api/users/X` |
| `recherche.html` | Résultats → `GET /api/search?q=...` |
| `validation_demande.html` | Demandes à valider → `GET /api/user-skills?status=pending` |
| `gestion_comptes.html` | Tous users → `GET /api/admin/users` |
| `moderation.html` | Signalements → `GET /api/admin/moderation` |
| `statistiques_admin.html` | Stats → `GET /api/admin/stats` |

### 5.2 Remplacer les simulations dans les JS

| Fichier JS | Simulation → Action réelle |
|---|---|
| `dashboard.js` | Toast "Navigation : X" → `fetch()` + navigation réelle |
| `login.js` | `await delay(1600)` → `POST /api/auth/login` avec JWT |
| `mes_demandes.js` | `TODO: Implement backend call` → vraies requêtes fetch |
| `marketplace.js` | `setTimeout` simulation → `POST /api/posts/{id}/responses` |
| `nouvelle_demande.js` | `showToast` + redirect → `POST /api/posts` + redirect |
| `profile.js` | Boutons statiques → `PUT /api/users/{id}` |
| `search-modal.js` | `mockSearch()` → `GET /api/search?q=...` |
| `mentor_apply.js` | Simulation → `POST /api/mentor/apply` |
| `mes_competances.js` | Simulation → CRUD via `fetch()` |
| `validation_demande.js` | Simulation → `PUT /api/user-skills/{id}/validate` |
| `passeport_pdf.js` | Toast "Génération du PDF" → `GET /api/passport/{id}/pdf` |
| `moderation.js` | Simulation → `PUT /api/admin/moderation/{id}` |
| `gestion_comptes.js` | Simulation → `GET/PUT /api/admin/users` |

### 5.3 Ajouter la gestion de session

- Stocker le JWT dans `localStorage` ou `sessionStorage` après login
- Ajouter un `Authorization: Bearer <token>` header sur chaque fetch
- Vérifier le token au chargement de chaque page
- Rediriger vers `login.html` si token invalide/expiré
- Afficher le rôle et le nom de l'utilisateur depuis le token JWT décodé

### 5.4 Modèle de fonction fetch à utiliser partout

```js
async function api(endpoint, options = {}) {
  const token = localStorage.getItem('token');
  const headers = {
    'Content-Type': 'application/json',
    ...(token && { 'Authorization': `Bearer ${token}` }),
    ...options.headers,
  };
  const res = await fetch(endpoint, { ...options, headers });
  if (!res.ok) {
    if (res.status === 401) { localStorage.removeItem('token'); window.location.href = 'login.html'; }
    throw new Error(await res.text());
  }
  return res.json();
}
```

---

## 6. ORDRE DE PRIORITÉ RECOMMANDÉ

### Phase 1 — Fondation (3 jours)
1. Config MySQL + `config/database.php` (PDO)
2. Routeur PHP + `.htaccess`
3. `AuthController` — inscription / connexion / JWT
4. Middleware auth + rôles
5. Modifier `login.js` + `inscription.js` pour appeler l'API réelle
6. Stocker JWT, afficher nom/rôle depuis le token

### Phase 2 — Cœur fonctionnel (1 semaine)
7. `SkillController` + `UserSkillController` (catalogue + déclaration)
8. `PostController` — créer/lister/modifier demandes et offres
9. `ResponseController` — proposer aide, accepter, noter
10. Connecter `mes_demandes.js`, `nouvelle_demande.js`, `marketplace.js`
11. Connecter `mes_competances.js`, `profile.js`

### Phase 3 — Valorisation (3-4 jours)
12. BadgeController — attribution et listing
13. NotificationController — système de notifs
14. Passport PDF — génération avec mpdf/tcpdf
15. Connecter badges, notifications, passeport

### Phase 4 — Admin & Recherche (3 jours)
16. AdminController — stats, users, modération
17. SearchController — recherche plein texte avec filtres
18. Connecter `gestion_comptes.js`, `moderation.js`, `statistiques_admin.js`, `recherche.js`

### Phase 5 — Finition (2 jours)
19. MentorApplicationController — workflow mentor
20. Améliorations sécurité (CSRF, rate limiting, validation)
21. Tests fonctionnels
22. Rapport PDF + diaporama

---

## 7. SÉCURITÉ (EXIGENCE CC3)

| Mesure | Implémentation |
|---|---|
| Hachage mots de passe | `password_hash($pw, PASSWORD_BCRYPT)` |
| Anti-SQL injection | PDO avec prepared statements (déjà prévu) |
| Anti-XSS | `htmlspecialchars($str, ENT_QUOTES, 'UTF-8')` dans toutes les vues |
| JWT | Stocké en localStorage + vérifié à chaque requête |
| CSRF | Token CSRF dans chaque formulaire sensible |
| Rate limiting | Sur endpoints login/register |
| Validation entrées | `Validator.php` — sanitize + validate serveur |
| HTTPS | Forcer en production via `.htaccess` |

---

## 8. COMPOSANTES FRONTEND QUI NÉCESSITENT PEU DE CHANGEMENT

Ces pages sont déjà bien structurées avec des `data-*` attributes et des classes CSS claires :
- `dashboard.html` — juste remplacer les valeurs statiques par des appels API
- `mes_demandes.html` — déjà pré-rempli de `data-request-id` et `data-action`
- `notifications.html` — structure DOM propice au rendu dynamique
- `parametres.html` — formulaire à connecter à `PUT /api/users/{id}`
- `login.html` / `inscription.html` — déjà presque prêts, juste changer la soumission

---

## 9. DÉPENDANCES COMPOSER RECOMMANDÉES

```json
{
  "require": {
    "firebase/php-jwt": "^6.0",
    "mpdf/mpdf": "^8.0",
    "vlucas/phpdotenv": "^5.0",
    "ramsey/uuid": "^4.0"
  }
}
```

---

## 10. SCHÉMA DE LA SESSION ULTILISATEUR (JWT PAYLOAD)

```json
{
  "sub": 1,
  "username": "sophie.martin",
  "email": "sophie@example.com",
  "role": "stagiaire",
  "iat": 1717000000,
  "exp": 1717086400
}
```

Permet d'afficher les infos utilisateur sans requête DB à chaque page.
