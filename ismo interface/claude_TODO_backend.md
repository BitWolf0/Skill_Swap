# ISMO-SkillSwap — Backend TODO (Based on Repo Audit)
> PHP + MySQL — Required by spec. Zero backend files currently exist in the repo.

---

## 🔵 1. Project Folder Structure

- [ ] Create `/backend/config/database.php` — PDO connection singleton with error handling
- [ ] Create `/backend/middleware/auth_check.php` — redirect to login if no session
- [ ] Create `/backend/middleware/role_check.php` — block page access if role not allowed

---

## 🔵 2. Database Setup

- [ ] Run `ismo_skillswap.sql` (already in repo) to create base tables
- [ ] Verify all required tables exist: `users`, `competences`, `user_competences`, `demandes`, `offres`, `notations`, `badges`, `user_badges`
- [ ] Confirm required columns exist: `users.points`, `users.note_moyenne`, `users.statut`, `demandes.statut`

---

## 🔵 3. Auth API — `/backend/api/auth.php`

- [ ] `POST ?action=login` — verify email + `password_verify()`, start session, return role
- [ ] `POST ?action=register` — insert user with `password_hash()`, status = `en_attente`
- [ ] `GET ?action=logout` — `session_destroy()`, redirect to login
- [ ] `GET ?action=me` — return current session user data as JSON

---

## 🔵 4. Users API — `/backend/api/users.php`

- [ ] `GET` — list all users (admin only)
- [ ] `PUT ?id=X` — update own profile (or admin updates any)
- [ ] `PATCH ?action=validate&id=X` — admin activates account
- [ ] `PATCH ?action=suspend&id=X` — admin suspends account

---

## 🔵 5. Skills API — `/backend/api/competences.php`

- [ ] `GET` — return full skills catalogue
- [ ] `POST` — stagiaire/mentor declares a skill (status = `en_attente`)
- [ ] `PATCH ?action=validate&id=X` — formateur/admin validates a skill
- [ ] `PATCH ?action=refuse&id=X` — formateur/admin refuses a skill
- [ ] `POST ?action=catalogue` — admin adds a new skill to catalogue
- [ ] `DELETE ?id=X` — admin removes skill from catalogue

---

## 🔵 6. Help Requests API — `/backend/api/demandes.php`

- [ ] `GET` — list requests with optional filters (skill, status, filière)
- [ ] `POST` — create a new help request
- [ ] `PUT ?id=X` — edit own request
- [ ] `DELETE ?id=X` — delete request (own, or admin/formateur)
- [ ] `PATCH ?action=offer&id=X` — mentor applies to help
- [ ] `PATCH ?action=accept&id=X&mentor_id=Y` — request author accepts a mentor
- [ ] `PATCH ?action=resolve&id=X` — mark request as resolved, trigger points award

---

## 🔵 7. Ratings API — `/backend/api/notations.php`

- [ ] `POST` — submit a rating (note 1–5 + commentaire, tied to `demande_id`)
- [ ] `GET ?mentor_id=X` — get all ratings for a mentor
- [ ] After every insert: recalculate and update `users.note_moyenne`

---

## 🔵 8. Badges & Gamification API — `/backend/api/badges.php`

- [ ] `GET` — list all badge definitions
- [ ] `POST ?action=award` — formateur/admin manually awards a badge to a user
- [ ] `DELETE ?user_badge_id=X` — remove a badge from a user
- [ ] Auto-badge logic: after each resolved demande, check thresholds and award automatically (e.g. 5 helps → "Helper" badge)
- [ ] Points system: add points to `users.points` after resolve and after receiving a rating

---

## 🔵 9. Statistics API — `/backend/api/statistiques.php`

- [ ] `GET ?role=formateur` — return: total demandes, résolues, compétences validées, avg rating, top skills
- [ ] `GET ?role=admin` — same + total users, active mentors, recent registrations

---

## 🔵 10. Connect Frontend JS to Backend

- [ ] Replace all `console.log()` mock submissions in JS files with real `fetch()` POST calls
- [ ] Replace all hardcoded mock data arrays in JS files with `fetch()` GET calls to the API
- [ ] Add loading state (spinner / disabled button) during every fetch call
- [ ] Handle API errors: display user-friendly inline messages on failure (not `alert()`)

---

## Summary

| Endpoint file | Methods | Roles |
|---|---|---|
| `auth.php` | login, register, logout, me | All |
| `users.php` | list, update, validate, suspend | Admin, self |
| `competences.php` | list, declare, validate, refuse, catalogue CRUD | All, Formateur, Admin |
| `demandes.php` | list, create, edit, delete, offer, accept, resolve | All |
| `notations.php` | submit, list by mentor | Stagiaire, Mentor |
| `badges.php` | list, award, remove, auto-award | Formateur, Admin, system |
| `statistiques.php` | stats by role | Formateur, Admin |

> **Start here:** `database.php` → `auth.php` → `users.php` → everything else depends on users being authenticated.
