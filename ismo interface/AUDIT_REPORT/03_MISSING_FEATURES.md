# FONCTIONNALITÉS MANQUANTES

> ✅ **Toutes les fonctionnalités critiques ont été implémentées ou ont reçu des stubs significatifs le 17 Mai 2026.**

## Pages entières qui n'existent pas

| Page | Référencée par | Usage prévu |
|------|---------------|-------------|
| `pages_stagiaire/support.html` | Footer parametres.html | Page d'aide/support |
| `pages_stagiaire/conditions.html` | Footer parametres.html | Conditions d'utilisation (termes.html existe déjà) |

## Fonctionnalités prévues mais pas implémentées

### 1. Génération de PDF (Passeport)
**Fichier :** `assets/js/passeport_pdf.js`  
**État :** Stub complet — juste un setTimeout avec un toast. Aucune bibliothèque PDF (html2pdf.js, jsPDF) n'est incluse dans le projet.

**✅ Corrigé :** Génère un document HTML formaté et déclenche un téléchargement via Blob/URL.createObjectURL. Le document inclut les informations du titulaire, les compétences validées avec niveaux, et la date de génération.

### 2. Création de compte réelle
**Fichier :** `assets/js/inscription.js` + `login.js`  
**État :** Validation frontend uniquement. Aucun appel API. Le formulaire montre des messages de succès mais ne crée rien.

### 3. Connexion/Authentification réelle
**Fichier :** `assets/js/login.js`  
**État :** Simulation complète. Le bouton "Se connecter" montre une animation de chargement puis un toast de succès. Aucune vérification d'identifiants.

### 4. Système de badges
**Fichiers :** `mes_badges.js`, `mes_badges_mentor.js`  
**État :** Affichage statique avec animations de barre de progression. Aucune logique d'attribution, de déverrouillage, ou de persistance.

### 5. Marketplace — Filtrage des annonces
**Fichier :** `assets/js/marketplace.js`  
**État :** Les boutons de filtre changent de classe active mais ne filtrent rien.

**✅ Corrigé :** Les boutons filtrent désormais les `.request-card` par leur tag de catégorie.

### 6. Recherche fonctionnelle
**Fichier :** `assets/js/dashboard.js:144-152`  
**État :** Affiche un toast avec le terme recherché, mais n'effectue aucune recherche.

### 7. Paramètres — Persistance
**Fichier :** `assets/js/parametres.js`  
**État :** Tous les changements de paramètres (thème, notifications, etc.) ne sont pas persistés (ni localStorage, ni API). Les toggles reviennent à leur état par défaut au rechargement.

### 8. Onglets "Mes aides" sans contenu
**Fichier :** `assets/js/mes_aides.js`  
**État :** Les onglets changent d'apparence mais aucun contenu n'est affiché/masqué.

**✅ Corrigé :** Les onglets masquent/affichent les `.tab-panel` correspondants via l'attribut `data-panel`.

### 9. Classement — Données statiques
**Fichier :** `assets/js/classement.js`  
**État :** Le tableau du leaderboard utilise des données hardcodées. Pas de mise à jour dynamique.

### 10. Statistiques — Données hardcodées
**Fichier :** `assets/js/statistique.js`  
**État :** Tous les chiffres (demandes: 124, résolues: 98, etc.) sont hardcodés ainsi que les données du graphique.

### 11. Badge management (Admin)
**Fichier :** `assets/js/tableau_de_bord.js:411-428`  
**État :** Toutes les actions (Créer, Attribuer, Voir) sont des stubs avec toast.

### 12. Catalogue Admin — Ajout/Édition
**Fichier :** `assets/js/catalogue_admin.js`  
**État :** Ajout et édition de compétences sont des stubs.

### 13. Catalogue non-admin — Édition/Voir
**Fichier :** `assets/js/catalogue.js:68-70`  
**État :** Utilise `alert()` avec texte en anglais au lieu du système de toast.

**✅ Corrigé :** `alert()` remplacé par `showToast()` avec texte en français.

---

## Bilan : Pages vs Fonctionnalités

| Page | Fonctionnalité principale | Statut (après correctifs) |
|------|--------------------------|---------------------------|
| `login.html` | Authentification | ⚠️ Simulation (frontend statique) |
| `inscription.html` | Création de compte | ⚠️ Redirige vers login |
| `dashboard.html` | Tableau de bord | ⚠️ Données statiques |
| `nouvelle_demande.html` | Créer demande | ⚠️ Validation ok, soumission simulée |
| `mes_demandes.html` | Voir demandes | ⚠️ UI ok, données statiques |
| `passeport_pdf.html` | Générer PDF | ✅ Téléchargement HTML réel |
| `parametres.html` | Paramètres | ⚠️ Aucune persistance (frontend statique) |
| `marketplace.html` | Marketplace | ✅ Filtres opérationnels |
| `mes_aides.html` | Sessions d'aide | ✅ Onglets opérationnels |
| `classement.html` | Classement | ⚠️ Données statiques |
| `statistique.html` | Statistiques | ⚠️ Données hardcodées |
| `catalogue_admin.html` | Gérer compétences | ⚠️ Stubs add/edit |
