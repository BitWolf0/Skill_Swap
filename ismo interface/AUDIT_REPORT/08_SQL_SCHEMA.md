# ANALYSE DU SCHÉMA MySQL

**Fichier :** `ismo_skillswap.sql`

## Vue d'ensemble
- 18 tables
- Engine: InnoDB
- Charset: utf8mb4
- Collation: utf8mb4_general_ci

## Tables existantes

| Table | Rôle probable |
|-------|--------------|
| `utilisateur` | Utilisateurs (stagiaire, mentor, formateur, admin) |
| `stagiaire` | Profils stagiaires |
| `mentor` | Profils mentors |
| `formateur` | Profils formateurs |
| `competence` | Compétences |
| `demande_aide` | Demandes d'aide |
| `session_aide` | Sessions de mentorat |
| `badge` | Définitions de badges |
| `obtention_badge` | Badges obtenus |
| `catalogue` | Catalogue de compétences |
| `notification` | Notifications |
| `message` | Messages |
| `evaluation` | Évaluations |
| `categorie` | Catégories |
| `administrateur` | Admins |
| `parametre` | Paramètres système |
| `fichier` | Fichiers uploadés |
| `log_activite` | Logs d'activité |

## Points d'attention

### Aucune table de jointure pour compétences multiples
Si un utilisateur peut avoir plusieurs compétences, une table `utilisateur_competence` (ou `stagiaire_competence`, `mentor_competence`) serait nécessaire.

### Index manquants probables
Vérifier que les clés étrangères (comme `id_utilisateur`, `id_competence`, `id_mentor`) ont des INDEX correspondants. InnoDB les crée automatiquement pour les FOREIGN KEY, mais les colonnes fréquemment utilisées dans les WHERE/JOIN (comme `statut`, `date_creation`, `categorie`) pourraient bénéficier d'index supplémentaires.

### Relation nombreux-à-nombreux
Si un stagiaire peut avoir plusieurs mentors et vice-versa, une table de liaison est nécessaire.
