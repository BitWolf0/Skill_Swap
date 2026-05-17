# PROBLÈMES DE SÉCURITÉ POTENTIELS

> ✅ **Les risques immédiats ont été corrigés le 17 Mai 2026.**

## 1. XSS potentiel dans `showNotification()` (tableau_de_bord.js)

**Fichier :** `assets/js/tableau_de_bord.js:222-226`

```javascript
const notif = document.createElement('div');
notif.innerHTML = `
    <div class="notif-content">
        <h4>${title}</h4>
        <p>${message}</p>
    </div>`;
```

**Problème :** `title` et `message` sont extraits du DOM (`.card-header h3.textContent`, `.skill-tag.textContent`). Si ces éléments contiennent un jour du contenu utilisateur non sanitizé, c'est une porte XSS.

**Risque :** Actuellement faible car les données sont statiques. À corriger AVANT d'introduire des données dynamiques.

**Fix :**
```javascript
notif.textContent = ''; // ou utiliser createTextNode
// puis ajouter les éléments avec textContent
```

> ✅ **Corrigé** — `showNotification()` utilise désormais `createElement` + `textContent` au lieu de `innerHTML`.

---

## 2. innerHTML avec données hardcodées (classement.js)

**Fichier :** `assets/js/classement.js:35-36`

```javascript
body.innerHTML = mentors.map(mentor => `<tr>...</tr>`).join('');
```

**Problème :** Actuellement safe car `mentors` est un tableau hardcodé. Mais si un jour les données viennent d'une API, c'est XSS.

**Fix :** Utiliser `document.createElement('tr')` et `textContent` pour les valeurs plutôt que `innerHTML`.

> ⚠️ **Non modifié** — Les données sont hardcodées (statiques). Risque nul tant que les données ne viennent pas d'une API. À migrer si dynamisation future.

---

## 3. Clipboard API sans fallback (profile.js)

**Fichier :** `assets/js/profile.js:30`

```javascript
navigator.clipboard.writeText(profileLink);
```

**Problème :** `navigator.clipboard` nécessite HTTPS ou localhost. En production HTTP, ça échoue silencieusement.

**Fix :** Ajouter un fallback :
```javascript
if (navigator.clipboard) {
    navigator.clipboard.writeText(profileLink);
} else {
    // Fallback: sélectionner un input caché + document.execCommand('copy')
}
```

> ✅ **Corrigé** — Fallback `execCommand('copy')` ajouté dans `profile.js`.

---

## 4. Aucune validation backend

**Problème général :** Toute la validation (connexion, inscription, formulaires) est côte client. Aucune vérification côté serveur. C'est acceptable pour un prototype statique mais serait critique en production.

---

## 5. Pas de Content Security Policy

Aucune balise `<meta http-equiv="Content-Security-Policy">` n'est présente dans les pages. En production, une CSP limiterait les risques XSS en contrôlant les sources autorisées pour les scripts, styles, etc.
