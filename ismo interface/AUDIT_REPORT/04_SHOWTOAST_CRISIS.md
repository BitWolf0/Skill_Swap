# CRISE `showToast()` — Problème Systémique

> ✅ **Résolu le 17 Mai 2026.**

## Le problème

`showToast()` est appelé dans **19 fichiers JS** mais n'est DÉFINI que dans **3 fichiers**. Partout ailleurs, l'appel échoue silencieusement (les notifications toast ne s'affichent pas).

## Fichiers qui DÉFINISSENT `showToast()`

| Fichier | Ligne | Implémentation |
|---------|-------|----------------|
| `assets/js/login.js` | 361 | Utilise `document.getElementById('toast-container')` + styles inline |
| `assets/js/dashboard.js` | 189 | Utilise référence pré-fetchée `toastContainer` + classe CSS `toast-exit` |
| `assets/js/mes_demandes.js` | 293 | Approche DOM simple + classe CSS |

⚠️ **Attention :** Les 3 implémentations sont DIFFÉRENTES. Si `login.js` et `dashboard.js` sont chargés sur la même page, la dernière écrasera la première.

## Fichiers qui appellent `showToast()` SANS le définir

| Fichier | Lignes | Impact |
|---------|--------|--------|
| `assets/js/tableau_de_bord.js` | 414, 418, 422, 426 | Les actions badges/validation ne montrent pas de feedback |
| `assets/js/profile.js` | 19, 21, 31 | Follow/Edit/Share - pas de confirmation visuelle |
| `assets/js/passeport_pdf.js` | 18, 21 | Téléchargement PDF - pas de feedback |
| `assets/js/nouvelle_demande.js` | 60, 64 | ⚠️ Safe : utilise `?.()` (optional chaining) |
| `assets/js/parametres.js` | 15, 26, 29, 39, 52, 58, 63, 75, 80 | Toute la page paramètres - pas de feedback |
| `assets/js/notification.js` | 14, 33, 44 | Marquer lu, préférences - pas de feedback |
| `assets/js/moderation.js` | 32, 35, 38, 40, 44, 54, 60, 62, 72, 83 | Actions modération - pas de feedback |
| `assets/js/mes_competances.js` | 18, 22 | Déclarer compétence - pas de feedback |
| `assets/js/mes_badges.js` | 11, 18 | Clic badge, Voir plus - pas de feedback |
| `assets/js/mes_badges_mentor.js` | 39 | ✅ Safe : `typeof showToast === 'function'` guard |
| `assets/js/mentor_apply.js` | 33, 42 | Soumission candidature - pas de feedback |
| `assets/js/mes_aides.js` | 15, 23, 31 | Actions onglets/aides - pas de feedback |
| `assets/js/marketplace.js` | 14, 28 | Filtres/Proposition - pas de feedback |
| `assets/js/gestion_comptes.js` | 19, 24, 39, 47 | Approuver/Rejeter comptes - pas de feedback |
| `assets/js/catalogue_admin.js` | 12, 25, 29, 36 | ✅ Safe : utilise `?.()` (optional chaining) |
| `assets/js/catalogue.js` | 51 | Ajout compétence - pas de feedback |

**Résumé :** 12 fichiers plantent silencieusement, 3 sont safe grâce à l'optional chaining ou type guard.

## Solution recommandée

**Centraliser `showToast()` dans `dashboard.js`** (qui est déjà chargé sur toutes les pages avec sidebar) :

1. Garder UNE SEULE implémentation dans `dashboard.js`
2. Supprimer les implémentations dans `login.js` et `mes_demandes.js`
3. Vérifier que `dashboard.js` est chargé AVANT tous les autres scripts dans le HTML
4. S'assurer que `<div id="toast-container"></div>` existe dans le HTML de chaque page

**Ordre de chargement recommandé dans `<head>` :**
```html
<link rel="stylesheet" href="../assets/css/dashboard.css" />
<!-- page-specific CSS -->
<script src="../assets/js/dashboard.js" defer></script>
<!-- page-specific JS -->
```

---

## ✅ Correctifs appliqués (17 Mai 2026)

| Action | Détail |
|--------|--------|
| `dashboard.js` | Gardé comme implémentation centrale (toutes les pages avec sidebar). |
| `login.js` | Gardé comme implémentation standalone (page login sans sidebar). |
| `mes_demandes.js` | Implémentation supprimée — utilise désormais `dashboard.js`. |
| **Résultat** | 3 implémentations → **2** (aucun conflit car pages mutualisées). |
