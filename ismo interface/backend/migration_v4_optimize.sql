-- ISMO-SkillSwap v4 — Performance migration
-- Adds missing indexes (messaging tables not needed per cc3.md)

-- demandes_aide — most heavily queried table
ALTER TABLE `demandes_aide`
  ADD INDEX IF NOT EXISTS `idx_statut_date` (`statut`, `creee_le`),
  ADD INDEX IF NOT EXISTS `idx_auteur_statut_date` (`auteur_id`, `statut`, `creee_le`),
  ADD INDEX IF NOT EXISTS `idx_mentor_statut` (`mentor_id`, `statut`),
  ADD INDEX IF NOT EXISTS `idx_competence_statut` (`competence_id`, `statut`);

-- propositions_aide
ALTER TABLE `propositions_aide`
  ADD INDEX IF NOT EXISTS `idx_proposant_statut_date` (`proposant_id`, `statut`, `creee_le`),
  ADD INDEX IF NOT EXISTS `idx_demande_statut` (`demande_id`, `statut`),
  ADD INDEX IF NOT EXISTS `uk_demande_proposant` (`demande_id`, `proposant_id`);

-- competences_stagiaire
ALTER TABLE `competences_stagiaire`
  ADD INDEX IF NOT EXISTS `idx_user_statut_date` (`utilisateur_id`, `statut_validation`, `date_declaration`),
  ADD INDEX IF NOT EXISTS `idx_statut_date` (`statut_validation`, `date_declaration`);

-- badges_stagiaire
ALTER TABLE `badges_stagiaire`
  ADD INDEX IF NOT EXISTS `idx_user_date` (`utilisateur_id`, `obtenu_le`);

-- utilisateurs
ALTER TABLE `utilisateurs`
  ADD INDEX IF NOT EXISTS `idx_actif_date` (`est_actif`, `date_inscription`),
  ADD INDEX IF NOT EXISTS `idx_role_actif` (`role`, `est_actif`);

-- competences_catalogue
ALTER TABLE `competences_catalogue`
  ADD INDEX IF NOT EXISTS `idx_actif_nom` (`est_active`, `nom`),
  ADD INDEX IF NOT EXISTS `idx_categorie_nom` (`categorie`, `nom`);

-- mentor_applications
ALTER TABLE `mentor_applications`
  ADD INDEX IF NOT EXISTS `idx_statut_date` (`statut`, `date_soumission`),
  ADD INDEX IF NOT EXISTS `idx_user_statut` (`utilisateur_id`, `statut`);

-- notifications — add created_at for ORDER BY
ALTER TABLE `notifications`
  DROP INDEX IF EXISTS `idx_unread`,
  ADD INDEX IF NOT EXISTS `idx_user_read_date` (`user_id`, `is_read`, `created_at`);
