# AUDIT DU SCHÉMA v3 — ISMO-SkillSwap

Date : 26 Mai 2026
Fichier analysé : [ismo_skillswap_v3.sql](../ismo_skillswap_v3.sql)
Contexte : schéma minimal aligné sur le frontend statique du projet

## Conclusion
Le schéma v3 va dans la bonne direction. Il réduit fortement le nombre de tables tout en couvrant les flux réellement présents dans l’interface : authentification/profil, compétences, candidatures mentor, demandes d’aide, marketplace, badges, notifications, support et modération.

Le point fort principal est la fusion des tables qui se recoupaient sans valeur fonctionnelle visible dans le projet. Le schéma est donc plus simple à maintenir que v2.

## Ce qu’il faut maintenir

### 1. Le noyau `users` + `skills`
- Garder `users` comme table d’identité centrale.
- Garder `user_skills` comme pont entre utilisateurs et compétences.
- Conserver les index sur `email`, `username`, `role`, `status` et `skill_id`.

### 2. La fusion des flux métier
- Conserver `skill_posts` pour regrouper demandes d’aide et annonces marketplace.
- Conserver `skill_responses` pour les réponses, propositions et suivis.
- Conserver `service_cases` pour regrouper support et modération.

### 3. Les vues dérivées
- Garder `vw_user_dashboard`, `vw_platform_statistics` et `vw_moderation_queue`.
- C’est une bonne décision pour un projet statique sans backend applicatif.

### 4. Les garde-fous de base
- Conserver les clés étrangères principales.
- Conserver `is_read`, `read_at`, `status`, `created_at`, `updated_at`.
- Conserver les valeurs par défaut pour l’avatar et les statuts.

## Ce qu’il faut corriger

### 1. Ne pas utiliser `DROP DATABASE` en version livrable
- Le script commence par `DROP DATABASE IF EXISTS`.
- C’est acceptable pour du développement local, mais dangereux pour un fichier de livraison ou de déploiement.
- Mieux : séparer un script de création propre et un script de reset local.

### 2. Clarifier la frontière entre `skill_posts` et `skill_responses`
- Le mélange demandes / offres est propre, mais il faut des règles claires côté données.
- `post_type`, `service_type`, `target_user_id` et `response_kind` doivent être documentés comme les champs qui distinguent les cas d’usage.
- Sans cette documentation, le modèle devient simple mais ambigu.

### 3. Encadrer `service_cases`
- La fusion support + modération est pertinente, mais elle est très large.
- Il faut préciser les combinaisons valides entre `case_type`, `category`, `status` et `severity_level`.
- Sans cela, la table risque de devenir un fourre-tout.

### 4. Réduire la dépendance aux vues pour les statistiques lourdes
- Les vues sont correctes pour un projet léger.
- À mesure que les données grossissent, `vw_user_dashboard` peut devenir coûteuse à calculer.
- Si la plateforme passe en backend réel, les statistiques devront probablement être matérialisées ou calculées côté service.

### 5. Ajouter des données de référence
- Le schéma est minimal, mais il manque un vrai socle de seed data.
- Il faut au minimum prévoir des compétences initiales, des badges de base et quelques comptes de test.
- Sans cela, les pages comme badges, classement, dashboard et recherche resteront visuellement vides.

### 6. Prévoir la promotion stagiaire → mentor
- `mentor_applications` existe, mais le schéma ne formalise pas le changement de rôle.
- Il faut définir le point de vérité : mise à jour directe du champ `role` ou procédure applicative après approbation.

### 7. Revoir le niveau de normalisation si le projet grandit
- Le modèle minimal est bon pour démarrer.
- Si de nouvelles fonctionnalités apparaissent, `notifications` ou `service_cases` pourraient avoir besoin d’un sous-type ou d’un champ `metadata` JSON.

## Priorités recommandées

### Haute priorité
- Retirer `DROP DATABASE` du fichier livré.
- Documenter précisément les valeurs autorisées de `skill_posts`, `skill_responses` et `service_cases`.
- Ajouter un seed minimal.

### Priorité moyenne
- Prévoir la logique de passage de rôle après une candidature mentor.
- Valider la performance des vues quand les données augmentent.

### Priorité basse
- Ajouter des champs d’extension (`metadata` JSON) uniquement si de nouveaux cas d’usage arrivent.

## Verdict
Le schéma v3 doit être conservé comme base minimale. Il remplit bien l’objectif de réduction du nombre de tables, mais il doit être durci sur trois points avant d’être considéré comme propre à l’usage : sécurité du script de démarrage, clarification des cas d’usage fusionnés, et données de base pour alimenter l’interface.
