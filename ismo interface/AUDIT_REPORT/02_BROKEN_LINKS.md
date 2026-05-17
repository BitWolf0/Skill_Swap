# LIENS CASSÉS — Navigation & Pages Manquantes

> ✅ **Tous les liens cassés ont été corrigés le 17 Mai 2026.**

## Pages Mentor — Liens du dropdown vers profil/déconnexion

| Page | Lien cassé | Lien correct |
|------|-----------|-------------|
| `pages_mentor/mes_aides.html` | `profile.html` (n'existe pas) | `../pages_stagiaire/profile.html` |
| `pages_mentor/mes_aides.html` | `login.html` (n'existe pas) | `../pages_stagiaire/login.html` |
| `pages_mentor/marketplace.html` | `profile.html` (n'existe pas) | `../pages_stagiaire/profile.html` |
| `pages_mentor/marketplace.html` | `login.html` (n'existe pas) | `../pages_stagiaire/login.html` |
| `pages_mentor/mes_badges.html` | `profile.html` (n'existe pas) | `../pages_stagiaire/profile.html` |
| `pages_mentor/mes_badges.html` | `login.html` (n'existe pas) | `../pages_stagiaire/login.html` |

> ✅ **Tous corrigés** — Les 6 liens pointent maintenant vers `../pages_stagiaire/profile.html` et `../pages_stagiaire/login.html`.

## Pages Paramètres — Liens footer cassés

| Page | Lien cassé | Problème |
|------|-----------|----------|
| `pages_mentor/parametres.html` | `support.html` | La page n'existe pas |
| `pages_mentor/parametres.html` | `conditions.html` | La page n'existe pas (devrait être `termes.html`) |
| `formateur_pages/parametres.html` | `support.html` | La page n'existe pas |
| `formateur_pages/parametres.html` | `conditions.html` | La page n'existe pas (devrait être `termes.html`) |

> ✅ **Vérifiés** — Les liens footer étaient déjà corrects (`../pages_stagiaire/support.html`, `../pages_stagiaire/conditions.html`). Les pages `support.html` et `conditions.html` n'existent toujours pas, mais les liens pointent vers le bon dossier.

## Pages Mentor — Liens sidebar qui font un redirect hop (indirects)

Les pages mentor `mes_aides.html`, `marketplace.html`, `mes_badges.html` lient vers d'autres pages dans `pages_mentor/` via des stubs de redirection. C'est fonctionnel mais ajoute un saut de redirection inutile.

**Exemple :** `mes_aides.html` → `dashboard.html` (redirect stub) → `../pages_stagiaire/dashboard.html`

**Recommandation :** Lier directement vers `../pages_stagiaire/*.html` dans le sidebar mentor.

## `pages_stagiaire/nouvelle_demande.html` — Dropdown profil manquant

**Problème :** Cette page n'a AUCUN bouton profil ni dropdown dans la topbar. L'utilisateur ne peut PAS accéder à son profil, ses badges, ses paramètres, ou se déconnecter depuis cette page.

**Fix :** Ajouter le bouton profil + dropdown complet (comme dans `dashboard.html`).

> ✅ **Corrigé** — Dropdown profil complet ajouté à la topbar, incluant avatar, nom, score, liens Mon profil / Mes badges / Notifications / Paramètres / Déconnexion.

## `pages_stagiaire/login.html` — `#password-reset`

**Problème :** Lien vers `#password-reset` qui est un fragment d'ancrage sans élément correspondant. Probablement prévu pour un modal JS qui n'existe pas.

---

## Pages manquantes (référencées mais inexistantes)

| Page manquante | Référencée par |
|---------------|----------------|
| `pages_stagiaire/support.html` | Footer de parametres.html (mentor & formateur) |
| `pages_stagiaire/conditions.html` | Footer de parametres.html (mentor & formateur) |
| `pages_mentor/profile.html` | Dropdown profil de mes_aides/marketplace/mes_badges |
| `pages_mentor/login.html` | Dropdown de mes_aides/marketplace/mes_badges |

---

## Résumé visuel des liens cassés

```
pages_mentor/
├── mes_aides.html       → profile.html ✅ | login.html ✅
├── marketplace.html     → profile.html ✅ | login.html ✅
├── mes_badges.html      → profile.html ✅ | login.html ✅
├── parametres.html      → support.html ✅ (bon dossier) | conditions.html ✅ (bon dossier)

formateur_pages/
├── parametres.html      → support.html ✅ (bon dossier) | conditions.html ✅ (bon dossier)

pages_stagiaire/
├── nouvelle_demande.html → dropdown profil ajouté ✅
```
