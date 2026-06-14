<?php
require_once __DIR__ . '/../backend/config.php';
requireAuth();
requireRole([ROLE_FORMATEUR, ROLE_ADMIN]);

$pageTitle = 'ISMO-SkillSwap — Paramètres';
$currentPage = 'parametres';
$basePath = '..';

include __DIR__ . '/../backend/includes/header.php';
?>
<?php include __DIR__ . '/../backend/includes/sidebar_formateur.php'; ?>
<?php include __DIR__ . '/../backend/includes/topbar.php'; ?>

<main class="content-area" id="main-content">
  <section class="content-main">
    <h1>Paramètres</h1>
    <p><a href="profile.php">Modifier mon profil</a></p>
  </section>
</main>
<?php include __DIR__ . '/../backend/includes/footer.php'; ?>
