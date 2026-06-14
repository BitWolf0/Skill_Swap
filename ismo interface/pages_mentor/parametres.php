<?php
require_once __DIR__ . '/../backend/config.php';
requireAuth();
$user = getCurrentUser($db);

$pageTitle = 'ISMO-SkillSwap — Paramètres';
$currentPage = 'parametres';
$basePath = '..';

include __DIR__ . '/../backend/includes/header.php';
?>
<?php include __DIR__ . '/../backend/includes/sidebar_mentor.php'; ?>
<?php include __DIR__ . '/../backend/includes/topbar.php'; ?>

<main class="content-area" id="main-content">
  <section class="content-main">
    <div class="page-head">
      <div class="page-title-wrap">
        <h1 class="page-title">Paramètres</h1>
      </div>
    </div>
    <div class="settings-section">
      <h2>Paramètres du compte</h2>
      <p class="text-muted">Gérez vos préférences et informations personnelles.</p>
      <ul class="settings-list">
        <li><a href="profile.php">Modifier mon profil</a></li>

      </ul>
    </div>
  </section>
</main>
<?php include __DIR__ . '/../backend/includes/footer.php'; ?>
