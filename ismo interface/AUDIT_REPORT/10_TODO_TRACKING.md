# SUIVI DU TODO vs RÉALITÉ

## Items TODO résolus

| Item | Statut | Vérification |
|------|--------|-------------|
| ✅ `mes_demandes.js` existe | ✅ Résolu | Le fichier existe et est chargé |
| ✅ Links sidebar mentor | ✅ Stubs redirect existent | `dashboard.html`, `mes_demandes.html`, etc. sont des stubs |

## Items TODO NON résolus

### 🔴 Haute priorité

| Item | Problème |
|------|----------|
| **Navigation Mentor Cassée** | ❌ Toujours cassé — `pages_mentor/mes_aides.html`, `marketplace.html`, `mes_badges.html` ont des liens dropdown vers `profile.html` et `login.html` qui n'existent PAS dans `pages_mentor/`. |
| **Cohérence Admin** | ❌ `statistique_adm.html` existe mais est référencé comme `statisque_adm.html` dans certaines navigations TODO. |

### 🟡 Moyenne priorité

| Item | Problème |
|------|----------|
| **Correction "Tableau"** | ❌ `formateur_pages/tableu_de_bord.html` existe (orthographe correcte). Mais `assets/css/tableu_de_bord.css` est orthographié `tableau_de_bord.css` (correct). Vérifier si le fichier HTML charge le bon CSS. |
| **Orthographe "Stagiaire"** | ❌ Dossier `pages_stagiere` n'a PAS été renommé en `pages_stagiaire`. |
| **Normaliser noms mentor** | ❌ Incohérences persistent entre fichiers. |

### 🟢 Basse priorité

| Item | Problème |
|------|----------|
| **Unification langue** | ❌ Mélange français/anglais toujours présent (fichiers ET chaînes JS). |
| **Remplacer # restants** | ❌ `login.html` a `#password-reset`. |
| **Vérification Responsive** | ⚠️ Partiellement fait. Plusieurs fichiers manquent de breakpoints (voir `06_UI_POLISH.md`). |

---

## Nouveaux problèmes DÉCOUVERTS (pas dans le TODO)

1. ❌ SyntaxError potentielle dans `mentor_apply.js` (apostrophe)
2. ❌ `showToast()` non défini dans 16 fichiers JS
3. ❌ `mes_aides.js` — onglets sans changement de contenu
4. ❌ `marketplace.js` — filtres inopérants
5. ❌ `passeport_pdf.js` — pas de vrai téléchargement
6. ❌ Conflits CSS `:root` tokens (dashboard.css vs login.css)
7. ❌ `catalogue.css` — 40+ couleurs hardcodées
8. ❌ `classement.js` — pas d'activation initiale des tabs
9. ❌ `dashboard.css` — 5 classes dupliquées intra-fichier
10. ❌ 3 implémentations `showToast()` différentes
11. ❌ 20 `console.log` dans le code de prod
12. ❌ `localStorage.setItem('selectedRole')` jamais lu
13. ❌ `confirm()` avec anglais (`parametres.js:49`)
14. ❌ `nouvelle_demande.html` — pas de dropdown profil
15. ❌ `pages_mentor/parametres.html` — liens support/conditions cassés
16. ❌ `formateur_pages/parametres.html` — liens support/conditions cassés
17. ❌ Aucun état focus-visible sur éléments interactifs
18. ❌ Contraste insuffisant sur gray-400, tag-warning, tag-danger
19. ❌ Tailles de police < 12px (`.badge`, `.task-time`, `.mini-role`)
20. ❌ `prefers-reduced-motion` absent de TOUS les fichiers CSS
21. ❌ `prefers-color-scheme: dark` absent (pas de dark mode)
22. ❌ Variables CSS manquantes (cyan, violet, etc.)
23. ❌ XSS potentiel (tableau_de_bord.js innerHTML)
24. ❌ Clipboard API sans fallback (profile.js)
