# ISMO-SkillSwap

Plateforme multi-rôles d'échange de compétences (Skill Swap) — **stagiaire / mentor / formateur / administrateur**.

## Stack

- **Frontend** : HTML, CSS, JS vanilla (pur statique)
- **Backend** : PHP 8.5+ (sessions, PDO MySQL)
- **Base de données** : MariaDB 10.4 / MySQL 8.0
- **Build** : Aucun — serveur Apache + PHP suffit

## Installation

```bash
# 1. Cloner le dépôt
git clone <repo-url>
cd "ismo interface"

# 2. Importer la base
mysql -u root -p < ismo_skillswap_v4.sql

# 3. Configurer la connexion
#    Éditer backend/config.php avec vos identifiants MySQL

# 4. Lancer (via XAMPP / Apache)
#    Le fichier index.php redirige vers login.php
```

## Comptes de démonstration

| Email | Mot de passe | Rôle |
|---|---|---|
| `admin@test.com` | `admin123` | Administrateur |
| `pierre@test.com` | `password123` | Formateur |
| `jean@test.com` | `password123` | Stagiaire |
| `sophie@test.com` | `password123` | Stagiaire |
| `lucas@test.com` | `lucas123` | Mentor |

> Les mots de passe réels sont hashés en bcrypt dans le dump.

## Structure du projet

```
ismo interface/
├── backend/               # API, auth, config, includes
│   ├── api/               # Endpoints REST PHP
│   ├── auth/              # login.php, register.php
│   ├── includes/          # header, sidebar, topbar
│   └── functions.php
├── assets/
│   ├── css/               # dashboard.css, pages spécifiques
│   ├── images/            # avatars, logo
│   └── js/                # dashboard.js (dropdown, toast, search)
├── pages_stagiaire/       # Dashboard, demandes, compétences, etc.
├── pages_mentor/          # Dashboard, marketplace, propositions
├── pages_admin/           # Comptes, badges, stats, modération
├── formateur_pages/       # Validation compétences, catalogue
├── ismo_skillswap_v4.sql  # Schéma + données de démo
├── login.php              # Page de connexion
├── index.php              # Redirection selon rôle
└── DESIGN_SYSTEM.md       # Tokens CSS, layout, composants
```

## Rôles

| Rôle | Accès |
|---|---|
| **Stagiaire** | Déclare ses compétences, publie des demandes d'aide, suit son passeport |
| **Mentor** | Propose son aide sur les demandes, accumule des points et badges |
| **Formateur** | Valide les compétences des stagiaires, gère le catalogue |
| **Administrateur** | Gère les comptes, badges, modération, statistiques |

## Développement

Aucun build — ouvrir les fichiers `.php` directement via Apache ou Live Server.

```bash
# VS Code Live Server (port 5501)
code "ismo interface" && start http://localhost:5501
```

