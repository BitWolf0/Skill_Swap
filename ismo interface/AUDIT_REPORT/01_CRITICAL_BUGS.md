# CRITICAL BUGS — Bloquant / Casse le fonctionnement

## 1. SyntaxError potentielle dans `mentor_apply.js`

**Fichier :** `assets/js/mentor_apply.js:40`

```javascript
: 'Candidature envoyée avec succès ! N'oubliez pas d'ajouter une motivation pour augmenter vos chances.';
```

**Problème :** L'apostrophe dans `N'oubliez` ET `d'ajouter` peut être interprétée comme un délimiteur de chaîne si ce sont des guillemets simples ASCII (U+0027). Cela casse TOUTE la page mentor_apply — le script ne s'exécute pas du tout, empêchant la soumission du formulaire.

**Fix :** Remplacer les apostrophes par le caractère Unicode curly apostrophe (') ou échapper avec `\'`.

**Correctif immédiat :**
```javascript
: 'Candidature envoyée avec succès ! N\u2019oubliez pas d\u2019ajouter une motivation pour augmenter vos chances.';
```
Ou utiliser des guillemets anglais (") pour la chaîne externe.

---

## 2. `showToast()` non défini dans 16 fichiers JS

**Voir le rapport dédié :** `04_SHOWTOAST_CRISIS.md`

Toutes les notifications toast (« Fonctionnalité à venir », confirmations, erreurs) plantent silencieusement sur la plupart des pages. Seules les pages qui chargent `login.js`, `dashboard.js` ou `mes_demandes.js` ont des toasts fonctionnels.

---

## 3. Tab without content switching in `mes_aides.js`

**Fichier :** `assets/js/mes_aides.js:13-16`

```javascript
tabBtn.addEventListener('click', function () {
    document.querySelector('.tab-btn.active')?.classList.remove('active');
    this.classList.add('active');
    showToast('Changement d\'onglet...');
});
```

**Problème :** Le clic sur un onglet change la classe `active` visuelle mais n'affiche JAMAIS le panneau de contenu correspondant. Les onglets sont purement cosmétiques — aucun contenu n'est caché/montré.

**Fix :** Ajouter la logique pour cacher tous les `data-panel` et montrer celui correspondant au `data-tab` cliqué.

---

## 4. `marketplace.js` — Les filtres ne filtrent rien

**Fichier :** `assets/js/marketplace.js:11-15`

```javascript
filterBtns.forEach(btn => {
    btn.addEventListener('click', function () {
        document.querySelector('.filter-btn.active')?.classList.remove('active');
        this.classList.add('active');
        showToast('Filtre changé');
    });
});
```

**Problème :** Les boutons de filtre changent de classe active mais aucun tri/filtrage des cartes de marché n'est implémenté. Même problème que les onglets de `mes_aides.js`.

---

## 5. `passeport_pdf.js` — Aucun téléchargement réel

**Fichier :** `assets/js/passeport_pdf.js:15-22`

```javascript
btnDownload?.addEventListener('click', () => {
    showToast?.('Génération du Passeport PDF en cours…', 'info');
    setTimeout(() => {
        showToast?.('Passeport téléchargé avec succès ! ✅', 'success');
    }, 2000);
});
```

**Problème :** Aucun PDF n'est généré ni téléchargé. C'est un timeout avec un toast. La fonctionnalité principale de la page est absente.

---

## 6. `mes_demandes.js` — Appel API qui échoue toujours

**Fichier :** `assets/js/mes_demandes.js:267-277`

```javascript
const response = await fetch('/api/ratings', { ... });
```

**Problème :** C'est le SEUL appel `fetch()` de tout le projet. Comme c'est un frontend statique sans backend, cette requête échoue TOUJOURS. Le catch handler gère élégamment en mode simulation, mais cette ligne donne l'impression qu'un backend existe.

**Fix :** Soit supprimer complètement (laisser la simulation), soit commenter avec une TODO pour quand le backend sera prêt.

---

## 7. `dashboard.css` — Conflits intra-fichier

**Fichier :** `assets/css/dashboard.css`

Les classes suivantes sont DÉFINIES DEUX FOIS dans le même fichier avec des valeurs différentes :

| Classe | 1ère définition | 2ème définition |
|--------|----------------|-----------------|
| `.badge` | L.726 | L.2220 |
| `.quick-stats` | L.793 (3 colonnes) | L.1912 (4 colonnes) |
| `.qstat-card` | L.800 (padding 16px) | L.1918 (padding 12px) |
| `.page-title` | L.611 (1.45rem) | L.1870 (1.75rem) |
| `.page-sub` | L.619 | L.1876 |

**Problème :** La 2ème définition écrase la 1ère (ordre source). Les sections "admin accounts" et "main dashboard" ont des styles différents pour les mêmes classes. Cela peut casser le layout des pages qui utilisent ces classes.

---

## 8. `tableau_de_bord.js` — `handleBadgeAction()` appelle `showToast()` non défini

**Fichier :** `assets/js/tableau_de_bord.js:411-428`

```javascript
function handleBadgeAction(action) {
    showToast('Fonctionnalité à venir : Création de badge', 'info'); // showToast NON défini
}
```

**Problème :** Cette fonction est auto-initialisée dans son propre DOMContentLoaded. Elle dépend de `dashboard.js` chargé AVANT. Si l'ordre de chargement des scripts change, les actions de badge plantent.
