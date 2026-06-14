<?php
/**
 * ISMO-SkillSwap — Footer Include (scripts)
 */
$basePath = $basePath ?? '..';

$jsPageMap = [
    'messagerie' => 'messagerie', 'conversation' => 'messagerie',
    'recherche' => 'recherche', 'classement' => 'classement',
    'mes_competences' => 'mes_competences', 'mes_demandes' => 'mes_demandes',
    'mes_badges' => 'mes_badges', 'notification' => 'notification',
    'parametres' => 'parametres', 'profile' => 'profile',
    'mentor_apply' => 'mentor_apply', 'marketplace' => 'marketplace',
    'mes_aides' => 'mes_aides',
    'passeport_pdf' => 'passeport_pdf',
    'catalogue_admin' => 'catalogue_admin', 'catalogue' => 'catalogue',
    'statistique' => 'statistique', 'statistiques_admin' => 'statistique',
    'tableau_de_bord' => 'tableau_de_bord', 'validation_demande' => 'validation_demande',
    'gestion_comptes' => 'gestion_comptes', 'gestion_badges' => 'gestion_badges', 'moderation' => 'moderation',
    'info' => 'dashboard',
];
?>
  <div class="toast-container" id="toast-container" aria-live="polite" aria-atomic="true"></div>
  <script src="<?= $basePath ?>/assets/js/dashboard.js"></script>
  <?php if (isset($jsPageMap[$currentPage])): ?>
  <script src="<?= $basePath ?>/assets/js/<?= $jsPageMap[$currentPage] ?>.js"></script>
  <?php endif; ?>
</body>
</html>
