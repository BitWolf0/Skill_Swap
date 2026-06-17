# ISMO-SkillSwap — Audit Complet & Plan d'Action

> Généré le 20 mai 2026 — Basé sur le Cahier des Charges fonctionnel et l'état actuel du projet

---

## 1. Résumé

| Métrique | Valeur |
|---|---|
| Pages HTML | 40 pages (stagiaire: 17, mentor: 10, admin: 6, formateur: 7) |
| Fichiers PHP | 0 (backend totalement absent) |
| CSS | 22 fichiers (1 shared + 21 page-specific) |
| JS | 23 fichiers (1 shared + 22 page-specific) |
| Base de données | Schéma MySQL défini (18 tables) mais non connecté |
| État | **Prototype frontend statique** — 100% HTML/CSS/JS, aucune logique backend |

---

## 2. Correspondance avec le Cahier des Charges

### 2.1 Objectifs Fonctionnels (OF1–OF9)

| Code | Objectif | Statut | Détail |
|---|---|---|---|
| **OF1** | Publier une demande d'aide technique | ✅ UI OK | `nouvelle_demande.html` — formulaire complet, validation JS |
| **OF2** | Proposer son aide | ✅ UI OK | Marketplace + mentor_apply. Filtres fonctionnels |
| **OF3** | Système de notation des mentors | ⚠️ Stub | Modal UI présent, soumission = toast uniquement |
| **OF4** | Profil de compétences détaillé | ❌ Stub | `mes_competances.js` = "Fonctionnalité à venir" |
| **OF5** | Formateurs valident les compétences | ⚠️ Partiel | `validation_demande.html` existe, logique backend absente |
| **OF6** | Passeport de Compétences exportable (PDF) | ❌ Stub | Bouton download = toast uniquement, pas de vrai PDF |
| **OF7** | Système de badges et niveaux | ❌ Stub | Badges affichés en HTML statique, attribution = toast |
| **OF8** | Moteur de recherche par compétence | ⚠️ Partiel | Barre de recherche + filtres niveau/filière OK, pas de backend |
| **OF9** | Tableau de bord statistique | ⚠️ Partiel | Stats hardcodées, pas de données dynamiques ni graphiques réels |

### 2.2 Modules Fonctionnels

#### Authentification & Profils
| Fonctionnalité | Stagiaire | Mentor | Formateur | Admin |
|---|---|---|---|---|
| **Inscription** | ❌ (redirect) | ❌ | ❌ | ❌ |
| **Connexion** | ❌ (simulée) | ❌ (simulée) | ❌ | ❌ |
| **Modifier profil** | ⚠️ (toast) | ⚠️ | ⚠️ | ⚠️ |
| **Ajouter/modifier compétences** | ❌ stub | ❌ stub | ✖ | ✖ |
| **Valider compte** | ✖ | ✖ | ✖ | ❌ stub |
| **Suspendre compte** | ✖ | ✖ | ✖ | ❌ stub |

#### Marketplace d'Entraide
| Fonctionnalité | Statut |
|---|---|
| Publier demande d'aide | ✅ UI complet |
| Modifier/supprimer demande | ⚠️ UI présent, pas de persistance |
| Proposer son aide | ✅ UI complet |
| Marquer comme résolu | ⚠️ Toast uniquement |
| Noter le mentor | ⚠️ Modal UI, pas de persistence |
| Voir historique d'aides | ✅ UI OK |
| Supprimer publication inappropriée | ❌ Stub |

#### Gestion des Compétences
| Fonctionnalité | Statut |
|---|---|
| Déclarer compétence | ❌ "Fonctionnalité à venir" |
| Choisir niveau estimé | ❌ Stub |
| Valider/Refuser compétence | ❌ Backend manquant |
| Modifier catalogue compétences | ⚠️ Partiel (catalogue_admin) |

#### Passeport Compétences
| Fonctionnalité | Statut |
|---|---|
| Consulter son passeport | ✅ UI OK |
| Voir compétences validées | ✅ Affichage statique |
| Voir badges obtenus | ✅ Affichage statique |
| Générer PDF | ❌ Stub (toast) |
| Voir classement filière | ⚠️ Données hardcodées |

#### Gamification & Badges
| Fonctionnalité | Statut |
|---|---|
| Gagner des points | ❌ Non implémenté |
| Voir son score | ⚠️ Hardcodé |
| Attribuer/Retirer badge | ❌ Stub |
| Voir Top Mentors | ⚠️ Hardcodé |

#### Recherche & Filtrage
| Fonctionnalité | Statut |
|---|---|
| Recherche par compétence | ⚠️ UI OK, données statiques |
| Filtrer par niveau/filière | ✅ OK |
| Voir disponibilité mentor | ✅ OK |

#### Tableau de bord
| Indicateur | Statut |
|---|---|
| Aides réalisées | ⚠️ Hardcodé |
| Note moyenne | ⚠️ Hardcodé |
| Compétences demandées | ⚠️ Hardcodé |
| Statistiques globales | ⚠️ Hardcodé |

---

## 3. CE QUI DOIT ÊTRE CRÉÉ (Priorité Haute)

### 3.1 Backend PHP — NOUVEAU
- Aucun fichier PHP n'existe. Le projet nécessite un backend complet :
  - Système d'authentification (hachage bcrypt, sessions JWT)
  - API REST pour toutes les opérations CRUD
  - Connexion à la base MySQL (`ismo_skillswap.sql` existant)
  - Upload de fichiers (images, documents)
  - Génération PDF côté serveur (DomPDF, TCPDF, etc.)

### 3.2 Page d'Inscription — NOUVELLE
- `inscription.html` redirige immédiatement vers `login.html?show=signup`
- Le formulaire d'inscription existe dans `login.html` mais ne fait rien (toast uniquement)
- **Créer une vraie page d'inscription** ou transformer le formulaire existant en logique fonctionnelle

### 3.3 Dashboard Mentor — À CRÉER
- `pages_mentor/dashboard.html` = page vide qui redirige vers le dashboard stagiaire
- **Créer un vrai dashboard mentor** avec :
  - Demandes d'aide en attente
  - Sessions de mentorat récentes
  - Statistiques personnelles (aides fournies, notes, badges)
  - Compétences validées par les formateurs

### 3.4 Déclaration de Compétence — À CRÉER
- `mes_competances.js` = **stub complet** (`showToast('Fonctionnalité à venir')`)
- Créer un formulaire/modal pour :
  - Sélectionner une compétence depuis le catalogue
  - Choisir son niveau estimé (débutant/intermédiaire/avancé/expert)
  - Ajouter des preuves (liens, descriptions)
  - Soumettre pour validation formateur

### 3.5 Génération PDF du Passeport — À CRÉER
- `passeport_pdf.js` = bouton download = toast uniquement
- Sans backend : utiliser **html2pdf.js** ou **jsPDF** côté client
- Avec backend : générer PDF via PHP (TCPDF/Dompdf)

### 3.6 Gestion des Comptes Admin — À CRÉER
- `gestion_comptes.js` = toutes les actions sont des stubs (création, approbation, rejet)
- Créer les workflows complets :
  - Création de compte (formulaire)
  - Approbation/Rejet de comptes en attente
  - Suspension/Suppression de comptes
  - Filtres et recherche

### 3.7 Modération — À CRÉER
- `moderation.js` = toutes les actions "Fonctionnalité à venir"
- Créer les vues et logiques pour :
  - Messages en masse
  - Règles de modération
  - Historique des signalements
  - Actions sur les signalements (approuver/supprimer/suspendre)

### 3.8 Attribution de Badges — À CRÉER
- Admin/formateur doit pouvoir :
  - Créer des badges
  - Attribuer des badges aux utilisateurs
  - Retirer des badges
  - Voir les badges attribués

### 3.9 Système de Notation — À CRÉER
- Soumettre une note après une session d'aide
- Persister la note (localStorage ou backend)
- Calculer la moyenne affichée sur les profils

### 3.10 Notifications Dynamiques — À CRÉER
- `notification.js` = liste statique hardcodée
- Créer un système de notifications (même en localStorage pour prototype)

### 3.11 Statistiques Dynamiques — À CRÉER
- Graphiques et indicateurs actuellement hardcodés
- Intégrer Chart.js pour des graphiques réels, même avec données mockées

---

## 4. CE QUI DOIT ÊTRE CORRIGÉ

### 4.1 Problèmes Hautes Priorité

| # | Problème | Fichier | Description |
|---|---|---|---|
| **P1** | Dashboard mentor = redirection vide | `pages_mentor/dashboard.html` | N'a PAS de contenu, redirige vers stagiaire |
| **P2** | Déclaration compétence = stub | `assets/js/mes_competances.js` | `showToast('à venir')` — rien ne fonctionne |
| **P3** | Téléchargement PDF = toast | `assets/js/passeport_pdf.js` | Pas de vrai téléchargement PDF |
| **P4** | Gestion comptes = stubs | `assets/js/gestion_comptes.js` | Création/approbation/rejet = toast uniquement |
| **P5** | Modération = stubs | `assets/js/moderation.js` | Messages masse/règles/historique = toast |
| **P6** | Badges = pas d'attribution | `assets/js/tableau_de_bord.js` | Créer/Assigner badges = toast |
| **P7** | Notation = toast uniquement | `assets/js/mes_demandes.js` | Modal étoiles OK, soumission = toast |
| **P8** | Recherche topbar = toast | `assets/js/dashboard.js` (Ctrl+K) | Cherche mais n'affiche que "Recherche pour: ..." |
| **P9** | Paramètres = pas de persistence | `assets/js/parametres.js` | Toasts uniquement, rien sauvegardé (même pas localStorage) |
| **P10** | Inscription = redirect | `pages_stagiaire/inscription.html` | Pas de formulaire d'inscription propre |

### 4.2 Problèmes Moyennes Priorité

| # | Problème | Fichier | Description |
|---|---|---|---|
| **P11** | Profil = édition stub | `assets/js/profile.js` | Boutons d'édition = toast |
| **P12** | Classement = données hardcodées | `assets/js/classement.js` | 10 utilisateurs en dur, pas de tri dynamique |
| **P13** | Chartes = pas de graphiques réels | `assets/js/statistique.js`, `tableau_de_bord.js` | Placeholders sans Chart.js |
| **P14** | Couleurs hardcodées dans catalogue.css | `assets/css/catalogue.css` | ~40 couleurs en dur, pas de CSS vars |
| **P15** | Login = simulation uniquement | `assets/js/login.js` | Animation de connexion + toast, pas d'auth réelle |
| **P16** | Notification = liste statique | `assets/js/notification.js` | Données en dur, lecture/écriture uniquement en JS |
| **P17** | ctrl+K = pas de recherche réelle | `assets/js/dashboard.js` | Affiche toast avec terme, ne filtre rien |

### 4.3 Problèmes Faibles Priorité / Cosmétiques

| # | Problème | Description |
|---|---|---|
| **P18** | `parametres.js` textes en anglais | "Theme changed to", "Language changed to", "Delete" — pas français |
| **P19** | `notification.js` textes en anglais | "All notifications marked as read", "Notification dismissed" |
| **P20** | Fichiers mal orthographiés | `tableu_de_bord` → `tableau_de_bord`, `statisque_adm` → `statistique_adm` |
| **P21** | `aria-current="page"` mentor vers stagiaire | Les liens actifs du menu mentor pointent vers pages stagiaire |
| **P22** | Mode sombre non implémenté | `parametres.html` a l'option thème mais ne fait rien |
| **P23** | Pas de breakpoints 480px sur certaines pages | Responsive à améliorer |
| **P24** | Données utilisateur = partout statiques | Aucune donnée dynamique dans toute l'application |

---

## 5. Plan d'Action Recommandé

### Phase 1 — Backend Foundation (semaine 1-2)
1. Installer PHP 8+ et configurer l'environnement (XAMPP/WAMP/Laragon)
2. Importer `ismo_skillswap.sql` dans MySQL
3. Créer la couche de connexion BDD (PDO)
4. Implémenter l'authentification (inscription, connexion, déconnexion, rôles)
5. Créer les endpoints API REST de base

### Phase 2 — Modules critiques (semaine 3-4)
1. Dashboard mentor réel
2. Déclaration de compétence avec validation formateur
3. Gestion des comptes admin (CRUD complet)
4. Modération (signalements + actions)
5. Système de notation avec persistence

### Phase 3 — Fonctionnalités avancées (semaine 5-6)
1. Passeport PDF (généré côté serveur)
2. Badges et gamification (attribution, calcul points)
3. Moteur de recherche avec filtres multiples
4. Notifications en temps réel
5. Statistiques avec graphiques (Chart.js)

### Phase 4 — Polish (semaine 7)
1. Corriger couleurs hardcodées dans catalogue.css → CSS vars
2. Traduire textes anglais restants en français
3. Renommer fichiers mal orthographiés
4. Ajouter breakpoints responsive manquants
5. Audit accessibilité (ARIA, focus, contrastes)

---

## 6. Notes Techniques

- **HTML** : 40 pages suivent le pattern sidebar (248px) + topbar (64px) + main-content. Design tokens dans `dashboard.css:root`.
- **CSS** : Utiliser les CSS vars existantes (`--blue-600`, `--gray-50`, `--radius-md`, etc.) — ne PAS hardcoder de couleurs.
- **JS** : `'use strict'`, DOM refs en haut, event listeners groupés. `showToast()` centralisé dans `dashboard.js`.
- **Base de données** : Schéma complet avec 18 tables, contraintes FK, index. Prêt pour import.
- **Conventions** : UI en français, noms de fichiers mixtes français/anglais (ne pas renommer sans demande explicite).
- **SEO** : Aucune méta-description, balises `og:` ou `twitter:` nulle part. À prévoir.
