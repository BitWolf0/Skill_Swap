# Rapport de Projet — ISMO-SkillSwap

**Filière :** DEV 101
**Module :** M107 — Sites Web Dynamiques (CC3)
**Année :** 2025/2026
**Institut :** OFPPT — ISMO Tétouan

---

## 1. Description des fonctionnalités

ISMO-SkillSwap est une plateforme web d'entraide et de valorisation des compétences destinée aux stagiaires de l'ISMO Tétouan. Elle repose sur quatre acteurs : **Stagiaire**, **Mentor**, **Formateur** et **Administrateur**.

### 1.1 Stagiaire

| Fonctionnalité | Description |
|---|---|
| Inscription & Connexion | Création de compte et authentification sécurisée |
| Publication d'une demande d'aide | Formulaire avec titre, compétence, description, urgence et type de service |
| Consultation de ses demandes | Liste filtrée (ouvertes, en cours, terminées) avec statut visible |
| Modification / Fermeture des demandes | L'auteur peut modifier ou fermer sa propre demande |
| Déclaration de compétences | Modal avec sélection de compétence, niveau estimé, années d'expérience |
| Profil utilisateur | Photo, bio, ville, disponibilité, téléphone — modifiable |
| Badges et niveaux | Badges obtenus et disponibles avec barème de points |
| Passeport de compétences | Récapitulatif des compétences validées exportable en PDF |
| Messagerie interne | Conversations avec mentors et répondants |
| Recherche | Recherche globale avec filtres par compétence, niveau, filière |
| Classement | Top des mentors par réputation |

### 1.2 Mentor

| Fonctionnalité | Description |
|---|---|
| Dashboard mentor | Statistiques : réponses données, aides acceptées, note moyenne |
| Marketplace | Consulter les demandes d'aide ouvertes et proposer son aide |
| Mes demandes | Gérer ses propres demandes d'aide |
| Mes aides | Suivi des réponses proposées et acceptées |
| Badges, compétences, classement | Comme le stagiaire, avec vue mentor |

### 1.3 Formateur

| Fonctionnalité | Description |
|---|---|
| Dashboard formateur | Aperçu des demandes en attente de validation |
| Catalogue des compétences | Consultation du catalogue complet |
| Validation des compétences | Valider ou refuser les compétences déclarées par les stagiaires |
| Statistiques | Indicateurs sur les validations et les compétences |
| Attribution de badges | Assigner des badges officiels aux stagiaires méritants |

### 1.4 Administrateur

| Fonctionnalité | Description |
|---|---|
| Tableau de bord admin | Statistiques globales : utilisateurs, mentors, signalements |
| Gestion des comptes | Valider, suspendre ou activer les comptes utilisateurs |
| Modération | Gestion des signalements et contenus inappropriés |
| Catalogue admin | Ajouter, modifier ou désactiver des compétences |
| Gestion des badges | Créer, attribuer ou retirer des badges |
| Candidatures mentor | Examiner et approuver/rejeter les candidatures mentor |
| Statistiques avancées | Top compétences, derniers badges attribués |

---

## 2. Planification du projet

| Phase | Tâches | Durée | Période |
|---|---|---|---|
| **1. Conception** | Modélisation MERISE (MCD/MLD), Maquettage UI/UX (Figma) | 5 jours | 29–30 Avril |
| **2. Architecture technique** | Conception base de données MySQL (18 tables, vues dérivées) | 15 jours | 11 Mai |
| **3. Développement Frontend** | Intégration des maquettes (HTML, CSS, JS) — 40+ pages | 1 semaine | 18 Mai |
| **4. Développement Backend** | API REST PHP — Authentification, rôles, CRUD | 1 semaine | 26 Mai |
| **5. Fonctionnalités avancées** | Messagerie, notifications, badges, recherche filtrée | 3 semaines | 20 Juin |
| **6. Gamification** | Système de points, niveaux, badges automatiques | 1 semaine | 28 Juin |
| **7. Tests & validation** | Tests fonctionnels E2E, débogage cross-features | 3 jours | 31 Juin |
| **8. Présentation finale** | Rapport + diaporama de soutenance | 1 jour | Début Juillet |

### Répartition des tâches

(Gestion d'équipe — à adapter selon votre groupe)

- **Membre 1 :** Modélisation BD, backend API (auth, posts, skills)
- **Membre 2 :** Frontend stagiaire/mentor (HTML/CSS/JS), intégration maquettes
- **Membre 3 :** Pages admin/formateur, messagerie, notifications
- **Membre 4 :** Gamification, badges, passeport PDF, tests E2E

---

## 3. Réalisations (interfaces)

### 3.1 Module d'authentification

- **Page de connexion** *(voir Annexe 1)* — Formulaire email/mot de passe, liens inscription et mot de passe oublié. Design responsive, validation JS.
- **Page d'inscription** *(voir Annexe 2)* — Formulaire complet avec prénom, nom, email, mot de passe, sélection de rôle.

### 3.2 Dashboard et navigation

- **Dashboard stagiaire** *(voir Annexe 3)* — Cartes de statistiques (demandes, badges, compétences), accès rapide aux actions.
- **Dashboard mentor** *(voir Annexe 4)* — Statistiques dédiées (réponses données, note moyenne), liste des demandes ouvertes.
- **Dashboard admin** *(voir Annexe 5)* — Indicateurs globaux (utilisateurs, mentors, signalements), tableau des derniers inscrits.

### 3.3 Gestion des demandes d'aide

- **Nouvelle demande** *(voir Annexe 6)* — Formulaire avec titre, sélection de compétence, description, niveau d'urgence, type de service.
- **Mes demandes** *(voir Annexe 7)* — Liste des demandes avec filtres (toutes, ouvertes, en cours, terminées). Boutons Modifier/Fermer.

### 3.4 Compétences et badges

- **Déclaration de compétence** *(voir Annexe 8)* — Modal avec sélection de compétence, niveau, années d'expérience.
- **Mes compétences** *(voir Annexe 9)* — Grille des compétences déclarées avec statut de validation.
- **Mes badges** *(voir Annexe 10)* — Section badges obtenus et badges disponibles avec indication des points.

### 3.5 Profil et paramètres

- **Profil utilisateur** *(voir Annexe 11)* — Carte d'identité (nom, rôle, réputation, points), formulaire d'édition.
- **Paramètres** *(voir Annexe 12)* — Configuration des notifications et préférences.

### 3.6 Administration

- **Gestion des comptes** *(voir Annexe 13)* — Tableau des utilisateurs avec recherche, filtre par rôle, actions (valider/suspendre/activer).
- **Catalogue admin** *(voir Annexe 14)* — Liste des compétences avec compteur de déclarations, ajout et désactivation.
- **Statistiques** *(voir Annexe 15)* — Indicateurs clés, top compétences, derniers badges attribués.
- **Modération** *(voir Annexe 16)* — File d'attente des signalements.

### 3.7 Marketplace et messagerie

- **Marketplace mentor** *(voir Annexe 17)* — Demandes d'aide ouvertes avec proposition d'aide.
- **Messagerie** *(voir Annexe 18)* — Conversations avec historique et notifications.

---

## 4. Difficultés rencontrées

### 4.1 Migration du frontend statique vers le backend PHP

Le projet est passé d'un prototype statique (40+ pages HTML/CSS/JS) à une application PHP dynamique avec base de données MySQL. La migration a nécessité :

- Remplacement de toutes les URLs et chemins relatifs
- Adaptation des includes PHP (header, sidebar, footer) avec gestion des rôles
- Injection des données PHP dans les templates sans casser le CSS/JS existant

### 4.2 Incohérence entre format FormData et JSON dans l'API

Certains endpoints backend attendaient des données au format `application/x-www-form-urlencoded` (formulaires HTML classiques), tandis que d'autres (notamment les appels AJAX) utilisaient du JSON avec `Content-Type: application/json`. Cela a provoqué des erreurs silencieuses où `$_POST` était vide alors que `php://input` contenait les données. La solution a été de standardiser toutes les API sur `jsonBody()`.

### 4.3 Évolution du schéma de base de données (v2 → v3)

Le schéma initial (v2) comportait 18 tables avec des redondances et une complexité inutile. La version 3 a fusionné :
- `user_profiles` → intégré dans `users`
- `help_requests` + `marketplace_listings` → `skill_posts`
- `help_responses` + `mentor_relationships` + `mentoring_sessions` → `skill_responses`
- `support_tickets` + `moderation_reports` → `service_cases`

Cette restructuration a nécessité la réécriture partielle de plusieurs endpoints API et requêtes SQL.

### 4.4 Gestion des sessions et routage par rôle

Chaque rôle (stagiaire, mentor, formateur, admin) possède son propre jeu de pages et son propre sidebar. Le routage après connexion est basé sur le rôle. La synchronisation des cookies PHP (PHPSESSID) avec le navigateur Playwright pour les tests E2E a été un défi, nécessitant un « bridge » manuel entre la session HTTP (requests) et le contexte navigateur.

### 4.5 Système de messagerie

Le passage d'une démonstration statique à un vrai système de messagerie a été complexe :

- Gestion des conversations (création, participants)
- Marqueurs de lecture (last_read_at)
- Notifications en temps réel
- Affichage des messages dans l'ordre chronologique avec pagination

### 4.6 Tests E2E automatisés

L'écriture de tests E2E fiables avec Playwright a posé plusieurs défis :

- Synchronisation des sessions PHP entre requêtes HTTP et navigateur
- Gestion des timeouts et des attentes réseau (`networkidle`)
- Création de données de test isolées (email unique, skills dédiées)
- Nettoyage des données après les tests

---

## 5. Extensions possibles

### 5.1 Notifications en temps réel (WebSocket)

Actuellement, les notifications sont pollées via des requêtes AJAX. L'intégration de WebSocket (via Socket.IO ou Ratchet PHP) permettrait des notifications push instantanées lorsqu'un mentor répond à une demande ou qu'un badge est attribué.

### 5.2 Application mobile (PWA ou React Native)

Une Progressive Web App (PWA) permettrait aux stagiaires de recevoir des notifications push sur leur mobile et de consulter les demandes en mode hors-ligne. Une alternative native (React Native) offrirait une expérience plus fluide avec accès à l'appareil photo pour le scan de badges QR.

### 5.3 Matching intelligent par IA

Implémentation d'un algorithme de recommandation pour suggérer automatiquement le mentor le plus adapté à chaque demande, basé sur :

- Historique des compétences validées
- Taux de succès des précédentes interventions
- Disponibilité actuelle
- Note et appréciations reçues

### 5.4 Intégration calendrier pour sessions de mentorat

Ajout d'un module de planification avec Google Calendar / CalDAV pour organiser des sessions de mentorat en visioconférence, avec :

- Créneaux de disponibilité des mentors
- Rappels automatiques par email
- Lien de réunion (Meet, Zoom, Teams) intégré

### 5.5 Partage des badges sur les réseaux sociaux

Permettre aux stagiaires de partager leurs badges et leur passeport de compétences sur LinkedIn, Twitter et GitHub, avec génération d'images de badge personnalisées et Open Graph tags.

### 5.6 Tableau de bord analytique avancé

Ajout de graphiques interactifs (Chart.js) pour visualiser :

- Évolution du nombre de demandes par mois
- Taux de résolution par compétence
- Heatmap des périodes d'activité
- Progression individuelle des stagiaires

---

*Document généré le 30 Mai 2026 — ISMO-SkillSwap v3*
