<?php
/**
 * ISMO-SkillSwap — Footer Include (scripts)
 */
$basePath = $basePath ?? '..';
$assetBase = __DIR__ . '/../../assets';
$jsPageMap = [

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
$jsVer = file_exists($assetBase . '/js/dashboard.js') ? '?v=' . filemtime($assetBase . '/js/dashboard.js') : '';?>
  <div class="toast-container" id="toast-container" aria-live="polite" aria-atomic="true"></div>
  <script src="<?= $basePath ?>/assets/js/dashboard.min.js<?= file_exists($assetBase . '/js/dashboard.min.js') ? '?v=' . filemtime($assetBase . '/js/dashboard.min.js') : '' ?>"></script>
  <?php if (isset($jsPageMap[$currentPage])):
    $pageJsFile = $jsPageMap[$currentPage] . '.min.js';
    $pageJsVer = file_exists($assetBase . '/js/' . $pageJsFile) ? '?v=' . filemtime($assetBase . '/js/' . $pageJsFile) : '';
  ?>
  <script src="<?= $basePath ?>/assets/js/<?= $pageJsFile . $pageJsVer ?>"></script>
  <?php endif; ?>
</body>
</html>
