<?php
/**
 * ISMO-SkillSwap — HTML Header Include
 * Pass $pageTitle and $pageDescription in the calling page
 */
require_once __DIR__ . '/../config.php';
$pageTitle = $pageTitle ?? 'ISMO-SkillSwap';
$pageDescription = $pageDescription ?? 'Plateforme d\'entraide et de valorisation des compétences';
$currentPage = $currentPage ?? '';
$basePath = $basePath ?? '..';

// Auto-map currentPage to CSS file
$cssPageMap = [
    'login' => 'login', 'messagerie' => 'messagerie', 'conversation' => 'messagerie',
    'classement' => 'classement', 'mes_competences' => 'mes_competences',
    'mes_demandes' => 'mes_demandes', 'mes_badges' => 'mes_badges',
    'notification' => 'notification', 'recherche' => 'recherche',
    'parametres' => 'parametres', 'profile' => 'profile',
    'mentor_apply' => 'mentor_apply', 'marketplace' => 'marketplace',
    'mes_aides' => 'mes_aides', 'nouvelle_demande' => 'nouvelle_demande',
    'passeport_pdf' => 'passeport_pdf',
    'catalogue_admin' => 'catalogue_admin', 'catalogue' => 'catalogue',
    'demande_detail' => 'demande_detail',
    'statistique' => 'statistique', 'statistiques_admin' => 'statistique',
    'tableau_de_bord' => 'tableau_de_bord', 'validation_demande' => 'validation_demande',
    'gestion_comptes' => 'dashboard', 'moderation' => 'dashboard',
    'gestion_badges' => 'dashboard', 'candidatures_mentor' => 'dashboard',
    'info' => 'info', '404' => 'info',
];
$cssFile = $cssPageMap[$currentPage] ?? 'dashboard';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= h($pageTitle) ?></title>
  <meta name="description" content="<?= h($pageDescription) ?>" />
  <meta name="csrf-token" content="<?= csrfToken() ?>" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="<?= $basePath ?>/assets/css/tailwind.css" />
  <link rel="stylesheet" href="<?= $basePath ?>/assets/css/dashboard.css" />
  <?php if ($cssFile !== 'dashboard'): ?>
  <link rel="stylesheet" href="<?= $basePath ?>/assets/css/<?= $cssFile ?>.css" />
  <?php endif; ?>
</head>
<body>
