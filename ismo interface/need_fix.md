# Liste des Corrections Nécessaires - ISMO-SkillSwap

Ce document répertorie les corrections à apporter pour assurer la cohérence, la fonctionnalité et la qualité visuelle de la plateforme.

## 🔴 PRIORITÉ HAUTE : Corrections Structurelles et Liens Cassés

### 1. Orthographe et Renommage de Fichiers (Cohérence)
- [ ] **Tableau de bord :** Renommer `formateur_pages/tableu_de_bord.html` → `tableau_de_bord.html`.
- [ ] **Assets CSS/JS :** Renommer `assets/css/tableu_de_bord.css` → `tableau_de_bord.css` et `assets/js/tableu_de_bord.js` → `tableau_de_bord.js`.
- [ ] **Statistiques Admin :** Renommer `pages_admin/statisque_adm.html` → `statistique_adm.html`.
- [ ] **Dossier Stagiaire :** Renommer le dossier `pages_stagiere` → `pages_stagiaire` (Recommandé pour l'orthographe française).
- [ ] **Mise à jour des liens :** Après renommage, mettre à jour tous les `<link>`, `<script>` et `<a> href` dans tous les fichiers du projet.

### 2. Liens de Navigation et Placeholders
- [ ] **Login Stagiaire :** Dans `pages_stagiere/login.html`, remplacer les `href="#"` des "Conditions d'utilisation" et "Politique de confidentialité" par des liens réels ou des ancres valides.
- [ ] **Paramètres :** Dans `pages_stagiere/parametres.html`, les liens du footer (Centre d'aide, Support, etc.) pointent vers des dossiers `/pages/` inexistants.
- [ ] **Sidebar Admin :** S'assurer que le lien "Statistiques" pointe vers le bon fichier (même si le renommage n'est pas fait immédiatement).
- [ ] **Menu Profil :** Harmoniser les menus déroulants de profil entre `formateur_pages` et `pages_stagiere` (libellés et cibles identiques).

## 🟡 PRIORITÉ MOYENNE : UI/UX et Design

### 3. Migration Emoji vers SVG
- [ ] Remplacer tous les emojis restants par des icônes SVG cohérentes (ex: 👋, 🚀, 💎, 📅, 👥, ✓, ✗, 🏆, 📚).
- [ ] Vérifier particulièrement les pages `formateur_pages/statistique.html` et `pages_admin/statisque_adm.html`.

### 4. Refonte Visuelle et Polissage
- [ ] **Passeport PDF :** Restyler `pages_stagiere/passeport_pdf.html` pour qu'il corresponde à la charte graphique du dashboard (hiérarchie des cartes, espacement, panneaux consistants).
- [ ] **Hover Mentor :** Dans `pages_mentor/*`, corriger l'état au survol des bordures pour utiliser une couleur douce au lieu de "claquer" au noir pur.
- [ ] **Soulignement Sidebar :** S'assurer qu'aucun lien de la sidebar ne soit souligné (`text-decoration: none`) dans tous les états (hover, active).
- [ ] **Catalogue Formateur :** Améliorer le style de `formateur_pages/catalogue.html` pour une meilleure lisibilité.
- [ ] **Statistiques Admin :** Améliorer le style de `pages_admin/statisque_adm.html`.

### 5. Mise en Page Spécifique (Paramètres)
- [ ] **Layout Paramètres :** Dans `pages_stagiere/parametres.html` :
    - Supprimer la div contenant la version.
    - Passer la div "Besoin d'aide" en footer de page.
    - Étendre la section principale sur toute la largeur (`full-width`).
    - Formater la photo de profil en rectangle aux bords arrondis (au lieu d'un cercle parfait si nécessaire pour le design).

## 🟢 PRIORITÉ BASSE : Fonctionnalités et Accessibilité

### 6. Nouvelles Fonctionnalités Frontend
- [ ] **Formulaires de demande :** Finaliser la création des formulaires de soumission de demande dans `pages_stagiere/`.
- [ ] **Édition de Profil :** Rendre fonctionnelle la logique d'édition de profil dans `parametres.html` (gestion des champs, prévisualisation de l'avatar).
- [ ] **Accessibilité :** Ajouter ou compléter les attributs `aria-*` sur les boutons, menus déroulants et champs de recherche.

### 7. Gestion des Assets
- [ ] Vérifier que toutes les pages utilisent bien `/assets/images/default_avatar.svg` par défaut pour les utilisateurs sans photo.

---
*Note : Ce document doit être mis à jour au fur et à jour de la résolution des points.*
