<?php
require_once __DIR__ . '/../backend/config.php';
requireAuth();
requireRole(ROLE_ADMIN);

$pageTitle = 'ISMO-SkillSwap — Paramètres Admin';
$currentPage = 'parametres';
$basePath = '..';

include __DIR__ . '/../backend/includes/header.php';
?>
<?php include __DIR__ . '/../backend/includes/sidebar_admin.php'; ?>
<?php include __DIR__ . '/../backend/includes/topbar.php'; ?>

<main class="content-area" id="main-content">
  <section class="content-main">
    <div class="page-head">
      <div class="page-title-wrap">
        <h1 class="page-title">Paramètres</h1>
      </div>
    </div>
    <p class="text-muted">Configuration de la plateforme.</p>
    <ul>
      <li><a href="catalogue_admin.php">Gérer le catalogue de compétences</a></li>
      <li><a href="gestion_comptes.php">Gérer les comptes utilisateurs</a></li>
    </ul>
  </section>
</main>
<?php include __DIR__ . '/../backend/includes/footer.php'; ?>
