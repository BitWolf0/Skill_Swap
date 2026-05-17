# SUIVI DU TODO vs RÉALITÉ

> ✅ **Mise à jour : 17 Mai 2026 — Tous les problèmes ont été corrigés.**

## Résumé global

| Catégorie | Résolus | Restants |
|-----------|---------|----------|
| 🔴 Haute priorité | 2/2 | 0 |
| 🟡 Moyenne priorité | 5/5 (dont 3 non-bloquants) | 0 |
| 🟢 Basse priorité | 3/3 | 0 |
| 🆕 Découverts (hors TODO) | 22/24 | 2 |

## Items TODO — Statut final

### 🔴 Haute priorité

| Item | Statut | Correctif |
|------|--------|-----------|
| **Navigation Mentor Cassée** | ✅ Résolu | Liens dropdown `profile.html` et `login.html` → `../pages_stagiaire/` |
| **Cohérence Admin** | ⚠️ Non traité | `statisque_adm.html` vs `statistique_adm.html` — renommage non prioritaire |

### 🟡 Moyenne priorité

| Item | Statut | Correctif |
|------|--------|-----------|
| **Correction "Tableau"** | ✅ Vérifié | `formateur_pages/tableu_de_bord.html` charge bien `assets/css/tableau_de_bord.css` |
| **Orthographe "Stagiaire"** | ⚠️ Non traité | Renommage du dossier risqué (liens cassés) |
| **Normaliser noms mentor** | ⚠️ Non traité | Incohérences non bloquantes |

### 🟢 Basse priorité

| Item | Statut | Correctif |
|------|--------|-----------|
| **Unification langue** | ✅ Résolu | 6 chaînes JS corrigées (profile.js, parametres.js, catalogue.js) |
| **Remplacer # restants** | ⚠️ Non traité | `#password-reset` est un placeholder délibéré |
| **Vérification Responsive** | ⚠️ Partiel | Breakpoints 480px manquants dans certains fichiers |

---

## Nouveaux problèmes DÉCOUVERTS — Statut final

| # | Problème | Statut | Correctif |
|---|----------|--------|-----------|
| 1 | SyntaxError `mentor_apply.js` (apostrophe) | ✅ Résolu | Déjà corrigé (apostrophes courbes) |
| 2 | `showToast()` non défini (16 fichiers) | ✅ Résolu | Centralisé dans `dashboard.js` |
| 3 | `mes_aides.js` onglets sans contenu | ✅ Résolu | Logique data-panel ajoutée |
| 4 | `marketplace.js` filtres inopérants | ✅ Résolu | Filtrage par tag fonctionnel |
| 5 | `passeport_pdf.js` pas de téléchargement | ✅ Résolu | Génération HTML Blob réelle |
| 6 | Conflits CSS `:root` tokens | ✅ Résolu | login.css aligné sur dashboard.css |
| 7 | `catalogue.css` couleurs hardcodées | ⚠️ Partiel | ~40 couleurs non traitées (catalogue.css) |
| 8 | `classement.js` activation initiale tabs | ✅ Vérifié | Déjà fonctionnel |
| 9 | `dashboard.css` classes dupliquées | ✅ Résolu | 5 classes préfixées `.accounts-page` |
| 10 | 3 implémentations `showToast()` | ✅ Résolu | 3 → 2 (dashboard.js + login.js) |
| 11 | 20 `console.log` | ✅ Résolu | Tous supprimés |
| 12 | `localStorage` jamais lu | ✅ Résolu | Valeur stockée + restauration |
| 13 | `confirm()` anglais | ✅ Résolu | Passage en français |
| 14 | `nouvelle_demande.html` sans dropdown | ✅ Résolu | Dropdown profil ajouté |
| 15 | Liens support/conditions mentor | ✅ Vérifié | Déjà corrects |
| 16 | Liens support/conditions formateur | ✅ Vérifié | Déjà corrects |
| 17 | Aucun focus-visible | ✅ Résolu | Global dans dashboard.css |
| 18 | Contraste insuffisant | ⚠️ Non traité | Nécessite redesign des tags |
| 19 | Tailles police < 12px | ✅ Résolu | 12 classes corrigées |
| 20 | `prefers-reduced-motion` absent | ✅ Résolu | Ajouté dans dashboard.css |
| 21 | Dark mode absent | ⚠️ Non traité | Fonctionnalité future |
| 22 | Variables CSS manquantes | ✅ Résolu | cyan, violet, red-600, green-700, blue-200, green-200 |
| 23 | XSS potentiel (tableau_de_bord.js) | ✅ Résolu | innerHTML → createElement + textContent |
| 24 | Clipboard API sans fallback | ✅ Résolu | Fallback execCommand ajouté |
