# QUALITÉ DU CODE JS

## 1. `console.log` à nettoyer

### `tableau_de_bord.js` — 11 logs
```
L.35: 'Section changed to:', section
L.41: 'Loading dashboard'
L.44: 'Loading validation'
L.48: 'Loading statistics'
L.51: 'Loading catalog'
L.78: 'Role changed to:', role
L.124: `Validation ${action} for ${name} - ${skill}`
L.193: 'View all tasks clicked'
L.208: 'Notification icon clicked'
L.363: `[${timestamp}] ${action}`, details
L.437: 'Dashboard initialized successfully'
```

### `mes_demandes.js` — 9 logs
```
L.15: '[mes_demandes.js] Initializing...'
L.26: '[mes_demandes.js] Ready'
L.121: '[mes_demandes.js] View responses for request:', requestId
L.132: '[mes_demandes.js] Edit request:', requestId
L.143: '[mes_demandes.js] Close request:', requestId
L.157: '[mes_demandes.js] Delete request:', requestId
L.263: '[mes_demandes] Submitting rating', payload
L.276: '[mes_demandes] Rating submit failed, simulating success', err
L.316: '[mes_demandes.js] Loading requests from backend...'
```

---

## 2. Fonctions définies mais jamais appelées

| Fichier | Fonction | Ligne |
|---------|----------|-------|
| `mes_demandes.js` | `loadRequests()` | 315 |
| `mes_demandes.js` | `showLoading()` | 323 |
| `mes_demandes.js` | `showEmpty()` | 333 |

Ces 3 fonctions ne sont JAMAIS invoquées. Le TODO commentaire sur `loadRequests()` confirme qu'elle est en attente d'implémentation.

---

## 3. Incohérences de langue (Anglais vs Français)

Le projet est en français mais plusieurs chaînes sont en anglais :

| Fichier | Texte anglais | Corrigé français |
|---------|--------------|------------------|
| `profile.js:18-22` | "Following", "User added to your network", "Follow" | "Abonné", etc. |
| `parametres.js:49` | `action.includes('Delete')` | `action.includes('Supprimer')` |
| `parametres.js:63` | "Your data has been downloaded" | "Données téléchargées" |
| `parametres.js:79` | "You're up to date!" | "Tout est à jour!" |
| `catalogue.js:68` | "Functionality to edit this skill will be implemented soon!" | "Fonctionnalité d'édition à venir" |
| `catalogue.js:70` | "Functionality to view this skill will be implemented soon!" | "Fonctionnalité de vue à venir" |

---

## 4. Dépendances Fragiles

### Ordre de chargement des scripts

Plusieurs fichiers dépendent d'un autre fichier chargé AVANT sans garantie :

```javascript
// moderation.js:92 (comment)
// "Import shared toast function from dashboard.js (assumed to be loaded first)"

// gestion_comptes.js:54 (comment)
// "Import shared toast function from dashboard.js (assumed to be loaded first)"
```

Si l'ordre des `<script>` dans le HTML change, ces pages perdent la fonction toast.

---

## 5. Requêtes DOM au niveau module (hors DOMContentLoaded)

Plusieurs fichiers querySelector au niveau module, ce qui peut retourner `null` si le script s'exécute avant que le DOM soit prêt :

| Fichier | Éléments | Risque |
|---------|----------|--------|
| `recherche.js:5-9` | `.mentor-card`, `.search-input`, etc. | ⚠️ |
| `nouvelle_demande.js:5-12` | `#request-form`, `#mentor-field`, etc. | ⚠️ |
| `mentor_apply.js:8-11` | `.mentor-skills-grid`, `#btn-apply`, etc. | ⚠️ |

**Fix :** Envelopper dans `document.addEventListener('DOMContentLoaded', ...)` ou utiliser l'attribut `defer` sur les `<script>` (qui est déjà utilisé dans la plupart des pages).

---

## 6. localStorage utilisé mais pas lu

```javascript
// tableau_de_bord.js:81
localStorage.setItem('selectedRole', role);
```

`selectedRole` est écrit mais JAMAIS lu ailleurs dans le codebase. L'information est perdue au rechargement.

**À faire :** Soit lire cette valeur à l'initialisation pour restaurer le rôle sélectionné, soit supprimer la ligne.

---

## 7. Système de duplication toast (3 implémentations)

Trois implémentations différentes de `showToast()` existent :

### `login.js` (L.361)
```javascript
function showToast(message, type = 'success') {
    const container = document.getElementById('toast-container');
    // ...styles inline, SVGs hardcodés
}
```

### `dashboard.js` (L.189)
```javascript
function showToast(message, type = 'success', duration = 3000) {
    toastContainer.appendChild(toast);
    // ...utilise classe CSS .toast-exit
}
```

### `mes_demandes.js` (L.293)
```javascript
const showToast = (msg, type = 'success') => {
    // ...classe CSS toast show/hide + setTimeout
};
```

**Risque :** Si les 2 premiers fichiers sont chargés sur la même page, la dernière implémentation écrase la précédente. Le comportement change selon l'ordre de chargement.

---

## 8. Fichier `mes_competances.js` — Delay avec double toast confus

```javascript
showToast('Enregistrement de la compétence...', 'info');
setTimeout(() => {
    showToast('Fonctionnalité à venir : Ajout de compétence', 'warning');
}, 1500);
```

**Problème UX :** Un toast "info" suivi d'un toast "warning" 1.5s plus tard crée de la confusion. L'utilisateur pense d'abord que ça fonctionne, puis apprend que non.
